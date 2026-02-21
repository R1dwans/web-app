<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\ProgramStudy;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    /**
     * Public staff listing
     */
    public function indexPublic()
    {
        $staff = Staff::where('is_active', true)
            ->with('programStudy')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $programStudies = ProgramStudy::orderBy('name')->get();

        return Inertia::render('Public/Staff/Index', [
            'staff' => $staff,
            'programStudies' => $programStudies,
        ]);
    }

    /**
     * Admin listing
     */
    public function index(Request $request)
    {
        $staff = Staff::with('programStudy')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $stats = [
            'total' => $staff->count(),
            'dosen' => $staff->where('staff_type', 'dosen')->count(),
            'tendik' => $staff->where('staff_type', 'tendik')->count(),
            'pimpinan' => $staff->where('staff_type', 'pimpinan')->count(),
            'active' => $staff->where('is_active', true)->count(),
        ];

        return Inertia::render('Admin/Staff/Index', [
            'staff' => $staff,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new staff member.
     */
    public function create()
    {
        $programStudies = ProgramStudy::orderBy('name')->get();

        return Inertia::render('Admin/Staff/Create', [
            'programStudies' => $programStudies,
        ]);
    }

    /**
     * Store a newly created staff member.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'position' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'education' => 'nullable|string|max:500',
            'expertise' => 'nullable|string',
            'bio' => 'nullable|string',
            'program_study_id' => 'nullable|exists:program_studies,id',
            'staff_type' => 'required|in:dosen,tendik,pimpinan',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('staff', 'public');
        }

        Staff::create($validated);

        return Redirect::route('staff.index')->with('success', 'Data staf berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified staff member.
     */
    public function edit(Staff $staff)
    {
        $programStudies = ProgramStudy::orderBy('name')->get();

        return Inertia::render('Admin/Staff/Edit', [
            'staff' => $staff->load('programStudy'),
            'programStudies' => $programStudies,
        ]);
    }

    /**
     * Update the specified staff member.
     */
    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'position' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'education' => 'nullable|string|max:500',
            'expertise' => 'nullable|string',
            'bio' => 'nullable|string',
            'program_study_id' => 'nullable|exists:program_studies,id',
            'staff_type' => 'required|in:dosen,tendik,pimpinan',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($staff->photo) {
                Storage::disk('public')->delete($staff->photo);
            }
            $validated['photo'] = $request->file('photo')->store('staff', 'public');
        } else {
            unset($validated['photo']);
        }

        $staff->update($validated);

        return Redirect::route('staff.index')->with('success', 'Data staf berhasil diperbarui.');
    }

    /**
     * Remove the specified staff member.
     */
    public function destroy(Staff $staff)
    {
        if ($staff->photo) {
            Storage::disk('public')->delete($staff->photo);
        }

        $staff->delete();

        return Redirect::route('staff.index')->with('success', 'Data staf berhasil dihapus.');
    }
}
