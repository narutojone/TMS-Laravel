<?php

namespace App\Repositories\DocumentationPage;

use App\Core\Validators\LaravelValidator;
use App\Core\Validators\ValidableInterface;

/**
 * This validator is going to be used before saving into the database when we have a create request
 *
 * Validation rules that we need to use before saving to the database.
 * We try to keep the data clean and without mistakes.
 *
 * After a request passes it's request validator than we need to manipulate the data, do something with it.
 * After the manipulation, we need to check that the data that is going to be saved to database is correct
 */
class DocumentationPageCreateValidator extends LaravelValidator implements ValidableInterface
{

    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = [
        'title'          => 'required|max:255',
        'content'        => 'required',
        'order'          => 'required|integer',
        'parent_page_id' => 'integer|nullable|exists:documentation_pages,id',
    ];

    /**
     * Validation messages
     *
     * @var array
     */
    protected $messages = [];

}
