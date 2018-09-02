<?php

namespace App\Repositories\TemplateSubtaskModule;

use Illuminate\Database\Eloquent\Model;

class TemplateSubtaskModule extends Model
{
    const EMPLOYEE = 'Employee';
    const MANAGER = 'Manager';
    const ASIGNEES = [
        self::EMPLOYEE  => self::EMPLOYEE,
        self::MANAGER   => self::MANAGER,
    ];

    const TARGET_CLIENT = 'Client';
    const TARGET_INTERNAL_PROJECT = 'Internal Project';
    const TARGETS = [
        self::TARGET_CLIENT => self::TARGET_CLIENT,
        self::TARGET_INTERNAL_PROJECT => self::TARGET_INTERNAL_PROJECT,
    ];

    const REPEATING_YES_LABEL = "Yes";
    const REPEATING_NO_LABEL = "No";

    const REPEATING_YES_VALUE = 1;
    const REPEATING_NO_VALUE = 0;

    const REPEATING_OPTIONS = [
        self::REPEATING_YES_VALUE => self::REPEATING_YES_LABEL,
        self::REPEATING_NO_VALUE => self::REPEATING_NO_LABEL,
    ];

    protected $table = 'template_subtasks_modules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subtask_id',
        'subtask_module_id',
        'settings',
    ];

    public $timestamps = false;
}
