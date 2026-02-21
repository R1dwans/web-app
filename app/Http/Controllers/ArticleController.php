<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::with(['user', 'category'])->latest()->get();
        
        $stats = [
            'total' => $articles->count(),
            'published' => $articles->where('is_published', true)->count(),
            'draft' => $articles->where('is_published', false)->count(),
        ];

        return Inertia::render('Admin/Articles/Index', [
            'articles' => $articles,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Articles/Create', [
            'categories' => Category::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|max:6144', // Max 6MB, allow string or file
            'is_published' => 'boolean',
            'layout' => 'nullable|in:default,full,sidebar-left,sidebar-right,centered',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('articles', 'public');
        } elseif (is_string($request->image) && !empty($request->image)) {
            // Extract relative path from URL or use as is if it's a path
            // Example URL: http://domain.com/storage/articles/image.jpg
            // We want: articles/image.jpg
            $urlPath = parse_url($request->image, PHP_URL_PATH);
            $relativePath = str_replace('/storage/', '', $urlPath);
            $imagePath = ltrim($relativePath, '/');
        }

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->title) . '-' . Str::random(5);

        $article = Article::create([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'image' => $imagePath,
            'is_published' => $request->is_published ?? false,
            'layout' => $request->layout ?? 'default',
            'category_id' => $request->category_id,
            'user_id' => Auth::id(),
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
        ]);

        return Redirect::route('articles.edit', $article->id)->with('success', 'Article created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        //
    }

    public function showPublic(Article $article)
    {
        // Get related articles (same category, random or latest, exclude current)
        $relatedArticles = Article::with(['user', 'category'])
            ->where('is_published', true)
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->latest()
            ->take(3)
            ->get();

        return Inertia::render('Public/Articles/Show', [
            'article' => $article->load(['user', 'category']),
            'relatedArticles' => $relatedArticles,
        ]);
    }

    public function indexPublic(Request $request)
    {
        $query = Article::with(['user', 'category'])->where('is_published', true);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $articles = $query->latest()->paginate(9)->withQueryString();
        $categories = Category::has('articles')->get();
        $recentArticles = Article::where('is_published', true)->latest()->take(5)->get();

        return Inertia::render('Public/Articles/Index', [
            'articles' => $articles,
            'categories' => $categories,
            'recentArticles' => $recentArticles,
            'filters' => $request->only(['search', 'category']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        return Inertia::render('Admin/Articles/Edit', [
            'article' => $article,
            'categories' => Category::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug,' . $article->id,
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|max:6144', // Max 6MB
            'is_published' => 'boolean',
            'layout' => 'nullable|in:default,full,sidebar-left,sidebar-right,centered',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $data = [
            'title' => $request->title,
            'content' => $request->content,
            'is_published' => $request->is_published ?? false,
            'layout' => $request->layout ?? 'default',
            'category_id' => $request->category_id,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
        ];

        if ($request->slug) {
            $data['slug'] = Str::slug($request->slug);
        } elseif ($request->title !== $article->title) {
            $data['slug'] = Str::slug($request->title) . '-' . Str::random(5);
        }

        if ($request->hasFile('image')) {
            // Delete old image
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $data['image'] = $request->file('image')->store('articles', 'public');
        } elseif (is_string($request->image) && !empty($request->image)) {
             // Handle library selection
            $urlPath = parse_url($request->image, PHP_URL_PATH);
            $relativePath = str_replace('/storage/', '', $urlPath);
            $relativePath = ltrim($relativePath, '/');
            
            // Only update if different
            if ($article->image !== $relativePath) {
                 $data['image'] = $relativePath;
            }
        }

        $article->update($data);

        return Redirect::back()->with('success', 'Article updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }
        
        $article->delete();

        return Redirect::route('articles.index')->with('success', 'Article deleted successfully.');
    }

    /**
     * Upload image from Editor
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:6144', // Max 6MB
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('content-images', 'public');
            return response()->json(['url' => asset('storage/' . $path)]);
        }

        return response()->json(['error' => 'Image upload failed'], 400);
    }
}
