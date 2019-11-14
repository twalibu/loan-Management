<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionAlert extends Model
{
    /**
     * Get Tenant Subscription
     */
    public function subscription()
    {
        return $this->belongsTo('App\TenantSubscription', 'subscription_id');
    }
}
