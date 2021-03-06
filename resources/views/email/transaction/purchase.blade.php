@extends('email.layout')

@section('main-content')

    <h2 style="color:#2ab27b;line-height:30px;margin-bottom:12px;margin:0 0 12px">Welcome to <?= $site_name ?>!</h2>

    <p style="font-size:18px;line-height:24px;margin:0 0 16px;">
        You've been granted access to <?= !empty($access_level) ? "<strong>$access_level</strong>" : ""?>.</p>

    @if( !empty( $cbreceipt) )
        <p style="font-size:20px;line-height:26px;margin:0 0 16px">
            <strong>Ready to login?</strong> Click the button below to get started!
        </p>

        <hr style="border:none;border-bottom:1px solid #ececec;margin:1.5rem 0;width:100%">
        <div style="text-align:center;margin:2rem 0">
            <table cellpadding="0" cellspacing="0"
                   style="border-collapse:collapse;background:#2ab27b;border-bottom:2px solid #1f8b5f;border-radius:4px;padding:14px 32px;display:inline-block">
                <tbody>
                <tr>
                    <td style="border-collapse:collapse">
                        <a href="<?= $cb_link; ?>"
                           style="color:white;font-weight:normal;text-decoration:none;word-break:break-word;display:inline-block;letter-spacing:1px;font-size:20px;line-height:26px"
                           align="center" target="_blank">
                            Click here to access your product
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <p style="font-size:12px;line-height:20px;margin:0 auto 1rem;color:#aaa;text-align:center;max-width:100%;word-break:break-word;margin-bottom:2rem">
            You may copy/paste this link into your browser:<br><a href="<?= $cb_link; ?>"
                    style="color:#439fe0;font-weight:bold;text-decoration:none;word-break:break-word" target="_blank">
                <?= $cb_link; ?>
            </a>
        </p>
    @else
        <p style="font-size:20px;line-height:26px;margin:0 0 16px">
            <strong>Ready to login?</strong> Below you'll find your login details and a link to get started.
        </p>

        <hr style="border:none;border-bottom:1px solid #ececec;margin:1.5rem 0;width:100%">
    <p style="font-size:17px;line-height:24px;margin:0 0 16px">Your Login Details</p>
    <ul style="font-size:17px;line-height:24px;margin:0 0 16px;margin-bottom:1.5rem;list-style:none;padding-left:1rem">
        <?= !empty($user_password) ? "<li><strong>E-mail:</strong> $user_email</li><li><strong>Password:</strong> $user_password</li>" : "<li><strong>E-mail:</strong> $user_email</li><li><strong>Password:</strong> use your existing password</li><li>&nbsp;</li><li>forgot your password? Click here:<br><a href=\"$reset_url\">$reset_url</a></li>" ?>
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