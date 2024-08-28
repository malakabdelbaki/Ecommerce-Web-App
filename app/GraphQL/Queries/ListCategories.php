<?php

namespace App\GraphQL\Queries;

use App\Models\Category;

class ListCategories
{
    /**
     * Create a new class instance.
     */
    public function resolve()
    {
        try {
            return Category::whereNull('parent_id')->with('children')->get();
        }
        catch (\Exception $e) {
            throw new \GraphQL\Error\Error('Failed to load categories: ', $e->getMessage());
        }
    }
}
