<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanOverdue extends Model
{
	/**
     * Get the Tenant.
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
