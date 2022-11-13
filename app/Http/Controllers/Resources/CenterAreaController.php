<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\Validator;
// Data modal
use App\CentersAndAreas;

class CenterAreaController extends Controller
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
     * Show the application all center added by the user Index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(CentersAndAreas $center, User $user)
    {
        $allCenters = $center::with('user')->where('status', '=', '1')->orderBy('center_name', 'asc')->get();
        if ($allCenters) {
            return response()->json([
                'centers' => $allCenters,
            ], 200);
        } else {
            return response()->json(['error' => 'No data found! Please add Centers'], 400);
        }
    }

    /**
     * Show the application Centers create.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function add_new_center(Request $request, CentersAndAreas $center)
    {
        if ($request) {

            $validator = Validator::make($request->all(), [
                'center_name' => 'required',
                'center_address' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
        }

        $uniq_centers   =   [
            "center_name"   => $request->center_name
        ];

        $centers    =   [
            "center_address"    => $request->center_address,
            "user_id"   => Auth::id(),
            "status"    => 1
        ];

        $created_center  =   CentersAndAreas::updateOrCreate($uniq_centers, $centers);
        if ($created_center) {
            return response()->json([
                'message' => 'Center created successfully!',
                'data' => $created_center,
                'status' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'Something wen wrong! Please try again.',
                'data' => $created_center,
                'status' => false

            ], 201);
        }
    }


    public function delete_center($id, CentersAndAreas $center)
    {
        if ($id) {
            $delete_data     =   $center::where('id', $id)->where('status', 1)->update(array('status' => 0));
            if ($delete_data) {
                return response()->json([
                    'message' => 'Center deleted successfully!',
                    'data' => $delete_data,
                    'status' => true
                ], 200);
            } else {
                return response()->json(['error' => 'No center found! its already deleted!'], 400);
            }
        } else {
            return response()->json(['error' => 'ID is not matched with our records!'], 400);
        }
    }
}
