<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Article;
use App\Models\Page;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::withCount('items')->latest()->get();
        return Inertia::render('Admin/Menus/Index', [
            'menus' => $menus,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Menus/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255|unique:menus,location',
            'is_active' => 'boolean',
        ]);

        Menu::create([
            'name' => $request->name,
            'location' => $request->location,
            'is_active' => $request->is_active ?? true,
        ]);

        return Redirect::route('menus.index')->with('success', 'Menu created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        return Inertia::render('Admin/Menus/Edit', [
            'menu' => $menu->load(['items' => function ($query) {
                $query->orderBy('order');
            }]),
            'articles' => Article::where('is_published', true)
                ->select('id', 'title', 'slug')
                ->orderBy('title')
                ->get(),
            'pages' => Page::where('is_published', true)
                ->select('id', 'title', 'slug')
                ->orderBy('title')
                ->get(),
            'categories' => Category::select('id', 'name', 'slug')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255|unique:menus,location,'.$menu->id,
            'is_active' => 'boolean',
        ]);

        $menu->update([
            'name' => $request->name,
            'location' => $request->location,
            'is_active' => $request->is_active ?? true,
        ]);

        return Redirect::back()->with('success', 'Menu details updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();

        return Redirect::route('menus.index')->with('success', 'Menu deleted successfully.');
    }
}
