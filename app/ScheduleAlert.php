<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleAlert extends Model
{
    /**
     * Get Alert Schedule
     */
    public function schedule()
    {
        return $this->belongsTo('App\LoanPaymentSchedule', 'schedule_id');
    }

    /**
     * Get Alert Loan
     */
    public function loan()
    {
        return $this->belongsTo('App\Loan');
    }

    /**
     * Get Alert Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }
}
