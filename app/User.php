<?php

namespace App;

class User extends \Cartalyst\Sentinel\Users\EloquentUser
{
    /**
     * {@inheritDoc}
     */
    protected $table = 'users';

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'email',
        'password',
        'permissions',
        'staff_id',
        'tenant_id',
    ];

    /**
     * Get the User Staff.
     */
    public function staff()
    {
        return $this->belongsTo('App\Staff');
    }

    /**
     * Get the User Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }

    /**
     * The Eloquent roles model name.
     *
     * @var string
     */
    protected static $rolesModel = 'App\Role';


    /**
     * Returns the roles relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(static::$rolesModel, 'role_users', 'user_id', 'role_id')->withTimestamps();
    }
}
