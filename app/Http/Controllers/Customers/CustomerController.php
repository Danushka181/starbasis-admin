<?php

namespace App\Http\Controllers\Customers;

use App\CustomerDetails;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CustomerDetails $customers)
    {
        //
        $groups = $customers::with(['center', 'group', 'user'])->where('status', '=', '1')->orderBy('c_name', 'asc')->get();
        if ($groups) {
            return response()->json([
                'customers' => $groups,
            ], 200);
        } else {
            return response()->json(['error' => 'No data found! Please add Centers'], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request) {
            $validator = Validator::make(
                $request->all(),
                [
                    'c_name' => 'required|max:255|min:5',
                    'c_address' => 'required',
                    'c_bday' => 'required|date',
                    'c_age' => 'required|numeric',
                    'c_id_number' => 'required',
                    'c_mobile_number' => 'required|numeric',
                    'c_land_number' => 'required|numeric',
                    'c_month_income' => 'required',
                    'c_ceb_number' => 'required',
                    'c_gender' => 'required',
                    'c_married' => 'required',
                    'c_bank_account' => 'required',
                    'c_id_copy' => 'required',
                    "c_group" => 'required',
                ],
                [
                    'c_name.required' => 'Name is required',
                    'c_name.min' => 'Please give a valid Customer name',
                    'c_address.required' => 'Address is required',
                    'c_bday.required' => 'Birthday is required',
                    'c_bday.date' => 'Birthday is not a Valid date',
                    'c_age.required' => 'Age is required',
                    'c_age.numeric' => 'Age should be a number values',
                    'c_id_number.required' => 'ID Number is required',
                    'c_mobile_number.required' => 'Mobile number is required',
                    'c_mobile_number.numeric' => 'Mobile number should be numbers',
                    'c_land_number.required' => 'Mobile number is required',
                    'c_land_number.numeric' => 'Mobile number is not valid',
                    'c_month_income.required' => 'Please add monthly Income',
                    'c_ceb_number.required' => 'Electricity account number is required',
                    'c_gender.required' => 'Gender is required',
                    'c_married.required' => 'Married is required',
                    'c_bank_account.required' => 'Bank account number is required',
                    'c_id_copy.required' => 'Please upload ID copy',
                    "c_group" => 'Please select customer group',
                ]
            );

            if ($validator->fails()) {
                return response()->json($validator->errors(), 201);
            }

            // Update id card Image
            $id_copy       =   $this->updateImageData($request, 'c_id_copy', $request->c_id_number, 'id-card-front');
            $id_copy_back  =   $this->updateImageData($request, 'c_id_copy_back', $request->c_id_number, 'id-card-back');
            $c_ceb_bill    =   $this->updateImageData($request, 'c_ceb_bill', $request->c_id_number, 'cdb-bill');
            $c_bank_book   =   $this->updateImageData($request, 'c_bank_book', $request->c_id_number, 'bank-book');

            $uniq_groups    =   [
                "c_id_number" => $request->c_id_number,
            ];

            $groups     =   [
                "c_name"            =>  $request->c_name,
                "c_address"         =>  $request->c_address,
                "c_bday"            =>  $request->c_bday,
                "c_age"             =>  $request->c_age,
                "c_mobile_number"   =>  $request->c_mobile_number,
                "c_land_number"     =>  $request->c_land_number,
                "c_month_income"    =>  $request->c_month_income,
                "c_ceb_number"      =>  $request->c_ceb_number,
                "c_job"             =>  $request->c_job,
                "c_office_number"   =>  $request->c_office_number,
                "c_gender"          =>  $request->c_gender,
                "c_married"         =>  $request->c_married,
                "c_sup_name"        =>  $request->c_sup_name,
                "c_sup_job"         =>  $request->c_sup_job,
                "c_sup_phone"       =>  $request->c_sup_phone,
                "c_sup_id_number"   =>  $request->c_sup_id_number,
                "c_bank_account"    =>  $request->c_bank_account,
                "c_bank_name"       =>  $request->c_bank_name,
                "c_bank_branch"     =>  $request->c_bank_branch,
                "c_id_copy"         =>  $id_copy,
                "c_id_copy_back"   =>   $id_copy_back,
                "c_ceb_bill"        =>  $c_ceb_bill,
                "c_bank_book"       =>  $c_bank_book,
                "status"            =>  1,
                "c_group"           =>  $request->c_group,
                "c_user"            =>  Auth::id(),
            ];

            $created_group  =   CustomerDetails::updateOrCreate($uniq_groups, $groups);
            if ($created_group) {
                return response()->json([
                    'message' => 'Customer added successfully!',
                    'data' => $created_group,
                    'status' => true
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Something wen wrong! Please try again.',
                    'data' => $created_group,
                    'status' => false

                ], 201);
            }
        }
    }

    public function updateImageData($req, $key, $name, $option)
    {
        $file = $req->file($key);
        $extension = $file->getClientOriginalExtension();
        $full_file_name = trim(str_replace(' ', '-', $name)) . '-' . $option . '-' . time() . '.' . $extension;
        $add_file    =   $file->storeAs('uploads/customers', $full_file_name,  ['disk' => 'local']);
        return $add_file;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CustomerDetails  $customerDetails
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, CustomerDetails $customerDetails)
    {
        $groups = $customerDetails::with(['center', 'group', 'user'])->where('status', '=', '1')->where('id', '=', $request->id)->first();
        if ($groups) {
            return response()->json([
                'customer' => $groups,
            ], 200);
        } else {
            return response()->json(['error' => 'No data found! This customer not in list'], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CustomerDetails  $customerDetails
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerDetails $customerDetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CustomerDetails  $customerDetails
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerDetails $customerDetails)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CustomerDetails  $customerDetails
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerDetails $customerDetails)
    {
        //
    }
}
