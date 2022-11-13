<?php

namespace App\LoanModel;

use Illuminate\Database\Eloquent\Model;
use App\LoanModel\Loans;

class LoanApprovals extends Model
{
    //
    protected $fillable = [
        "l_comments",
        "l_id",
        "l_approved",
        "l_approve_state",
        'status'

    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'l_approved', 'id');
    }

    public function loans()
    {
        return $this->belongsTo(Loans::class, 'l_id', 'id');
    }
}
