<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerDetails extends Model
{
    //
    protected $fillable = [
        "c_name",
        "c_address",
        "c_bday",
        "c_age",
        "c_id_number",
        "c_mobile_number",
        "c_land_number",
        "c_month_income",
        "c_ceb_number",
        "c_job",
        "c_office_number",
        "c_gender",
        "c_married",
        "c_sup_name",
        "c_sup_job",
        "c_sup_phone",
        "c_sup_id_number",
        "c_bank_account",
        "c_bank_name",
        "c_bank_branch",
        "c_id_copy",
        "c_id_copy_back",
        "c_ceb_bill",
        "c_bank_book",
        "status",
        "c_center",
        "c_group",
        "c_user"
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'c_user', 'id');
    }

    public function center()
    {
        return $this->belongsTo('App\CentersAndAreas', 'c_center', 'id');
    }

    public function group()
    {
        return $this->belongsTo('App\CustomerGroups', 'c_group', 'id');
    }
}
