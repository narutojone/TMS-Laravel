<?php

namespace App\Http\Controllers;

use App\Repositories\File\File;
use App\Repositories\FolderTemplate\FolderTemplate;
use Illuminate\Http\Request;
use FileVault;

class FilesController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function download($path, $fileName)
	{
		return response()->download(storage_path('uploads/' . $path . '/' . $fileName));
	}

	public function fileVaultDownload($id)
    {
        $file = File::where('filevault_id', $id)->first();
        if(!$file) {
            abort(404, 'File not found');
        }

        $response = FileVault::download($id);

        return response($response['data'], 200, [
            'Content-Type'        => $response['Content-Type'],
            'Content-Disposition' => 'attachment; filename="'.$file->name.'"',
        ]);
    }

	public function updateFile(Request $request)
    {
        if(!$request->hasFile('file')) {
            return redirect()->back()->with('error', 'Missing file!');
        }
        if(!$request->filled('client-id')) {
            return redirect()->back()->with('error', 'Internal error! client-id not found');
        }
        if(!$request->filled('folder-id')) {
            return redirect()->back()->with('error', 'Internal error! folder-id not found');
        }
        if(!$request->filled('file-id')) {
            return redirect()->back()->with('error', 'Internal error! file-id not found');
        }

        $clientId = $request->get('client-id');

        $existingFile = File::where('id', $request->get('file-id'))->first();
        $folder = FolderTemplate::where('id', $request->get('folder-id'))->first();
        $file = $request->file('file');
        $result = FileVault::update($existingFile->filevault_id, $clientId, $file, $folder->fullPath(), $existingFile->name);
        if(!$result) {
            return redirect()->back()->with('error', 'Internal error! FileVault could not be updated!');
        }

        // Update file 'updated_at' timestamp
        $existingFile->touch();
        return redirect()->back()->with('success', 'File updated!');
    }
}
