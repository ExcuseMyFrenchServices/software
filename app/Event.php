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
    protected $fillable = ['event_name', 'client_id', 'booking_date', 'event_date', 'start_time', 'finish_time', 'number_staff', 'address','details','uniform', 'glasses', 'soft_drinks', 'bar', 'notes', 'notification_status'];

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

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function feedback()
    {
        return $this->hasOne('App\Feedback');
    }
}
