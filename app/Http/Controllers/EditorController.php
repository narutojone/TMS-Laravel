<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

// EditorController handles routes used by Froala WYSIWYG editor
class EditorController extends Controller
{
    const VISIBILITY_PUBLIC = 'public';
    const VISIBILITY_PRIVATE = 'private';

    protected $imagesBasePath = '/editor/images/';
    protected $imagesBasePathPublic = '/editor/images/public/';
    protected $allowedExtensions = ['jpg', 'jpeg', 'png'];
    protected $allowedVisibilityOptions = [self::VISIBILITY_PUBLIC, self::VISIBILITY_PRIVATE];

    public function storeImage(Request $request)
    {
        $file = $request->file('file');
        $visibility = $request->get('visibility', self::VISIBILITY_PRIVATE);

        // Check is a file has been uploaded
        if(!$file) {
            return response()->json(['error' => 'No file provided'], 500);
        }

        $fileExtension = $file->getClientOriginalExtension();

        // Validate file extension
        if(!in_array($fileExtension, $this->allowedExtensions)) {
            return response()->json(['error' => 'Unsupported file'], 422);
        }

        // Validate visibility options
        if(!in_array($visibility, $this->allowedVisibilityOptions)) {
            return response()->json(['error' => 'Visibility option not allowed'], 422);
        }

        $newFileName = $this->generateFileName($file->getClientOriginalExtension());

        if($visibility == self::VISIBILITY_PRIVATE) {
            Storage::disk('uploads')->putFileAs($this->imagesBasePath, $file, $newFileName);
            return response()->json([
                'link' => $this->imagesBasePath . $newFileName,
            ], 200);
        }
        else {
            Storage::disk('uploads')->putFileAs($this->imagesBasePathPublic, $file, $newFileName);
            return response()->json([
                'link' => $this->imagesBasePathPublic . $newFileName,
            ], 200);
        }
    }

    public function getPrivateImage(string $fileName)
    {
        $path = storage_path('uploads'. $this->imagesBasePath . $fileName);
        if(!File::exists($path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $file = File::get($path);
        $mimeType = File::mimeType($path);

        return response()->make($file, 200)->header('Content-Type', $mimeType);
    }

    public function getPublicImage(string $fileName)
    {
        $path = storage_path('uploads'. $this->imagesBasePathPublic . $fileName);
        if(!File::exists($path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $file = File::get($path);
        $mimeType = File::mimeType($path);

        return response()->make($file, 200)->header('Content-Type', $mimeType);
    }

    protected function generateFileName(string $extension) : string
    {
        return uniqid('', true) . '_' . Auth::user()->id . '.' . $extension;
    }
}