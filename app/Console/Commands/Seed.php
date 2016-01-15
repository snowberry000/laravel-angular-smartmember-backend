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


class Seed extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'seed';
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Seed database.';
	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{

		//$this->accessLedgers(1);
		//$this->accessLevels(1);
		//$this->roles(1);
		//$this->seo_settings(1);
		//$this->site_notices(1);
		//$this->transactions(1);
		//$this->user_notes(1);
		//$this->access_passes(1);
		//$this->affiliates(1);
		//$this->affiliate_teams(1);
		//$this->comments(1);
		//$this->downloads(1);
		//$this->lessons(1);
		//$this->modules(1);
		//$this->posts(1);
		//$this->special_pages(1);
		//$this->support_articles(1);
		//$this->support_categories(1);
		//$this->support_tickets(1);
		$this->canned_responses(1);

	}

	public function accessLedgers($site_id)
	{
		$old_access_ledgers = \DB::connection('old_sm')
				->select('select * from smartm_accessledgers where site_id = :site_id', ['site_id' => $site_id]);
		foreach ($old_access_ledgers as $access_ledgers) {
				
		}
	}

	public function accessLevels($site_id)
	{
		$old_access_levels = \DB::connection('old_sm')
				->select('select * from smartm_accesslevels where site_id = :site_id', ['site_id' => $site_id]);
		foreach ($old_access_levels as $access_level) {
			AccessLevel::create(array(
										'site_id' => $site_id ,
										'name' => $access_level->name ,
										'information_url' => $access_level->info_url ,
										'redirect_url' => $access_level->redirect_url ,
										'product_id' => $access_level->jvzoo_product_id ,
										'price' => $access_level->stripe_price ,
										'payment_interval' => $access_level->payment_type ,
										'stripe_plan_id' => $access_level->stripe_plan_id
								));
		}
	}

	public function roles($site_id)
	{
		$old_roles = \DB::connection('old_sm')
				->select('select * from smartm_roles where site_id = :site_id', ['site_id' => $site_id]);
		foreach ($old_roles as $role) {

			Role::create(array(
									'site_id' => $site_id ,
									'user_id' => $role->user_id ,
									'company_id' => $role->company_id ,
									'role_type' => RoleType::where('role_name' ,'like' ,$role->role)->first()->id
							
							));
		}
	}

	public function seo_settings($site_id)
	{
		$old_seo_settings = \DB::connection('old_sm')
				->select('select * from smartm_seo_settings where site_id = :site_id', ['site_id' => $site_id]);

		foreach ($old_seo_settings as $seo_settings) {
			$link_type = 1;
			switch ($seo_settings->type) {
				case 'lesson':
					$link_type = 2;
					break;
				case 'download':
					$link_type = 3;
					break;
				case 'livecast':
					$link_type = 5;
					break;
				case 'post':
					$link_type = 4;
					break;
				default:
					# code...
					break;
			}
			SeoSetting::create(array(
										'site_id' =>$site_id ,
										'company_id' => $seo_settings->company_id ,
										'target_id' => $seo_settings->target_id ,
										'meta_key' => $seo_settings->meta_key ,
										'meta_value' => $seo_settings->meta_value ,
										'link_type' => $link_type

								));
		}
	}

	public function site_notices($site_id)
	{
		$old_site_notices = \DB::connection('old_sm')
				->select('select * from smartm_sitenotices where site_id = :site_id', ['site_id' => $site_id]);
		foreach ($old_site_notices as $old_notice) {

			SiteNotice::create(array(
									'site_id' => $site_id ,
									'title' => $old_notice->title ,
									'content' => $old_notice->content ,
							
							));
		}
	}

	public function transactions($site_id)
	{
		$old_transactions = \DB::connection('old_sm')
				->select('select * from smartm_transactions where site_id = :site_id', ['site_id' => $site_id]);
		foreach ($old_transactions as $transaction) {

			Transaction::create(array(
									'site_id' => $site_id ,
									'user_id' => $transaction->user_id ,
									'source' => $transaction->source ,
									'affiliate_id' => $transaction->affiliate_id ,
									'product_id' => $transaction->product_id ,
									'name' => $transaction->name ,
									'email' => $transaction->email ,
									'payment_method' => $transaction->payment_method ,
									'price' => $transaction->price ,
									'association_hash' => $transaction->association_hash ,
									'data' => $transaction->data ,
							
							));
		}
	}

	public function user_notes($site_id)
	{
		$old_user_notes = \DB::connection('old_sm')
				->select('select * from smartm_user_note where site_id = :site_id', ['site_id' => $site_id]);
		foreach ($old_user_notes as $user_note) {

			UserNote::create(array(
									'site_id' => $site_id ,
									'company_id' => $user_note->company_id ,
									'lesson_id' => $user_note->lesson_id ,
									'complete' => $user_note->complete ,
									'user_id' => $user_note->user_id ,
									'note' => $user_note->note ,
							
							));
		}
	}

	public function access_passes($site_id)
	{
		$old_access_passes = \DB::connection('old_sm')
				->select('select * from smartm_accessledgers where site_id = :site_id', ['site_id' => $site_id]);
		foreach ($old_access_passes as $access_pass) {

			Pass::create(array(
									'site_id' => $site_id ,
									'access_level_id' => $access_pass->access_level_id ,
									'expired_at' => $access_pass->expire_date ,
									'user_id' => $access_pass->user_id ,							
							));
		}
	}

	public function affiliates($site_id)
	{
		$old_affiliates = \DB::connection('old_sm')
				->select('select * from smartm_affiliates where site_id = :site_id', ['site_id' => $site_id]);
		foreach ($old_affiliates as $affiliate) {

			Affiliate::create(array(
									'site_id' => $site_id ,
									'user_id' => $affiliate->jvzoo_user_id ,
									'company_id' => $affiliate->company_id ,
									'affiliate_request_id' => $affiliate->affiliate_request_id ,
									'user_email' => $affiliate->jvzoo_user_email ,
									'user_name' => $affiliate->jvzoo_user_name ,
									'user_country' => $affiliate->jvzoo_user_country ,
									'user_note' => $affiliate->jvzoo_user_note,
									'admin_note' => $affiliate->admin_note ,
									'past_sales' => $affiliate->past_sales ,
									'product_name' => $affiliate->product_name ,
									'featured_image' => $affiliate->featured_image ,
									'original' => $affiliate->original
							));
		}
	}

	public function affiliate_teams($site_id)
	{
		$old_affiliate_teams = \DB::connection('old_sm')
				->select('select * from smartm_affteam where site_id = :site_id', ['site_id' => $site_id]);
		foreach ($old_affiliate_teams as $affiliate_team) {

			AffiliateTeam::create(array(
									'site_id' => $site_id ,
									'company_id' => $affiliate_team->company_id ,	
									'name' => $affiliate_team->name ,						
							));
		}
	}

	public function comments($site_id)
	{
		$old_comments = \DB::connection('old_sm')
				->select('select * from smartm_comments where site_id = :site_id', ['site_id' => $site_id]);
		foreach ($old_comments as $comment) {
			Comment::create(array(
									'user_id' => $comment->user_id ,	
									'parent_id' => $comment->parent_id ,	
									'target_id' => $comment->target_id ,	
									'body' => $comment->comment ,	
									'public' => $comment->public ,
									'type' => 0 
			
							));
			
		}
	}

	public function downloads($site_id)
	{
		$old_downloads = \DB::connection('old_sm')
				->select('select * from smartm_downloads where site_id = :site_id', ['site_id' => $site_id]);
		foreach ($old_downloads as $download) {
			Download::create(array(
									'site_id' => $site_id ,
									'creator_id' => $download->creator_id ,	
									'title' => $download->title ,	
									'description' => $download->description ,	
									//'download_button_text' => $download->public ,
									'media_item_id' => $download->media_item_id ,	
									'embed_content' => $download->embed_content ,	
									'featured_image' => $download->featured_image ,	
									'permalink' => '' 
							));
			
		}
	}

	public function lessons($site_id)
	{
		$old_lessons = \DB::connection('old_sm')
				->select('select * from smartm_lessons where site_id = :site_id', ['site_id' => $site_id]);
		foreach ($old_lessons as $lesson) {
			Lesson::create(array(
									'site_id' => $site_id ,
									'author_id' => $lesson->author_id ,
									'module_id' => $lesson->module_id ,
									'sort_order' => $lesson->sort_order ,
									'next_lesson' => $lesson->next_lesson ,
									'prev_lesson' => $lesson->prev_lesson ,
									'presenter' => $lesson->presenter ,
									'note' => $lesson->note ,
									'transcript_content' => $lesson->transcript_content ,
									'title' => $lesson->title ,	
									'audio_file' => $lesson->audio_file ,	
									'embed_content' => $lesson->embed_content ,	
									'featured_image' => $lesson->featured_image ,	
									'permalink' => '' 
							));
			
		}
	}

	public function modules($site_id)
	{
		$old_modules = \DB::connection('old_sm')
				->select('select * from smartm_modules where site_id = :site_id', ['site_id' => $site_id]);
		foreach ($old_modules as $module) {
			Module::create(array(
									'site_id' => $site_id ,
									'company_id' => $module->company_id ,
									'sort_order' => $module->sort_order ,
									'note' => $module->note ,
									'title' => $module->title ,	
							));
			
		}
	}

	public function posts($site_id)
	{
		$old_posts = \DB::connection('old_sm')
				->select('select * from smartm_posts where site_id = :site_id', ['site_id' => $site_id]);
		foreach ($old_posts as $post) {
			Post::create(array(
									'site_id' => $site_id ,
									'author_id' => $post->author_id ,
									'company_id' => $post->company_id ,
									'note' => $post->note ,
									'title' => $post->title ,	
									'embed_content' => $post->embed_content ,	
									'content' => $post->content ,	
									'featured_image' => $post->featured_image ,	
									'permalink' => '' 
							));
			
		}
	}

	public function special_pages($site_id)
	{
		$old_special_pages = \DB::connection('old_sm')
				->select('select * from smartm_special_pages where site_id = :site_id', ['site_id' => $site_id]);
		foreach ($old_special_pages as $special_page) {
			SpecialPage::create(array(
									'site_id' => $site_id ,
									'company_id' => $special_page->company_id ,
									'type' => $special_page->type ,	
									'note' => $special_page->note ,
									'title' => $special_page->title ,	
									'embed_content' => $special_page->embed_content ,	
									'content' => $special_page->content ,	
									'featured_image' => $special_page->featured_image ,
									'multiple' => $special_page->multiple
							));
			
		}
	}

	public function support_articles($site_id)
	{
		$old_support_articles = \DB::connection('old_sm')
				->select('select * from smartm_support_article where site_id = :site_id', ['site_id' => $site_id]);
		foreach ($old_support_articles as $support_article) {
			SupportArticle::create(array(
									'site_id' => $site_id ,
									'author_id' => $support_article->author_id ,
									'category_id' => $support_article->category_id ,	
									'sort_order' => $support_article->sort_order ,
									'title' => $support_article->title ,	
									'embed_content' => $support_article->embed_content ,	
									'content' => $support_article->content ,	
									'featured_image' => $support_article->featured_image ,
									'permalink' => ''
							));
			
		}
	}

	public function support_categories($site_id)
	{
		$old_support_categories = \DB::connection('old_sm')
				->select('select * from smartm_support_category where site_id = :site_id', ['site_id' => $site_id]);
		foreach ($old_support_categories as $support_category) {
			SupportCategory::create(array(
									'id' => $support_category->id,
									'site_id' => $site_id ,
									'sort_order' => $support_category->sort_order ,
									'title' => $support_category->title ,	
							));
			
		}
	}

	public function support_tickets($site_id)
	{
		$old_support_tickets = \DB::connection('old_sm')
				->select('select * from smartm_support_ticket where site_id = :site_id', ['site_id' => $site_id]);
		foreach ($old_support_tickets as $support_ticket) {
			SupportTicket::create(array(
									'id' => $support_ticket->id,
									'site_id' => $site_id ,
									'user_id' => $support_ticket->user_id,
									'customer_id' => $support_ticket->customer_id,
									'subject' => $support_ticket->subject ,
									'message' => $support_ticket->message ,	
									'parent_id' => $support_ticket->parent_id ,
									'type' => $support_ticket->type ,
									'category' => $support_ticket->category ,	
									'priority' => $support_ticket->priority ,	
									'read' => $support_ticket->read ,	
									'status' => $support_ticket->status ,
									'attachment' => $support_ticket->attachment ,	

							));
			
		}
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