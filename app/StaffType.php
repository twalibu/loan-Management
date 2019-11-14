<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffType extends Model
{
    /**
     * Get Staff 
     */
    public function staff()
    {
        return $this->hasMany('App\Staff', 'staff_type_id');
    }

    /**
     * Get Tenant 
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }
}
