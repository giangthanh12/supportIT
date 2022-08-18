<?php

namespace App\Console\Commands;

use App\Jobs\NotifyBitrix;
use App\Jobs\NotifySystem;
use App\Traits\HistoryTrait;
use App\Models\Config;
use App\Models\Ticket;
use App\Notify\NotifyBitrix\NotifyBitrix24;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CloseTicket extends Command
{
    use HistoryTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:close';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'auto close ticket';

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
     $timeClose = (int) Config::find("timeclose")->cfg_value;
     $tickets = Ticket::where("status",3)->get();
     if(!empty($tickets)) {
        $notifyBitrix24 = new NotifyBitrix24();
        try {
            foreach ($tickets as $ticket) {
                if($ticket->status == 3) {
                    if((strtotime(Carbon::now()) - strtotime(Carbon::parse($ticket->deadline))) >= $timeClose * 3600) {
                        $ticket->status = 4;
                        $this->addHistory($ticket->id, "Trạng thái yêu cầu thay đổi từ đã xử lý <i class='fas fa-arrow-right'></i> đóng yêu cầu");
                        $ticket->save();
                        // sendnotify
                        $attribute = [
                            "message"=>"Hệ thống đã tự động đóng yêu cầu.",
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
            }
            info("Trạng thái yêu cầu không thay đổi");
        } catch (\Throwable $th) {
            info($th);
        }
     }
    }
}
