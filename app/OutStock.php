<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OutStock extends Model
{
     /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'outStock';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['event_id','name','brand','category','quantity'];

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

    public function event()
    {
        return $this->belongsTo('App\Event');
    }
}
