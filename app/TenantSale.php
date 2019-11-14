<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TenantSale extends Model
{
    /**
     * Get the Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }
}
