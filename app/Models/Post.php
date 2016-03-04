<?php namespace App\Models;

use App\Models\ShortCode;

class Post extends Root
{
    protected $table = 'posts';
	protected $with = ['categories'];

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
		return $this->belongsToMany('App\Models\Category', 'posts_categories', 'post_id', 'category_id')->withTimestamps()->wherePivot( 'deleted_at', null )->distinct();
    }

    public function discussion_settings(){
        return $this->belongsTo('App\Models\DiscussionSettings');
    }

    public function getContentAttribute($value)
    {
        return ShortCode::replaceShortcode($value);
    }

    public function dripfeed()
    {
        return $this->hasOne('App\Models\DripFeed', 'target_id', 'id')->whereType('posts');
    }

    public static function create(array $post_data = array()){
        $discussions = [];
        $categories = [];
        $tags = [];
        $dripfeed = [];
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

        if (isset($post_data['dripfeed_settings']) && !empty($post_data['dripfeed_settings']))
        {
            $dripfeed = $post_data["dripfeed_settings"];
        }
        unset($post_data["dripfeed_settings"]);

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

        if ($dripfeed)
        {
            DripFeed::set($post, $dripfeed);
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

        if( !empty( $post_data['remove_dripfeed'] ) )
        {
            DripFeed::remove($this);
        }
        elseif (isset($post_data['dripfeed_settings']) && !empty($post_data['dripfeed_settings']))
        {
            $dripfeed = $post_data['dripfeed_settings'];
            DripFeed::set($this, $dripfeed);
        }
        unset($post_data['dripfeed_settings']);
        unset($post_data['remove_dripfeed']);

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

Post::saved(function($model){
	if( \Input::has('chosen_categories') )
	{
		$category_ids = [];

		foreach( \Input::get('chosen_categories') as $key => $val )
		{
            if (!in_array($val, $category_ids))
			    $category_ids[] = $val;

			$category = PostCategory::wherePostId( $model->id )->whereCategoryId( $val )->first();

			if( !$category )
                $category = PostCategory::create( ['post_id' => $model->id, 'category_id' => $val ] );
		}

		$extra_categories = PostCategory::wherePostId( $model->id );

		if( !empty( $category_ids ) )
			$extra_categories = $extra_categories->whereNotIn( 'category_id', $category_ids );

		$extra_categories = $extra_categories->get();

		foreach( $extra_categories as $extra_category )
			$extra_category->delete();
	}
    if (\Input::has('seo_settings'))
    {
        $seo = \Input::get('seo_settings');
        if ($seo){
            SeoSetting::savePage($seo, $model->site_id, 4, $model->id);
        }
    }
});

Post::updated(function($model) {
    if (\Input::has('remove_dripfeed'))
    {
        DripFeed::remove($model);
    }
    elseif (\Input::has('dripfeed_settings'))
    {
        \Log::info('set dripfeed');
        DripFeed::set($model, \Input::get('dripfeed_settings'));
    }
    if (\Input::has("discussion_settings")){
        $discussions = \Input::get("discussion_settings");

        if($model->discussion_settings)
            $model->discussion_settings->update($discussions);
        else{
            $discussions =  DiscussionSettings::create($discussions);
            $model->discussion_settings()->associate($discussions);
            $model->discussion_settings_id = $discussions->id;
            $model->save();
        }
    }
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
