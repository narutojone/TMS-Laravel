<?php

namespace App\Repositories\Contact;

use App\Repositories\Client\Client;
use App\Repositories\ContactEmail\ContactEmail;
use App\Repositories\ContactPhone\ContactPhone;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{

    const ACTIVE = 1;
    const NOT_ACTIVE = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'notes',
        'active',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Get the clients on which the contact person is assigned to
     */
    public function clients()
    {
        return $this->belongsToMany(Client::class)
            ->withPivot('primary')
            ->orderBy('client_contact.primary', 'DESC')
            ->orderBy('name', 'ASC');
    }

    /**
     * Get the phones owned by tha contact.
     */
    public function phones()
    {
        return $this->hasMany(ContactPhone::class, 'contact_id')->orderBy('primary', 'DESC');
    }

    /**
     * Get the email addresses owned by tha contact.
     */
    public function emails()
    {
        return $this->hasMany(ContactEmail::class, 'contact_id')->orderBy('primary', 'DESC');
    }

    public function isPrimary()
    {
        return (bool)$this->pivot->primary;
    }

}
