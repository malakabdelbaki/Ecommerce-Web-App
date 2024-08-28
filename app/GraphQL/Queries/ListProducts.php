<?php

namespace App\GraphQL\Queries;

use App\Models\Product;

class ListProducts
{
    /**
     * Create a new class instance.
     */
    public function resolve($root, array $args)
    {
        $input = $args['input'];
        $query= Product::query();

        if(!empty($input['category_id'])){
            $query->where('category_id',$input['category_id']);
        }

        if(!empty($input['search'])){
            $query->whereRaw('name LIKE ?',
                                  ['%'.$input['search'].'%']);
        }

        if(!empty($input['sort'])){
            $sortField = $input['sort']['field'];
            $sortDirection = $input['sort']['direction'];

            $validSortFields = ['price', 'created_at']; 
            if (in_array($sortField, $validSortFields)) {
                $query->orderBy($sortField, $sortDirection);
            }
        }

        return $query->paginate($input['count']??10 ,['*'],'page',$input['page'] ?? 1);
    }
}
