<?php

namespace App\LoanModel;

use Illuminate\Database\Eloquent\Model;
use App\LoanModel\LoanProducts;
use App\LoanModel\LoanApprovals;
use App\CustomerDetails;

class Loans extends Model
{
    //
    protected $fillable = [
        "l_amount",
        "l_pending_amount",
        "l_duration",
        "l_status",
        "l_installment",
        "l_product",
        "l_customer",
        "l_start",
        "l_end",
        "l_last_payment",
        "l_installment_count",
        "l_document_charge",
        "l_stage",
        "status",
        "user"

    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user', 'id');
    }

    public function loanProduct()
    {
        return $this->belongsTo(LoanProducts::class, 'l_product', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(CustomerDetails::class, 'l_customer', 'id');
    }

    public function approval()
    {
        return $this->hasMany(LoanApprovals::class, 'l_id', 'id')->with(['user']);
    }

    public function getApprovedLoans()
    {
        return $this->approval()->where('l_approve_state', '=', '1');
    }

    public function getRejectedLoans()
    {
        return $this->approval()->where('l_approve_state', '=', '0');
    }
}
