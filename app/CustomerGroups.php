<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\CustomerDetails;
use App\CentersAndAreas;

class CustomerGroups extends Model
{
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_name', 'group_desc', 'center_id', 'status', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function center()
    {
        return $this->belongsTo(CentersAndAreas::class, 'center_id', 'id');
    }

    public function users_set()
    {
        return $this->hasMany(CustomerDetails::class, 'c_group', 'id');
    }
}
