<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\EncryptedFiles;

class FileController
{

    public function index()
    {
        $files = EncryptedFiles::orderBy('name', 'asc')->get();

        return response()->json([
            'files' => $files
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'name' => 'nullable|string'
        ]);

        $file = $request->file('file');

        $originalName = $file->getClientOriginalName();

        // base name + extension split
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);

        // initial name choice
        $name = $request->input('name', $originalName);

        // check duplicates in DB
        $existingCount = EncryptedFiles::where('name', 'like', $baseName . '%')
            ->count();

        if ($existingCount > 0) {
            $name = $baseName . ' (' . ($existingCount + 1) . ')';
            if ($extension) {
                $name .= '.' . $extension;
            }
        }

        $path = $file->store('uploads', 'local');

        $savedFile = EncryptedFiles::create([
            'fs_name' => $path,
            'name' => $name,
            'added_by' => 1
        ]);

        return response()->json([
            'message' => 'File uploaded successfully',
            'file' => $savedFile
        ]);
    }


    public function show($id)
    {
        $file = EncryptedFiles::find($id);

        if (!$file) {
            return response()->json([
                'message' => 'File not found in database'
            ], 404);
        }

        $path = $file->fs_name;

        if (!Storage::disk('local')->exists($path)) {
            return response()->json([
                'message' => 'File not found'
            ], 404);
        }

        return response()->download(
            Storage::disk('local')->path($path),
            $file->name
        );
    }
    
}
