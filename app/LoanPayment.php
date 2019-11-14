<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanPayment extends Model
{
    /**
     * Get Payment Method.
     */
    public function method()
    {
        return $this->belongsTo('App\PaymentMethod');
    }

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
