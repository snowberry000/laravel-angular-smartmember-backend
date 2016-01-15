<?php namespace App\Http\Controllers\Api;

use App\Models\TicketNote;
use App\Models\User;
use App\Models\AccessLevel;


class AdminNoteController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new TicketNote();   

    }
}