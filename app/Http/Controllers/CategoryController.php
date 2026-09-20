<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Public browsing - no authorization needed
        if ($request->filled('search')) {
            $categories = Category::where('name', 'like', '%' . $request->search . '%')->get();
        } else {
            $categories = Cache::remember('all_categories', now()->addHours(24), function () {
                return Category::all();
            });
        }

        return view('categories.index', [
            'categories' => $categories,
            'search' => $request->search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Category::class);
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Cache::forget('all_categories');
        $this->authorize('create', Category::class);
        $validated = $request->validate(['name' => 'required|string|max:255']);
        $validated['slug'] = Str::slug($validated['name']);
        Category::create($validated);
        return redirect()->route('categories.index');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Public browsing - no authorization needed
        $category = Category::findOrFail($id);
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('update', $category);
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Cache::forget('all_categories');
        $category = Category::findOrFail($id);
        $this->authorize('update', $category);
        $validated = $request->validate(['name' => 'required|string|max:255']);
        $validated['slug'] = Str::slug($validated['name']);
        $category->update($validated);
        return redirect()->route('categories.index', $category->id);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Cache::forget('all_categories');
        $category = Category::findOrFail($id);
        $this->authorize('delete', $category);
        $category->delete();
        return redirect()->route('categories.index');
    }
}
