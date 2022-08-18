<?php

namespace App\Jobs;

use App\Helper\Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyBitrix implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dataSend;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dataSend)
    {
        $this->dataSend = $dataSend;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->dataSend;
        Helper::notify($data["storeToken"], $data["to"],
        $data["message"],
        $data['title'],
        $data['group_name'],
        $data['deadline'],
    );
    }
}

