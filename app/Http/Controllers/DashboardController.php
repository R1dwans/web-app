<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Event;
use App\Models\Facility;
use App\Models\Page;
use App\Models\ProgramStudy;
use App\Models\User;
use App\Models\Document;
use App\Models\Slider;
use App\Models\Menu;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'articles' => Article::count(),
            'events' => Event::count(),
            'facilities' => Facility::count(),
            'pages' => Page::count(),
            'program_studies' => ProgramStudy::count(),
            'users' => User::count(),
            'documents' => Document::count(),
            'sliders' => Slider::count(),
        ];

        $recentArticles = Article::with('user', 'category')
            ->latest()
            ->take(5)
            ->get(['id', 'title', 'slug', 'created_at', 'user_id', 'category_id', 'image']);

        $upcomingEvents = Event::where('start_date', '>=', now())
            ->where('is_published', true)
            ->orderBy('start_date')
            ->take(5)
            ->get(['id', 'title', 'slug', 'start_date', 'location']);

        $recentUsers = User::latest()
            ->take(5)
            ->get(['id', 'name', 'email', 'created_at']);

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentArticles' => $recentArticles,
            'upcomingEvents' => $upcomingEvents,
            'recentUsers' => $recentUsers,
        ]);
    }
}
