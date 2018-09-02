<?php

namespace App\Repositories\TemplateSubtaskVersion;

use Illuminate\Database\Eloquent\Model;

class TemplateSubtaskVersion extends Model
{
	protected $table = 'template_subtask_versions';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'subtask_template_id',
		'version_no',
        'created_by',
        'title',
		'description'
	];
}
