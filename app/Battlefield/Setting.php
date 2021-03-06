<?php

namespace BFACP\Battlefield;

use BFACP\Elegant;
use BFACP\Exceptions\RconException;
use Illuminate\Support\Facades\Crypt;

/**
 * Class Setting.
 */
class Setting extends Elegant
{
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
    protected $table = 'bfacp_settings_servers';

    /**
     * Table primary key.
     *
     * @var string
     */
    protected $primaryKey = 'server_id';

    /**
     * Fields not allowed to be mass assigned.
     *
     * @var array
     */
    protected $guarded = ['server_id'];

    /**
     * The attributes excluded form the models JSON response.
     *
     * @var array
     */
    protected $hidden = ['rcon_password', 'monitor_key'];

    /**
     * Date fields to convert to carbon instances.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * Append custom attributes to output.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * Models to be loaded automatically.
     *
     * @var array
     */
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function server()
    {
        return $this->belongsTo(\BFACP\Battlefield\Server\Server::class, 'server_id');
    }

    /**
     * Decypts the RCON Password.
     *
     * @return string
     */
    public function getPassword()
    {
        if (empty($this->rcon_password)) {
            throw new RconException(500, 'RCON Password Not Set');
        }

        return Crypt::decrypt($this->rcon_password);
    }

    /**
     * Encrypts the password to be safely stored.
     *
     * @param string $value
     */
    public function setRconPasswordAttribute($value)
    {
        $this->attributes['rcon_password'] = Crypt::encrypt($value);
    }
}
