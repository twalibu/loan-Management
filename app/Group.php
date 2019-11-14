<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    /**
     * Get the Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }

    /**
     * Get Group Contacts
     */
    public function contacts()
    {
        return $this->hasMany('App\Contact');
    }
}
