<?php

namespace App\Repositories\TemplateSubtask;

use App\Repositories\Subtask\Subtask;
use App\Repositories\SubtaskFileTemplate\SubtaskFileTemplate;
use App\Repositories\SubtaskModuleTemplate\SubtaskModuleTemplate;
use App\Repositories\Template\Template;
use App\Repositories\TemplateSubtaskVersion\TemplateSubtaskVersion;
use Illuminate\Database\Eloquent\Model;

class TemplateSubtask extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'template_id',
        'order',
        'title',
        'active',
    ];

    /**
     * Get the template that owns the subtask.
     */
    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * The subtasks owned by the templatesubtask.
     */
    public function subtasks()
    {
        return $this->hasMany(Subtask::class, 'subtaskTemplateId');
    }

    public function versions()
    {
        return $this->hasMany(TemplateSubtaskVersion::class,'subtask_template_id','id')->orderBy('id', 'DESC');
    }

	public function modules()
	{
		return $this->hasMany(SubtaskModuleTemplate::class, 'template_subtasks_modules', 'subtask_id', 'subtask_module_id');
	}

	public function fileTemplates()
	{
		return $this->hasMany(SubtaskFileTemplate::class, 'subtask_id', 'id');
	}
}
