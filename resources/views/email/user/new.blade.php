@extends('email.layout')

@section('main-content')

<div style="max-width:600px;margin:0 auto">
	<p style="font-size:17px;line-height:24px;margin:0 0 16px;text-align:center">
	<h2 style="color:#3a3b3c;line-height:30px;margin-bottom:12px;margin:0 auto 0.75rem;font-size:1.8rem;text-align:center">
		Welcome to <?= $subdomain ?>
	</h2>
	<!-- <h3 style="color:#3a3b3c;line-height:26px;margin-bottom:2rem;font-size:1.2rem;text-align:center;margin:0 auto 1rem">Monday, August 24th - Sunday, August 30th</h3> -->
	<p style="font-size:17px;line-height:24px;margin:0 0 16px"></p>
	<p style="font-size:17px;line-height:24px;margin:0 0 16px">
		Click the following link for email verification:
	<hr style="border:none;border-bottom:1px solid #ececec;margin:1.5rem 0;width:100%">
	<p style="font-size:17px;line-height:24px;margin:0 0 16px">
		<a href="<?= $verify_url ?>" style="color:#439fe0;font-weight:bold;text-decoration:none;word-break:break-word" target="_blank">Verify</a>
	</p>
	</p>
</div>

@stop