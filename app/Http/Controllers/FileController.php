<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\EncryptedFiles;
use Illuminate\Support\Facades\Crypt;

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
            'file' => 'required|file|max:2048', // 2MB (KB)
            'name' => 'nullable|string'
        ], [
            'file.max' => 'File too large. Maximum allowed size is 2MB.'
        ]);

        $file = $request->file('file');

        $originalName = $file->getClientOriginalName();

        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);

        $name = $request->input('name', $originalName);

        $counter = 1;
        while (EncryptedFiles::where('name', $name)->exists()) {
            $name = $baseName . " ($counter)" . ($extension ? ".$extension" : '');
            $counter++;
        }

        $fileContent = file_get_contents($file->getRealPath());
        $encryptedContent = Crypt::encryptString($fileContent);

        $storedName = uniqid() . '.enc';
        $path = 'uploads/' . $storedName;

        Storage::disk('local')->put($path, $encryptedContent);

        $savedFile = EncryptedFiles::create([
            'fs_name' => $path,
            'name' => $name,
            'added_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'File uploaded and encrypted successfully',
            'file' => $savedFile
        ]);
    }


    public function show($id)
    {
        $file = EncryptedFiles::find($id);

        if (!$file) {
            return response()->json([
                'message' => 'File not found'
            ], 404);
        }

        if (!Storage::disk('local')->exists($file->fs_name)) {
            return response()->json([
                'message' => 'File not found on disk'
            ], 404);
        }

        $encryptedContent = Storage::disk('local')->get($file->fs_name);

        $decryptedContent = Crypt::decryptString($encryptedContent);

        return response($decryptedContent, 200)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="'.$file->name.'"');
    }
    
}
