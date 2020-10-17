<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use Illuminate\Support\Facades\Gate;

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

        if (Gate::denies('admin-conab')) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

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

        if (Gate::denies('admin-conab')) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        $category->update($validated);

        return response()->json($category, 200);
    }

    public function destroy(\App\Category $category)
    {
        if (Gate::denies('admin-conab')) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        $category->delete();
        return response()->json([], 204);
    }
}
