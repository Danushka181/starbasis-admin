<?php

namespace App\Http\Controllers\Loans;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;


// database Modal
use App\LoanModel\LoanProducts;

class LoansProductsController extends Controller
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
     * Display a listing of the Loan Products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // get all loan Products with active state
        $products = LoanProducts::with(['user'])->where('status', '=', '1')->orderBy('loan_product_name', 'ASC')->get();
        if ($products) {
            return response()->json([
                'loan-products' => $products,
            ], 200);
        } else {
            return response()->json(['error' => 'No data found! Please add Products'], 400);
        }
    }

    /**
     * Show the form for creating a new Loan Products.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // create a loan products
        // validate request
        $validator = Validator::make($request->all(), [
            'loan_product_name' => 'required|max:255',
            'rate' => 'required|numeric',
            'document_charge' => 'required|numeric',
            'max_loan_amount' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $prod_data     =   [
            "loan_product_name" => $request->loan_product_name,
            "rate"              =>  $request->rate,
            "document_charge"   =>  $request->document_charge,
            "max_loan_amount"   =>  $request->max_loan_amount,
            "status"            =>  1,
            "user_id"              =>  Auth::id(),
        ];

        try {
            $create_prod  =   LoanProducts::create($prod_data);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['error' => ['Loan product already added! Please use different name']], 400);
        }


        $msg = 'Loan Product added successfully';
        if ($create_prod) {
            return response()->json([
                'message' => $msg,
                'data' => $create_prod,
                'status' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'Something wen wrong! Please try again.',
                'data' => $create_prod,
                'status' => false

            ], 201);
        }
    }

    /**
     * Store a newly created Loan Products in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified Loan Products.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified Loan Products.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified Loan Products in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified Loan Products from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(LoanProducts $product, $id)
    {
        //
        if ($id) {
            $delete_data     =   $product::where('id', $id)->where('status', 1)->update(array('status' => 0));
            if ($delete_data) {
                return response()->json([
                    'message' => 'Loan Product deleted successfully!',
                    'data' => $delete_data,
                    'status' => true
                ], 200);
            } else {
                return response()->json(['error' => 'No rates found! its already deleted!'], 400);
            }
        } else {
            return response()->json(['error' => 'ID is not matched with our records!'], 400);
        }
    }
}
