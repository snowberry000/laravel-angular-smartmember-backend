<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailQueue;
use Carbon\Carbon;
use Exception;
use PRedis;

class SmartMailEngine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smartmail-engine';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process cron activities.';

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
    public function handle()
	{
		\Log::info( "Starting e-mail queue processing." );
		$now = Carbon::now();

		$last_site = PRedis::get( 'last_site_email_queued_for' );

		$sites = EmailQueue::distinct()
			->whereNotNull( 'site_id' )
			->where( 'site_id', '!=', 0 )
			->where( 'send_at', '<=', Carbon::now() );

		if( $last_site )
			$sites = $sites->where( 'site_id', '>', $last_site );

		$sites = $sites->orderBy('site_id', 'asc')
                ->select('site_id')
                ->lists('site_id');

		if( !$sites || count( $sites ) < 1 )
		{
			PRedis::setex('last_site_email_queued_for', 24 * 60 * 60, 0);

			$sites = EmailQueue::distinct()
				->whereNotNull('site_id')
				->where('site_id', '!=', 0)
				->where('send_at', '<=', Carbon::now() )
				->orderBy('site_id', 'asc')
				->select('site_id')
				->lists('site_id');
		}

        // \Config::set('smartmail.debug', true);

		// FORCE CHRIS
		$sites[] = 6325;

        foreach ($sites as $site)
        {
			PRedis::setex('last_site_email_queued_for', 24 * 60 * 60, $site);

            $queue = new EmailQueue;
            try
            {
                \Log::info("Processing queue for " . $site);
                $queue->processQueue($site, true);
            } 
            catch (Exception $e)
            {
				\Log::info("Failed to process email queue for site " . $site );

				do {
					\Log::info( $e->getFile() . ':' . $e->getLine() . ' ' . $e->getMessage() . ' (' . $e->getCode() . ') [' . get_class($e) . ']' );
				} while($e = $e->getPrevious());
            }
            continue;
        }

    }
}
