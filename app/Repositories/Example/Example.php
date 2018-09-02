<?php

namespace App\Repositories\Example;

use Illuminate\Database\Eloquent\Model;

/** 
 * THe model for the Example entity
*/
class Example extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'some_name', 'some_boolean', 'some_enum', 'some_json', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * Get the user that owns the note.
     */
    public function user()
    {
        // Here if we sould have a repository for User the path of the model will be diffrent
        return $this->belongsTo('App\Repositories\User\User');
    }
}