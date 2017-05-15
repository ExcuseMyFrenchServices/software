<?php
namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'password', 'role_id', 'hash'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'role_id' => 'integer'
    ];

    public function role()
    {
        return $this->hasOne('App\Role');
    }

    public function profile()
    {
        return $this->hasOne('App\Profile');
    }

    public function availabilities()
    {
        return $this->hasMany('App\Availability');
    }

    public function assignments()
    {
        return $this->hasMany('App\Assignment');
    }

    public function jobs()
    {
        return $this->hasMany('App\Job');
    }
}
