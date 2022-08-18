<?php

namespace App\Http\Controllers\Api;

use App\bitrix\CRest;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
class AuthController extends Controller
{
    public function login() {
    $data = [];
    $dataJson = request();
    $data_array = $dataJson->request;
    $data["access_token"] = $data_array->get("AUTH_ID");
    $data["domain"] = $dataJson->get('DOMAIN');
    $data["refresh_token"] = $data_array->get('REFRESH_ID');
    $data["application_token"] = $dataJson->get('APP_SID');
    $json = json_encode($data);
    File::put('../app/bitrix/setting.json',$json);
    $userCallback= CRest::call('user.current',[],$data);
    $user = User::updateOrCreate([
        "id"=> $userCallback["result"]["ID"],
        "email"=> $userCallback["result"]["EMAIL"],
    ],
    [
        "name"=> $userCallback["result"]["LAST_NAME"]." ".$userCallback["result"]["NAME"],
        'api_token' => Str::random(60),
        "avatar"=>$userCallback["result"]["PERSONAL_PHOTO"],
        "user_bitrix_id"=>$userCallback["result"]["ID"],
    ]);
    DB::table('store_tokens')->updateOrInsert(["user_id"=>$user->id],$data);
    return redirect()->route("api.dashboard",['token'=>$user->api_token]);
    }
    public function get_user() {
        return auth()->user();
    }
}
