<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:query-builder-playground')]
#[Description('Command description')]
class QueryBuilderPlayground extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $categories = Category::query()->with('products')->get();

        foreach ($categories as $category) {
            dump('Category name: '.$category->name);
            dump('Total products: '.count($category->products));

            foreach ($category->products as $product) {
                dump('--- Product name: '.$product->name);
            }

            dump('--------------------------------------------');
        }
    }
}
