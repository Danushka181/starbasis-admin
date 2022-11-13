<?php

namespace App\LoanModel;

use Illuminate\Database\Eloquent\Model;

class LoanProducts extends Model
{
    //
    protected $fillable = [
        "loan_product_name",
        "rate",
        "document_charge",
        "max_loan_amount",
        "status",
        "user_id"
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
