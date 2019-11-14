<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends \Cartalyst\Sentinel\Roles\EloquentRole
{
    /**
     * {@inheritDoc}
     */
    protected $table = 'roles';

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'name',
        'slug',
        'permissions',
        'tenant_id',
    ];

    /**
     * Get the Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }

    /**
     * The Eloquent users model name.
     *
     * @var string
     */
    protected static $usersModel = 'App\User';

    /**
     * The Users relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(static::$usersModel, 'role_users', 'role_id', 'user_id')->withTimestamps();
    }
}
