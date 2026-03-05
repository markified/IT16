<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
            ->withSum('products', 'quantity')
            ->orderBy('name')
            ->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:categories,code',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Category name is required.',
            'code.required' => 'Category code is required.',
            'code.unique' => 'This category code is already in use.',
            'code.max' => 'Category code cannot exceed 10 characters.',
        ]);

        try {
            $validated['is_active'] = $request->has('is_active');
            Category::create($validated);
            return redirect()->route('categories.index')->with('success', 'Category created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create category. Please try again.');
        }
    }

    public function show(string $id)
    {
        $category = Category::with(['products' => function ($query) {
            $query->orderBy('name');
        }])->findOrFail($id);

        return view('categories.show', compact('category'));
    }

    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:categories,code,' . $id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Category name is required.',
            'code.required' => 'Category code is required.',
            'code.unique' => 'This category code is already in use.',
            'code.max' => 'Category code cannot exceed 10 characters.',
        ]);

        try {
            $validated['is_active'] = $request->has('is_active');
            $category->update($validated);
            return redirect()->route('categories.index')->with('success', 'Category updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update category. Please try again.');
        }
    }

    public function destroy(string $id)
    {
        try {
            $category = Category::findOrFail($id);

            if ($category->products()->count() > 0) {
                return redirect()->route('categories.index')
                    ->with('error', 'Cannot delete category with products. Move or delete products first.');
            }

            $category->delete();
            return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('categories.index')->with('error', 'Failed to delete category. Please try again.');
        }
    }
}
