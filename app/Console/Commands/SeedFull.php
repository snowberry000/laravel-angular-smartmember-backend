<?php namespace App\Console\Commands;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Models\AccessLevel;
use App\Models\Role;
use App\Models\RoleType;
use App\Models\SeoSetting;
use App\Models\SiteNotice;
use App\Models\Transaction;
use App\Models\UserNote;
use App\Models\AccessLevel\Pass;
use App\Models\Affiliate;
use App\Models\Comment;
use App\Models\Download;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Post;
use App\Models\SpecialPage;
use App\Models\SupportArticle;
use App\Models\SupportCategory;
use App\Models\SupportTicket;
use App\Models\CannedResponse;


class SeedFull extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'smartm_migrate';
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Migrate database.';

	private $db_smartm, $db_smartmembers;
	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->db_smartm = \DB::connection('old_sm');
		$this->db_smartmembers = \DB::connection('mysql');

	}
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		exec(realpath(__DIR__.'/../../../migrate.sh'));
        
		$this->grantedAccessLevels();
		$this->siteMenus();
        $this->siteFooter();
        $this->lessons_permalinks();
        $this->pages_permalinks();
        $this->posts_permalinks();
        $this->downloads_permalinks(); 
        $this->articles_permalinks();  
        $this->livecasts_permalinks();                            
	}

	public function grantedAccessLevels()
    {
        $accessLevels = $this->db_smartm->select('select id, granted_access_levels from smartm_accesslevels');

        $grants = [];
        foreach ($accessLevels as $accessLevel) 
        {
            if ($accessLevel->granted_access_levels)
            {
                $grantedAccesses = unserialize($accessLevel->granted_access_levels); 
                foreach($grantedAccesses as $grantedAccess)
                {
                	if ($grantedAccess == $accessLevel->id) continue;

                    $grants[] = array('access_level_id' => $accessLevel->id , 'grant_id' => $grantedAccess);                  
                }
            }

            if (count($grants) > 1024)
			{
				$this->flush('access_grants', $grants);
				unset($grants);
				$grants = [];
			}
        }

        $this->flush('access_grants', $grants);
    }

    public function siteMenus()
    {
    	$options = $this->db_smartm->select('select site_id, meta_key, meta_value from smartm_site_options where meta_key= :key',
    			['key' => 'menu_items']);

        $menus = [];
        foreach ($options as $option) 
        {
        	if($option->meta_value);
            {
                $items = unserialize($option->meta_value); 
                
                foreach ($items as $item) 
                {
                	$menu_item = [];
                	$menu_item['site_id'] = $option->site_id;

                	foreach ($item as $key => $value) {
                		$menu_item[$key] = $value;
                	}

                	$menus[] = $menu_item;
                	unset($menu_item);
                }
            }


        	if (count($menus) > 1024)
        	{
        		$this->flush('sites_menu_items', $menus);
        		unset($menus);
        		$menus = [];
        	}
        }

        $this->flush('sites_menu_items', $menus);
    }

     public function siteFooter()
    {
        $options = $this->db_smartm->select('select id, site_id, meta_key, meta_value from smartm_site_options where meta_key= :key',
                ['key' => 'footer_menu_items']);

        $menus = [];
        foreach ($options as $option) 
        {
            if ($option->id == 7778) continue;

            if($option->meta_value);
            {
                
                try 
                {
                    $items = unserialize($option->meta_value);     
                } 
                catch (ErrorException $e)
                {
                    print "Failed to unserialize " . $option->meta_value;
                    print_r($e->getMessage());
                }
                
                foreach ($items as $item) 
                {
                    $menu_item = [];
                    $menu_item['site_id'] = $option->site_id;

                    foreach ($item as $key => $value) {
                        $menu_item[$key] = $value;
                    }

                    $menus[] = $menu_item;
                    unset($menu_item);
                }
            }


            if (count($menus) > 1024)
            {
                $this->flush('sites_footer_menu_items', $menus);
                unset($menus);
                $menus = [];
            }
        }

        $this->flush('sites_footer_menu_items', $menus);
    }

    public function pages_permalinks(){
        $pages = $this->db_smartmembers->select("select id , permalink , title from custom_pages where permalink = ''");
        foreach ($pages as $page) {
            $pages_flush = array('permalink'=>$this->checkPermalink($page->title));
            $this->update('custom_pages',$page->id, $pages_flush);
        }
    }

    public function posts_permalinks(){
        $posts = $this->db_smartmembers->select("select id , permalink , title from posts where permalink = ''");
        foreach ($posts as $post) {
            $posts_flush = array('permalink'=>$this->checkPermalink($post->title));
            $this->update('posts',$post->id, $posts_flush);
        }
    }

    public function downloads_permalinks(){
        $downloads = $this->db_smartmembers->select("select id , permalink , title from download_center where permalink = ''");
        foreach ($downloads as $download) {
            $downloads_flush = array('permalink'=>$this->checkPermalink($download->title));
            $this->update('download_center',$download->id, $downloads_flush);
        }
    }

    public function lessons_permalinks(){
        $lessons = $this->db_smartmembers->select("select id , permalink , title from lessons where permalink = ''");
        foreach ($lessons as $lesson) {
            $lessons_flush = array('permalink'=>$this->checkPermalink($lesson->title));
            $this->update('lessons',$lesson->id, $lessons_flush);
        }
    }

    public function articles_permalinks(){
        $articles = $this->db_smartmembers->select("select id , permalink , title from support_articles where permalink = ''");
        foreach ($articles as $article) {
            $articles_flush = array('permalink'=>$this->checkPermalink($article->title));
            $this->update('support_articles',$article->id, $articles_flush);
        }
    }

    public function livecasts_permalinks(){
        $livecasts = $this->db_smartmembers->select("select id , permalink , title from livecasts where permalink = ''");
        foreach ($livecasts as $livecast) {
            $livecasts_flush = array('permalink'=>$this->checkPermalink($livecast->title));
            $this->update('livecasts',$livecast->id, $livecasts_flush);
        }
    }

    public function emailRecipients()
    {
    	// $emails = $this->db_smartm->select('select id, email_lists, site_id, company_id from smartm_emails');

     //    $recipients = [];
     //    foreach ($emails as $email) 
     //    {
     //    	if($email->email_lists);
     //        {
     //            $lists = unserialize($email->email_lists); 
                
     //            foreach ($lists as $list) 
     //            {
     //            	$recipient = [];
     //            	$recipient['site_id'] = $option->site_id;

     //            	foreach ($item as $key => $value) {
     //            		$menu_item[$key] = $value;
     //            	}

     //            	$menus[] = $menu_item;
     //            	unset($menu_item);
     //            }
     //        }


     //    	if (count($menus) > 1024)
     //    	{
     //    		$this->flush('sites_menu_items', $menus);
     //    		unset($menus);
     //    		$menus = [];
     //    	}
     //    }

     //    $this->flush('sites_menu_items', $menus);
    }

    private function flush($table, $data)
    {
    	if ( ! $data || empty($data) ) return;

    	$this->db_smartmembers->table($table)->insert($data);
    }

    private function update($table, $id ,$data)
    {
        if ( ! $data || empty($data) ) return;

        $this->db_smartmembers->table($table)->where('id',$id)->update($data);
    }

    private function checkPermalink($title){
        //Make sure Permalink doesnt 
        $permalink = str_replace(" ", "-", trim($title)); 
        return $permalink .= "-" . date(DATE_ATOM);
    }

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['path', InputArgument::OPTIONAL, 'Path to CSV'],
		];
	}
	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
		];
	}
}