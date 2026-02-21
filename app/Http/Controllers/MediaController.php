<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $images = [];
        $directories = ['articles', 'content-images'];

        foreach ($directories as $directory) {
            $files = Storage::disk('public')->files($directory);
            foreach ($files as $file) {
                // Filter only images
                if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
                    $images[] = [
                        'url' => asset('storage/' . $file),
                        'path' => $file,
                        'name' => basename($file),
                        'size' => Storage::disk('public')->size($file),
                        'last_modified' => Storage::disk('public')->lastModified($file),
                    ];
                }
            }
        }

        // Sort by newest first
        usort($images, function ($a, $b) {
            return $b['last_modified'] - $a['last_modified'];
        });

        return response()->json($images);
    }
}
