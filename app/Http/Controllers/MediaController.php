<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Display a listing of media files.
     */
    public function index(Request $request)
    {
        $images = [];
        $directories = ['articles', 'content-images', 'pages'];

        foreach ($directories as $directory) {
            if (!Storage::disk('public')->exists($directory))
                continue;
            $files = Storage::disk('public')->files($directory);
            foreach ($files as $file) {
                if (preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $file)) {
                    $images[] = [
                        'url' => asset('storage/' . $file),
                        'path' => $file,
                        'name' => basename($file),
                        'directory' => $directory,
                        'size' => Storage::disk('public')->size($file),
                        'last_modified' => Storage::disk('public')->lastModified($file),
                    ];
                }
            }
        }

        usort($images, function ($a, $b) {
            return $b['last_modified'] - $a['last_modified'];
        });

        return response()->json($images);
    }

    /**
     * Upload a new media file.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,gif,webp,svg|max:5120',
            'directory' => 'nullable|string|in:articles,content-images,pages',
        ]);

        $directory = $request->input('directory', 'pages');
        $path = $request->file('file')->store($directory, 'public');

        return response()->json([
            'url' => asset('storage/' . $path),
            'path' => $path,
            'name' => basename($path),
            'directory' => $directory,
            'size' => Storage::disk('public')->size($path),
        ]);
    }

    /**
     * Delete a media file.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $path = $request->input('path');

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'File not found'], 404);
    }
}
