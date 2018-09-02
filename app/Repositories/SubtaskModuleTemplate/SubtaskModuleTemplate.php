<?php

namespace App\Repositories\SubtaskModuleTemplate;

use Illuminate\Database\Eloquent\Model;

/** 
 * THe model for the SubtaskModuleTemplate entity
*/
class SubtaskModuleTemplate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'template',
        'user_input',
        'settings',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the user that owns the note.
     */
    public function user()
    {
        // Here if we sould have a repository for User the path of the model will be diffrent
        return $this->belongsTo('App\Repositories\User\User');
    }
}