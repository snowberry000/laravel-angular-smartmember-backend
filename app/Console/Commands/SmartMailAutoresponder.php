<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailAutoResponder;

use Exception;

class SmartMailAutoresponder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smartmail-autoresponder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process auto-responders activities.';

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
        EmailAutoResponder::processAutoResponders();
    }
}
