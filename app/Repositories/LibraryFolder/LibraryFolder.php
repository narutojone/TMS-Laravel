<?php

namespace App\Repositories\LibraryFolder;

use Illuminate\Database\Eloquent\Model;

class LibraryFolder extends Model
{

    protected $fillable = ['name', 'parent_id', 'visible'];

    public function subfolders()
    {
        return $this->hasMany('App\Repositories\LibraryFolder\LibraryFolder','parent_id','id');
    }

    public function files()
    {
        return $this->hasMany('App\Repositories\LibraryFile\LibraryFile', 'folder_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Repositories\LibraryFolder\LibraryFolder','parent_id','id');
    }

    public function fullPath()
    {
        $path = [];
        $model = clone($this);
        while($model->parent != null ) {

            $model = $model->parent;
            $path[] = $model->name;
        }
        $path = array_reverse($path);

        return implode('/', $path).'/'.$this->name;
    }
}
