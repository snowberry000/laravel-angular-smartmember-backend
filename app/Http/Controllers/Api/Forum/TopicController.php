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
    	$topic = $query->wherePermalink($permalink)->whereSiteId($this->site->id)->first();
        if ($topic)
        {
            if ($topic->total_views && !empty($topic->total_views))
                $topic->total_views++;
            else
                $topic->total_views = 0;

            $topic->save();

            return $topic;
        } else {
            $query2 = Topic::with(['user','replies.user','category']);
            $topic2 = $query2->wherePermalink($permalink)->first();

            if ($topic2) {
                if ($topic2->total_views && !empty($topic2->total_views))
                    $topic2->total_views++;
                else
                    $topic2->total_views = 0;

                $topic2->save();

                return $topic2;
            } else {
                \App::abort('404','Topic not found');
            }
        }
    }

    public function store(){
        \Input::merge(['site_id' => $this->site->id]);
        $topic = parent::store();
        $data = $topic->toArray();
        $data["user"] = $topic->user;
        return $data;
    }
}