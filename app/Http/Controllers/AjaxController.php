<?php

namespace App\Http\Controllers;

use App\Repositories\Client\Client;
use App\Repositories\File\File;
use App\Repositories\FolderTemplate\FolderTemplate;
use App\Repositories\Template\TemplateInterface;
use App\Repositories\TemplateSubtask\TemplateSubtaskInterface;
use App\Repositories\User\User;
use FileVault;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get template.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function getTemplate (Request $request)
    {
        $templateRepository = app()->make(TemplateInterface::class);
        $templateSubtaskRepository = app()->make(TemplateSubtaskInterface::class);
        $templateSubtasks = [];

        $template = $templateRepository->find($request->input('templateId'));
        $templateSubtasksModels = $templateSubtaskRepository->model()->where('template_id', $template->id)->get();

        foreach ($templateSubtasksModels as $templateSubtasksModel) {
            $templateSubtasks[$templateSubtasksModel->title] = $templateSubtasksModel->versions()->first()->description;
        }


        return [
            'template' => [
                'title' => $template->title,
                'description' => $template->versions->first()->description,
            ],
            'templateSubtasks' => $templateSubtasks,
        ];
    }

	public function storeClientFile(Request $request, $clientId)
	{
		if(!$request->filled('folder')) {
			return response('Folder ID is required', 400);
		}

		$file = $request->file('file');
		$fileName = $file->getClientOriginalName();
		$folderId = $request->get('folder');

		// Check if filename already exists
		$fileExists = File::where('client_id', $clientId)->where('folder_id', $folderId)->where('name', 'LIKE', $fileName)->count();
		if($fileExists) {
			return response('File already exists', 400);
		}

		// Return error if filesize is above config limit
		if($file->getClientSize() > config('app.max_upload_file_size')){
			return response('File size is greater than '.(config('app.max_upload_file_size')/1024/1024).'MB.', 400);
		}

		$storagePath = FolderTemplate::where('id', $folderId)->first()->fullPath();
		$fileVaultId = FileVault::store($clientId, $file, $storagePath, $fileName);

		if($fileVaultId !== false) {
            File::create([
                'name' => $fileName,
                'client_id' => $clientId,
                'subtask_id' => null,
                'folder_id' => $folderId,
                'filevault_id' => $fileVaultId,
            ]);
            return 'file uploaded successfully';
        }
        else {
            return response('Internal error! File has not been uploaded. Please contact an administrator!', 400);
        }


	}

	public function deleteFile(Request $request, $clientId)
	{
		$returnData = ['success'=>false];
		$fileId = $request->get('fileId');

		// Check if file ID is present
		if(is_null($fileId)) {
			$returnData['error'] = "Missing file ID";
			return response()->json($returnData);
		}

		// Check if file belongs to client
		$file = File::where('id', $fileId)->where('client_id', $clientId)->first();
		if(!$file) {
			$returnData['error'] = "Invalid file";
			return response()->json($returnData);
		}

		// Delete the file
		$deleted = FileVault::delete($file->filevault_id);
		if(!$deleted) {
			$returnData['error'] = "File could not be deleted from FileVault";
			return response()->json($returnData);
		}

		$file->delete();

		$returnData['success'] = true;
		return response()->json($returnData);
	}

	public function createClientFolder(Request $request, $clientId)
	{
		$returnData = ['success'=>false];
		$folderName = $request->get('folderName','');

		// Check if folder name is present
        if(trim($folderName) == '') {
            $returnData['error'] = "Please enter a folder name";
            return response()->json($returnData);
        }

		// Check if client exists
		$client = Client::where('id', $clientId)->first();
		if(!$client) {
			$returnData['error'] = "Client not found";
			return response()->json($returnData);
		}

		// Check permissions
		if(!Auth::user()->hasRole(User::ROLE_ADMIN) && (Auth::user()->id != $client->manager_id) && (Auth::user()->id != $client->employee_id) ) {
			$returnData['error'] = "Unauthorised action";
			return response()->json($returnData);
		}

		// Check if folder already exists
		$existingFolder = FolderTemplate::where('name', 'LIKE', $folderName)->where('parent_id', $request->get('parent'))->where('client_id', $client->id)->count();
		if($existingFolder) {
			$returnData['error'] = "Folder already exists";
			return response()->json($returnData);
		}

		// Create the folder
		FolderTemplate::create([
			'parent_id'	=> $request->get('parent'),
			'client_id'	=> $clientId,
			'name'		=> $folderName,
		]);

		$returnData['success'] = true;
		return response()->json($returnData);
	}

	public function deleteClientFolder(Request $request, $clientId)
	{
		$returnData = ['success'=>false];
		$folderId = $request->get('folderId');

		// Check if folder belongs to the client
		$folder = FolderTemplate::where('id', $folderId)->where('client_id', $clientId)->first();
		if(!$folder) {
			$returnData['error'] = "Unauthorised action";
			return response()->json($returnData);
		}

		// Check if folder is empty (no files and no subfolders)
		$files = File::where('folder_id', $folder->id)->count();
		if($files) {
			$returnData['error'] = "Folder is not empty, It contains {$files} file(s)";
			return response()->json($returnData);
		}

		$subfolders = $folder->subfolders($clientId)->count();
		if($subfolders) {
			$returnData['error'] = "Folder is not empty, It contains subfolders";
			return response()->json($returnData);
		}

		$folder->delete();

		$returnData['success'] = true;
		return response()->json($returnData);

	}
}
