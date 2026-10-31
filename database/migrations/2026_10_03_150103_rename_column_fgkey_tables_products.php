<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('products', 'catergory_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->renameColumn('catergory_id', 'category_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('products', 'category_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->renameColumn('category_id', 'catergory_id');
            });
        }
    }
};
