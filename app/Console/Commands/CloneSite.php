<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Exception;

class CloneSite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clone-site';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process site cloning.';

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
