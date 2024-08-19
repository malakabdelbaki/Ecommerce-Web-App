<?php

namespace App\GraphQL\Queries;

use App\Models\Product;

class ListProducts
{
    /**
     * Create a new class instance.
     */
    public function resolve($root, array $args){
        $query= Product::query();

        if(!empty($args['category_id'])){
            $query->where('category_id',$args['category_id']);
        }

        if(!empty($args['search'])){
            $query->whereRaw('LOWER(name) LIKE ?',
                                  ['%'.strtolower($args['search']).'%']);
        }

        if(!empty($args['sort'])){
            switch($args['sort']){
                case 'price_asc':
                    $query->orderBy('price','asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price','desc');
                    break;
                case 'created_at_asc':
                    $query->orderBy('created_at','asc');
                    break;
                case 'created_at_desc':
                    $query->orderBy('created_at','desc');
                    break;

            }
        }

        $count = $args['count']??10;

        return $query->paginate($count,['*'],'page',$args['page'] ?? 1);
    }
}
