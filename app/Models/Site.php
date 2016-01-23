<?php namespace App\Models;

use App\Models\Site\Role;
use App\Models\TeamRole;
use App\Models\User;
use App\Models\AppConfiguration\SendGridEmail;
use App\Http\Controllers\Api\SiteController;
use DB;
use SMCache;

class Site extends Root
{
    protected $table = 'sites';

    public function setSubdomainAttribute( $value){
        if(preg_match('/[^a-z_\-0-9]/i', $value)){
          \App::abort(403,"This subdomain is not in a valid format. Only Alphanumeric is allowed");
        }
        $this->attributes['subdomain'] = $value;
    }

    public function addMember($user, $type='member', $password = '', $skip_email = false, $cbreceipt=false){
        if (Role::whereUserId($user->id)
            ->whereSiteId($this->id)
			->whereType($type)->first()){
            return;
        }

        $member = Role::create(array(
                    "user_id" => $user->id,
                    "site_id" => $this->id,
					"type" => $type
                ));
        
        $this->total_members = $this->total_members + 1;
		$this->save();

        if ($type == 'member' && !$skip_email) {
            SendGridEmail::sendNewUserSiteEmail($user, $this, $password, $cbreceipt);
        } 
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function media(){
        return $this->hasMany('App\Models\Media');
    }

    public function configured_app()
    {
        return $this->hasMany('App\Models\AppConfiguration', 'site_id');
    }

    public function meta_data()
    {
        return $this->hasMany('App\Models\SiteMetaData', 'site_id');
    }

    public function menu_items()
    {
        return $this->hasMany('App\Models\SiteMenuItem', 'site_id')->orderBy('sort_order','desc')->orderBy('created_at','desc');
    }

    public function footer_menu_items()
    {
        return $this->hasMany('App\Models\SiteFooterMenuItem', 'site_id')->orderBy('sort_order','desc')->orderBy('created_at','desc');
    }

    public function affiliates()
    {
        return $this->hasMany('App\Models\Affiliate', 'site_id');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction', 'site_id');
    }

    public function vimeo_integration()
    {
        return $this->hasOne('App\Models\VimeoIntegration', 'site_id');
    }

    public function ad()
    {
        return $this->hasMany('App\Models\SiteAds', 'site_id')->orderBy('sort_order','ASC')->orderBy('created_at','desc')->where('display',true);
    }

	public function widgets()
	{
		return $this->hasMany('App\Models\Widget', 'site_id')->orderBy('sort_order','ASC')->orderBy('created_at','desc');
	}

    public function members()
    {
        return $this->hasMany('App\Models\Site\Role', 'site_id')->distinct();
    }

    public function links()
    {
        return $this->hasManu('App\Models\Link', 'site_id');
    }

    public static function getShareData($subdomain){
        $site = self::whereSubdomain($subdomain)->first();
        $meta = array(
            "title" => $site->name
        );

        $settings = \App\Models\SiteMetaData::whereSiteId($site->id)->get(); 


        if ($settings){
            foreach ($settings as $setting){
                if ($setting["key"] == "fb_share_title"){
                    $meta["title"] = $setting["value"];
                }else if ($setting["key"] == "fb_share_description"){
                    $meta["description"] = $setting["value"];
                }else if ($setting["key"] == "fb_share_image"){
                    $meta["image"] = $setting["value"];
                }
            }
        }
        return $meta;
    }

	public static function applySearchQuery($q, $value)
	{
		if(!empty($value))
			return $q->where(function($query) use ($value) {
				$query->whereRaw("CONCAT( `subdomain`, '.smartmember.com' ) LIKE '%" . $value . "%'");
				$query->orwhere('name', 'like','%' . $value . "%");
				$query->orwhere('subdomain', 'like','%' . $value . "%");
				$query->orwhere('domain', 'like','%' . $value . "%");
			});
		else
			return $q;
	}

    public function getHeaderBackgroundColor()
    {
        $color = \App\Models\SiteMetaData::whereSiteId($this->id)->whereKey('primary_theme_color')->first();

        if( $color )
            return $color->value;

        $theme = \App\Models\SiteMetaData::whereSiteId($this->id)->whereKey('theme')->first();

        if( $theme )
            $theme = $theme->value;
        else
            $theme = 'default';

        switch( $theme )
        {
            case 'default':
                return '#222222';
                break;
            case 'cerulean':
                return '#033c73';
                break;
            case 'cosmo':
                return '#2780e3';
                break;
            case 'cyborg':
                return '#222222';
                break;
            case 'darkly':
                return '#00bc8c';
                break;
            case 'flatly':
                return '#18bc9c';
                break;
            case 'journal':
                return '#eb6864';
                break;
            case 'lumen':
                return '#ffffff';
                break;
            case 'paper':
                return '#2196f3';
                break;
            case 'readable':
                return '#ffffff';
                break;
            case 'sandstone':
                return '#93c54b';
                break;
            case 'simplex':
                return '#d9230f';
                break;
            case 'slate':
                return '#7a8288';
                break;
            case 'spacelab':
                return '#446e9b';
                break;
            case 'superhero':
                return '#df691a';
                break;
            case 'united':
                return '#772953';
                break;
            case 'yeti':
                return '#008cba';
                break;
        }
    }

    public function clone_site($site_id , $clone_id , $user_id){
        $this->clone_posts($site_id , $clone_id , $user_id);
        $this->clone_downloads($site_id , $clone_id , $user_id);
        $this->clone_lessons($site_id , $clone_id , $user_id);
        $this->clone_pages($site_id , $clone_id , $user_id);
        $this->clone_livecasts($site_id , $clone_id , $user_id);
        $this->clone_articles($site_id , $clone_id , $user_id);
        $this->clone_menu_items($site_id , $clone_id , $user_id);
        $this->clone_footer_menu_items($site_id , $clone_id , $user_id);
        $this->clone_site_meta_data($site_id , $clone_id , $user_id);
        $this->clone_special_pages($site_id , $clone_id , $user_id);
        $this->clone_access_levels($site_id, $clone_id, $user_id);
        $this->clone_ads_banner($site_id, $clone_id, $user_id);
        $this->clone_widgets($site_id, $clone_id, $user_id);
		$this->clone_forum_categories($site_id, $clone_id, $user_id);
        $this->fix_access_levels($site_id, $clone_id);
        return array('success' => true);
    }

	private function fix_access_levels( $site_id, $clone_id )
	{
		$old_access_levels = \App\Models\AccessLevel::whereSiteId( $clone_id )->get();
		$access_levels = array();

		foreach( $old_access_levels as $access_level )
		{
			$new_access_level = \App\Models\AccessLevel::whereSiteId( $site_id )->whereName( $access_level->name )->first();

			if( $new_access_level )
				$access_levels[ $access_level->id ] = $new_access_level->id;
		}

		if( !empty( $access_levels ) )
		{
			$downloads = \App\Models\Download::whereSiteId( $site_id )->where( 'access_level_id', '!=', 0 )->get();
			$this->update_access_levels( $downloads, $access_levels );

			$lessons = \App\Models\Lesson::whereSiteId( $site_id )->where( 'access_level_id', '!=', 0 )->get();
			$this->update_access_levels( $lessons, $access_levels );

			$pages = \App\Models\CustomPage::whereSiteId( $site_id )->where( 'access_level_id', '!=', 0 )->get();
			$this->update_access_levels( $pages, $access_levels );

			$livecasts = \App\Models\Livecast::whereSiteId( $site_id )->where( 'access_level_id', '!=', 0 )->get();
			$this->update_access_levels( $livecasts, $access_levels );

			$forum_categories = \App\Models\Forum\Category::whereSiteId( $site_id )->where( 'access_level_id', '!=', 0 )
				->get();
			$this->update_access_levels( $forum_categories, $access_levels );
		}

		return array('success' => true);
	}

	private function update_access_levels( $things, $access_levels )
	{
		foreach( $things as $thing )
		{
			if( !empty( $access_levels[ $thing->access_level_id ] ) )
			{
				$thing->access_level_id = $access_levels[ $thing->access_level_id ];
				$thing->save();
			}
		}
	}

    private function clone_access_levels($site_id, $clone_id, $user_id) {
        $columns = 'name , information_url , redirect_url , product_id , jvzoo_button , price , currency ,
        payment_interval , stripe_plan_id , hash, expiration_period';
        return $this->clone_table( 'access_levels', $columns, $site_id, $clone_id );
    }

    private function clone_ads_banner($site_id, $clone_id, $user_id) {
        $columns = 'banner_url , banner_image_url , open_in_new_tab , sort_order , custom_ad';
        return $this->clone_table( 'sites_ads', $columns, $site_id, $clone_id );
    }

	private function clone_widgets($site_id, $clone_id, $user_id) {
		$columns = 'sidebar_id , target_id , sort_order, type';
		$result = $this->clone_table( 'widgets', $columns, $site_id, $clone_id );

		$clone_widgets = Module::whereSiteId($site_id)->orderBy('id')->get();
		$widgets = Widget::whereSiteId($clone_id)->orderBy('id')->get();

		/* need to clone the widget meta still
		$original_widget_ids = [];
		foreach( $widgets as $widget )
			$original_widget_ids[] = $widget->id;
		*/

		$clone_banners = SiteAds::whereSiteId($site_id)->orderBy('id')->get();
		$banners = SiteAds::whereSiteId($clone_id)->orderBy('id')->get();

		foreach ($banners as $index => $banner)
		{
			DB::table('widgets')
				->where('target_id', $banner->id)
				->where('site_id' , $site_id)
				->whereType('banner')
				->update(['target_id' => $clone_banners[$index]->id]);
		}

		return array('success' => $result);
	}

	private function clone_forum_categories($site_id, $clone_id, $user_id) {
		$columns = 'title, description, parent_id, access_level_id, access_level_type, allow_content, icon, permalink';
		return $this->clone_table( 'forum_categories', $columns, $site_id, $clone_id, true );
	}

    private function clone_downloads($site_id , $clone_id , $user_id){
        $columns = 'title , description , download_button_text , sort_order , media_item_id , access_level_type , access_level_id , embed_content , featured_image , permalink';
        $return = $this->clone_table( 'download_center', $columns, $site_id, $clone_id, true, $user_id, 'creator_id' );

		$columns = 'title, url, aws_key, type';
		$this->clone_table( 'media_items', $columns, $site_id, $clone_id );

		$clone_media_items = MediaItem::whereSiteId($site_id)->orderBy('id')->get();
		$media_items = MediaItem::whereSiteId($clone_id)->orderBy('id')->get();

		foreach ($media_items as $index => $media_item) {
			DB::table('download_center')
				->where('media_item_id', $media_item->id)
				->where('site_id' , $site_id)
				->update(['media_item_id' => $clone_media_items[$index]->id]);
		}

		return $return;
    }

    private function clone_posts($site_id , $clone_id , $user_id){
        $columns = 'title , content , note , embed_content , featured_image , access_level_type , access_level_id , permalink , transcript_content , transcript_button_text , transcript_content_public , audio_file';
		return $this->clone_table( 'posts', $columns, $site_id, $clone_id, true, $user_id, 'author_id' );
    }

    private function clone_modules($site_id , $clone_id , $user_id){
        $columns = 'sort_order,title,note,access_level';
        $result = $this->clone_table( 'modules', $columns, $site_id, $clone_id );
        
        $clone_modules = Module::whereSiteId($site_id)->orderBy('id')->get();
        $modules = Module::whereSiteId($clone_id)->orderBy('id')->get();

        foreach ($modules as $index => $module) {
            DB::table('lessons')
                ->where('module_id', $module->id)
                ->where('site_id' , $site_id)
                ->update(['module_id' => $clone_modules[$index]->id]);
        }

        return $result;
    }

    private function clone_discussion_settings($site_id , $clone_id , $table_name){
        //$columns = DB::select('show columns from discussion_settings where Field NOT IN (\'id\', \'created_at\' , \'deleted_at\' , \'updated_at\' , \'site_id\')');
        //$columns = array_pluck($columns , 'Field');
        //$columns = implode(',' , $columns);
        return;
        
        $columns = 'show_comments,newest_comments_first,close_to_new_comments,allow_replies,public_comments  ';
        //$result = DB::insert('insert into discussion_settings ('.$columns.' , site_id , created_at) select '.$columns.' , '.$site_id.' , \''.(new \DateTime())->format('Y-m-d H:i:s').'\' from discussion_settings where id IN (select discussion_settings_id from '.$table_name.' where site_id = '.$clone_id.' and deleted_at is null) ');
        $result = DB::table($table_name)
                ->select(DB::raw('count(*) as count_settings'))
                ->where('site_id' , '=' , $clone_id)
                ->whereNull('deleted_at')->get();

        $query = 'INSERT INTO discussion_settings ('.$columns.' , site_id , created_at) VALUES';
        for($i=0 ; $i < $result[0]->count_settings; $i++){
            if($i != 0)
                $query = $query . ',';
            $query = $query.' (0,0,0,0,0,' . $site_id .' , \''.(new \DateTime())->format('Y-m-d H:i:s').'\')';
        }

        if($result[0]->count_settings)
            DB::insert($query);
        $clone_settings = DiscussionSettings::whereSiteId($site_id)->orderBy('id')->get();
        $settings = DiscussionSettings::whereSiteId($clone_id)->orderBy('id')->get();

        //return $lessons;
        foreach ($settings as $index => $setting) {
            DB::table($table_name)
                ->where('discussion_settings_id', $setting->id)
                ->where('site_id' , $site_id)
                ->update(['discussion_settings_id' => $clone_settings[$index]->id]);
        }

        return array('success' => $result);
    }

    private function clone_lessons($site_id , $clone_id , $user_id){
        
        //$columns = DB::select('show columns from lessons where Field NOT IN (\'id\', \'created_at\' , \'deleted_at\' , \'updated_at\' , \'site_id\' , \'author_id\')');
        //$columns = array_pluck($columns , 'Field');
        //$columns = implode(',' , $columns);
        
        $columns = 'module_id,sort_order,next_lesson,prev_lesson,presenter,title,content,note,type,embed_content,featured_image,transcript_content,transcript_button_text,transcript_content_public,audio_file,access_level_type,access_level_id,discussion_settings_id,permalink,remote_id ';
        $result = DB::insert('insert into lessons ('.$columns.' , site_id , author_id , created_at) select '.$columns.' , '.$site_id.' , '.$user_id.' , \''.(new \DateTime())->format('Y-m-d H:i:s').'\' from lessons as l where l.site_id='.$clone_id.' and l.deleted_at is null');

        $this->clone_modules($site_id , $clone_id , $user_id);
        $this->clone_discussion_settings($site_id , $clone_id , 'lessons');
        $this->clone_permalinks($site_id , $clone_id , 'lessons');

        return array('success' => $result);
    }

    private function clone_pages($site_id , $clone_id , $user_id){
        $columns = 'title,content,note,embed_content,featured_image,access_level_type,access_level_id,permalink';
		return $this->clone_table( 'custom_pages', $columns, $site_id, $clone_id, true );
    }

    private function clone_livecasts($site_id , $clone_id , $user_id){
        $columns = 'title,content,note,embed_content,featured_image,access_level_type,access_level_id,permalink';
		return $this->clone_table( 'livecasts', $columns, $site_id, $clone_id, true, $user_id, 'author_id' );
    }

    private function clone_articles($site_id , $clone_id , $user_id){
        $columns = 'title,content,embed_content,featured_image,permalink,sort_order';
		return $this->clone_table( 'support_articles', $columns, $site_id, $clone_id, true, $user_id, 'author_id' );
    }

    private function clone_menu_items($site_id , $clone_id , $user_id){
        $columns = 'url,label,icon,sort_order,custom_icon';
		return $this->clone_table( 'sites_menu_items', $columns, $site_id, $clone_id );
    }

    private function clone_footer_menu_items($site_id , $clone_id , $user_id){
        $columns = 'url,label,sort_order';
		return $this->clone_table( 'sites_footer_menu_items', $columns, $site_id, $clone_id );
    }

    private function clone_site_meta_data($site_id , $clone_id , $user_id){
        $columns = 'data_type,value';
        $exclude = "l.`key` != 'facebook_conversion_pixel' AND l.`key` != 'facebook_retargetting_pixel' AND l.`key` != 'google_analytics_id' AND l.`key` != 'bing_id'";
        $sql = "insert into site_meta_data (".$columns." , `key`, site_id , created_at) select ".$columns.",`key`,".$site_id.",'".(new \DateTime())->format('Y-m-d H:i:s')."' from site_meta_data as l where (" . $exclude . ") AND l.site_id=".$clone_id." and l.deleted_at is null";
        $result = DB::insert($sql);
        return array('success' => $result);
    }

    private function clone_special_pages($site_id , $clone_id , $user_id){
        $columns = 'type,title,content,note,embed_content,featured_image,file_url,access_level,multiple,free_item_url,free_item_text,continue_refund_text,use_free_item_url';
		return $this->clone_table( 'special_pages', $columns, $site_id, $clone_id );
    }

	private function clone_table( $table_name, $columns, $site_id, $clone_id, $permalinks = false, $user_id = false, $user_field = false )
	{
		$sql = 'insert into ' . $table_name . ' ( '.$columns . ( !empty( $site_id ) ? ', site_id' : '' ) . ', created_at ' . ( !empty( $user_id ) && !empty( $user_field ) ? ', ' . $user_field : '' ) . ')
		select '.$columns. ( !empty( $site_id ) ? ', ' . $site_id : '' ) .',\'
		'.(new \DateTime())->format('Y-m-d H:i:s').'\' ' .
		( !empty( $user_id ) && !empty( $user_field ) ? ', ' . $user_id : '' )
	    . ' from '.$table_name.' as t where t.deleted_at is null ' . ( !empty( $clone_id ) ? ' and t.site_id = ' . $clone_id . ' ' : '' );

		\Log::info( 'cloning ' . $table_name . ': ' . $sql );

		$result = DB::insert( $sql );

		if( $permalinks )
			$this->clone_permalinks( $site_id, $clone_id, $table_name );

		return array('success' => $result);
	}

    private function clone_permalinks($site_id , $clone_id , $table_name){
        //$columns = DB::select('show columns from permalinks where Field NOT IN (\'id\', \'created_at\' , \'deleted_at\' , \'updated_at\' , \'site_id\')');
        //$columns = array_pluck($columns , 'Field');
        //$columns = implode(',' , $columns);
        
        $columns = 'permalink,type,target_id ';
        $result = DB::insert('insert into permalinks ('.$columns.' , site_id , created_at) select '.$columns.' , '.$site_id.' , \''.(new \DateTime())->format('Y-m-d H:i:s').'\' from permalinks as l where l.site_id='.$clone_id.' and type = \''.$table_name.'\''.' and l.deleted_at is null');

        $permalinks = Permalink::whereSiteId($site_id)->whereType($table_name)->get();
        foreach ($permalinks as $key => $permalink) {
            $target_id = DB::select('select id from '.$table_name.' where permalink = \''.$permalink->permalink.'\' and site_id = '.$site_id);

            if(!empty($target_id))
            {
                $target_id = $target_id[0]->id;
                $permalink->target_id = $target_id;
                $permalink->save();
            }
        }
        return array('success' => $result);
    }
}

Site::creating(function($site){
    if (Site::whereSubdomain($site->subdomain)->first()){
        \App::abort("409","A site with '" . $site->subdomain . "' already exists");
    }

    $site->user_id = \Auth::user()->id;

	if( !\Auth::user()->setup_wizard_complete )
	{
		\Auth::user()->setup_wizard_complete = 1;
		\Auth::user()->save();
	}

	\App\Models\Event::Log( 'site-created', array(
		'site_id' => 0,
		'user_id' => $site->user_id,
		'subdomain' => $site->subdomain
	) );
});

Site::saving(function($site){
    $routes[] = 'site_details';
    
    SMCache::reset($routes);

	BridgePage::clearHomepageCache( $site->id );

    return $site;
});

Site::deleting(function($site){

    //$company->permalink = Company::setPermalink($company);
    $routes[] = 'site_details';

	BridgePage::clearHomepageCache( $site->id );

    SMCache::reset($routes);
    return $site;
});

Site::created(function($site){
    $site->addMember(\Auth::user(),'owner');
   // SendGridEmail::sendNewSiteEmail(\Auth::user(), $site);
});
