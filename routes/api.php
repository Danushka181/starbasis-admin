<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\Books\BooksController;
use App\Http\Controllers\Books\CategoryController;

// resources files use
use App\Http\Controllers\Resources\CenterAreaController;
use App\Http\Controllers\Resources\CustomerGroupsController;
use App\Http\Controllers\Customers\CustomerController;

// Loan rates
use App\Http\Controllers\Loans\LoansProductsController;
use App\Http\Controllers\Loans\LoansController;
use App\Http\Controllers\LoanApproval\LoanApprovalController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login']);

// Api author Routes list
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});


// Api books Routes list
Route::group([
    'namespace' => 'Books',
    'middleware' => 'auth:api',
    'prefix' => 'books'

], function ($router) {
    Route::get('/all', [BooksController::class, 'index']);
    Route::get('/add-book', [BooksController::class, 'addbooks']);
});

// Api Category routes list
Route::group([
    'namespace' => 'Books',
    'middleware' => 'auth:api',
    'prefix' => 'category'

], function ($router) {
    Route::get('/all', [CategoryController::class, 'index']);
    Route::post('/add-cetegory', [CategoryController::class, 'addcategory']);
});

// create centers data
Route::group([
    'namespace' => 'Resources',
    'middleware' => 'auth:api',
    'prefix' => 'centers'

], function ($router) {
    // add delete centers
    Route::get('/', [CenterAreaController::class, 'index']);
    Route::post('/add-center', [CenterAreaController::class, 'add_new_center']);
    Route::post('/delete-center/{id}', [CenterAreaController::class, 'delete_center']);

    // add and delete customer groups
});

// create Customers Groups
Route::group([
    'namespace' => 'Resources',
    'middleware' => 'auth:api',
    'prefix' => 'customer-groups'

], function ($router) {
    // add and delete customer groups
    Route::get('/', [CustomerGroupsController::class, 'index']);
    Route::post('/create', [CustomerGroupsController::class, 'create_group']);
    Route::post('/delete/{id}', [CustomerGroupsController::class, 'delete']);
});

// create Customers Groups
Route::group([
    'namespace' => 'Customers',
    'middleware' => 'auth:api',
    'prefix' => 'customers'

], function ($router) {
    // add and delete customer groups
    Route::get('/', [CustomerController::class, 'index']);
    Route::post('/create', [CustomerController::class, 'create']);
    Route::post('/show/{id}', [CustomerController::class, 'show']);
    Route::post('/delete/{id}', [CustomerController::class, 'delete']);
});

// create Loans Products
Route::group([
    'namespace' => 'Loans',
    'middleware' => 'auth:api',
    'prefix' => 'loans-products'

], function ($router) {
    // add and delete customer groups
    Route::get('/', [LoansProductsController::class, 'index']);
    Route::post('/create', [LoansProductsController::class, 'create']);
    Route::post('/show/{id}', [LoansProductsController::class, 'show']);
    Route::post('/delete/{id}', [LoansProductsController::class, 'destroy']);
});

// create Loans
Route::group([
    'namespace' => 'Loans',
    'middleware' => 'auth:api',
    'prefix' => 'loans'

], function ($router) {
    // add and delete customer groups
    Route::get('/', [LoansController::class, 'index']);
    Route::get('/issued-loans', [LoansController::class, 'issued']);
    Route::get('/pending-loans', [LoansController::class, 'pending']);
    Route::get('/rejected-loans', [LoansController::class, 'rejected']);
    Route::post('/create', [LoansController::class, 'create']);
    Route::post('/show/{id}', [LoansController::class, 'show']);
    Route::post('/delete/{id}', [LoansController::class, 'destroy']);
});

// Loan approvals actions
Route::group([
    'namespace' => 'LoanApproval',
    'middleware' => 'auth:api',
    'prefix' => 'loans-approval'

], function ($router) {
    // make changes of loans approval
    Route::get('/', [LoanApprovalController::class, 'index']);
    Route::post('/create', [LoanApprovalController::class, 'create']);
});
