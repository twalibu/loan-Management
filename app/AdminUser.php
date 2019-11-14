<?php

namespace App;

class AdminUser extends \Sentinel\Models\User
{

	/**
     * {@inheritDoc}
     */
    protected $table = 'admin_users';

    /**
	 * Returns the relationship between users and groups.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function groups()
	{
		return $this->belongsToMany(static::$groupModel, static::$userGroupsPivot, 'group_id', 'user_id');
	}
}
