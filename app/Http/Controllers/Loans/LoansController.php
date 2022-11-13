<?php

namespace App\Http\Controllers\Loans;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Core\LoanCalculations;
use App\Http\Controllers\LoanApproval\LoanApprovalController;

use App\LoanModel\Loans;
use App\LoanModel\LoanApprovals;

class LoansController extends Controller
{

    protected $dateFormat = 'd-m-Y';
    protected $approvalCount = 2;
    protected $pendingCount = 2;

    /**
     * Display a listing of the Loans.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get all loan Products with active state
        $loans = Loans::with(['user', 'loanProduct', 'customer', 'approval'])->where('status', '=', '1')->orderBy('l_amount', 'ASC')->get();
        if ($loans) {
            return response()->json([
                'loans' => $loans,
            ], 200);
        } else {
            return response()->json(['error' => 'No data found! Please add Products'], 400);
        }
    }
    // All issued Loans
    public function issued()
    {
        $loans = Loans::with([
            'user',
            'loanProduct',
            'customer',
            'getApprovedLoans',
            'getRejectedLoans'
        ])
            ->withCount('getApprovedLoans')
            ->having('get_approved_loans_count', '>', $this->approvalCount)
            ->where('status', '=', '1')->orderBy('id', 'ASC')->get();

        if ($loans) {
            return response()->json([
                'loans' => $loans,
            ], 200);
        } else {
            return response()->json(['error' => 'No data found! Please approve pending loans'], 400);
        }
    }
    // all pending loans
    public function pending()
    {
        $loans = Loans::with([
            'user',
            'loanProduct',
            'customer',
            'getApprovedLoans',
            'getRejectedLoans'
        ])
            ->withCount('getApprovedLoans')
            ->withCount('getRejectedLoans')
            ->having('get_approved_loans_count', '<=', $this->pendingCount)
            ->having('get_rejected_loans_count', '<=', 1)
            ->where('status', '=', '1')->orderBy('id', 'DESC')->get();

        if ($loans) {
            return response()->json([
                'pending-loans' => $loans,
            ], 200);
        } else {
            return response()->json(['error' => 'No data found! Please add loans'], 400);
        }
    }
    // All rejected Loans
    public function rejected()
    {
        $loans = Loans::with([
            'user',
            'loanProduct',
            'customer',
            'getApprovedLoans',
            'getRejectedLoans'
        ])
            ->withCount('getApprovedLoans')
            ->withCount('getRejectedLoans')
            ->having('get_rejected_loans_count', '>', 1)
            ->where('status', '=', '1')->orderBy('id', 'ASC')->get();

        if ($loans) {
            return response()->json([
                'pending-loans' => $loans,
            ], 200);
        } else {
            return response()->json(['error' => 'No data found! Please add loans'], 400);
        }
    }

    /**
     * Show the form for creating a new Loans.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, LoanCalculations $loanCalc)
    {
        //
        $validator = Validator::make(
            $request->all(),
            [
                'l_amount' => 'required|numeric',
                'l_duration' => 'required|numeric',
                'l_product' => 'required|numeric',
                'l_customer' => 'required|numeric',
                'l_start' => 'required|date',
            ],
            [
                'l_amount.required' => 'Loan amount is required',
                'l_duration.required' => 'Loan Duration is required',
                'l_customer.required' => 'Loan Customer is required',
                'l_product.required' => 'Loan Product is required',
                'l_start.required' => 'Loan Start Date is required',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $loanAmount        =   $request->l_amount;
        $loanProductId      =   $request->l_product;
        $loanDuration       =   $request->l_duration;
        $loanStartDate      =   $request->l_start;

        $loanLimitExceed = $loanCalc->isParsingMaxLoanLimit($loanAmount, $loanProductId);
        if ($loanLimitExceed) {
            return response()->json(['error' => 'Loan amount is exceeding max Loan Limit'], 400);
        }

        $getLoanEndDate             =   $loanCalc->calculateDueDate($loanStartDate, $loanDuration);  // calculate loan end date
        $getDocumentCharge          =   $loanCalc->calculateDocumentCharges($loanAmount, $loanProductId); // calculate document charges
        $getWeeksForSelectedPeriod  =   $loanCalc->getWeeksForLoanPayments($loanStartDate, $getLoanEndDate); // get weeks count for loan
        $getTotalLoanAmount         =   $loanCalc->getLoanTotal($loanAmount, $loanProductId); // get loan amount with intensest rate
        $getWeekInstalment          =   $loanCalc->getWeeklyInstalment($getTotalLoanAmount, $getWeeksForSelectedPeriod); // get weekly installment count

        $loanData  =   array(
            'l_amount' => $loanAmount,
            'l_pending_amount' => $getTotalLoanAmount,
            'l_duration' => $request->l_duration, // NEED TO BE MONTHS
            'l_status' => 1,
            'l_installment' => $getWeekInstalment, // need to calc loan installment
            'l_product' => $loanProductId,
            'l_customer' => $request->l_customer,
            'l_start' => $loanStartDate,
            'l_end' => $getLoanEndDate,
            'l_last_payment' => null,
            'l_installment_count' => $getWeeksForSelectedPeriod,
            'l_document_charge' => $getDocumentCharge,
            'l_stage' => 1,
            'status' => 1,
            'user' => Auth::id(),
        );

        if ($request->is_save == 1) {
            try {
                $loanData   =   Loans::create($loanData);

                return response()->json([
                    'message' => 'Loan created successfully',
                    'data' => $loanData,
                    'status' => true
                ], 200);
            } catch (\Throwable $e) {
                return response()->json(['error' => 'Loan Create Field! Try again.'], 400);
            }
        } elseif ($request->is_save == 0) {
            return response()->json([
                'message' => 'Loan data ready to save!',
                'data' => $loanData,
                'status' => true
            ], 200);
        } else {
            return response()->json(['error' => 'Loan Create Field! Try again.'], 400);
        }
    }

    /**
     * Store a newly created Loans in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

    }

    /**
     * Display the specified Loans.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        // get all loan Products with active state
        $loans = Loans::with(['user', 'loanProduct', 'customer', 'approval'])
            ->withCount('getApprovedLoans')
            ->where('status', '=', '1',)->where('id', '=', $id)->first();
        if ($loans) {
            return response()->json([
                'loans' => $loans,
            ], 200);
        } else {
            return response()->json(['error' => 'No data found! Please add Products'], 400);
        }
    }

    /**
     * Show the form for editing the specified Loans.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified Loans in storage.
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
     * Remove the specified Loans from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
