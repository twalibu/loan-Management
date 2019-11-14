<?php

namespace App;

class AdminThrottle extends \Cartalyst\Sentry\Throttling\Eloquent\Throttle
{

	/**
     * {@inheritDoc}
     */
    protected $table = 'admin_throttle';
}
