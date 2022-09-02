<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\StatisticController;
use App\Http\Controllers\Api\TicketController;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// })->namespace("api.dashboard");
Route::get('/', [DashboardController::class, "index"])->name("api.dashboard");
Route::middleware('auth:api')->get("get-ticket-month",[DashboardController::class, "get_ticket_month"]);
Route::middleware('auth:api')->get("get-user",[AuthController::class, "get_user"]);
Route::middleware('auth:api')->get("get-info-ticket",[DashboardController::class, "get_info_ticket"]);
Route::middleware('auth:api')->get("get-ticketNotDone",[DashboardController::class, "get_ticketNotDone"]);

// route category
Route::get('/group', [GroupController::class, "index"])->name("api.group");
Route::middleware('auth:api')->get("/group/getData", [GroupController::class, "getData"])->name("api.group-getData");
Route::middleware('auth:api')->post("/group/save", [GroupController::class, "save"])->name("api.group-save");
Route::middleware('auth:api')->get("/group/detail/{id}", [GroupController::class, "detail"])->name("api.group-detail");
Route::middleware('auth:api')->post("/group/update/{id}", [GroupController::class, "update"])->name("api.group-update");
Route::middleware('auth:api')->delete("/group/delete/{id}", [GroupController::class, "delete"])->name("api.group-delete");
Route::get("/ticket/get-user-availble", [GroupController::class, "getUserAvailble"])->name("api.get-user-availble");
Route::middleware('auth:api')->get("/get-statistic-staff/{id_group}", [DashboardController::class, "get_statistic_staff"])->name("api.get-statistic-staff");
//statistic
Route::get("/statistic", [StatisticController::class, "index"])->name("api.statistic");
Route::middleware('auth:api')->get("/statistic/getData", [StatisticController::class, "getData"]);
// settings
Route::get("/settings", [ConfigController::class, "index"])->name("api.settings");
Route::middleware("auth:api")->get("/settings/holiday/getData", [ConfigController::class, "getDataHolidays"])->name("api.getDataHolidays");
Route::middleware("auth:api")->get("/settings/calendar/getData", [ConfigController::class, "getDataCalendars"])->name("api.getDataCalendars");
Route::middleware("auth:api")->post("/settings/save-time", [ConfigController::class, "saveTimeClose"])->name("api.timeclose-save");
Route::middleware("auth:api")->post("/settings/calendar/save", [ConfigController::class, "saveCalendar"])->name("api.calendar-save");
Route::middleware("auth:api")->get("/settings/calendar/detail/{id}", [ConfigController::class, "detailCalendar"])->name("api.detailCalendar");
Route::middleware("auth:api")->post("/settings/update-calendar/{id}", [ConfigController::class, "updateCalendar"])->name("api.calendar-update");
Route::middleware("auth:api")->delete("/settings/delete-calendar/{id}", [ConfigController::class, "deleteCalendar"])->name("api.calendar-delete");
Route::middleware("auth:api")->post("/settings/holiday/save", [ConfigController::class, "saveHoliday"])->name("api.holiday-save");
Route::middleware("auth:api")->get("/settings/holiday/detail/{id}", [ConfigController::class, "detailHoliday"])->name("api.holiday-edit");
Route::middleware("auth:api")->post("/settings/update-holiday/{id}", [ConfigController::class, "updateHoliday"])->name("api.holiday-update");
Route::middleware("auth:api")->delete("/settings/holiday/delete/{id}", [ConfigController::class, "deleteHoliday"])->name("api.holiday-delete");
Route::middleware("auth:api")->delete("/settings/calendar/delete/{id}", [ConfigController::class, "deleteCalendar"]);
// ticket and comment
Route::get('/my-ticket', [TicketController::class, "get_my_ticket"])->name("api.my-ticket");
Route::get('/assign-ticket',[TicketController::class, "get_assign_ticket"])->name("api.assign-ticket");
Route::get("/ticket/get-group", [TicketController::class, "getGroup"])->name("api.getGroup");
Route::get("/ticket/get-assignee", [TicketController::class, "getAssignee"])->name("api.get-assignee");
Route::get("/ticket/get-members", [TicketController::class, "getMembers"])->name("api.get-members");
Route::get("/ticket/get-assignee-group", [TicketController::class, "getAssigneeGroup"])->name("api.get-assignee-group");
Route::get("/ticket/get-assignee-by-group/{group_id}", [TicketController::class, "getAssigneeByGroup"])->name("api.getAssigneeByGroup");
Route::get("/ticket/get-assignee-by-ticket/{ticket_id}", [TicketController::class, "getAssigneeByTicket"])->name("api.getAssigneeByTicket");
Route::get("/ticket/get-ticket-incomplete", [TicketController::class, "get_ticket_incomplete"])->name("api.get-ticket-incomplete");
Route::middleware("auth:api")->post("/ticket/save", [TicketController::class, "save"])->name("api.save");
Route::middleware("auth:api")->post("/ticket/save-shortcut", [TicketController::class, "save_shortcut"])->name("api.save-shortcut");
Route::middleware("auth:api")->get("/ticket/getdata", [TicketController::class, "getdata"])->name("api.getdata");
Route::middleware("auth:api")->get('/ticket/getAssignTicket',[TicketController::class, "getdata_assign_ticket"]);
Route::get("/ticket/detail/{id}", [TicketController::class, "detail"])->name("api.detail");
Route::middleware("auth:api")->post("/ticket/update/{id}", [TicketController::class, "update"])->name("api.update");
Route::delete("/ticket/delete/{id}", [TicketController::class, "delete"])->name("api.delete");
Route::middleware("auth:api")->post("/ticket/check-permisiion-assign", [TicketController::class, "check_permisiion_assign"])->name("api.check-permisiion-assign");
Route::middleware("auth:api")->get("/ticket/histories/get/{id_ticket}", [TicketController::class, "getHistories"])->name("api.getHistories");
Route::middleware("auth:api")->post("/ticket/confirm-ticket", [TicketController::class, "confirm_ticket"])->name("api.confirm-ticket");
// ticket comment
Route::middleware("auth:api")->post("/ticket/comment", [TicketController::class, "comment"])->name("api.comment");
Route::middleware("auth:api")->post("/ticket/update-status", [TicketController::class, "update_status"])->name("api.update-status");
Route::middleware("auth:api")->post("/ticket/update-success/{ticket_id}", [TicketController::class, "update_success"])->name("api.update-success");
Route::middleware("auth:api")->post('/ticket/like-comment',[TicketController::class, "like_comment"])->name("api.like-comment");
Route::middleware("auth:api")->post('/ticket/update-comment',[TicketController::class, "update_comment"])->name("api.update-comment");
Route::middleware("auth:api")->delete('/ticket/delete-comment',[TicketController::class, "delete_comment"])->name("api.delete-comment");
Route::middleware("auth:api")->post('/ticket/update-assignee',[TicketController::class, "update_assignee"])->name("api.update-assignee");
Route::middleware("auth:api")->post('/ticket/update-assignee-ticket',[TicketController::class, "update_assignee_ticket"])->name("api.update-assignee-ticket");
Route::middleware("auth:api")->get('/ticket/getComments/{id_ticket}',[TicketController::class, "getComments"])->name("api.getComments");

