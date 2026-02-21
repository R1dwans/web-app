<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class FacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $facilities = Facility::latest()->get();
        
        $stats = [
            'total' => $facilities->count(),
            'active' => $facilities->where('is_active', true)->count(),
            'inactive' => $facilities->where('is_active', false)->count(),
        ];

        return Inertia::render('Admin/Facilities/Index', [
            'facilities' => $facilities,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Facilities/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer',
            'image' => 'nullable|image|max:6144', // Max 6MB
            'is_active' => 'boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('facilities', 'public');
        }

        Facility::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'description' => $request->description,
            'location' => $request->location,
            'capacity' => $request->capacity,
            'image' => $imagePath,
            'is_active' => $request->is_active ?? true,
        ]);

        return Redirect::route('facilities.index')->with('success', 'Facility created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function showPublic(Facility $facility)
    {
        if (!$facility->is_active) {
            abort(404);
        }

        return Inertia::render('Public/Facilities/Show', [
            'facility' => $facility,
        ]);
    }

    public function indexPublic()
    {
        $facilities = Facility::where('is_active', true)->latest()->get();
        return Inertia::render('Public/Facilities/Index', [
            'facilities' => $facilities,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Facility $facility)
    {
        return Inertia::render('Admin/Facilities/Edit', [
            'facility' => $facility,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Facility $facility)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer',
            'image' => 'nullable|image|max:6144', // Max 6MB
            'is_active' => 'boolean',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'capacity' => $request->capacity,
            'is_active' => $request->is_active ?? true,
        ];

        if ($request->title !== $facility->title) {
            $data['slug'] = Str::slug($request->title) . '-' . Str::random(5);
        }

        if ($request->hasFile('image')) {
            if ($facility->image) {
                Storage::disk('public')->delete($facility->image);
            }
            $data['image'] = $request->file('image')->store('facilities', 'public');
        }

        $facility->update($data);

        return Redirect::route('facilities.index')->with('success', 'Facility updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Facility $facility)
    {
        if ($facility->image) {
            Storage::disk('public')->delete($facility->image);
        }

        $facility->delete();

        return Redirect::route('facilities.index')->with('success', 'Facility deleted successfully.');
    }
}
