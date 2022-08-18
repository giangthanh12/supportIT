<?php

use App\bitrix\CRest;
use App\bitrix\CRestTest;
use App\Helper\Helper;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\TicketController;
use App\Models\Config;
use App\Models\Group;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::POST('/auth/callback/bitrix24',[ApiAuthController::class, "login"])->withoutMiddleware(["auth"]);
Route::get("/", [DashboardController::class, "index"])->name("ticket.dashboard");
Route::get("/get-ticketNotDone", [DashboardController::class, "get_ticketNotDone"])->name("ticket.get-ticketNotDone");
Route::get('/statistic',[StatisticController::class, "index"])->name("ticket.statistic");

//login auth

Route::get("/logout", [AuthController::class, "logout"])->name("auth.logout");
Route::get("get-ticket-month",[DashboardController::class, "get_ticket_month"]);

Route::withoutMiddleware(["auth"])->group(function() {
    Route::get("/login", [AuthController::class, "login"])->name("auth.login");
    Route::get('/auth/redirect/{provider}', function ($provider) {
        return Socialite::driver($provider)->redirect();
    })->name("auth.redirect");
    Route::get('/auth/callback/{provider}', [AuthController::class, "callback"]);
});

Route::get("chart", function() {
    return view("chart");
});

Route::get("test", function() {
    $data = Group::select("id","group_name","leader_id")->with("user:id,name")->withCount([
        'ticket as total' => function ($query) {
            $query->whereIn('status', [1,2,3,4]);
        },
        'ticket as totalDone' => function ($query) {
            $query->whereIn('status', [3,4]);
        },
        'ticket as totalNotDone' => function ($query) {
            $query->whereIn('status', [1,2]);
        }
    ])->get();
    return $data;
    // $data = Group::select("id", "group_name")->with("ticket:id,group_id")->get(); // muon lay truong cu the phai co khoa ngoai de hieu
});

// statistic
Route::get("/statistic/getData", [StatisticController::class, "getData"]);

Route::get("/test-notify", function() {
            $total_new_tickets_user = Ticket::query()->where("status", 1)->where("creator_id", Auth::id())->orWhere("cc",Auth::id())->get();
            dd($total_new_tickets_user);
    // $a = Ticket::find(41)->deadline;
    // return strtotime(Carbon::now()) - strtotime(Carbon::parse($a));
    // $result = CRest::call('im.notify.system.add', Array(
    //     'USER_ID' => 945,
    //     'MESSAGE' => 'Hệ thống đã tự động đóng yêu cầu.',
    //     "ATTACH" => Array(
    //         "ID" => 1,
    //         "COLOR" => "#29619b",
    //         "BLOCKS" => Array(
    //            Array("LINK" => Array(
    //               "NAME" => "Yêu cầu: Sửa ống nước",
    //               "DESC" => "",
    //               "LINK" => "https://b24-0ozis8.bitrix24.vn/marketplace/app/1/"
    //            )),
    //         )
    //     )
    //  ));
    // bắn notify
    // $storeToken = auth()->user()->storeToken;
    // $infoToken = [
    //     "access_token"=>$storeToken->access_token,
    //     "domain"=>$storeToken->domain,
    //     "refresh_token"=>$storeToken->refresh_token,
    //     "application_token"=>$storeToken->application_token,
    // ];
    // $data = array();
    // $userCallback= CRest::call('user.get',[ "id"=>[1,945]]);

        // foreach($userCallback["result"] as $userBitrix) {
        //     $data[] = ["id"=>$userBitrix["ID"], "email"=>$userBitrix["EMAIL"]];
        // }
    // dd($userCallback);

        // $userCallback= CRest::call(
        // 'user.get',
        // array(
        //     "to"=>945,
        //     "message"=> "Xin chào Nguyễn Thanh Giang",
        //     "ATTACH" => Array(
        //         "ID" => 1,
        //         "COLOR" => "#29619b",
        //         "BLOCKS" => Array(
        //            Array("LINK" => Array(
        //               "NAME" => "Yêu cầu: Sửa mạng cho cơ sở 6",
        //               "DESC" => "Cần làm trước khi về",
        //               "LINK" => "https://admin.sconnect.edu.vn/marketplace/app/7/"
        //            )),
        //         )
        //     )
        // ), $infoToken);

    // $userCallback= CRest::call(
    //     'im.notify',
    //     array(
    //         "to"=>945,
    //         "message"=> "Nguyễn Thanh Giang vừa thích yêu cầu của bạn",
    //         "ATTACH" => Array(
    //             "ID" => 1,
    //             "COLOR" => "#29619b",
    //             "BLOCKS" => Array(
    //                Array("LINK" => Array(
    //                   "NAME" => "Yêu cầu: Sửa mạng cho cơ sở 6",
    //                   "LINK" => "https://admin.sconnect.edu.vn/marketplace/app/7/",
    //                   "DISPLAY" => "LINE"
    //                )),
    //                Array("GRID" => Array(
    //                 [],[]
    //              )),
    //             ))), $infoToken);

// Array(
//     "NAME" => "Assigned to",
//     "VALUE" => "Evgeniy Shelenkov",
//     "DISPLAY" => "ROW",
//     "WIDTH" => 100
//  ),



// 'to' => 945,
// 'MESSAGE' => 'Personal notification',
// 'MESSAGE_OUT' => 'Personal notification text for email',
// 'TAG' => 'TEST',
// 'SUB_TAG' => 'SUB|TEST',
// 'ATTACH' => ''
//      )
Helper::notifySystem(945, "Hệ thống đã tự động đóng yêu cầu.", "Yêu cầu: Sửa mạng cho cơ sở 6", "Nhóm hỗ trợ IT", "18.08.2022 21:40");
});

Route::get("get-statistic-staff/{id_group}", [DashboardController::class, "get_statistic_staff"])->name("ticket.get-statistic-staff");

Route::get("/settings", [ConfigController::class, "index"])->name("ticket.settings");
Route::post("/settings/save-time", [ConfigController::class, "saveTimeClose"])->name("ticket.timeclose-save");
Route::post("/settings/save-calendar", [ConfigController::class, "saveCalendar"])->name("ticket.calendar-save");
Route::get("/settings/edit-calendar/{id}", [ConfigController::class, "editCalendar"])->name("ticket.calendar-edit");
Route::post("/settings/update-calendar/{id}", [ConfigController::class, "updateCalendar"])->name("ticket.calendar-update");
Route::DELETE("/settings/delete-calendar/{id}", [ConfigController::class, "deleteCalendar"])->name("ticket.calendar-delete");
Route::post("/settings/save-holiday", [ConfigController::class, "saveHoliday"])->name("ticket.holiday-save");
Route::get("/settings/edit-holiday/{id}", [ConfigController::class, "editHoliday"])->name("ticket.holiday-edit");
Route::post("/settings/update-holiday/{id}", [ConfigController::class, "updateHoliday"])->name("ticket.holiday-update");
Route::DELETE("/settings/delete-holiday/{id}", [ConfigController::class, "deleteHoliday"])->name("ticket.holiday-delete");
// route category
Route::get("/group", [GroupController::class, "index"])->name("ticket.group");
Route::get("/getData", [GroupController::class, "getData"])->name("ticket.getData");
Route::post("/save-group", [GroupController::class, "save"])->name("ticket.save-group");
Route::get("/get-detail-group/{id}", [GroupController::class, "detail"])->name("ticket.detail-group");
Route::post("/update-group/{id}", [GroupController::class, "update"])->name("ticket.update-group");
Route::get("/ticket/get-user-availble", [GroupController::class, "getUserAvailble"])->name("ticket.get-user-availble");
Route::delete("/delete-group/{id}", [GroupController::class, "delete"])->name("ticket.delete-group");

//route ticket
Route::get('/my-ticket', [TicketController::class, "get_my_ticket"])->name("ticket.my-ticket");
Route::get('/assign-ticket',[TicketController::class, "get_assign_ticket"])->name("ticket.assign-ticket");
Route::get("/ticket/get-group", [TicketController::class, "getGroup"])->name("ticket.getGroup");
Route::get("/ticket/get-assignee", [TicketController::class, "getAssignee"])->name("ticket.get-assignee");
Route::get("/ticket/get-members", [TicketController::class, "getMembers"])->name("ticket.get-members");
Route::get("/ticket/get-assignee-group", [TicketController::class, "getAssigneeGroup"])->name("ticket.get-assignee-group");
Route::get("/ticket/get-assignee-by-group/{group_id}", [TicketController::class, "getAssigneeByGroup"])->name("ticket.getAssigneeByGroup");
Route::get("/ticket/get-assignee-by-ticket/{ticket_id}", [TicketController::class, "getAssigneeByTicket"])->name("ticket.getAssigneeByTicket");
Route::get("/ticket/get-ticket-incomplete", [TicketController::class, "get_ticket_incomplete"])->name("ticket.get-ticket-incomplete");
Route::post("/ticket/save", [TicketController::class, "save"])->name("ticket.save");
Route::post("/ticket/confirm-ticket", [TicketController::class, "confirm_ticket"])->name("ticket.confirm-ticket");
Route::get("/ticket/getdata", [TicketController::class, "getdata"])->name("ticket.getdata");
Route::get('/ticket/getAssignTicket',[TicketController::class, "getdata_assign_ticket"]);
Route::get("/ticket/detail/{id}", [TicketController::class, "detail"])->name("ticket.detail");
Route::post("/ticket/update/{id}", [TicketController::class, "update"])->name("ticket.update");
Route::delete("/ticket/delete/{id}", [TicketController::class, "delete"])->name("ticket.delete");
Route::get("/ticket/histories/get/{id_ticket}", [TicketController::class, "getHistories"])->name("ticket.getHistories");

// ticket comment
Route::post("/ticket/comment", [TicketController::class, "comment"])->name("ticket.comment");
Route::post("/ticket/update-status", [TicketController::class, "update_status"])->name("ticket.update-status");
Route::post("/ticket/update-success/{ticket_id}", [TicketController::class, "update_success"])->name("ticket.update-success");
Route::post('/ticket/like-comment',[TicketController::class, "like_comment"])->name("ticket.like-comment");
Route::post('/ticket/update-comment',[TicketController::class, "update_comment"])->name("ticket.update-comment");
Route::DELETE('/ticket/delete-comment',[TicketController::class, "delete_comment"])->name("ticket.delete-comment");
Route::POST('/ticket/update-assignee',[TicketController::class, "update_assignee"])->name("ticket.update-assignee");
Route::POST('/ticket/update-assignee-ticket',[TicketController::class, "update_assignee_ticket"])->name("ticket.update-assignee-ticket");


