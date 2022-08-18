<?php

namespace App\Helper;

use App\bitrix\CRest;
use App\Models\Calendar;
use App\Models\Group;
use App\Models\Holiday;
use Carbon\Carbon;

class Helper
{
    public static function checkLeader($id_auth) {
        $group = Group::where('leader_id', $id_auth)->first();
        if(!empty($group)) return true;
        return false;
    }
    public static function checkTime($now) {
        $date =  $now->format("Y-m-d");
        $holiday = Holiday::where("date", $date)->first();
        $DAY =  $now->format('l');
        $time =  $now->format("H:i:s");
        $calendar = Calendar::where("DAY", $DAY)->where("from", "<=", $time)->where("to", ">=", $time)->first();
        if(!empty($calendar) && empty($holiday))
        return true;
        return false;
    }

    public static function notify($storeToken, $to, $message, $title,$group_name, $deadline) {
        $group_array = [];
        $deadline_array = [];

        if(!empty($group_name)) {
            $group_array =  Array(
                "NAME" => "Giao cho",
                "VALUE" => $group_name,
                "DISPLAY" => "ROW",
                "WIDTH" => 100
            );
        }
        if(!empty($deadline)) {
            $deadline_array = Array(
                "NAME" => "Đến hạn",
                "VALUE" => $deadline,
                "DISPLAY" => "ROW",
                "WIDTH" => 100
            );
        }

        try {
            $infoToken = [
                "access_token"=>$storeToken->access_token,
                "domain"=>$storeToken->domain,
                "refresh_token"=>$storeToken->refresh_token,
                "application_token"=>$storeToken->application_token,
            ];
            $userCallback= CRest::call(
                'im.notify',
                array(
                    "to"=>$to,
                    "message"=>  $message,
                    "ATTACH" => Array(
                        "ID" => 1,
                        "COLOR" => "#29619b",
                        "BLOCKS" => Array(
                           Array("LINK" => Array(
                              "NAME" => $title,
                              "LINK" => "https://admin.sconnect.edu.vn/marketplace/app/7/",
                              "DISPLAY" => "LINE"
                           )),
                           Array("GRID" => Array($group_array,$deadline_array)),
                        ))), $infoToken);
                return $userCallback;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public static function notifySystem($to,$message,$title,$group_name, $deadline) {
        $group_array =  Array(
            "NAME" => "Giao cho",
            "VALUE" => $group_name,
            "DISPLAY" => "ROW",
            "WIDTH" => 100
        );
        $deadline_array = Array(
            "NAME" => "Đến hạn",
            "VALUE" => $deadline,
            "DISPLAY" => "ROW",
            "WIDTH" => 100
        );
        try {
            $result = CRest::call('im.notify.system.add', Array(
                'USER_ID' =>$to,
                'MESSAGE' => $message,
                "ATTACH" => Array(
                    "ID" => 1,
                    "COLOR" => "#29619b",
                    "BLOCKS" => Array(
                       Array("LINK" => Array(
                          "NAME" => $title,
                          "LINK" => "https://admin.sconnect.edu.vn/marketplace/app/7/"
                       )),
                       Array("GRID" => Array($group_array,$deadline_array)),
                    )
                )
             ));
                return $result;
        } catch (\Throwable $th) {
            dd($th);
        }
    }
}
