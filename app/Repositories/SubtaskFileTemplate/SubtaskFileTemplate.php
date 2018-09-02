<?php

namespace App\Repositories\SubtaskFileTemplate;

use Illuminate\Database\Eloquent\Model;

class SubtaskFileTemplate extends Model
{
	protected $table = 'subtask_file_templates';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['subtask_id', 'original_name', 'path'];


}
