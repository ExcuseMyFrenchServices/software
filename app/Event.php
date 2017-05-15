<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'events';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['event_name', 'client_id', 'booking_date', 'event_date','event_type','guest_arrival_time','start_time', 'finish_time','guest_number', 'number_staff', 'address','details','uniform', 'glasses', 'soft_drinks', 'bar', 'notes', 'notification_status'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'client_id' => 'integer',
        'start_time' => 'array'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function assignments()
    {
        return $this->hasMany('App\Assignment');
    }

    public function barEvent()
    {
        return $this->hasOne('App\BarEvent');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function feedback()
    {
        return $this->hasOne('App\Feedback');
    }

    public function uniform()
    {
        return $this->hasOne('App\Uniform');
    }

    public function outStock()
    {
        return $this->hasMany('App\OutStock');
    }

    public function modifications()
    {
        return $this->hasMany('App\Modification');
    }
}
