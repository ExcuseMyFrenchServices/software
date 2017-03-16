<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Uniform extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'uniforms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['set_name','jacket','jacket_color','shirt','shirt_color','pant','pant_color','shoes','shoes_color'];

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
        'id' => 'integer',
    ];

    public function event()
    {
        return $this->belongsToOne('App\Event');
    }
}
