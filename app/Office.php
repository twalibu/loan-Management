<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    /**
     * Get Office Clients.
     */
    public function clients()
    {
        return $this->hasMany('App\Client');
    }

    /**
     * Get Office Loans.
     */
    public function loans()
    {
        return $this->hasMany('App\Loan');
    }

    /**
     * Get Office Staff.
     */
    public function staff()
    {
        return $this->hasMany('App\Staff');
    }

    /**
     * Get the Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }
}
