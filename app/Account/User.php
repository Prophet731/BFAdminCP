<?php

namespace BFACP\Account;

use BFACP\Facades\Main as MainHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Config as Config;
use Zizaco\Entrust\Traits\EntrustUserTrait;

/**
 * Class User.
 */
class User extends Authenticatable
{
    use EntrustUserTrait;

    /**
     * Validation rules.
     *
     * @var array
     */
    public static $rules = [
        'username' => 'required|unique:bfacp_users,username|alpha_dash|min:4',
        'email'    => 'required|unique:bfacp_users,email|email',
        'password' => 'required|min:8|confirmed',
    ];

    /**
     * Should model handle timestamps.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'bfacp_users';

    /**
     * Fields allowed to be mass assigned.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Date fields to convert to carbon instances.
     *
     * @var array
     */
    protected $dates = ['lastseen_at'];

    /**
     * The attributes excluded form the models JSON response.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Append custom attributes to output.
     *
     * @var array
     */
    protected $appends = ['gravatar', 'stamp', 'profile_url'];

    /**
     * Models to be loaded automatically.
     *
     * @var array
     */
    protected $with = ['setting', 'roles', 'soldiers'];

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    /**
     * Get the remember token for the user.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the remember token for the user.
     *
     * @param string $value
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Returns the name of the remember token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * @return Model
     */
    public function roles()
    {
        return $this->belongsToMany(\BFACP\Account\Role::class, Config::get('entrust.assigned_roles_table'));
    }

    /**
     * @return Model
     */
    public function setting()
    {
        return $this->hasOne(\BFACP\Account\Setting::class, 'user_id');
    }

    /**
     * @return Model
     */
    public function soldiers()
    {
        return $this->hasMany(\BFACP\Account\Soldier::class, 'user_id');
    }

    /**
     * Has user confirmed their account.
     *
     * @return bool
     */
    public function getConfirmedAttribute()
    {
        return $this->attributes['confirmed'] == 1;
    }

    /**
     * @return mixed|string
     */
    public function getStampAttribute()
    {
        if ($this->created_at instanceof Carbon) {
            return $this->created_at->toIso8601String();
        }

        return $this->created_at;
    }

    /**
     * Gets users gravatar image.
     *
     * @return string
     */
    public function getGravatarAttribute()
    {
        return MainHelper::gravatar($this->email);
    }

    /**
     * Get the users profile url.
     *
     * @return string
     */
    public function getProfileUrlAttribute()
    {
        return route('user.profile', [$this->id, strtolower($this->username)]);
    }

    /**
     * Encrypt user's password before saving.
     *
     * @param string $password
     *
     * @return string
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }
}
