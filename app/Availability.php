<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'availabilities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['date', 'times', 'user_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'times' => 'array'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
