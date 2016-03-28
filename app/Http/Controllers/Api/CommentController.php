<?php namespace App\Http\Controllers\Api;

use App\Models\Comment;
use App\Models\User;
use App\Helpers\SMAuthenticate;
use Auth;
class CommentController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new Comment();  
        $this->middleware("auth",['except' => array('index','show')]);
        $this->middleware('access' , ['only'=>array('show' )]);
    }

    public function index(){

		if( !$this->site ){
			$error = array("message" => 'This site does not exist. Please check URL.', "code" => 500);
			return response()->json($error)->setStatusCode(500);
		}

        \Input::merge(array('parent_id'=>0 , 'site_id'=>$this->site->id , 'target_id' =>\Input::get('target_id') , 'type' => \Input::get('type') ));

        $comments = parent::paginateIndex(['with' => ['user' , 'reply' , 'reply.user']]);

    	// $comments = Comment::with(['user' , 'reply','reply.user'])->whereSiteId($this->site->id)->whereTargetId(\Input::get('target_id'))->whereType(\Input::get('type'))->whereParentId(0)->get();
    	foreach ($comments['items'] as $i=>$comment) {
            if(!$comment->public){
                if (!SMAuthenticate::set()){
                    unset($comments[$i]);
                    continue;
                }else{
                    if($comment->user_id== Auth::user()->id){
                        continue;
                    }
                    else if( \SMRole::hasAccess($this->site->id,'manage_content') ){
                        continue;
                    }
                    unset($comments[$i]);
                    continue;
                }
            }
    	}
    	return $comments;
    }

    public function store(){
        \Input::merge(array('site_id'=>$this->site->id ));
    	$comment = parent::store();
    	$comment->user = User::find($comment->user_id);
        $comment->reply = Comment::whereParentId($comment->id)->with('user')->get();
    	return $comment;
    }

}