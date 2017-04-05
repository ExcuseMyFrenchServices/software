<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BarEvent extends Model
{
   /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bar_event';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['event_id','private','bar_back','bar_runner','classic_bartender','cockailt_bartender','flair_bartender','mixologist','glass_type', 'bar_number', 'notes','status'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'event_id' => 'integer',
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

    public function outStock()
    {
    	return $this->hasMany('App\outStock');
    }
}
