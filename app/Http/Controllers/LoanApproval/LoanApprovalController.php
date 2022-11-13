<?php

namespace App\Http\Controllers\LoanApproval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


use App\LoanModel\LoanApprovals;
use App\LoanModel\Loans;

class LoanApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'l_id' => 'required',
                'l_approve_state' => 'required',

            ],
            [
                'l_id.required' => 'Loan id is required',
                'l_approve_state.required' => 'Loan approve state is required',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $readyToSave    = array(
            "l_comments" => $request->l_comments ? $request->l_comments : '-',
            "l_id" => $request->l_id,
            "l_approved" => Auth::id(),
            "l_approve_state" => $request->l_approve_state,
            'status' => 1
        );

        $saveState  =   $this->store($readyToSave);
        return $saveState;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($readyToSave)
    {
        $loanID         =   $readyToSave['l_id'];
        $resMessage     =   $readyToSave['l_approve_state'] == 1 ? 'Loan approved successfully!' : 'Loan rejected successfully!';
        $isAlready      =   LoanApprovals::where("l_id", "=", $loanID)->where("l_approved", "=", Auth::id())->first();
        if ($isAlready) {
            if ($isAlready['l_approved'] == 1) {
                return response()->json(['error' => 'This is already approved by you!'], 400);
            } else {
                return response()->json(['error' => 'This is already Rejected by you!'], 400);
            }
        } else {
            try {
                $createApproval = LoanApprovals::create($readyToSave);
                return response()->json([
                    'message' => $resMessage,
                    'data' => $createApproval,
                    'status' => true
                ], 200);
            } catch (\Throwable $e) {
                // return response()->json(['error' => 'Loan Create Field! Try again.'], 400);
                return response()->json(['error' => $e], 400);
            }
        }

        return response()->json([
            'message' => 'This is already approved by you!',
            'data' => $isAlready,
            'status' => true
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
