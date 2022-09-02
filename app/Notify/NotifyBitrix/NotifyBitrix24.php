<?php

namespace App\Notify\NotifyBitrix;

use App\Jobs\NotifyBitrix;
use App\Jobs\NotifySystem;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class NotifyBitrix24
{
    public function sendAllMembers($ticket, $storeToken, $attribute) {
        $group_members = Group::find($ticket->group_id)->members_id;
        if(!is_null($group_members)) {
          try {
            $group_members = json_decode($group_members);
            foreach($group_members as $id_member) {
                if($id_member == Auth::id()) continue;
                $dataSend = [
                    "storeToken"=>$storeToken,
                    "to"=>$id_member,
                    "message"=>$attribute["message"],
                    "title"=>"Yêu cầu: $ticket->title",
                    "group_name"=>$attribute["group_name"],
                    "deadline"=>$attribute["deadline"],
                ];
                NotifyBitrix::dispatch($dataSend)->delay(now()->addSeconds(10));
            }
          } catch (\Throwable $th) {
            dd($th);
          }
        }

    }
    public function sendOneMember($ticket, $storeToken, $id_member,$attribute) {
        if(Auth::id() == $id_member) return;
        $dataSend = [
            "storeToken"=>$storeToken,
            "to"=>$id_member,
            "message"=>$attribute["message"],
            "title"=>"Yêu cầu: $ticket->title",
            "group_name"=>$attribute["group_name"],
            "deadline"=>$attribute["deadline"],
        ];
        NotifyBitrix::dispatch($dataSend)->delay(now()->addSeconds(10));
    }

    public function sendMembers($ticket, $storeToken, $assignees, $attribute) {
        $group_members = json_decode($assignees, true);
        if(!is_null($group_members) && !empty($group_members)) {
            try {
                foreach($group_members as $id_member) {
                    if(Auth::id() == $id_member) continue;
                    $dataSend = [
                        "storeToken"=>$storeToken,
                        "to"=>$id_member,
                        "message"=>$attribute["message"],
                        "title"=>"Yêu cầu: $ticket->title",
                        "group_name"=>$attribute["group_name"],
                        "deadline"=>$attribute["deadline"],
                    ];
                    NotifyBitrix::dispatch($dataSend)->delay(now()->addSeconds(10));
                }
              } catch (\Throwable $th) {
                dd($th);
              }
        }
    }

    public function sendLeaderAndRelatedMember($ticket, $storeToken, $attribute) {
            $assignees_id = json_decode($ticket->assignees_id);
            $group_leader = Group::find($ticket->group_id)->leader_id;
            $leader = User::find($group_leader);
            if($group_leader != Auth::id()) {
                $dataSendLeader = [
                    "storeToken"=>$storeToken,
                    "to"=>$leader->id,
                    "message"=>$attribute["message"],
                    "title"=>"Yêu cầu: $ticket->title",
                    "group_name"=>$attribute["group_name"],
                    "deadline"=>$attribute["deadline"],
                ];
                NotifyBitrix::dispatch($dataSendLeader)->delay(now()->addSeconds(10));
            }
            if(empty($assignees_id) || is_null($assignees_id)) return;
            foreach($assignees_id as $assignee_id) {
                if($assignee_id == $group_leader) continue;
                if($assignee_id ==  Auth::id()) continue;
                $dataSend= [
                    "storeToken"=>$storeToken,
                    "to"=>$assignee_id,
                    "message"=>$attribute["message"],
                    "title"=>"Yêu cầu: $ticket->title",
                    "group_name"=>$attribute["group_name"],
                    "deadline"=>$attribute["deadline"],
                ];
                NotifyBitrix::dispatch($dataSend)->delay(now()->addSeconds(10));
            }
    }
    // system
    public function sendSystem($to,$attribute) {
        $dataSend= [
            "to"=>$to,
            "message"=>$attribute["message"],
            "title"=>"Yêu cầu: ".$attribute["title"],
            "group_name"=>$attribute["group_name"],
            "deadline"=>$attribute["deadline"],
        ];
        NotifySystem::dispatch($dataSend)->delay(now()->addSeconds(10));
    }
}
