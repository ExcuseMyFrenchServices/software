<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayRoll extends Model
{
   protected $fillable = [
   		'date',
   		'event_name',
   		'start',
   		'end',
   		'total_hour',
   		'pay'
   ];
}
