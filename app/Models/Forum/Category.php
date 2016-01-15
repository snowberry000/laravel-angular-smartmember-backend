<?php namespace App\Models\Forum;

use App\Models\Root;

class Category extends Root{
    
    protected $table = 'forum_categories';

    public function topics(){
        return $this->hasMany('App\Models\Forum\Topic','category_id');
    }

    public function categories(){
        return $this->hasMany('App\Models\Forum\Category','parent_id');
    }

    public function site(){
        return $this->belongsTo('App\Models\Site');
    }

}

Category::created(function($cat){
   $cat->permalink = \App\Models\Permalink::set($cat);
   $cat->save();
});