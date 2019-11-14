<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TenantAddress extends Model
{
    /**
     * Get Tenant Region.
     */
    public function region()
    {
        return $this->belongsTo('App\Region');
    }

    /**
     * Get the Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }
}
