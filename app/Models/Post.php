<?php namespace App\Models;


class Post extends Root
{
    protected $table = 'posts';

    public function site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }

    public function access_level()
    {
        return $this->belongsTo('App\Models\AccessLevel', 'access_level_id');
    }

    public function applySearchQuery($q, $value)
    {
        if(!empty($value))
            return $q->where('title', 'like','%' . $value . "%");
        else
            return $q;
    }

    public function users()
    {
        return $this->belongsTo('App\Models\User', 'author_id') ;  
    }

    public function seo_settings()
    {
        return $this->hasMany('App\Models\SeoSetting', 'target_id', 'id')->whereLinkType(4);
    }

    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag','posts_tags','post_id','tag_id');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category','posts_categories','post_id','category_id');
    }

    public function discussion_settings(){
        return $this->belongsTo('App\Models\DiscussionSettings');
    }

    public static function create(array $post_data = array()){
        $discussions = [];
        $categories = [];
        $tags = [];
        $seo = null;
        unset($post_data['timeLeft']);
        unset($post_data['site']);

        $site_id = $post_data["site_id"];
        if (isset($post_data["discussion_settings"])){
            $discussions = $post_data["discussion_settings"];
            unset($post_data["discussion_settings"]);
        }

        if (isset($post_data["categories"])){
            $categories = $post_data["categories"];
            unset($post_data["categories"]);
        }


        if (isset($post_data["tags"])){
            $tags = $post_data["tags"];
            unset($post_data["tags"]);
        }

        if (isset($post_data["seo_settings"])){
            $seo = $post_data["seo_settings"];
            unset($post_data["seo_settings"]);
        }

        $discussions =  DiscussionSettings::create($discussions);

        unset($post_data['permalink']);

        $post = parent::create($post_data);
        $post->discussion_settings()->associate($discussions);
        foreach ($tags as $tag_data) {
            $tag = Tag::whereText($tag_data['text'])->first();
            if(!$tag)
                $tag = new Tag(array('text' => $tag_data['text'] , 'site_id'=>$site_id));
            $post->tags()->save($tag);
        }
        foreach ($categories as $category_data) {
            $category = Category::whereText($category_data['text'])->first();
            if(!$category)
                $category = new Category(array('text' => $category_data['text'] , 'site_id'=>$site_id));
            $post->categories()->save($category);
        }
        $post->save();
        if ($seo){
            SeoSetting::savePage($seo, $site_id, 4, $post->id);
        }

        return $post;

    }

    public function update(array $post_data = array()){
    
        $site_id = $post_data["site_id"];
        $post_id = $post_data["id"];
        unset($post_data['timeLeft']);
        unset($post_data['site']);
        if (isset($post_data["discussion_settings"])){
            $discussions = $post_data["discussion_settings"];

            if($this->discussion_settings)
                $this->discussion_settings->update($discussions);
            else{
                $discussions =  DiscussionSettings::create($discussions);
                $this->discussion_settings()->associate($discussions);
                $post_data['discussion_settings_id'] = $discussions->id;
            }
            unset($post_data["discussion_settings"]);
        }

        if (isset($post_data["categories"])){
            $categories = $post_data["categories"];
            unset($post_data["categories"]);
        }

        if (isset($post_data["tags"])){
            $tags = $post_data["tags"];
            unset($post_data["tags"]);
        }

        if (isset($post_data["seo_settings"])){
            $seo = $post_data["seo_settings"];
            SeoSetting::savePage($seo, $site_id, 4, $post_id);
            unset($post_data["seo_settings"]);
        }
        foreach ($tags as $tag_data) {
            $tag = Tag::whereText($tag_data['text'])->first();
            if(!$tag){
                $tag = new Tag(array('text' => $tag_data['text'] , 'site_id'=>$site_id));
                $this->tags()->save($tag);
            }
            $tag->fill($tag_data);
            $tag->save();
        }
        foreach ($categories as $category_data) {
            $category = Category::whereText($category_data['text'])->first();
            if(!$category){
                $category = new Category(array('text' => $category_data['text'] , 'site_id'=>$site_id));
                $this->categories()->save($category);
            }
            $category->fill($category_data);
            $category->save();
        }

        unset($post_data['permalink']);

        $this->fill($post_data);
        $this->save();
        
        $this->permalink = \App\Models\Permalink::set($this);
        $this->save();

        return $this;
    }

    public function addDiscussionSettings($model){
        $discussions =  DiscussionSettings::create();
        $model->discussion_settings_id = $discussions->id;
        $model->save();
    }
        
}

Post::creating(function($model){
    \App\Models\Permalink::handleReservedWords($model);
});

Post::created(function($model){
    $model->author_id = \Auth::user()->id;

    $model->permalink = \App\Models\Permalink::set($model);
    $model->save();
    return $model;
});

Post::updating(function($model){
    //return $model->checkPermalink();
});
