<?php namespace App\Models;

use App\Models\Root;
use App\Models\User;
use App\Models\UserNote;
use App\Models\Lesson;
use SMCache;
use App\Helpers\DomainHelper;

class UserNote extends Root
{
    protected $table = 'user_notes';
    
    public function user(){
    	$this->belongsTo("App\\Models\\User");
    }

    public function site(){
    	$this->belongsTo("App\\Models\\Site");
    }

    public function lesson(){
    	$this->belongsTo("App\\Models\\Lesson");
    }

    public function applySearchQuery($query , $value){
    	$users = User::where('first_name','like','%' . $value . "%")->orWhere('last_name','like','%' . $value . "%")->select(array('id'))->get();
        $lessons = Lesson::whereSiteId(\Input::get('site_id'))->where('title' , 'like','%' . $value . "%")->select(array('id'))->get();
        $notes = UserNote::whereSiteId(\Input::get('site_id'))->where('note' , 'like','%' . $value . "%")->select(array('id'))->get();
    	$query = $query->whereSiteId(\Input::get('site_id'))->whereIn('user_id' , $users)->orWhereIn('lesson_id',$lessons)->orWhereIn('id',$notes);
    	return $query;
    }

}

UserNote::saving(function($note){

    $subdomain = DomainHelper::getSubdomain();
    $routes[] = $subdomain . ':module_home:' . \Auth::user()->access_token;
    
    SMCache::clear($routes);
    return $note;
});

UserNote::deleting(function($note){

    $subdomain = DomainHelper::getSubdomain();
    $routes[] = $subdomain . ':module_home:' . \Auth::user()->access_token;
    
    SMCache::clear($routes);
    return $note;
});