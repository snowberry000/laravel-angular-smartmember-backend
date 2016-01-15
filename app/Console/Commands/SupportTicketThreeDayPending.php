<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SupportTicket;
use App\Models\AppConfiguration\SendGridEmail;

use Exception;

class SupportTicketThreeDayPending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support-three-day';

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
        $time = time() - 60 * 60 * 24 * 3; //UNIX time for 72 hours ago
        $three_days_ago = date( 'Y-m-d H:i:s', $time );
        \Log::info("processing three day pending tickets for tickets older than: " . $three_days_ago );
        $tickets = SupportTicket::whereStatus('pending')->whereThreeDaySent(0)->whereParentId(0)->where('last_replied_at','<', $three_days_ago )->get();
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

            SendGridEmail::sendThreeDayPendingSupportEmail( $full_ticket );
            $ticket->three_day_sent = 1;
            $ticket->save();
        }
    }
}
