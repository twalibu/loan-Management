<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanType extends Model
{
    /**
     * Get the Tenant.
     */
    public function loans()
    {
        return $this->hasMany('App\Loan','type_id');
    }

    /**
     * Get the Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }
}
