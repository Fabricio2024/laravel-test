<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use Illuminate\Http\Request;

class EntityController extends Controller
{
    public function getEntity(string $category) 
    {
        $entity = Entity::query()
            ->where('category_id', $category)
            ->with('category')
            ->get();

        return response()->json([
            "success" => true,
            "data" => $entity
        ]);
    }
}
