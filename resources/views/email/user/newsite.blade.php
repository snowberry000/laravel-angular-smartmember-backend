@extends('email.layout')

@section('main-content')

<div style="max-width:600px;margin:0 auto">
	<p style="font-size:17px;line-height:24px;margin:0 0 16px;text-align:center">
	<img src="https://ci5.googleusercontent.com/proxy/mCWhazDiZOCaHyx2AH0A0jb72_Ub9UkOE6pUrFpHC2AXVPgZqysEnADKkGOuRLW29-vesgXip7VETPsEiGC5FKx9q2bYL2fYQouALfvr2NZlzzngLmA=s0-d-e1-ft#https://slack.global.ssl.fastly.net/66f9/img/email/status@2x.png" width="76" height="76" style="outline:none;text-decoration:none;width:76px"></p>
	<h2 style="color:#3a3b3c;line-height:30px;margin-bottom:12px;margin:0 auto 0.75rem;font-size:1.8rem;text-align:center">
		<?= $site_name ?> has been created!
	</h2>
	<!-- <h3 style="color:#3a3b3c;line-height:26px;margin-bottom:2rem;font-size:1.2rem;text-align:center;margin:0 auto 1rem">Monday, August 24th - Sunday, August 30th</h3> -->
	<p style="font-size:17px;line-height:24px;margin:0 0 16px"></p>
	<hr style="border:none;border-bottom:1px solid #ececec;margin:1.5rem 0;width:100%">
	<p style="font-size:17px;line-height:24px;margin:0 0 16px">
		A new membership site has been setup using your email address.
	</p>
	<p style="font-size:17px;line-height:24px;margin:0 0 16px">
		You can manage this membership site separately by logging in through the link below.
	</p>

	<p style="font-size:17px;line-height:24px;margin:0 0 16px">
		<b>Your Membership URL:</b><br>
		<a href="<?= "http://".$subdomain.".smartmember.com"?>"><?= "http://".$subdomain.".smartmember.com" ?></a>
	</p>
</div>

@stop