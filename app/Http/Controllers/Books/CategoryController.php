<?php

namespace App\Http\Controllers\Books;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\User;
use Validator;
// Data modal
use App\BooksDetails;
use App\BooksCategory;


class CategoryController extends Controller
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

    public function index(BooksCategory $category)
    {
        $all_cats = $category::all()->where('user_id', Auth::id());
        return response()->json([
            "category_list" => $all_cats,
            "title" => "Category List",
            "user_id" => Auth::id(),
            "user_name" => Auth::user()->name
        ]);
    }

    public function addcategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'center_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $category['cateory_name']   = $request->cateory_name;
        $category['user_id']        = Auth::id();

        BooksCategory::updateOrCreate($category);


        return response()->json([
            'message' => $category,
        ], 201);
    }
}
