@extends('email.layout')

@section('main-content')

<p style="font-size:18px;line-height:24px;margin:0 0 16px;"><strong>Congratulations &amp; Welcome to Smart Member!</strong></p>

<p style="font-size:18px;line-height:24px;margin:0 0 16px;">This email contains several important details about your membership, so make sure to bookmark this and whitelist the following email address so you don’t miss anything important about your account!</p>

<p style="font-size:18px;line-height:24px;margin:0 0 16px;">Whitelist this email address:<br>
<a href="mailto:support@smartmember.com">support@smartmember.com</a></p>

<p style="font-size:18px;line-height:24px;margin:0 0 16px;"><i>(If you use gmail, hit the star button)</i></p>

<p style="font-size:18px;line-height:24px;margin:0 0 16px;">Here is what you will find in this email.</p>
<ol>
	<li>Your Login Details</li>
	<li>Important Links</li>
	<li>Customer Referral Program</li>
	<li>Personal Message from the Founder</li>
</ol>
&nbsp;

<hr />

<h2><span style="color: #ff0000;"><b>Your Login Details</b></span></h2>
<strong>Account Status:</strong>  Member
<?= !empty( $user_password ) ? "<p style=\"font-size:18px;line-height:24px;margin:0 0 16px;\"><strong>Login Using the Credentials Below:</strong><br><strong>E-mail: </strong> $user_email<br><strong>Password: </strong> $user_password&nbsp;</p>" : "    <p style=\"font-size:18px;line-height:24px;margin:0 0 16px;\"><strong>Login Using your existing password for:</strong><br><strong>E-mail: </strong> $user_email<p style=\"font-size:18px;line-height:24px;margin:0 0 16px;\"><strong>If you have forgotten your password you can reset it using this link:</strong><br><a href=\"$reset_url\">$reset_url</a>" ?><br>Sign in using this Url: <a href="<?=$login_url ?>"><?= $login_url ?></a>
&nbsp;

<hr />

<h2><span style="color: #ff0000;"><b>Important Links</b></span></h2>
<ul>
	<li><a href="http://training.smartmember.com/support">Customer Support</a></li>
	<li><a href="http://training.smartmember.com">Tutorial Videos</a></li>
	<li><a href="http://my.smartmember.com">Account Settings</a></li>
	<li><a href="mailto:support@smartmember.com">Feature Requests</a></li>
	<li><a href="mailto:support@smartmember.com">Report Bugs</a></li>
</ul>
&nbsp;

<hr />

<h2><span style="color: #ff0000;"><b>Customer Referral Program</b></span></h2>
<p style="font-size:18px;line-height:24px;margin:0 0 16px;">Do you have friend or associate that might be interested in Smart Member?</p>

<p style="font-size:18px;line-height:24px;margin:0 0 16px;">As a customer, you can earn 50% commissions for every referral you make. You simply sign up below to get your customer referral link. Then you introduce friends or associates to this awesome software! It’s free to join, so sign up below to get started!</p>

<p style="font-size:18px;line-height:24px;margin:0 0 16px;"><b><a href="http://smartmember.com/jv/">Click Here to Join the Customer Referral Program.</a></b></p>

&nbsp;

<hr />

<h2><span style="color: #ff0000;"><b>Personal Message from the Founder</b></span></h2>
<p style="font-size:18px;line-height:24px;margin:0 0 16px;">We are 100% dedicated to making Smart Member a long term software that hundreds of thousands of entrepreneurs around the world use on a daily basis.</p>

<p style="font-size:18px;line-height:24px;margin:0 0 16px;">In order to reach that goal, it’s vital that we support our members and build out the features that they want, as well as fix the bugs that they report.</p>

<p style="font-size:18px;line-height:24px;margin:0 0 16px;"><span style="text-decoration: underline;">You can count on us to do exactly that!</span></p>

<p style="font-size:18px;line-height:24px;margin:0 0 16px;">Every day, every week, every month, we will continue to improve Smart Member by adding amazing new features and listening to the feedback we receive from members.</p>

<p style="font-size:18px;line-height:24px;margin:0 0 16px;">If you feel like something is missing, or something should be added, or we should integrate with one of your favorite 3rd party services, all you need to do is fill out our <a href="http://training.smartmember.com/support">Feature Request Form</a>, and our team will research the idea and implement it as fast as possible!</p>

<p style="font-size:18px;line-height:24px;margin:0 0 16px;">If you find bugs <i>(which will obviously be there in the early days)</i>, please report them to us so that we have the chance to fix them as soon as we can.  The sooner we get past the bug fixing phase, the sooner we can start rolling out new features, so with your help we can get there as fast as possible!</p>

<p style="font-size:18px;line-height:24px;margin:0 0 16px;">Our goal is to have the MOST 3rd party integrations of any membership platform on the planet.  We want to implement with every popular email marketing platform, along with every popular affiliate marketing platform, and finally, every popular merchant account platform.</p>

<p style="font-size:18px;line-height:24px;margin:0 0 16px;">In order to reach this goal, we would love to hear from YOU about which platforms you feel are most important for us to implement first.  You can help us shape the direction of Smart Member, and together we can grow this software into a life-changing tool for everyone!</p>

<p style="font-size:18px;line-height:24px;margin:0 0 16px;">Finally, I want you to know that we are here for the long term.  As with any startup, there will be some bumps along the way in the early phases.  I hope that you share a vision with us, and that you hang in there with us long term as we grow this company and do our best to serve and deliver value to YOU!</p>

<p style="font-size:18px;line-height:24px;margin:0 0 16px;">We truly believe in this software and what it can do for entrepreneurs to drive the marketplace, and we are dedicated to making it the best membership platform on the planet!</p>

<p style="font-size:18px;line-height:24px;margin:0 0 16px;">Thank you for your support and for being a loyal Smart Member!</p>
<p style="padding-left: 30px;"><b><b>- Chris Record, Founder of SmartMember™</b></b></p>
<p style="padding-left: 30px;"></p>

@stop