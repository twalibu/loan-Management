<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    /**
     * Get the Tenants.
     */
    public function tenants()
    {
        return $this->hasMany('App\TenantAddress');
    }

    /**
     * Get the Clients.
     */
    public function clients()
    {
        return $this->hasMany('App\Client');
    }
}
