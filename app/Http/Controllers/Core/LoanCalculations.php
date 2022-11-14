<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\LoanModel\LoanProducts;
use Carbon\Carbon;

class LoanCalculations extends Controller
{
    protected $dateFormat = 'd-m-Y';


    public function getLoanProductsDetails($productId)
    {
        $loadProductDetails   =   LoanProducts::where('id', '=', $productId)->first();
        return $loadProductDetails;
    }

    public function calculateDueDate($start, $months)
    {
        $dueDate = Carbon::parse($start)->addMonths($months);
        if ($dueDate->dayOfWeek == Carbon::SUNDAY) {
            return Carbon::parse($dueDate)->format($this->dateFormat);
        } else {
            return Carbon::parse($dueDate)->next('Sunday')->format($this->dateFormat);
        }
    }


    // check loan amount exceed the loan amount
    public function isParsingMaxLoanLimit($loanAmount, $productId)
    {
        $loadProduct   =   $this->getLoanProductsDetails($productId);
        $maxLoanAmount  =   $loadProduct->max_loan_amount;
        if ($maxLoanAmount < $loanAmount) {
            return true;
        }
        return false;
    }

    // calculate loan document charges
    public function calculateDocumentCharges($amount, $productId)
    {
        $loadProduct    =   $this->getLoanProductsDetails($productId);
        $documentCharge =   $loadProduct->document_charge;
        $docCharges     =   $amount * $documentCharge / 100;
        return $this->formatToNumberFiveCents($docCharges);
    }

    // calculate sundays between two dates
    public function getSundaysBetweenTwoDates($startDate, $endDate)
    {
        $arrayOfDate = [];
        $startDate = Carbon::parse($startDate)->modify('this sunday');
        $endDate = Carbon::parse($endDate);

        for ($date = $startDate; $date->lte($endDate); $date->addWeek()) {
            $arrayOfDate[] = $date->format($this->dateFormat);
        }
        return $arrayOfDate;
    }

    // calculate loan payment weeks count as months and start date
    public function getWeeksForLoanPayments($start, $end)
    {
        $start          = Carbon::parse($start)->format($this->dateFormat);
        $end            = Carbon::parse($end)->format($this->dateFormat);
        $sunDaysList    = $this->getSundaysBetweenTwoDates($start, $end);
        $totalWeeks = count($sunDaysList);
        return $totalWeeks;
    }

    // get total loan amount with interest rate
    public function getLoanTotal($amount, $productId)
    {
        $loadProduct   =   $this->getLoanProductsDetails($productId);
        $interestRate  =   $loadProduct->rate;
        $loanTotal     =   ($amount * $interestRate / 100) + $amount;
        return $this->formatToNumberFiveCents($loanTotal);
    }

    // get weekly installment
    public function getWeeklyInstalment($amount, $installment)
    {
        $installment    =   $amount / $installment;
        return $this->formatToNumberFiveCents($installment);
    }

    // format value to five cents
    public function formatToNumberFiveCents($amount)
    {
        $whole = $amount * 100; // non float
        $rounded_whole = round(($whole * 2) / 10) * 5; // round to 5 cents
        $rounded = $rounded_whole / 100; // float
        return number_format(floatval($rounded), 2, '.', '');
    }
}
