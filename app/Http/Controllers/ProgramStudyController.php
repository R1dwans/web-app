<?php

namespace App\Http\Controllers;

use App\Models\ProgramStudy;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redirect;

class ProgramStudyController extends Controller
{
    public function indexPublic()
    {
        $programStudies = ProgramStudy::latest()->get();
        return Inertia::render('Public/ProgramStudy/Index', [
            'programStudies' => $programStudies,
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programStudies = ProgramStudy::latest()->get();
        
        $stats = [
            'total' => $programStudies->count(),
            'bachelor' => $programStudies->where('degree', 'S1')->count(),
            'diploma' => $programStudies->where('degree', 'D3')->count(),
        ];

        return Inertia::render('Admin/ProgramStudies/Index', [
            'programStudies' => $programStudies,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/ProgramStudies/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'degree' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        ProgramStudy::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'degree' => $request->degree,
            'description' => $request->description,
        ]);

        return Redirect::route('program-studies.index')->with('success', 'Program Study created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProgramStudy $programStudy)
    {
        return Inertia::render('Public/ProgramStudy/Show', [
            'programStudy' => $programStudy,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProgramStudy $programStudy)
    {
        return Inertia::render('Admin/ProgramStudies/Edit', [
            'programStudy' => $programStudy,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProgramStudy $programStudy)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'degree' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        $programStudy->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name), // Update slug if name changes? Usually risky for SEO but for init dev okay.
            'degree' => $request->degree,
            'description' => $request->description,
        ]);

        return Redirect::route('program-studies.index')->with('success', 'Program Study updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProgramStudy $programStudy)
    {
        $programStudy->delete();

        return Redirect::route('program-studies.index')->with('success', 'Program Study deleted successfully.');
    }
}
