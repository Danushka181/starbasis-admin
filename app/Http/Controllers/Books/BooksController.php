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

class BooksController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api');
    }

    /**
     * Show the application Books Index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(BooksDetails $book ) {
        $books = $book::all()->where('user_id', Auth::id());
        return $books;
    }

    /**
     * Show the application Books create.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function addbooks()
    {
        $select_list = $this->selectListCats();
        // Return books categoryies list 
        return response()->json([
            'books_category' => $select_list,
            'title' => 'Add a Book', 
            'user' => Auth::id()
        ]);
          
    }


    /* Select books Category List */
    public function selectListCats(){
        $catsList = BooksCategory::all()->where('user_id', Auth::id())->sortByDesc('id');
        $select_list = [];
        if ( $catsList ) {
            foreach ( $catsList as $key => $cat ) {
                $select_list[$cat->id] = $cat->cateory_name;
            }
        }
        return $select_list;
    }



}
