
<table cellpadding="0" cellspacing="0" width="600" style="margin:0 auto">
	    <tbody>
	        <tr>
	            <td height="20">&nbsp;</td>
	        </tr>
	        <tr>
	            <td valign="top" align="left">
	                <table width="100%" cellspacing="0" cellpadding="0">
	                    <tbody>
	                        <tr>
	                            <td>
	                                <span style="font-family:Arial,Helvetica,sans-serif;font-weight:bold;font-size:16px"><span style="display:none!important">## </span> Conversation History <span style="display:none!important">## </span></span>
	                            </td>
	                            <td>
	                            	<span style="font-family:Arial,Helvetica,sans-serif;font-weight:bold;font-size:16px"><span style="display:none!important">## </span>  <span style="display:none!important">## </span></span>
	                            </td>
	                            <td>
	                                <span style="font-family:Arial,Helvetica,sans-serif;font-weight:bold;font-size:16px"><span style="display:none!important">## </span> <a href="<?=$ticket_link?>">See this ticket on our website</a> <span style="display:none!important">## </span></span>
	                            </td>
	                        </tr>
	                    </tbody>
	                </table>
	            </td>
	        </tr>
	        <tr>
	            <td height="10" style="font-size:5px;line-height:5px">&nbsp;</td>
	        </tr>
	        <tr>
	            <td bgcolor="#FFF" style="background:#fff" valign="top" align="middle">
	                <table cellpadding="0" cellspacing="0" width="100%" style="margin:0 auto">
	                    <tbody>
	                        <tr>
	                            <td>
	                                <table cellpadding="0" cellspacing="0" width="100%">
	                                    <tbody>
	                                        <tr>
	                                            <td height="12" bgcolor="#acb94c" style="background-color:#acb94c;font-size:12px;line-height:12px">&nbsp;</td>
	                                        </tr>
	                                        <tr>
	                                            <td height="20" style="font-size:20px;line-height:20px">&nbsp;</td>
	                                        </tr>
	                                        <tr>
	                                            <td>
	                                                <table cellpadding="0" cellspacing="0" width="100%">
	                                                    <tbody>
	                                                        <tr>
	                                                            <td width="20">&nbsp;</td>
	                                                            <td>
	                                                                <span><img align="right" src="<?= !empty($site_logo) ? $site_logo: 'https://imbmediab.s3.amazonaws.com/1/c30e4d803551c1f887999ceeb0504185/smart%20member%20logo%20no%20box%20black.png'?>" height="52" alt="" border="0" style="border:0;margin:0 0 0 15px;display:block;<?= !empty( $header_bg_color ) ? 'background-color: ' . $header_bg_color : '' ?>" class="CToWUd"></span>
	                                                                <span style="display:block;font-family:Arial,Helvetica,sans-serif;font-size:28px;line-height:1.35;color:#544d44;margin:10px 0 0;padding:0;font-weight:bold"><?= $ticket_subject ?></span>
	                                                            </td>
	                                                            <td width="20">&nbsp;</td>
	                                                        </tr>
	                                                    </tbody>
	                                                </table>
	                                            </td>
	                                        </tr>
	                                        <tr>
	                                            <td height="20" style="font-size:1px;line-height:1px">&nbsp;</td>
	                                        </tr>
	                                        <tr>
	                                            <td align="center">
	                                                <table cellpadding="0" cellspacing="0" width="100%">
	                                                    <tbody>
	                                                        <tr>
	                                                            <td rowspan="3" bgcolor="#fff" width="1"></td>
	                                                            <td bgcolor="#fff" height="1"></td>
	                                                            <td rowspan="3" bgcolor="#fff" width="1"></td>
	                                                        </tr>
	                                                        <tr>
	                                                            <td align="center">
	                                                                <table cellpadding="0" cellspacing="0" width="100%" style="font-family:Arial,Helvetica,sans-serif">
	                                                                    <tbody>
	                                                                        <tr>
	                                                                            <td height="1" bgcolor="#dddddd" style="font-size:1px;line-height:1px;background-color:#dddddd">&nbsp;</td>
	                                                                        </tr>
	                                                                        <tr>
	                                                                            <td height="1" bgcolor="#ffffff" style="font-size:1px;line-height:1px;background-color:#ffffff">&nbsp;</td>
	                                                                        </tr>
	                                                                        <tr style="background-color:#fafafa" bgcolor="#fafafa">
	                                                                            <td>
	                                                                            	@if($ticket->data)
	                                                                            	@foreach ($ticket->data as $reply)
	                                                                            	@if(!$reply->ticket_id)

	                                                                                <table cellpadding="0" cellspacing="0" width="100%">
	                                                                                    <tbody>
	                                                                                        <tr>
	                                                                                            <td colspan="4" height="12" style="font-size:12px;line-height:12px">&nbsp;</td>
	                                                                                        </tr>
	                                                                                        <tr>
	                                                                                            <td height="30" align="left" valign="top">
	                                                                                                <table cellpadding="0" cellspacing="0" align="left">
	                                                                                                    <tbody>
	                                                                                                        <tr>
	                                                                                                            <td width="20">&nbsp;</td>
	                                                                                                            <td width="25"><img src="<?= !empty($reply->user->profile_image) ? $reply->user->profile_image : 'https://ci5.googleusercontent.com/proxy/3zP-qePsnIvqP_dof5apkgR9HZiBbpdzS7WvteaNwQSeDM3h9cWf-sHcHaNly-fI-42UDhAbATJj7zjkeeQVy90rHIz44jrP7Vs5Pb1otjVfIwygsDGSAbCaquaP3lJkLoBvH063WbB6GhwX-wkPzziy=s0-d-e1-ft#https://livechat.s3.amazonaws.com/6630241/avatars/104364e20d8dd289b52434479b8cb0b6.undefined'?>" height="25" width="25" align="left" border="0" class="CToWUd"></td>
	                                                                                                            <td width="8">&nbsp;</td>
	                                                                                                            <td style="color:#bcbab8;font-size:12px">
	                                                                                                                <?=  $reply->user->first_name. ' ' .$reply->user->last_name?>
	                                                                                                            </td>
	                                                                                                        </tr>
	                                                                                                    </tbody>
	                                                                                                </table>
	                                                                                                <table cellpadding="0" cellspacing="0" align="right">
	                                                                                                    <tbody>
	                                                                                                        <tr>
	                                                                                                            <td width="20">&nbsp;</td>
	                                                                                                            <td style="color:#bcbab8;font-size:10px;line-height:25px">
	                                                                                                                <?= $reply->created_at?>
	                                                                                                            </td>
	                                                                                                            <td width="20"></td>
	                                                                                                        </tr>
	                                                                                                    </tbody>
	                                                                                                </table>
	                                                                                            </td>
	                                                                                        </tr>
	                                                                                        <tr>
	                                                                                            <td height="2" style="font-size:2px;line-height:2px">&nbsp;</td>
	                                                                                        </tr>
	                                                                                        <tr>
	                                                                                            <td>
	                                                                                                <table cellpadding="0" cellspacing="0">
	                                                                                                    <tbody>
	                                                                                                        <tr>
	                                                                                                            <td width="20">&nbsp;</td>
	                                                                                                            <td height="14" align="left" colspan="2" style="color:#544d44;font-size:14px;line-height:22px">
	                                                                                                                <?= $reply->message ?>
	                                                                                                            </td>
	                                                                                                            <td width="20"></td>
	                                                                                                        </tr>
	                                                                                                    </tbody>
	                                                                                                </table>
	                                                                                            </td>
	                                                                                        </tr>
	                                                                                        <tr>
	                                                                                            <td height="10" style="font-size:10px;line-height:10px">&nbsp;</td>
	                                                                                        </tr>
	                                                                                    </tbody>
	                                                                                </table>
	                                                                                @endif
	                                                                                @if($reply->modified_attribute && $reply->modified_attribute=='rating_requested')
	                                                                                <div class="message show_user avatar first divider ng-scope" style="position:relative;max-width:100%;margin-bottom:0;margin-top:0;margin-left: 0;margin-right: 0;padding-left: 3px;padding-right: 0;border-top: 1px solid #eee;padding-top: 5px;padding-bottom: 5px;border-bottom: 1px solid #eee;color: #756868;background-color: #FFF7F7;"><em style="max-width:90%;"><span><strong class="ng-scope"><?= $ticket->user_name?></strong><span class="ng-scope"> was asked to rate the customer service</span></span></em></div>
	                                                                                @endif
                                                                                    @if($reply->modified_attribute && $reply->modified_attribute=='3_day')
                                                                                        <div class="message show_user avatar first divider ng-scope" style="position:relative;max-width:100%;margin-bottom:0;margin-top:0;margin-left: 0;margin-right: 0;padding-left: 3px;padding-right: 0;border-top: 1px solid #eee;padding-top: 5px;padding-bottom: 5px;border-bottom: 1px solid #eee;color: #756868;background-color: #FFF7F7;"><em style="max-width:90%;"><span>Auto follow-up sent to <strong class="ng-scope"><?= $ticket->user_name?></strong><span class="ng-scope"> after 3 days without a reply</span></span></em></div>
                                                                                    @endif
                                                                                    @if($reply->modified_attribute && $reply->modified_attribute=='5_day')
                                                                                        <div class="message show_user avatar first divider ng-scope" style="position:relative;max-width:100%;margin-bottom:0;margin-top:0;margin-left: 0;margin-right: 0;padding-left: 3px;padding-right: 0;border-top: 1px solid #eee;padding-top: 5px;padding-bottom: 5px;border-bottom: 1px solid #eee;color: #756868;background-color: #FFF7F7;"><em style="max-width:90%;"><span>Notice of ticket being closed sent to <strong class="ng-scope"><?= $ticket->user_name?></strong><span class="ng-scope"> after 5 days without a reply</span></span></em></div>
                                                                                    @endif
	                                                                                @if($reply->modified_attribute && $reply->modified_attribute=='status')
                                                                                        @if( $reply->user )
	                                                                                        <div class="message show_user avatar first divider ng-scope" style="position:relative;max-width:100%;margin-bottom:0;margin-top:0;margin-left: 0;margin-right: 0;padding-left: 3px;padding-right: 0;border-top: 1px solid #eee;padding-top: 5px;padding-bottom: 5px;border-bottom: 1px solid #eee;color: #756868;background-color: #FFF7F7;"><em style="max-width:90%;"><span><strong class="ng-scope"><?= $reply->user->first_name . ' ' .$reply->user->last_name ?></strong><span class="ng-scope"> changed ticket status from <?= $reply->old_value ?> to </span><strong class="ng-scope"><?= $reply->new_value ?></strong></span></em></div>
                                                                                        @endif
                                                                                        @if( !$reply->user )
                                                                                            <div class="message show_user avatar first divider ng-scope" style="position:relative;max-width:100%;margin-bottom:0;margin-top:0;margin-left: 0;margin-right: 0;padding-left: 3px;padding-right: 0;border-top: 1px solid #eee;padding-top: 5px;padding-bottom: 5px;border-bottom: 1px solid #eee;color: #756868;background-color: #FFF7F7;"><em style="max-width:90%;"><span><span>Ticket status changed from <?= $reply->old_value ?> to </span><strong class="ng-scope"><?= $reply->new_value ?></strong></span></em></div>
                                                                                        @endif
	                                                                                @endif
	                                                                                @if($reply->modified_attribute && $reply->modified_attribute=='agent_id')
	                                                                                <div ng-if="reply.modified_attribute" class="message show_user avatar first divider ng-scope" style="position:relative;max-width:100%;margin-bottom:0;margin-top:0;margin-left: 0;margin-right: 0;padding-left: 3px;padding-right: 0;border-top: 1px solid #eee;padding-top: 5px;padding-bottom: 5px;border-bottom: 1px solid #eee;color: #756868;background-color: #FFF7F7;"><em style="max-width:90%;"><span><span class="ng-scope"><?= $reply->user->first_name . ' ' .$reply->user->last_name ?> assigned ticket to </span><strong class="ng-scope"><?= $reply->new_value ?></strong></span></em></div>
	                                                                                @endif
	                                                                                @endforeach 
	                                                                                @endif
	                                                                            </td>
	                                                                        </tr>
	                                                                        <tr>
	                                                                            <td height="1" bgcolor="#dddddd" style="font-size:1px;line-height:1px;background-color:#dddddd">&nbsp;</td>
	                                                                        </tr>
	                                                                        <tr>
	                                                                            <td height="1" bgcolor="#ffffff" style="font-size:1px;line-height:1px;background-color:#ffffff">&nbsp;</td>
	                                                                        </tr>
	                                                                        <tr style="background-color:#fafafa" bgcolor="#fafafa">
	                                                                            <td>
	                                                                                <table cellpadding="0" cellspacing="0" width="100%">
	                                                                                    <tbody>
	                                                                                        <tr>
	                                                                                            <td colspan="4" height="12" style="font-size:12px;line-height:12px">&nbsp;</td>
	                                                                                        </tr>
	                                                                                        <tr>
	                                                                                            <td height="30" align="left" valign="top">
	                                                                                                <table cellpadding="0" cellspacing="0" align="left">
	                                                                                                    <tbody>
	                                                                                                        <tr>
	                                                                                                            <td width="20">&nbsp;</td>
	                                                                                                            <td width="25"><img src="<?= !empty($ticket->user->profile_image) ? $ticket->user->profile_image :'https://ci5.googleusercontent.com/proxy/3zP-qePsnIvqP_dof5apkgR9HZiBbpdzS7WvteaNwQSeDM3h9cWf-sHcHaNly-fI-42UDhAbATJj7zjkeeQVy90rHIz44jrP7Vs5Pb1otjVfIwygsDGSAbCaquaP3lJkLoBvH063WbB6GhwX-wkPzziy=s0-d-e1-ft#https://livechat.s3.amazonaws.com/6630241/avatars/104364e20d8dd289b52434479b8cb0b6.undefined'?>" height="25" width="25" align="left" border="0" class="CToWUd"></td>
	                                                                                                            <td width="8">&nbsp;</td>
	                                                                                                            <td style="color:#bcbab8;font-size:12px">
	                                                                                                                <?= $ticket->user_name?>
	                                                                                                            </td>
	                                                                                                        </tr>
	                                                                                                    </tbody>
	                                                                                                </table>
	                                                                                                <table cellpadding="0" cellspacing="0" align="right">
	                                                                                                    <tbody>
	                                                                                                        <tr>
	                                                                                                            <td width="20">&nbsp;</td>
	                                                                                                            <td style="color:#bcbab8;font-size:10px;line-height:25px">
	                                                                                                                <?= $ticket->created_at ?>
	                                                                                                            </td>
	                                                                                                            <td width="20"></td>
	                                                                                                        </tr>
	                                                                                                    </tbody>
	                                                                                                </table>
	                                                                                            </td>
	                                                                                        </tr>
	                                                                                        <tr>
	                                                                                            <td height="2" style="font-size:2px;line-height:2px">&nbsp;</td>
	                                                                                        </tr>
	                                                                                        <tr>
	                                                                                            <td>
	                                                                                                <table cellpadding="0" cellspacing="0">
	                                                                                                    <tbody>
	                                                                                                        <tr>
	                                                                                                            <td width="20">&nbsp;</td>
	                                                                                                            <td height="14" align="left" colspan="2" style="color:#544d44;font-size:14px;line-height:22px">
	                                                                                                                <?= $ticket_message?>
	                                                                                                            </td>
	                                                                                                            <td width="20"></td>
	                                                                                                        </tr>
	                                                                                                    </tbody>
	                                                                                                </table>
	                                                                                            </td>
	                                                                                        </tr>
	                                                                                        <tr>
	                                                                                            <td height="10" style="font-size:10px;line-height:10px">&nbsp;</td>
	                                                                                        </tr>
	                                                                                    </tbody>
	                                                                                </table>
	                                                                            </td>
	                                                                        </tr>
	                                                                    </tbody>
	                                                                </table>
	                                                            </td>
	                                                        </tr>
	                                                        <tr>
	                                                            <td bgcolor="#fff" height="1" style="font-size:1px;line-height:1px"></td>
	                                                        </tr>
	                                                    </tbody>
	                                                </table>
	                                            </td>
	                                        </tr>
	                                        <tr>
	                                            <td height="20">
	                                            </td>
	                                        </tr>
	                                        <tr>
	                                            <td height="20">
	                                            </td>
	                                        </tr>
	                                    </tbody>
	                                </table>
	                            </td>
	                        </tr>
	                    </tbody>
	                </table>
	            </td>
	        </tr>
	        <tr>
	            <td>
	                <table cellpadding="0" cellspacing="0" width="600" bgcolor="#444444" style="background-color:#444444">
	                    <tbody>
	                        <tr height="12">
	                            <td colspan="5">
	                            </td>
	                        </tr>
	                        <tr>
	                            <td width="15"></td>
	                            <td colspan="2">
	                            </td>
	                            <td align="right" style="text-align:right">
	                                <span style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#ffffff;margin:0;padding:0">
	                                Powered by <a href="http://www.smartmember.com/" style="color:#ffffff" target="_blank">Smartmember</a>
	                                </span>
	                            </td>
	                            <td width="15"></td>
	                        </tr>
	                        <tr height="12">
	                            <td colspan="5">
	                            </td>
	                        </tr>
	                    </tbody>
	                </table>
	            </td>
	        </tr>
	        <tr>
	            <td height="20"></td>
	        </tr>
	    </tbody>
	</table>
