@extends('email.layout')

@section('main-content')

<div style="max-width:600px;margin:0 auto">
	<p style="font-size:17px;line-height:24px;margin:0 0 16px;text-align:center"></p>
	<h4 style="color:#3a3b3c;line-height:30px;margin-bottom:12px;margin:0 auto 0.75rem;font-size:0.875rem;">
		Hello <?= $name ?>
	</h4>
	<!-- <h3 style="color:#3a3b3c;line-height:26px;margin-bottom:2rem;font-size:1.2rem;text-align:center;margin:0 auto 1rem">Monday, August 24th - Sunday, August 30th</h3> -->
	<p style="font-size:17px;line-height:24px;margin:0 0 16px"></p>
	<p style="font-size:17px;line-height:24px;margin:0 0 16px">
		We're writing to let you know a new support ticket was assigned to you (# <?= $ticket_id ?>).
	</p>
	<hr style="border:none;border-bottom:1px solid #ececec;margin:1.5rem 0;width:100%">
		@include ('email.support.conversation')
	<p style="font-size:17px;line-height:24px;margin:0 0 16px">
	</p>
	</p>
</div>

@stop