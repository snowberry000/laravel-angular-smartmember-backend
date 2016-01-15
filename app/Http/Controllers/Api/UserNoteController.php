<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\UserNote;
use App\Models\User;
use App\Models\Lesson;

class UserNoteController extends SMController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new UserNote();
    }
    
    public function store(){
        if (isset(\Auth::user()->id))
        {
            \Input::merge(array('user_id' =>  \Auth::user()->id));
            return parent::store();
        } else {
            \App::abort(403,"Session expired! Please log out and login again!");
        }
    }

    public function index(){
    	$notes = parent::paginateIndex();
    	foreach ($notes['items'] as $note) {
    		$note->user = User::find($note->user_id);
    		$note->lesson = Lesson::find($note->lesson_id);
    	}
    	return $notes;
    }

    public function single($lesson_id){
        if (\Auth::user())
        {
            return UserNote::whereUserId(\Auth::user()->id)->whereLessonId($lesson_id)->first();
        }
    }
}