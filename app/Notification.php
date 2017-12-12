<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
	protected $table = 'notifications';

    protected $fillable = ['message','client_id','user_id','event_id'];

    protected $casts = ['id','client_id','user_id','event_id'];

    protected $hidden = [];

    public function user(){
    	return $this->belongsTo('App\User');
    }

    public function client(){
    	return $this->belongsTo('App\Client');
    }

    public function event(){
    	return $this->belongsTo('App\Event');
    }
}
