<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SupportTicket;
use App\Models\AppConfiguration\SendGridEmail;

use Exception;

class SupportTicketFiveDayPending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support-five-day';

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
        $time = time() - 60 * 60 * 24 * 5; //UNIX time for 120 hours ago
        $five_days_ago = date( 'Y-m-d H:i:s', $time );
        \Log::info("processing five day pending tickets for tickets older than: " . $five_days_ago );
        $tickets = SupportTicket::whereStatus('pending')->whereFiveDaySent(0)->whereParentId(0)->where('last_replied_at','<', $five_days_ago )->get();

        foreach( $tickets as $ticket )
        {
            $full_ticket = SupportTicket::with(array(
                   'reply' => function ($query) {
                       $query->orderBy('created_at', 'desc');
                   } , 'reply.user' , 'user' , 'agent' ,
                   'actions'=> function ($query) {
                       $query->orderBy('created_at', 'desc');
                   } , 'actions.user'))->find( $ticket->id );

            $full_ticket->data = [];

            $full_ticket->data = \App\Http\Controllers\Api\SupportTicketController::sortTickets($full_ticket);

            SendGridEmail::sendFiveDayPendingSupportEmail( $full_ticket );

            $ticket->status = 'solved';
            $ticket->five_day_sent = 1;
            $ticket->save();
        }
    }
}
