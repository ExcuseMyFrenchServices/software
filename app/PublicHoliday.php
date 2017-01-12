<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PublicHoliday extends Model
{
	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'public_holidays';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['public_holiday_name','public_holiday_date','year'];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    	'id'=>'integer',
    	'public_holiday_name'=>'string'
    ];
}
