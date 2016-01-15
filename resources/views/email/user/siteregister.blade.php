@extends('email.layout')

@section('main-content')

	<h2 style="color:#2ab27b;line-height:30px;margin-bottom:12px;margin:0 0 12px">You're in!</h2>

	<p style="font-size:18px;line-height:24px;margin:0 0 16px;">
		You're now a member at <strong><?= $site_name ?></strong> - welcome!</p>

	<p style="font-size:20px;line-height:26px;margin:0 0 16px">
		<strong>Ready to login?</strong> Below you'll find your login details and a link to get started.
	</p>

	<hr style="border:none;border-bottom:1px solid #ececec;margin:1.5rem 0;width:100%">
    @if( !empty( $cbreceipt) )
        <div style="text-align:center;margin:2rem 0">
            <table cellpadding="0" cellspacing="0"
                   style="border-collapse:collapse;background:#2ab27b;border-bottom:2px solid #1f8b5f;border-radius:4px;padding:14px 32px;display:inline-block">
                <tbody>
                <tr>
                    <td style="border-collapse:collapse">
                        <a href="<?= $site_url; ?>?cbreceipt=<?= $cbreceipt; ?>"
                           style="color:white;font-weight:normal;text-decoration:none;word-break:break-word;display:inline-block;letter-spacing:1px;font-size:20px;line-height:26px"
                           align="center" target="_blank">
                            Click here to set up your account
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    @else
	<p style="font-size:17px;line-height:24px;margin:0 0 16px">Your Login Details</p>
	<ul style="font-size:17px;line-height:24px;margin:0 0 16px;margin-bottom:1.5rem;list-style:none;padding-left:1rem">
		<?= !empty($user_password) ? "<li><strong>E-mail:</strong> $user_email</li><li><strong>Password:</strong> $user_password</li>" : "<li><strong>E-mail:</strong> $user_email</li><li><strong>Password:</strong> use your existing password</li><li>forgot your password? Click here:<br><a href=\"$reset_url\">$reset_url</a></li>" ?>

            @if($smartmember_member)
                <li>&nbsp;</li>
                <li>If you need help getting started, checkout the video tutorials at <a href="http://help.smartmember.com">http://help.smartmember.com</a></li>
                <li>&nbsp;</li>
                <li>If you have an existing Smart Member account that you'd like to combine, or add alternative emails to - do so at <a href="http://my.smartmember.com/my/account/settings">http://my.smartmember.com/my/account/settings</a></li>
            @endif
	</ul>
	<hr style="border:none;border-bottom:1px solid #ececec;margin:1.5rem 0;width:100%">

	<div style="text-align:center;margin:2rem 0">
		<table cellpadding="0" cellspacing="0"
			   style="border-collapse:collapse;background:#2ab27b;border-bottom:2px solid #1f8b5f;border-radius:4px;padding:14px 32px;display:inline-block">
			<tbody>
			<tr>
				<td style="border-collapse:collapse">
					<a href="<?= $login_url; ?>"
					   style="color:white;font-weight:normal;text-decoration:none;word-break:break-word;display:inline-block;letter-spacing:1px;font-size:20px;line-height:26px"
					   align="center" target="_blank">
						Click here to sign in to <?= $site_name ?>
					</a>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
    @endif

	<p style="padding-left: 30px;"></p>

@stop