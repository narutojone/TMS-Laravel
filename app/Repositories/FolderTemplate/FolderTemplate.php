<?php

namespace App\Repositories\FolderTemplate;

use Illuminate\Database\Eloquent\Model;

class FolderTemplate extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'parent_id', 'client_id'];

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeMain($query)
    {
        return $query->where('parent_id', '=', 0);
    }

    /**
     * @param null $clientId
     *
     * @return $this
     */
    public function subfolders($clientId = null)
    {
        return $this->hasMany('App\Repositories\FolderTemplate\FolderTemplate', 'parent_id', 'id')->where(function ($query) use ($clientId) {
            if (is_null($clientId)) {
                return $query->whereNull('client_id');
            }

            return $query->where('client_id', $clientId)->orWhere('client_id', null);
        });
    }

    /**
     * @param $clientId
     *
     * @return $this
     */
    public function files($clientId)
    {
        return $this->hasMany('App\Repositories\File\File', 'folder_id', 'id')->where('client_id', $clientId);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('App\Repositories\FolderTemplate\FolderTemplate', 'parent_id', 'id');
    }

    /**
     * @return string
     */
    public function fullPath()
    {
        $path = [];
        $model = clone($this);
        while ($model->parent != null) {
            $model = $model->parent;
            $path[] = $model->name;
        }
        $path = array_reverse($path);

        return '/'.implode('/', $path).'/'.$this->name;
    }

    public function children()
    {
        // TODO (alex) - finish this
//		$results =  DB::select( DB::raw("
//			SELECT GROUP_CONCAT(lv SEPARATOR ',') as children FROM (
//				SELECT @pv:=(SELECT GROUP_CONCAT(id SEPARATOR ',') FROM folder_templates
//				WHERE FIND_IN_SET(parent_id, @pv)) AS lv FROM folder_templates
//				JOIN
//				(SELECT @pv:= $this->id) tmp
//			) a;
//		"));
//
//		return $results[0]->children;
    }
}
