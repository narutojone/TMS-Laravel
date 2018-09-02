<?php

namespace App\Repositories\LibraryFile;

use Illuminate\Database\Eloquent\Model;

class LibraryFile extends Model
{
    protected $table = 'library_files';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'folder_id', 'path'];

}