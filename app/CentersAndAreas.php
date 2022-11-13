<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CentersAndAreas extends Model
{
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'center_name', 'user_id', 'center_address', 'status'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
