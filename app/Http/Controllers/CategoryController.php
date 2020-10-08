<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::all(), 200);
    }

    public function show(\App\Category $category)
    {
        return response()->json($category, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:categories',
            'description' => 'string'
        ]);

        $category = Category::create($validated);
        $category->save();

        return response()->json($category, 201);
    }

    public function update(Request $request, \App\Category $category)
    {
        $validated = $request->validate([
            'name' => 'string|unique:categories',
            'description' => 'string'
        ]);

        $category->update($validated);

        return response()->json($category, 200);
    }
}
