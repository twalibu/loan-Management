<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TenantSubscription extends Model
{
    /**
     * Get Tenant Subscription Alert
     */
    public function alert()
    {
        return $this->hasOne('App\SubscriptionAlert', 'subscription_id');
    }

    /**
     * Get Tenant Subscription
     */
    public function type()
    {
        return $this->belongsTo('App\SubscriptionType', 'subscription_id');
    }

    /**
     * Get the Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }
}
