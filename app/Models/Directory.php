<?php namespace App\Models;


class Directory extends Root
{
    protected $table = 'directory_listings';

    public function site() {
        return $this->belongsTo('App\Models\Site');
    }

    public function approve(){
    	$this->pending_updates = false;
    	$this->is_approved = true;
    	$this->title = $this->pending_title;
        $this->subtitle = $this->pending_subtitle;
        $this->description = $this->pending_description;
    	$this->pricing = $this->pending_pricing;
    	$this->image = $this->pending_image;
    	parent::save();        
    	return true;
    }

    public function save(array $options = array()){
        // parent::save();
        if(parent::save())
        {
            $this->approve();
        }

    }

    public static function getAllCategories(){
        $records = \DB::select('select distinct(category) from directory_listings');

        $cats = [];
        foreach ($records as $record){
            if ($record->category){
                $cats[] = $record->category;
            }
        }
        return $cats;
    }

}

Directory::creating(function($directory){    
    if ($directory->permalink){
        if(Directory::wherePermalink($directory->permalink)->first()){
            \App::abort(402,"Permalink is already taken, please choose something else");
        }
    }
});

Directory::created(function($directory){
    $directory->approve();
});