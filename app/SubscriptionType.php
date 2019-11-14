<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionType extends Model
{
    /**
     * Get the Tenant.
     */
    public function tenants()
    {
        return $this->hasMany('App\TenantSubscription', 'subscription_id');
    }
}
