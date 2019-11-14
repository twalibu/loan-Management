<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanPaymentSchedule extends Model
{
    /**
     * Get Schedule Alert.
     */
    public function alert()
    {
        return $this->hasOne('App\ScheduleAlert', 'schedule_id');
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
