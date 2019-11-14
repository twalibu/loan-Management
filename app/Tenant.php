<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    /**
     * Get Tenant Schedule
     */
    public function schedule()
    {
        return $this->hasOne('App\TenantSchedule');
    }

    /**
     * Get Tenant Subscription
     */
    public function subscription()
    {
        return $this->hasOne('App\TenantSubscription');
    }

    /**
     * Get Tenant Address
     */
    public function address()
    {
        return $this->hasOne('App\TenantAddress');
    }

    /**
     * Get Tenant SMS Details
     */
    public function sms()
    {
        return $this->hasOne('App\TenantSMS');
    }

    /**
     * Get Tenant Sales Contacts
     */
    public function sales()
    {
        return $this->hasOne('App\TenantSale');
    }

     /**
     * Get Tenant Contact Persons
     */
    public function contacts()
    {
        return $this->hasMany('App\TenantContact');
    }

    /**
     * Get Tenant Clients
     */
    public function clients()
    {
        return $this->hasMany('App\Client');
    }

    /**
     * Get Tenant Loans
     */
    public function loans()
    {
        return $this->hasMany('App\Loan');
    }

    /**
     * Get Tenant Summaries
     */
    public function summaries()
    {
        return $this->hasMany('App\LoanSummary');
    }

    /**
     * Get Tenant Staff
     */
    public function staff()
    {
        return $this->hasMany('App\Staff');
    }

    /**
     * Get Tenant Staff Type
     */
    public function types()
    {
        return $this->hasMany('App\StaffType');
    }

    /**
     * Get Tenant Offices
     */
    public function offices()
    {
        return $this->hasMany('App\Office');
    }

    /**
     * Get Tenant Loan Types
     */
    public function loanTypes()
    {
        return $this->hasMany('App\LoanType');
    }

    /**
     * Get Tenant Overdue Penalt Types
     */
    public function overdues()
    {
        return $this->hasMany('App\LoanOverdue');
    }

    /**
     * Get Tenant Overdue Penalt
     */
    public function penalts()
    {
        return $this->hasMany('App\LoanPenalt');
    }

    /**
     * Get Tenant Loan Payment Schedule
     */
    public function paymentSchedules()
    {
        return $this->hasMany('App\LoanPaymentSchedule');
    }

    /**
     * Get Tenant Overdue Penalt
     */
    public function overwrites()
    {
        return $this->hasMany('App\LoanOverwrite');
    }
    
    /**
     * Get Tenant Payments
     */
    public function loanPayments()
    {
        return $this->hasMany('App\LoanPayment');
    }

    /**
     * Get Tenant Payment Methods
     */
    public function paymentMethods()
    {
        return $this->hasMany('App\PaymentMethod');
    }

    /**
     * Get Tenant Groups
     */
    public function groups()
    {
        return $this->hasMany('App\Group');
    }

    /**
     * Get Tenant Users
     */
    public function users()
    {
        return $this->hasMany('App\User');
    }

    /**
     * Get Tenant Roles
     */
    public function roles()
    {
        return $this->hasMany('App\Role');
    }
}
