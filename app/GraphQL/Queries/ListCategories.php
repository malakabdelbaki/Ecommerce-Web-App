<?php

namespace App\GraphQL\Queries;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

class ListCategories
{
    public function resolve()
    {
       try {
        $categories = DB::select("
                WITH RECURSIVE category_path AS (
                    SELECT
                        id,
                        name,
                        parent_id,
                        CAST(name AS CHAR(255)) AS path
                    FROM
                        categories
                    WHERE
                        parent_id IS NULL

                    UNION ALL

                    SELECT
                        c.id,
                        c.name,
                        c.parent_id,
                        CONCAT(cp.path, ' → ', c.name) AS path
                    FROM
                        categories c
                    INNER JOIN
                        category_path cp ON c.parent_id = cp.id
                )
                SELECT
                    id,
                    name,
                    parent_id,
                    path
                FROM
                    category_path
            ");

        $categoryModels = Category::hydrate($categories);

        return $categoryModels;
       }
       catch (\Exception $e) {
           throw new \GraphQL\Error\Error('Failed to load categories: ', $e->getMessage());
       }
    }
}
