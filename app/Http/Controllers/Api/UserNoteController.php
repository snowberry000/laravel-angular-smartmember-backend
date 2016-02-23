<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\UserNote;
use App\Models\User;
use App\Models\Lesson;
use Input;
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
        $page_size = config("vars.default_page_size");
        $query = $this->model;

        $query = $query->orderBy('id' , 'DESC');
        $query = $query->whereNull('deleted_at');
        $query = $query->whereNotNull('note');
        foreach (Input::all() as $key => $value){
            switch($key){
                case 'q':
                    if (Input::get('q')){
                        $query = $this->model->applySearchQuery($query,$value);
                    }
                    break;
                case 'view':
                case 'p':
                case 'bypass_paging':
                    break;
                default:
                    if( !empty( $value ) || $value === 0 || $value === "0" )
                        $query->where($key,'=',$value);
            }
        }

        $notes = [];

        if(isset($params['distinct']) && $params['distinct'])
            $notes['total_count'] = $query->distinct()->count('user_id');
        else
            $notes['total_count'] = $query->count();
        if( !Input::has('bypass_paging') || !Input::get('bypass_paging') )
            $query = $query->take($page_size);

        if( Input::has('p') )
            $query->skip((Input::get('p')-1)*$page_size);

        $notes['items'] = $query->get();

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