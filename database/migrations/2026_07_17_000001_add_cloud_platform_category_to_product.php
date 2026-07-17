<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up(): void
    {
        // Create the category if it doesn't already exist
        $existing = DB::table('categories')->where('slug', 'cloud-platform')->first();

        $categoryId = $existing
            ? $existing->id
            : DB::table('categories')->insertGetId([
                'name' => 'Cloud Platform',
                'slug' => 'cloud-platform',
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        // Assign the category to the Core Platform product
        DB::table('products')
            ->where('slug', 'core-platform')
            ->whereNull('category_id')
            ->update(['category_id' => $categoryId]);
    }

    public function down(): void
    {
        $category = DB::table('categories')->where('slug', 'cloud-platform')->first();
        if ($category) {
            DB::table('products')->where('category_id', $category->id)->update(['category_id' => null]);
            DB::table('categories')->where('id', $category->id)->delete();
        }
    }
};
