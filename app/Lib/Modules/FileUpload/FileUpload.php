<?php
namespace App\Lib\Modules\FileUpload;

use App\Repositories\File\File;
use App\Repositories\FolderTemplate\FolderTemplate;
use App\Repositories\TemplateSubtask\TemplateSubtask;
use App\Repositories\Subtask\Subtask;
use FileVault;
use App\Lib\Modules\iModules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileUpload implements iModules {

    private $id = 1;
    private $moduleName = 'File upload';
    private $fileTemplatePath = 'subtasks-templates';

    public function validateRequest(TemplateSubtask $templateSubtask, $moduleData)
    {
        $errors = [];
        if(!isset($moduleData['folder']) || is_null($moduleData['folder'])) {
            $errors[] = $this->generateErrorLine('File upload location is required');
        }
        if( !is_numeric($moduleData['file-count']) || $moduleData['file-count']<1 ) {
            $errors[] = $this->generateErrorLine('The number of uploaded files should be greater than 0');
        }
        if( !is_numeric($moduleData['deadline-offset'])) {
            $errors[] = $this->generateErrorLine('The deadline offset should be an integer value');
        }
        return $errors;
    }

    public function getSettings(TemplateSubtask $subtask)
    {
        $module = DB::table('template_subtasks_modules')->where('subtask_id', $subtask->id)->where('subtask_module_id', $this->id)->first();
        $existingSettings = ($module) ? json_decode($module->settings, true) : [];

        return [
            'mainFolders'           => FolderTemplate::where('parent_id', '=', 0)->whereNull('client_id')->get(),
            'file-count-types'      => [1=>'Fixed amount', 2=>'At least'],
            'file-count-type'       => isset($existingSettings['file-count-type']) ? $existingSettings['file-count-type'] : 1,
            'file-count'            => isset($existingSettings['file-count']) ? $existingSettings['file-count'] : 1,
            'folder'                => isset($existingSettings['folder']) ? $existingSettings['folder'] : null,
            'file-template'         => isset($existingSettings['file-template']) ? $existingSettings['file-template'] : null,
            'has-template'          => isset($existingSettings['has-template']) ? $existingSettings['has-template'] : false,
            'year'                  => isset($existingSettings['year']) ? $existingSettings['year'] : false,
            'termin'                => isset($existingSettings['termin']) ? $existingSettings['termin'] : false,
            'deadline-offset'       => isset($existingSettings['deadline-offset']) ? $existingSettings['deadline-offset'] : 0,
            'custom-title'          => isset($existingSettings['custom-title']) ? $existingSettings['custom-title'] : '',
            'filename-structure'    => isset($existingSettings['filename-structure']) ? $existingSettings['filename-structure'] : '',
            'upload-required'       => isset($existingSettings['upload-required']) ? $existingSettings['upload-required'] : true,
        ];
    }

    public function update(TemplateSubtask $subtask, $params)
    {
        $active = (isset($params['active']) && $params['active']=='on');
        if(!$active) {
            DB::table('template_subtasks_modules')->where('subtask_id', $subtask->id)->where('subtask_module_id', $this->id)->delete();
        }
        else {
            $existingSettings = $this->getSettings($subtask);
            $settings = [
                'file-count-type'       => (isset($params['file-count-type'])) ? $params['file-count-type'] : 1,
                'file-count'            => (isset($params['file-count'])) ? $params['file-count'] : 1,
                'folder'                => (isset($params['folder'])) ? $params['folder'] : $existingSettings['folder'],
                'file-template'         => $existingSettings['file-template'],
                'has-template'          => (isset($params['has-template']) && $params['has-template']=='on') ? true : false,
                'year'                  => (isset($params['year']) && $params['year']=='on'),
                'termin'                => (isset($params['year']) && $params['year']=='on' && isset($params['termin']) && $params['termin']=='on'),
                'deadline-offset'       => (isset($params['year']) && $params['year']=='on' && isset($params['deadline-offset'])) ? $params['deadline-offset'] : 0,
                'custom-title'          => (isset($params['custom-title'])) ? $params['custom-title'] : '',
                'filename-structure'    => (isset($params['filename-structure'])) ? $params['filename-structure'] : '',
                'upload-required'       => (isset($params['upload-required'])) ? $params['upload-required'] : 0,
            ];

            // Check if file template is present and save it
            $storagePath = $this->getFileTemplateStoragePath($subtask->id);
            if(isset($params['has-template']) && $params['has-template']=='on' && isset($params['fileTemplate']) && !empty($params['fileTemplate'])) {
                // Remove old file template (if any)
                if(!is_null($existingSettings['file-template'])) {
                    if (Storage::disk('uploads')->has($storagePath, $existingSettings['file-template'])) {
                        Storage::disk('uploads')->delete($storagePath . '/' . $existingSettings['file-template']);
                    }
                }

                $fileName = $params['fileTemplate']->getClientOriginalName();
                Storage::disk('uploads')->putFileAs($storagePath, $params['fileTemplate'], $fileName);
                $settings['file-template'] = $fileName;
            }

            // check if we need to remove existing template
            if(!isset($params['has-template']) && !is_null($settings['file-template'])) {
                if(Storage::disk('uploads')->has($storagePath, $existingSettings['file-template'])) {
                    Storage::disk('uploads')->delete($storagePath .'/'. $existingSettings['file-template']);
                }
                $settings['file-template'] = null;
            }

            // Persist data
            $subtaskModule = DB::table('template_subtasks_modules')->where('subtask_id', $subtask->id)->where('subtask_module_id', $this->id)->exists();
            if(!$subtaskModule) {
                DB::table('template_subtasks_modules')->insert(
                    ['subtask_id' => $subtask->id, 'subtask_module_id' => $this->id, 'settings' => json_encode($settings)]
                );
            }
            else {
                DB::table('template_subtasks_modules')->where('subtask_id', $subtask->id)->where('subtask_module_id', $this->id)->update(
                    ['settings' => json_encode($settings)]
                );
            }
        }

        return [];
    }

    public function validateUserInput(Subtask $subtask, $moduleData)
    {
        $errors = [];
        if (isset($moduleData['has-files-for-upload']) && ($moduleData['has-files-for-upload'] == 0)) {
            if (empty($moduleData['upload-not-needed-reason'])) {
                $errors[] = $this->generateErrorLine('You need to specify a reason, for which the upload of file(s) is not needed.');
            }
        } else {
            // get storage location from subtask module template
            $settings = $this->getSettings($subtask->template);

            $errors = [];
            // Check if the correct number of files is present
            if(!isset($moduleData['file']) || is_null($moduleData['file']) || !is_array($moduleData['file'])) {
                $errors[] = $this->generateErrorLine('File upload is required');
            }

            if($settings['file-count-type'] == 1 && count($moduleData['file'])!=$settings['file-count']) {
                $errors[] = $this->generateErrorLine('You need to upload ' . $settings['file-count'] . ' file(s) in order to complete the subtask');
            }
            elseif($settings['file-count-type'] == 2 && count($moduleData['file'])<$settings['file-count']) {
                $errors[] = $this->generateErrorLine('You need to upload at least' . $settings['file-count'] . ' file(s) in order to complete the subtask');
            }

            // Check if all file names are present
            if(!isset($moduleData['fileName']) || count(array_filter($moduleData['fileName']))< count($moduleData['file']) )  {
                $errors[] = $this->generateErrorLine('All file names are required');
            }

            // Check if all filenames are unique AND size is under max upload limit (check config)
            $uploadLocation = $this->getFinalStorageFolder($subtask, $settings);
            foreach($moduleData['file'] as $fileIndex=>$file) {
                $fileName = $moduleData['fileName'][$fileIndex] . '.' . $file->getClientOriginalExtension();
                $existingFilename = File::where('client_id', $subtask->task->client->id)->where('folder_id', $uploadLocation->id)->where('name','LIKE', $fileName)->count();
                if($existingFilename) {
                    $errors[] = $this->generateErrorLine('Filename already exists: '. $fileName);
                }

                if($file->getClientSize() > config('app.max_upload_file_size')){
                    $errors[] = $this->generateErrorLine('The filesize of the file named "'. $fileName .'" is greater than the limit of '.(config('app.max_upload_file_size')/1024/1024).'MB.');
                }
            }

            // Check if there are more than one filename in each form
            if (count($moduleData['file']) !== count(array_flip(array_map('strtolower', $moduleData['fileName']))) ) {
                $errors[] = $this->generateErrorLine('A filename was used several times in the same form');
            }
        }

        return $errors;
    }

    private function generateErrorLine($error)
    {
        return 'Module - ' . $this->moduleName . ': ' . $error;
    }

    public function processData(Subtask $subtask, $moduleData)
    {
        $returnValue = ['success'=>true, 'errors'=>[]];
        if (isset($moduleData['has-files-for-upload']) && ((int)$moduleData['has-files-for-upload'] == 0)) {
            $subtask->upload_not_needed_reason = $moduleData['upload-not-needed-reason'];
            $subtask->save();
        } else {
            // get storage location from subtask module template
            $settings = $this->getSettings($subtask->template);
            $storageFolder = $this->getFinalStorageFolder($subtask, $settings);


            foreach($moduleData['file'] as $fileIndex => $file) {
                $fileName = str_replace('/', '-', $moduleData['fileName'][$fileIndex] . '.' . $file->getClientOriginalExtension());

                // Upload file to FileVault
                $fileVaultId = FileVault::store($subtask->task->client->id, $file, $storageFolder->fullPath(), $fileName);
                if($fileVaultId !== false) {
                    // Save file data
                    File::create([
                        'name' => $fileName,
                        'client_id' => $subtask->task->client->id,
                        'subtask_id' => $subtask->id,
                        'folder_id' => $storageFolder->id,
                        'filevault_id' => $fileVaultId,
                    ]);
                }
                else {
                    $returnValue['success'] = false;
                    $returnValue['errors'] += ["Internal error! File '$fileName' could not be uploaded. Please contact an administrator!"];
                }
            }
        }

        return $returnValue;
    }

    private function getFinalStorageFolder(Subtask $subtask, $settings)
    {
        $clientId = $subtask->task->client->id;
        $mainFolder = FolderTemplate::where('id', $settings['folder'])->first();
        // Check if we need to save inside the 'year' folder
        if($settings['year']) {
            $subtaskDeadline = !is_null($subtask->deadline) ? $subtask->deadline : $subtask->task->deadline;

            // Check if we have an offset for current deadline
            if( (int)$settings['deadline-offset'] != 0) {
                $subtaskDeadline = $subtaskDeadline->addMonths($settings['deadline-offset']);
            }

            $yearSubfolder = $this->getSubfolder($mainFolder->id, $clientId, $subtaskDeadline->year);

            // Check if we need to save inside the 'termin' folder
            if($settings['termin']) {
                $terminSubfolderName = 'Termin ' . (int)ceil($subtaskDeadline->month/2);
                return $this->getSubfolder($yearSubfolder->id, $clientId, $terminSubfolderName);
            }
            else {
                return $yearSubfolder;
            }
        }
        return $mainFolder;
    }

    private function getFileTemplateStoragePath($templateSubtaskId)
    {
        return $this->fileTemplatePath . '/' . $templateSubtaskId;
    }

    private function getSubfolder($parentId, $clientId, $folderName)
    {
        return FolderTemplate::firstOrCreate(
            ['parent_id' => $parentId, 'name' => $folderName, 'client_id' => $clientId]
        );
    }
}