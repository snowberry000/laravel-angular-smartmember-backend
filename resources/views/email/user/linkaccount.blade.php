@extends('email.layout')

@section('main-content')

<div style="max-width:600px;margin:0 auto">
	<p style="font-size:17px;line-height:24px;margin:0 0 16px;text-align:center">
	<h2 style="color:#3a3b3c;line-height:30px;margin-bottom:12px;margin:0 auto 0.75rem;font-size:1.8rem;text-align:center">
		We received a request to link this email with your account.
	</h2>
	<p style="font-size:20px;line-height:26px;margin:0 0 25px">
		If you requested to link <?= $email ?>, click the button below. If you didn't make the request please ignore this email.
	<hr style="border:none;border-bottom:1px solid #ececec;margin:1.5rem 0;width:100%">

	<div style="text-align:center;margin:2rem 0">
		<table cellpadding="0" cellspacing="0"
			   style="border-collapse:collapse;background:#2ab27b;border-bottom:2px solid #1f8b5f;border-radius:4px;padding:14px 32px;display:inline-block">
			<tbody>
			<tr>
				<td style="border-collapse:collapse">
					<a href="<?= $verify_link; ?>"
					   style="color:white;font-weight:normal;text-decoration:none;word-break:break-word;display:inline-block;letter-spacing:1px;font-size:20px;line-height:26px"
					   align="center" target="_blank">
						Click here to link the accounts
					</a>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>

@stop