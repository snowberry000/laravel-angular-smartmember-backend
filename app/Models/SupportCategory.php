<?php namespace App\Models;


class SupportCategory extends Root
{
    protected $table = 'support_categories';

    public function applySearchQuery($q, $value)
	{

		return $q->where('title', 'like','%' . $value . "%");
	}

    public function articles(){
    	return $this->hasMany("App\\Models\\SupportArticle" , 'category_id')->orderBy("sort_order",'ASC');
    }
}
