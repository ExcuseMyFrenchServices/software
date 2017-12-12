<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'clients';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email','second_email','third_email', 'phone_number'];

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
        'id' => 'integer'
    ];

    public function events()
    {
        return $this->hasMany('App\Event');
    }

    public function feedbacks()
    {
        return $this->hasMany('App\Feedback');
    }
    
    public function notifications(){
        return $this->hasMany('App\Notification');
    }
}
