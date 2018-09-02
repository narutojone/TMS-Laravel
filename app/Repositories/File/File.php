<?php

namespace App\Repositories\File;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'client_id', 'subtask_id', 'folder_id', 'filevault_id'];

}