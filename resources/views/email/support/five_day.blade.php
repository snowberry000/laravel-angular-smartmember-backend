@extends('email.layout')

@section('main-content')

<div style="max-width:600px;margin:0 auto">
	<p style="font-size:17px;line-height:24px;margin:0 0 16px;text-align:center"></p>
	<h4 style="color:#3a3b3c;line-height:30px;margin-bottom:12px;margin:0 auto 0.75rem;font-size:0.875rem;">
		Hello <?= $name ?>
	</h4>
	<p style="font-size:17px;line-height:24px;margin:0 0 16px"></p>
	<p style="font-size:17px;line-height:24px;margin:0 0 16px">
		We have noticed that this ticket has not been updated for a period of 5 days.
	</p>
    <p style="font-size:17px;line-height:24px;margin:0 0 16px">We will assume that this ticket is resolved and close the ticket.  If you still need help, just open the ticket and respond and it will reopen the ticket.</p>
    <p style="font-size:17px;line-height:24px;margin:0 0 16px">The full conversation history is shown below.</p>
	<hr style="border:none;border-bottom:1px solid #ececec;margin:1.5rem 0;width:100%">
		 @include('email.support.conversation')			
	<p style="font-size:17px;line-height:24px;margin:0 0 16px">
	</p>
	</p>
</div>

@stop