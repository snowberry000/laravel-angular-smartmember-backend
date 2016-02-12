<?php namespace App\Models;


class Category extends Root
{
    protected $table = 'categories';
    protected $hidden = array('pivot');

    public function site(){
    	return $this->belongsTo('App\\Models\\Site');
    }

    public function post(){
    	return $this->belongsTo('App\\Models\\Post');
    }

	public function applySearchQuery($q, $value)
	{
		if(!empty($value))
			return $q->where('title', 'like','%' . $value . "%");
		else
			return $q;
	}
}

Category::creating(function($model){
	\App\Models\Permalink::handleReservedWords($model);
});

Category::created(function($model){
	$model->permalink = \App\Models\Permalink::set($model);
	$model->save();
	return $model;
});