<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::latest()->get();
        return Inertia::render('Admin/Pages/Index', [
            'pages' => $pages,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Pages/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'nullable|string',
            'is_published' => 'boolean',
            'layout' => 'nullable|in:default,full,sidebar-left,sidebar-right,centered',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);

        $page = Page::create([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'is_published' => $request->is_published ?? false,
            'layout' => $request->layout ?? 'default',
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        return Redirect::route('pages.edit', $page->id)->with('success', 'Page created successfully.');
    }

    public function edit(Page $page)
    {
        return Inertia::render('Admin/Pages/Edit', [
            'page' => $page,
        ]);
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'nullable|string',
            'is_published' => 'boolean',
            'layout' => 'nullable|in:default,full,sidebar-left,sidebar-right,centered',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);

        $page->update([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'is_published' => $request->is_published ?? false,
            'layout' => $request->layout ?? 'default',
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        return Redirect::back()->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return Redirect::route('pages.index')->with('success', 'Page deleted successfully.');
    }

    // Public
    public function showPublic(Page $page)
    {
        if (!$page->is_published) {
            abort(404);
        }

        $recentArticles = \App\Models\Article::where('is_published', true)
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        $recentEvents = \App\Models\Event::where('is_published', true)
            ->latest('start_date')
            ->take(3)
            ->get();

        return Inertia::render('Public/Page', [
            'page' => $page,
            'recentArticles' => $recentArticles,
            'recentEvents' => $recentEvents,
        ]);
    }
}
