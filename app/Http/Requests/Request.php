<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * This is class is used by Create or Update Reuests classed.
 * We might need this to be able to add more methods available for all Requests Validation classes
 */
abstract class Request extends FormRequest
{
    protected $message = 'Validation errors';

    /**
     * @return string
     */
    final public function getMessage() {
        return $this->message;
    }
}
