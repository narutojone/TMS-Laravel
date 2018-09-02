<?php

namespace App\Http\Controllers;

use App\Repositories\File\File;
use App\Repositories\FolderTemplate\FolderTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FoldersStructureController extends Controller
{

	public function index(Request $request) {

		$mainFolders = FolderTemplate::where('parent_id', '=', 0)->whereNull('client_id')->get();

		return view('settings.folder-structure')->with([
			'mainFolders' => $mainFolders,
		]);
	}


	public function addFolder(Request $request, $parentId)
	{
		$parentFolder = ($parentId != 0 ) ? FolderTemplate::where('id', $parentId)->first()->name : '- none -';

		return view('settings.folder-structure-add')->with([
			'parentFolder' => $parentFolder,
			'parentFolderId' => $parentId
		]);
	}

	public function saveFolder(Request $request) {
		$this->validate($request, [
			'name' => 'required',
		]);

		FolderTemplate::create([
			'name' => $request->input('name'),
			'parent_id' => $request->input('parent_id')
		]);

		return redirect()
			->action('FoldersStructureController@index')
			->with('success', 'Folder added.');

	}

	public function deleteFolder(Request $request)
	{
		$returnData = ['success'=>false];
		$folderId = $request->get('folderId');

		$folder = FolderTemplate::where('id', $folderId)->whereNull('client_id')->first();
		if(!$folder) {
			$returnData['error'] = "Folder not found";
			return response()->json($returnData);
		}

		$subfolders = $folder->subfolders->count();
		if($subfolders) {
			$returnData['error'] = "Folder is not empty, It contains subfolders";
			return response()->json($returnData);
		}

		// Check if folder is empty (no files and no subfolders)
		$files = File::where('folder_id', $folder->id)->count();
		if($files) {
			$returnData['error'] = "Folder is not empty, It contains {$files} file(s)";
			return response()->json($returnData);
		}

		// Check if any modules use this folder
		$modulesWichUseFolders = [1];
		$modules = DB::table('template_subtasks_modules')->whereIn('subtask_module_id', $modulesWichUseFolders)->get();
		foreach($modules as $module) {
			if(isset($module->settings)) {
				$settings = json_decode($module->settings, true);
				if(isset($settings['folder']) && $settings['folder'] == $folderId) {
					$returnData['error'] = "Folder is used by subtask #".$module->subtask_id;
					return response()->json($returnData);
				}
			}
		}

		$folder->delete();

		$returnData['success'] = true;
		return response()->json($returnData);
	}
}