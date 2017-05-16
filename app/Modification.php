<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modification extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'modifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'role', 'modifications', 'event_id','new_value','old_value'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
