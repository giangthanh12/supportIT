<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
   public function index() {
    $users = User::select("id", "name")->get();
    return view("api.group",compact("users"));
   }
   public function getData() {
    $data = array();
    $groups = Group::select("id","group_name","members_id as members","created_at")->latest()->get();
    $data['data'] = $groups;
     return $data;
   }
   public function getUserAvailble() {
    $users = User::select("id","name as text","email")->get();
    return $users;
   }
   public function save(Request $request) {
    $validator = Validator::make($request->all(), [
        'group_name'=>'required',
        "memberIds"=>"array",
        "leader_id" => Rule::in($request->memberIds)
    ]);
    if($validator->fails()) {
        return response()->json([
            'errors'=>"Đã có lỗi xảy ra trong quá trình update"
        ],Response::HTTP_BAD_REQUEST);
    }
    $membersdata = array_combine($request->memberIds,$request->membersdata);
    foreach($membersdata as $id=>$data) {
        User::firstOrCreate([
            'id' => (int) $id,
            "email"=>$data["email"]
        ],
        [
            "name"=>$data["text"]
        ]
    );
    }
    $memberIds = null;
    $group_name = $request->group_name;
    $leader_id = empty($request->leader_id) ? NULL : $request->leader_id;
    if(!is_null($request->memberIds)) {
        $memberIds = json_encode($request->memberIds);
    }
    Group::create(
        [
            "leader_id"=>$leader_id,
            "group_name"=>$group_name,
            "members_id"=>$memberIds
        ]);
    return response()->json([
        'msg'=>"Thêm nhóm thành công"
    ],Response::HTTP_CREATED);
   }
   public function detail($id) {
        $group = Group::find($id);
        if(is_null($group)) {
            return response()->json([
                'errors'=>"Không tồn tại dữ liệu"
            ],Response::HTTP_BAD_REQUEST);
        }
        return response()->json([
            'data'=>$group
        ],Response::HTTP_OK);
    }
    public function update(Request $request, $id) {
        $group = Group::find($id);
        if(is_null($group)) {
            return response()->json([
                'errors'=>"Không tồn tại dữ liệu"
            ],Response::HTTP_BAD_REQUEST);
        }
        $validator = Validator::make($request->all(), [
            'group_name'=>'required',
            "memberIds"=>"array",
            "leader_id" => Rule::in($request->memberIds)
        ]);
        if($validator->fails()) {
            return response()->json([
                'errors'=>"Đã có lỗi xảy ra trong quá trình cập nhật"
            ],Response::HTTP_BAD_REQUEST);
        }
        $membersdata = array_combine($request->memberIds,$request->membersdata);
        foreach($membersdata as $id=>$data) {
            User::firstOrCreate([
                'id' => (int) $id,
                "email"=>$data["email"]
            ],
            [
                "name"=>$data["text"]
            ]
        );
        }
        $memberIds = null;
        $group_name = $request->group_name;
        $leader_id = empty($request->leader_id) ? NULL : $request->leader_id;
        if(!is_null($request->memberIds)) {
            $memberIds = json_encode($request->memberIds);
        }
        $group->update([
                "leader_id"=>$leader_id,
                "group_name"=>$group_name,
                "members_id"=>$memberIds
                ]);
        return response()->json([
            'msg'=>"Cập nhật nhóm thành công"
        ],Response::HTTP_OK);
    }
    public function delete($id) {
        $group = Group::find($id);
        if(is_null($group)) {
            return response()->json([
                'errors'=>"Không tồn tại dữ liệu"
            ],Response::HTTP_BAD_REQUEST);
        }
        $group->delete();
        return response()->json([
            'msg'=>"Xóa dữ liệu thành công"
        ],Response::HTTP_OK);
    }
}

