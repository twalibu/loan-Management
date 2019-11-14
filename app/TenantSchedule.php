<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TenantSchedule extends Model
{
    /**
     * Get the Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }
}
