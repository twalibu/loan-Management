<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SMSReport extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 's_m_s_reports';
	
    /**
     * Get the Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }
}
