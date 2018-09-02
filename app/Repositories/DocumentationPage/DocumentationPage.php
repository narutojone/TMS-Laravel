<?php

namespace App\Repositories\DocumentationPage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentationPage extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'parent_page_id',
        'title',
        'content',
        'order',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentPage()
    {
        return $this->belongsTo(DocumentationPage::class, 'parent_page_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childPages()
    {
        return $this->hasMany(DocumentationPage::class, 'parent_page_id', 'id');
    }
}
