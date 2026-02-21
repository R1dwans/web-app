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
            'blocks' => 'nullable|array',
            'editor_mode' => 'nullable|in:editor,builder',
            'is_published' => 'boolean',
            'layout' => 'nullable|in:default,full,sidebar-left,sidebar-right,centered',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);

        $page = Page::create([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->input('content'),
            'blocks' => $request->blocks,
            'editor_mode' => $request->editor_mode ?? 'editor',
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
            'blocks' => 'nullable|array',
            'editor_mode' => 'nullable|in:editor,builder',
            'is_published' => 'boolean',
            'layout' => 'nullable|in:default,full,sidebar-left,sidebar-right,centered',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);

        $page->update([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->input('content'),
            'blocks' => $request->blocks,
            'editor_mode' => $request->editor_mode ?? 'editor',
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

        // Hydrate dynamic blocks
        $dynamicData = [];
        if ($page->editor_mode === 'builder' && is_array($page->blocks)) {
            $dynamicData = self::hydrateDynamicBlocksStatic($page->blocks);
        }

        return Inertia::render('Public/Page', [
            'page' => $page,
            'recentArticles' => $recentArticles,
            'recentEvents' => $recentEvents,
            'dynamicData' => $dynamicData,
        ]);
    }

    /**
     * Hydrate dynamic blocks with data from the database.
     */
    public static function hydrateDynamicBlocksStatic(array $blocks): array
    {
        $data = [];
        $dynamicTypes = ['latest_articles', 'slider_module', 'events_list', 'facilities_grid', 'program_studies'];

        foreach ($blocks as $block) {
            if (!in_array($block['type'] ?? '', $dynamicTypes))
                continue;
            $blockData = $block['data'] ?? [];
            $blockId = $block['id'] ?? null;
            if (!$blockId)
                continue;

            switch ($block['type']) {
                case 'latest_articles':
                    $q = \App\Models\Article::where('is_published', true)->with(['user', 'category'])->latest();
                    if (!empty($blockData['category_id']))
                        $q->where('category_id', $blockData['category_id']);
                    $data[$blockId] = $q->take($blockData['limit'] ?? 6)->get();
                    break;
                case 'slider_module':
                    $data[$blockId] = \App\Models\Slider::where('is_active', true)->orderBy('order')->get();
                    break;
                case 'events_list':
                    $q = \App\Models\Event::where('is_published', true)->orderBy('start_date');
                    if (!empty($blockData['show_upcoming_only']))
                        $q->where('start_date', '>=', now());
                    $data[$blockId] = $q->take($blockData['limit'] ?? 6)->get();
                    break;
                case 'facilities_grid':
                    $data[$blockId] = \App\Models\Facility::where('is_active', true)->take($blockData['limit'] ?? 8)->get();
                    break;
                case 'program_studies':
                    $data[$blockId] = \App\Models\ProgramStudy::orderBy('name')->get();
                    break;
            }
        }

        return $data;
    }
}
