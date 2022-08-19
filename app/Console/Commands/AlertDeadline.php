<?php

namespace App\Console\Commands;

use App\Models\Config;
use App\Models\Ticket;
use App\Notify\NotifyBitrix\NotifyBitrix24;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AlertDeadline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:alertDeadline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'alert deadline for everyone';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tickets = Ticket::whereIn("status",[1,2])->get();
        if(!empty($tickets)) {
           $notifyBitrix24 = new NotifyBitrix24();
           try {
               foreach ($tickets as $ticket) {
                       if(strtotime(Carbon::now()) >= strtotime(Carbon::parse($ticket->deadline)) && is_null($ticket->confirm_deadline)) {
                        info("Trạng thái yêu cầu da thay đổi alert 2");
                          // sendnotify
                           $ticket->confirm_deadline = Carbon::now();
                           $ticket->save();
                           $attribute = [
                               "message"=>"Yêu cầu đã hết hạn. Hãy hoàn thành sớm yêu cầu sớm nhất có thể.",
                               "title"=>"Yêu cầu: ".$ticket->title,
                               "group_name"=>$ticket->group->group_name,
                               "deadline"=>$ticket->deadline,
                           ];
                           $notifyBitrix24->sendSystem($ticket->creator_id, $attribute); // creator
                           $notifyBitrix24->sendSystem($ticket->group->leader_id, $attribute); // leader
                           if(!is_null($ticket->assignees_id) && !empty($ticket->assignees_id)) {
                               foreach(json_decode($ticket->assignees_id) as $assignee_id) {
                                   if($ticket->group->leader_id == $assignee_id) continue;
                                   $notifyBitrix24->sendSystem($assignee_id, $attribute); // assignees
                               }
                           }
                           if(!is_null($ticket->cc) && !empty($ticket->cc) && $ticket->creator_id != $ticket->cc)
                           $notifyBitrix24->sendSystem($ticket->cc, $attribute); // cc
                           info("Đã cập nhật trạng thái đóng yêu cầu");
                       }
                       //bắn notify hệ thống tự động đóng yêu cầu
               }
               info("Trạng thái yêu cầu không thay đổi alert");
           } catch (\Throwable $th) {
               info($th);
           }
        }
    }
}
