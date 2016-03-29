<?php namespace App\Http\Controllers\Api;

use App\Models\Comment;
use App\Models\User;
use App\Helpers\SMAuthenticate;
use App\Models\AppConfiguration\SendGridEmail;
use Auth;
use SendGrid;

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

        if(\Input::has('parent_id')){
            $parent = Comment::whereId(\Input::get('parent_id'))->with('user')->first();

            if(isset($parent->id) && !empty($parent->user) && !empty($parent->user->email)){
                $user = array('email' => $parent->user->email , 'name' => isset($parent->user->first_name) ? $parent->user->first_name : '');
                SendGridEmail::sendCommentReplyEmail($user, $this->site);
            }
        }

    	$comment = parent::store();
    	$comment->user = User::find($comment->user_id);
        $comment->reply = Comment::whereParentId($comment->id)->with('user')->get();
    	return $comment;
    }

}