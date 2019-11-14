<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanSummary extends Model
{
    /**
     * Get Loan.
     */
    public function loan()
    {
        return $this->belongsTo('App\Loan');
    }

    /**
     * Get the Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }
}
