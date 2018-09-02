<?php

namespace App\Repositories\Note;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'note'];

    /**
     * Get the user that owns the note.
     */
    public function user()
    {
        return $this->belongsTo('App\Repositories\User\User');
    }
}