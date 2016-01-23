<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailRecipientsQueue;
use App\Models\EmailQueue;

use Exception;

class SmartMailSegmentEngine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smartmail-segment-engine';

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
        $sites = EmailRecipientsQueue::distinct()
                ->whereNotNull('site_id')
                ->where('site_id', '!=', 0)
                ->select('site_id')
                ->lists('site_id');

        // \Config::set('smartmail.debug', true);

        foreach ($sites as $site)
        {
            $queue = new EmailQueue;
            try
            {
                \Log::info("Processing recipients queue for " . $site);
                $queue->processRecipientsQueue($site, false);
            } 
            catch (Exception $e)
            {
                \Log::info("Failed to process email recipient queue for site " . $site . " " . $e->getMessage());
            }

            continue;
            
        }

    }
}
