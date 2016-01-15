<div style="background:#f9f9f9;color:#373737;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:17px;line-height:24px;max-width:100%;width:100%!important;margin:0 auto;padding:0">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;line-height:24px;margin:0;padding:0;width:100%;font-size:17px;color:#373737;background:#f9f9f9">
		<tbody>
		   <tr><td valign="top" style="border-collapse:collapse">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse">
					<tbody>
						<tr><td valign="bottom" style="border-collapse:collapse;padding:20px 16px 12px;<?= !empty( $header_bg_color ) ? 'background-color: ' . $header_bg_color : '' ?>">
							<div style="text-align:center">
								<a href="http://<?= $subdomain?>.smartmember.com" style="color:#439fe0;font-weight:bold;text-decoration:none;word-break:break-word" target="_blank">
									<img src="<?= !empty($site_logo) ? $site_logo: 'https://imbmediab.s3.amazonaws.com/1/c30e4d803551c1f887999ceeb0504185/smart%20member%20logo%20no%20box%20black.png' ?>" style="outline:none;text-decoration:none;border:none;width:120px;min-height:36px">
								</a>
							</div>
						</td></tr>
					</tbody>
				</table>
			</td></tr>
			<tr><td valign="top" style="border-collapse:collapse">
				<table cellpadding="32" cellspacing="0" border="0" align="center" style="border-collapse:collapse;background:white;border-radius:0.5rem;margin-bottom:1rem">
					<tbody>
						<tr><td width="546" valign="top" style="border-collapse:collapse">
							 @yield('main-content')	
						</td></tr>
					</tbody>
				</table>
			</td></tr>
		</tbody>
	</table>
</div>
				