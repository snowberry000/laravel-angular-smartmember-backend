<?php namespace App\Http\Controllers\Api\Forum;

use App\Models\User;
use App\Models\Forum\Category;
use App\Models\Forum\Topic;

use App\Http\Controllers\Api\SMController;

use Input;

class TopicController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new Topic();

        $this->middleware("auth", ['only' => ['store','destroy','update'] ]);
    }

    public function index(){
        return parent::index();
    }

    public function getByPermalink(){

    	$permalink = Input::get('permalink');
    	$query = Topic::with(['user','replies.user','category']);
    	$topic = $query->wherePermalink($permalink)->first();

        $topic->total_views++;
        $topic->save();

        return $topic;
    }

    public function store(){
        $topic = parent::store();
        $data = $topic->toArray();
        $data["user"] = $topic->user;
        return $data;
    }
}