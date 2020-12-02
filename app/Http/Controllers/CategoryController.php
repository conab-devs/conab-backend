<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Category;
use App\Http\Requests\Category\StoreRequest;
use App\Http\Requests\Category\UpdateRequest;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::all(), 200);
    }

    public function show(Category $category)
    {
        return response()->json($category, 200);
    }

    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        if (Gate::denies('admin-conab')) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        $category = Category::create($validated);
        $category->save();

        return response()->json($category, 201);
    }

    public function update(UpdateRequest $request, Category $category)
    {
        $validated = $request->validated();

        if (Gate::denies('admin-conab')) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        $category->update($validated);

        return response()->json($category, 200);
    }

    public function destroy(Category $category)
    {
        if (Gate::denies('admin-conab')) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        $category->delete();
        return response()->json(null, 204);
    }
}
