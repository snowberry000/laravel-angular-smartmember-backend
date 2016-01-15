<?php namespace App\Models;


class SupportArticle extends Root
{
    protected $table = 'support_articles';

    public static function create(array $data = array())
    {
        unset($data['permalink']);
        $article = parent::create($data);
        $article->save();
        return $article;
    }

    public function applySearchQuery($q, $value)
    {

        return $q->where('title', 'like','%' . $value . "%");
    }
    
    public function category(){
        return $this->belongsTo("App\\Models\\SupportCategory" , 'category_id');
    }

    public function update(array $data=array())
    {
        
        unset($data['permalink']);
        $data['site_id'] = $this->site->id;

        $this->fill($data);
        $this->save();

        $this->permalink = \App\Models\Permalink::set($this);
        $this->save();
        
        return $this;
    }
}

SupportArticle::creating(function($model){
    \App\Models\Permalink::handleReservedWords($model);
});

SupportArticle::created(function($model){
    $model->author_id = \Auth::user()->id;

    $model->permalink = \App\Models\Permalink::set($model);
    $model->save();
    return $model;
});

SupportArticle::updating(function($article){
    //$article->checkPermalink();
});
