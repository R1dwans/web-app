<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Page;
use App\Models\Event;
use App\Models\Facility;
use App\Models\ProgramStudy;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return Inertia::render('Public/Search', [
                'results' => [],
                'query' => '',
                'recentArticles' => Article::where('is_published', true)->with('category')->latest()->take(5)->get(),
                'categories' => Category::withCount(['articles' => fn($q) => $q->where('is_published', true)])->get(),
            ]);
        }

        $results = collect();

        // Articles
        $articles = Article::where('is_published', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            })
            ->get()
            ->map(function ($article) {
                return [
                    'title' => $article->title,
                    'type' => 'Berita',
                    'url' => route('public.articles.show', $article->slug),
                    'description' => substr(strip_tags($article->content), 0, 150) . '...',
                ];
            });
        $results = $results->concat($articles);

        // Pages
        $pages = Page::where('is_published', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            })
            ->get()
            ->map(function ($page) {
                return [
                    'title' => $page->title,
                    'type' => 'Halaman',
                    'url' => route('public.pages.show', $page->slug),
                    'description' => substr(strip_tags($page->content), 0, 150) . '...',
                ];
            });
        $results = $results->concat($pages);

        // Events
        $events = Event::where('is_published', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->get()
            ->map(function ($event) {
                return [
                    'title' => $event->title,
                    'type' => 'Agenda',
                    'url' => route('public.events.show', $event->slug),
                    'description' => substr(strip_tags($event->description), 0, 150) . '...',
                ];
            });
        $results = $results->concat($events);

        // Facilities
        $facilities = Facility::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->get()
            ->map(function ($facility) {
                return [
                    'title' => $facility->title,
                    'type' => 'Fasilitas',
                    'url' => route('public.facilities.show', $facility->slug),
                    'description' => substr(strip_tags($facility->description), 0, 150) . '...',
                ];
            });
        $results = $results->concat($facilities);

        // Program Studies
        $prodis = ProgramStudy::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->get()
            ->map(function ($prodi) {
                return [
                    'title' => "{$prodi->degree} {$prodi->name}",
                    'type' => 'Program Studi',
                    'url' => route('public.program-studies.show', $prodi->slug),
                    'description' => substr(strip_tags($prodi->description), 0, 150) . '...',
                ];
            });
        $results = $results->concat($prodis);

        return Inertia::render('Public/Search', [
            'results' => $results,
            'query' => $query,
            'recentArticles' => Article::where('is_published', true)->with('category')->latest()->take(5)->get(),
            'categories' => Category::withCount(['articles' => fn($q) => $q->where('is_published', true)])->get(),
        ]);
    }
}
