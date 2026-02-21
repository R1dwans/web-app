<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Event;
use App\Models\Facility;
use App\Models\Page;
use App\Models\ProgramStudy;
use App\Models\Slider;
use Illuminate\Http\Request;

class PageBuilderDataController extends Controller
{
    /**
     * Fetch dynamic data for page builder blocks.
     * Called from the public-facing page to hydrate dynamic blocks.
     */
    public function fetch(Request $request)
    {
        $blocks = $request->input('blocks', []);
        $result = [];

        foreach ($blocks as $block) {
            $type = $block['type'] ?? null;
            $data = $block['data'] ?? [];
            $blockId = $block['id'] ?? null;

            if (!$type || !$blockId)
                continue;

            switch ($type) {
                case 'latest_articles':
                    $query = Article::where('is_published', true)
                        ->with(['user', 'category'])
                        ->latest();

                    if (!empty($data['category_id'])) {
                        $query->where('category_id', $data['category_id']);
                    }

                    $result[$blockId] = $query->take($data['limit'] ?? 6)->get();
                    break;

                case 'slider_module':
                    $result[$blockId] = Slider::where('is_active', true)
                        ->orderBy('order')
                        ->get();
                    break;

                case 'events_list':
                    $query = Event::where('is_published', true)
                        ->orderBy('start_date');

                    if (!empty($data['show_upcoming_only'])) {
                        $query->where('start_date', '>=', now());
                    }

                    $result[$blockId] = $query->take($data['limit'] ?? 6)->get();
                    break;

                case 'facilities_grid':
                    $result[$blockId] = Facility::where('is_active', true)
                        ->take($data['limit'] ?? 8)
                        ->get();
                    break;

                case 'program_studies':
                    $result[$blockId] = ProgramStudy::orderBy('name')
                        ->get();
                    break;
            }
        }

        return response()->json($result);
    }

    /**
     * Get available categories for article filter in builder.
     */
    public function categories()
    {
        return response()->json(
            \App\Models\Category::select('id', 'name')->orderBy('name')->get()
        );
    }
}
