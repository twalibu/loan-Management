<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /**
     * Get Client Office.
     */
    public function office()
    {
        return $this->belongsTo('App\Office');
    }

    /**
     * Get Client Loans
     */
    public function loans()
    {
        return $this->hasMany('App\Loan');
    }

    /**
     * Get the Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }
}
