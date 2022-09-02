<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Models\Group;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
   use ResponseTrait;
   public function index() {
    $users = User::select("id", "name")->get();
    return view("group",compact("users"));
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
   public function save(GroupRequest $request) {
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
    return $this->successResponse([],"Thêm nhóm thành công",201);
   }
   public function detail($id) {
        $group = Group::find($id);
        if(is_null($group)) {
            return $this->errorResponse("Không tôn tài dữ liệu",Response::HTTP_BAD_REQUEST);
        }
        return $this->successResponse($group,"Lấy dữ liệu thành công",200);
    }
    public function update(GroupRequest $request, $id) {
        $group = Group::find($id);
        if(is_null($group)) {
            return $this->errorResponse("Không tôn tài dữ liệu",Response::HTTP_BAD_REQUEST);
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
        return $this->successResponse($group,"Cập nhật nhóm thành công",200);
    }
    public function delete($id) {
        $group = Group::find($id);
        if(is_null($group)) {
            return $this->errorResponse("Không tôn tài dữ liệu",Response::HTTP_BAD_REQUEST);
        }
        $group->delete();
        return $this->successResponse($group,"Xóa dữ liệu thành công",200);
    }
}
