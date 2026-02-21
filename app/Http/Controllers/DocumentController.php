<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documents = Document::latest()->get();
        
        $stats = [
            'total' => $documents->count(),
            'public' => $documents->where('is_public', true)->count(),
            'private' => $documents->where('is_public', false)->count(),
        ];

        return Inertia::render('Admin/Documents/Index', [
            'documents' => $documents,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Documents/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'file' => 'required|file|max:6144', // Max 6MB
            'is_public' => 'boolean',
        ]);

        $filePath = $request->file('file')->store('documents', 'public');

        Document::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'description' => $request->description,
            'category' => $request->category,
            'file_path' => $filePath,
            'is_public' => $request->is_public ?? true,
        ]);

        return Redirect::route('documents.index')->with('success', 'Document uploaded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        return Storage::download($document->file_path, $document->title . '.' . pathinfo($document->file_path, PATHINFO_EXTENSION));
    }

    public function indexPublic()
    {
        $documents = Document::where('is_public', true)
            ->latest()
            ->get()
            ->groupBy('category');

        return Inertia::render('Public/Documents/Index', [
            'groupedDocuments' => $documents,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        return Inertia::render('Admin/Documents/Edit', [
            'document' => $document,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'file' => 'nullable|file|max:6144', // Max 6MB
            'is_public' => 'boolean',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'is_public' => $request->is_public ?? true,
        ];

        if ($request->title !== $document->title) {
            $data['slug'] = Str::slug($request->title) . '-' . Str::random(5);
        }

        if ($request->hasFile('file')) {
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
            $data['file_path'] = $request->file('file')->store('documents', 'public');
        }

        $document->update($data);

        return Redirect::route('documents.index')->with('success', 'Document updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return Redirect::route('documents.index')->with('success', 'Document deleted successfully.');
    }
}
