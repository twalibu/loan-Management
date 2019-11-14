<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanPenalt extends Model
{
    /**
     * Get Loan.
     */
    public function loan()
    {
        return $this->belongsTo('App\Loan');
    }

    /**
     * Get Loan.
     */
    public function schedule()
    {
        return $this->belongsTo('App\LoanPaymentSchedule', 'schedule_id');
    }

    /**
     * Get the Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }
}
