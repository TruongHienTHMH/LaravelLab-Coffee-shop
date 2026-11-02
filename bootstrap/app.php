<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
        // Global context
        $exceptions->context(fn () => [
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
        
        // Custom Report: Xử lý khi có lỗi xảy ra (Logging / Slack / Alert)
        $exceptions->report(function (Throwable $e) {
            //
        });
        // Custom Render: Bắt các lỗi cụ thể để trả về format JSON chuẩn cho API
        // 1. Bắt lỗi không tìm thấy Route hoặc Model (404 Not Found)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu hoặc đường dẫn API không tồn tại.',
                    'error_code' => 'RESOURCE_NOT_FOUND',
                ], 404);
            }
        });
        // 2. Bắt lỗi Validate Form Request (422 Unprocessable Entity)
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu gửi lên không hợp lệ.',
                    'error_code' => 'VALIDATION_FAILED',
                    'errors' => $e->errors(),
                ], 422);
            }
        });
        // 3. Bắt TẤT CẢ các lỗi còn lại trong ứng dụng (Catch-all cho mọi Throwable)
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                // Xác định HTTP Status Code (Nếu là HttpException thì lấy code của nó, ngược lại là 500)
                $statusCode = $e instanceof HttpException ? $e->getStatusCode() : 500;
                return response()->json([
                    'success' => false,
                    // Nếu đang bật debug (local) thì hiển thị message thật, nếu production thì che giấu
                    'message' => config('app.debug') 
                        ? $e->getMessage() 
                        : 'Hệ thống đã xảy ra sự cố. Vui lòng liên hệ hỗ trợ.',
                         'error_code' => 'INTERNAL_SERVER_ERROR',
                         'tracking_id'=> (string) Str::uuid(), // Request/Tracking ID
                ], $statusCode);
            }
        });
    })->create();
