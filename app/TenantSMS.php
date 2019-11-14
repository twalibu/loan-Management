<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TenantSMS extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'tenant_s_m_s';
	
    /**
     * Get the Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }
}
