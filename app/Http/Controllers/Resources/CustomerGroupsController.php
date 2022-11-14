<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\Validator;
// Data modal
use App\CustomerGroups;
use App\CentersAndAreas;

class CustomerGroupsController extends Controller
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
    public function index(CustomerGroups $group_data, User $user, CentersAndAreas $center)
    {
        //
        $groups = $group_data::with(['user', 'center', 'users_set'])->where('status', '=', '1')->orderBy('group_name', 'asc')->get();
        $all_groups = [];
        if ($groups) {
            $key = 0;
            foreach ($groups as $group) {

                $log = $user::where('id', $group['user_id'])->first(); // or whatever, just get one log
                $username = $log->name;

                $center_data    =   $center::where('id', $group['center_id'])->first();

                $all_groups[$key]['id']             = $group['id'];
                $all_groups[$key]['group_name']     = $group['group_name'];
                $all_groups[$key]['group_desc']     = $group['group_desc'];
                $all_groups[$key]['center_id']      = $center_data;
                $all_groups[$key]['user_id']        = $username;
                $key++;
            }
        }
        if ($all_groups) {
            return response()->json([
                'groups' => $groups,
            ], 200);
        } else {
            return response()->json(['error' => 'No data found! Please add Customer Grups'], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_group(Request $request, CustomerGroups $groups)
    {
        //
        if ($request) {
            $validator = Validator::make($request->all(), [
                'group_name' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
        }

        $uniq_groups    =   [
            "group_name" => $request->group_name,
        ];

        $groups     =   [
            "group_desc" => $request->group_desc,
            "center_id" => $request->center_id,
            "user_id"   => Auth::id(),
            "status"    => 1
        ];

        $created_group  =   CustomerGroups::updateOrCreate($uniq_groups, $groups);
        if ($created_group) {
            return response()->json([
                'message' => 'Group created successfully!',
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
     * @param  \App\CustomerGroups  $customerGroups
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerGroups $customerGroups)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CustomerGroups  $customerGroups
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerGroups $customerGroups)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CustomerGroups  $customerGroups
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerGroups $customerGroups)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CustomerGroups  $customerGroups
     * @return \Illuminate\Http\Response
     */
    public function delete($id, CustomerGroups $customerGroups)
    {
        if ($id) {
            $delete_data     =   $customerGroups::where('id', $id)->where('status', 1)->update(array('status' => 0));

            if ($delete_data) {
                return response()->json([
                    'message' => 'Group deleted successfully!',
                    'data' => $delete_data,
                    'status' => true
                ], 200);
            } else {
                return response()->json(['error' => 'No group found! its already deleted!'], 400);
            }
        } else {
            return response()->json(['error' => 'Group ID is not matched with our records!'], 400);
        }
    }
}
