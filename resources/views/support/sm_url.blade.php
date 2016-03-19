<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>SM Site Info</title>
</head>
<body style="background-color:#f9f9f9;">
<div style="background:#f9f9f9;color:#373737;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:17px;line-height:24px;max-width:100%;width:100%!important;margin:0 auto;padding:0">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;line-height:24px;margin:0;padding:0;width:100%;font-size:17px;color:#373737;background:#f9f9f9">
        <tbody>
        <tr><td valign="top" style="border-collapse:collapse">
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse">
                    <tbody>
                    @if( !empty( $sm_url ) )
                    <tr>
                        <td valign="bottom" style="border-collapse:collapse;padding:20px 16px 12px;">
                            <div style="text-align:center">
                                ID: 
                                @if( !empty( $sm_id ) )
                                    <?= $sm_id ?>
                                @else
                                    <strong>None</strong>
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td valign="bottom" style="border-collapse:collapse;padding:20px 16px 12px;">
                            <div style="text-align:center">
                                Smart Member Url: <br /> <a href="<?= $sm_url ?>" target="_blank"><?= $sm_url ?></a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td valign="bottom" style="border-collapse:collapse;padding:20px 16px 12px;">
                            <div style="text-align:center">
                                Custom Url: <br />
                                @if( !empty( $custom_url ) )
                                    <a href="<?= $custom_url ?>" target="_blank"><?= $custom_url ?></a>
                                @else
                                    <strong>None</strong>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @else
                        <tr>
                            <td valign="bottom" style="border-collapse:collapse;padding:20px 16px 12px;">
                                <div style="text-align:center">
                                    No site found
                                </div>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
