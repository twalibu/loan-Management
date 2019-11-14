<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    /**
     * Get the Loan Summary.
     */
    public function summary()
    {
        return $this->hasOne('App\LoanSummary');
    }

    /**
     * Get the Loan Penalties.
     */
    public function penalts()
    {
        return $this->hasMany('App\LoanPenalt');
    }

    /**
     * Get the Loan Schedule Alerts.
     */
    public function alerts()
    {
        return $this->hasMany('App\ScheduleAlert');
    }

    /**
     * Get the Loan Overwrite.
     */
    public function overwrites()
    {
        return $this->hasMany('App\LoanOverwrite');
    }


    /**
     * Get the Loan Type.
     */
    public function payments()
    {
        return $this->hasMany('App\LoanPayment');
    }

    /**
     * Get the Loan Type.
     */
    public function schedules()
    {
        return $this->hasMany('App\LoanPaymentSchedule');
    }

    /**
     * Get the Loan Overdue Rate.
     */
    public function overdue()
    {
        return $this->belongsTo('App\LoanOverdue', 'penalt_id');
    }

    /**
     * Get the Loan Type.
     */
    public function type()
    {
        return $this->belongsTo('App\LoanType', 'type_id');
    }

    /**
     * Get the Loan Client.
     */
    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    /**
     * Get the Loan Office.
     */
    public function office()
    {
        return $this->belongsTo('App\Office');
    }

    /**
     * Get the Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }
}
