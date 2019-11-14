<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    /**
     * Get the Staff User
     */
    public function user()
    {
        return $this->hasOne('App\User');
    }

    /**
     * Get the Staff office
     */
    public function office()
    {
        return $this->belongsTo('App\Office');
    }

    /**
     * Get the Staff Type
     */
    public function type()
    {
        return $this->belongsTo('App\StaffType', 'staff_type_id');
    }

    /**
     * Get the Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }
}
