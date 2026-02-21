<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProgramStudyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\MediaController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Slider;
use App\Models\Article;
use App\Models\Event;

use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StaffController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'sliders' => Slider::where('is_active', true)->orderBy('order')->get(),
        'latestArticles' => Article::with(['user', 'category'])->where('is_published', true)->latest()->take(3)->get(),
        'upcomingEvents' => Event::where('is_published', true)->where('start_date', '>=', now())->orderBy('start_date')->take(3)->get(),
    ]);
})->name('welcome');

Route::get('/search', [SearchController::class, 'index'])->name('public.search');

Route::get('/prodi', [ProgramStudyController::class, 'indexPublic'])->name('public.program-studies.index');
Route::get('/prodi/{program_study:slug}', [ProgramStudyController::class, 'show'])->name('public.program-studies.show');
Route::get('/berita', [ArticleController::class, 'indexPublic'])->name('public.articles.index');
Route::get('/berita/{article:slug}', [ArticleController::class, 'showPublic'])->name('public.articles.show');
Route::get('/agenda', [EventController::class, 'indexPublic'])->name('public.events.index');
Route::get('/agenda/{event:slug}', [EventController::class, 'showPublic'])->name('public.events.show');
Route::get('/fasilitas', [FacilityController::class, 'indexPublic'])->name('public.facilities.index');
Route::get('/fasilitas/{facility:slug}', [FacilityController::class, 'showPublic'])->name('public.facilities.show');
Route::get('/download', [DocumentController::class, 'indexPublic'])->name('public.documents.index');
Route::get('/download/{document}', [DocumentController::class, 'show'])->name('public.documents.download');
Route::get('/staf', [StaffController::class, 'indexPublic'])->name('public.staff.index');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Admin Only Routes
    Route::group(['middleware' => ['role:admin']], function () {
        Route::resource('users', UserController::class);
        Route::resource('program-studies', ProgramStudyController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('menus', MenuController::class);
        Route::resource('menu-items', MenuItemController::class)->only(['store', 'update', 'destroy']);
        Route::post('menu-items/reorder', [MenuItemController::class, 'reorder'])->name('menu-items.reorder');
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::resource('staff', StaffController::class);
    });

    // Admin & Penulis Routes (Content Management)
    Route::group(['middleware' => ['role:admin|penulis']], function () {
        Route::post('/articles/upload-image', [ArticleController::class, 'uploadImage'])->name('articles.upload-image');
        Route::get('/media', [MediaController::class, 'index'])->name('media.index');
        Route::resource('articles', ArticleController::class);
        Route::resource('events', EventController::class);
        Route::resource('facilities', FacilityController::class);
        Route::resource('documents', DocumentController::class);
        Route::resource('sliders', SliderController::class);
        Route::resource('pages', PageController::class);
    });
});

require __DIR__.'/auth.php';

// IMPORTANT: This catch-all route MUST be the very last route
Route::get('/{page:slug}', [PageController::class, 'showPublic'])->name('public.pages.show');
