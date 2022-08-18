<?php
namespace App\Traits;

use App\Models\History;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

Trait HistoryTrait {
    public function addHistory($id_ticket, $desc) {
        History::query()->insert([
            'ticket_id'=>$id_ticket,
            'creator_id'=>Auth::check() ? Auth::id() : 0,
            'desc_change'=>$desc,
            "created_at"=>Carbon::now()
        ]);
    }
}
