<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryKeyword;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = Category::with('keywords')->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'color' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255'
        ]);

        $validated['slug'] = Str::slug($request['name']);
        if (is_null($request['icon'])) {
            $validated['icon'] = 'fa fa-circle';
        }

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'color' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255'
        ]);

        $validated['slug'] = Str::slug($request['name']);
        
        $category->update($validated);
        
        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        
        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * Show the form for editing keywords of the specified category.
     */
    public function editKeywords(Category $category)
    {
        $category->load('keywords');
        return view('categories.keywords', compact('category'));
    }

    /**
     * Update the keywords of the specified category.
     */
    public function updateKeywords(Request $request, Category $category)
    {
        $validated = $request->validate([
            'keywords' => 'required|array',
            'keywords.*' => 'required|string|max:255',
        ]);

        // Delete existing keywords
        $category->keywords()->delete();

        // Add new keywords
        foreach ($validated['keywords'] as $keyword) {
            if (!empty(trim($keyword))) {
                $category->keywords()->create(['keyword' => trim(strtolower($keyword))]);
            }
        }

        return redirect()->route('categories.keywords', $category)
            ->with('success', 'Category keywords updated successfully.');
    }
} 