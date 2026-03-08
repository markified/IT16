<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::active()->withCount('products')
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

    /**
     * Archive the specified category.
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->update(['is_archived' => true]);

            return redirect()->route('categories.index')->with('success', 'Category archived successfully');
        } catch (\Exception $e) {
            return redirect()->route('categories.index')->with('error', 'Failed to archive category. Please try again.');
        }
    }

    /**
     * Display archived categories.
     */
    public function archived()
    {
        $categories = Category::archived()->orderBy('updated_at', 'DESC')->get();

        return view('categories.archived', compact('categories'));
    }

    /**
     * Restore an archived category.
     */
    public function restore(string $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->update(['is_archived' => false]);

            return redirect()->back()->with('success', 'Category restored successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to restore category. Please try again.');
        }
    }

    /**
     * Permanently delete an archived category.
     */
    public function permanentDelete(string $id)
    {
        try {
            $category = Category::findOrFail($id);

            // Only allow permanent deletion of archived categories
            if (! $category->is_archived) {
                return redirect()->back()->with('error', 'Only archived categories can be permanently deleted.');
            }

            // Check if category has products
            if ($category->products()->count() > 0) {
                return redirect()->back()->with('error', 'Cannot delete category with assigned products.');
            }

            $categoryName = $category->name;
            $category->delete();

            return redirect()->back()->with('success', "Category '{$categoryName}' has been permanently deleted.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete category permanently. Please try again.');
        }
    }
}
