<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\bitrix\CRest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login() {
        return view("login");
    }
    public function callback($provider) {
       $userCallback = Socialite::driver("bitrix24")->user();
       $info_user = $userCallback->user;
        // login or create
        $user = User::updateOrCreate([
            "id"=> $userCallback->id,
            "email"=> $userCallback->email,
        ],
        [
            "name"=> $userCallback->user["LAST_NAME"]." ".$userCallback->user["NAME"],
            "avatar"=>$info_user["PERSONAL_PHOTO"],
            "user_bitrix_id"=>$userCallback->id
        ]);

        $data = [];
        $data_array = $userCallback->accessTokenResponseBody;
        $data["access_token"] = $data_array["access_token"];
        $data["domain"] = $data_array["domain"];
        $data["refresh_token"] = $data_array["refresh_token"];
        $data["application_token"] = $data_array["member_id"];
        $json = json_encode($data);
        File::put('../app/bitrix/setting.json',$json);
        DB::table('store_tokens')->updateOrInsert(["user_id"=>$user->id], [
            "access_token"=>$data_array["access_token"],
            "domain"=>$data_array["domain"],
            "refresh_token"=>$data_array["refresh_token"],
            "application_token"=>$data_array["member_id"],
        ]);
       Auth::login($user);
       return redirect()->route("ticket.dashboard");
    }
    public function logout() {
        Auth::logout();
        return redirect()->route("auth.login");
    }
}
