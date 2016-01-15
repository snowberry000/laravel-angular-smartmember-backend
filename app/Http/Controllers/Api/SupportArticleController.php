<?php namespace App\Http\Controllers\Api;

use App\Models\SupportArticle;

class SupportArticleController extends SMController
{
    public function __construct(){
        parent::__construct();

        $this->middleware("auth",['except' => array('index','show','getByPermalink')]);
        $this->middleware('admin',['except'=>array('index','show' , 'getByPermalink')]); 
        $this->model = new SupportArticle();
    }

    public function store()
    {
        \Input::merge(array('author_id'=>\Auth::user()->id,'site_id'=>$this->site->id));
        return parent::store();
    }

    public function update($model)
    {
        \Input::merge(array('site_id'=>$this->site->id));
        return parent::update($model);
    }

    public function index()
    {
        return parent::paginateIndex();
    }

    public function getByPermalink($id){

        $article = SupportArticle::wherePermalink($id)->whereSiteId($this->site->id)->first();
        if($article)
            return $this->show($article);
        \App::abort('404','Article not found');
    }

}