<?php

namespace App;

class AdminGroup extends \Sentinel\Models\Group
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_groups';

    /**
	 * Returns the relationship between groups and users.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function users()
	{
		return $this->belongsToMany(static::$userModel, static::$userGroupsPivot, 'user_id', 'group_id');
	}
}
