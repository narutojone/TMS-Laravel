<?php

namespace App\Http\Controllers;

use App\Repositories\LibraryFile\LibraryFile;
use App\Repositories\LibraryFolder\LibraryFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $mainFolders = LibraryFolder::where('parent_id', '=', 0)->get();
        $files = LibraryFile::all();

        return view('library.index')->with([
            'mainFolders' => $mainFolders,
            'files'       => $files,
        ]);
    }

    public function indexSettings(Request $request)
    {
        $mainFolders = LibraryFolder::where('parent_id', '=', 0)->get();

        $files = LibraryFile::all();

        return view('settings.library.index')->with([
            'mainFolders' => $mainFolders,
            'files'       => $files,
        ]);
    }

    public function uploadFiles(Request $request)
    {
        $files = $request->file('files');

        // Check if we have at least 1 file uploaded
        if( count($files) == 0) {
            return redirect()->back()->withError('No file uploaded!');
        }
        // Check if folder destination is set
        if(!$request->filled('folderId')) {
            return redirect()->back()->withError('Invalid folder destination');
        }
        // Check if destination folder is valid
        $folder = LibraryFolder::find($request->input('folderId'));
        if(!$folder) {
            return redirect()->back()->withError('Folder destination does not exist');
        }


        foreach($files as $file) {
            $storagePath = $folder->fullPath();
            $fileName = $file->getClientOriginalName();
            LibraryFile::create([
                'name'      => $fileName,
                'folder_id' => $folder->id,
                'path'      => $storagePath,
            ]);
            Storage::disk('uploads')->putFileAs('/library/'.$storagePath, $file, $fileName);
        }

        return redirect()->action('LibraryController@indexSettings')->with('success', 'Files uploaded!');
    }


    public function addFolder(Request $request, $parentId)
    {
        $parentFolder = ($parentId != 0 ) ? LibraryFolder::where('id', $parentId)->first()->name : '- none -';

        return view('settings.library.folder-add')->with([
            'parentFolder' => $parentFolder,
            'parentFolderId' => $parentId
        ]);
    }

    public function saveFolder(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        LibraryFolder::create([
            'name'      => $request->input('name'),
            'parent_id' => $request->input('parent_id', 0),
            'visible'   => $request->input('visible', 'off') == 'on' ? 1 : 0,
        ]);

        return redirect()
            ->action('LibraryController@indexSettings')
            ->with('success', 'Folder added.');

    }

    public function downloadFile(Request $request, $id)
    {
        // check if file exists
        $file = LibraryFile::where('id', $id)->first();
        if(!$file) {
            abort(404, 'File does not exist');
        }

        return response()->download(storage_path('uploads/library/' . $file->path . '/' . $file->name));
    }

    public function deleteFile(Request $request)
    {
        $returnData = ['success'=>false];
        $fileId = $request->get('fileId');

        // Check if file ID is present
        if(is_null($fileId)) {
            $returnData['error'] = "Missing file ID";
            return response()->json($returnData);
        }

        // Check if file exists
        $file = LibraryFile::where('id', $fileId)->first();
        if(!$file) {
            $returnData['error'] = "Invalid file";
            return response()->json($returnData);
        }

        // Delete the file
        if(Storage::disk('uploads')->has('/library/' . $file->path, $file->name)) {
            Storage::disk('uploads')->delete('/library/' . $file->path . '/' . $file->name);
        }

        $file->delete();

        $returnData['success'] = true;
        return response()->json($returnData);
    }

    public function deleteFolder(Request $request)
    {
        $returnData = ['success'=>false];
        $folderId = $request->get('folderId');

        // Check if folder ID is present
        if(is_null($folderId)) {
            $returnData['error'] = "Missing folder ID";
            return response()->json($returnData);
        }

        // Check if folder exists
        $folder = LibraryFolder::where('id', $folderId)->first();
        if(!$folder) {
            $returnData['error'] = "Invalid folder";
            return response()->json($returnData);
        }

        // Check if folder has subfolders
        if($folder->subfolders()->count() || $folder->files()->count()) {
            $returnData['error'] = "You can't delete this folder because it's not empty";
            return response()->json($returnData);
        }

        $folder->delete();

        $returnData['success'] = true;
        return response()->json($returnData);
    }

}