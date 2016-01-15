<?php namespace App\Http\Controllers\Api\Forum;

use App\Models\User;
use App\Models\Forum\Category;
use App\Models\Forum\Topic;
use App\Models\Forum\Reply;

use App\Http\Controllers\Api\SMController;

use Input;

class ReplyController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new Reply();
        $this->middleware("auth", ['only' => ['store','destroy','update'] ]);
    }

    public function store(){
    	$reply = parent::store();
    	$data = $reply->toArray();
    	$data['user'] = $reply->user;
    	return $data;
    }

}