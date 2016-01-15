@extends('email.layout')

@section('main-content')

<div style="max-width:600px;margin:0 auto">
	<p style="font-size:17px;line-height:24px;margin:0 0 16px;text-align:center">
	<h2 style="color:#3a3b3c;line-height:30px;margin-bottom:12px;margin:0 auto 0.75rem;font-size:1.8rem;text-align:center">
		Your Account Verification Code
	</h2>
	<p style="font-size:14px;line-height:26px;margin:0 0 25px;text-align:center;">
		If you requested a verification code for <br><strong><?= $email ?></strong><br> copy the code below. <br>Otherwise, please ignore this message.
	<hr style="border:none;border-bottom:1px solid #ececec;margin:0;width:100%">

	<div style="text-align:center;margin:0;">
		<table cellpadding="0" cellspacing="0"
			   style="border-collapse:collapse;border-radius:4px;padding:14px 32px;display:inline-block">
			<tbody>
			<tr>
				<td style="border-collapse:collapse">
					<div>
                        <strong style="text-decoration:underline;color: #008000;font-size:32px;"><?= $verification_code ?></strong>
                    </div>
                    <div style="margin-top: 20px;">
                        <strong>Verification Code</strong>
                    </div>
                    <small>*Note that this code is only valid for fifteen minutes.</small>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>

@stop