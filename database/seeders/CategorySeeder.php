<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * @var array<int, array{id: int, name: string, description: string}>
     */
    public $categories = [
        [
            'id' => 1,
            'name' => 'Cafe',
            'description' => '',
        ],
        [
            'id' => 2,
            'name' => 'Chicken',
            'description' => '',
        ],
    ];

    /**
     * @return array<int, array{id: int, name: string, description: string}>
     */
    private function getCategories(): array
    {
        return $this->categories;
    }

    public function run(): void
    {
        foreach ($this->getCategories() as $category) {
            Category::firstOrCreate(['id' => $category['id']], $category);
        }

        // Sinh thêm 1.000 records.
        Category::factory()->count(1000)->create();
    }
}
