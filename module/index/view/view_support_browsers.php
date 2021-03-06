<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="images/common/favicon.ico">
<link rel="apple-touch-icon-precomposed" href="images/common/favicon.png" />
<link rel="stylesheet" href="css/common.css">
<link rel="stylesheet" href="css/login.css">
<title><?php echo $this->title ?></title></head>
<body>
	<div id="title-header"><h1><?php echo $this->site_title; ?></h1></div>

	<div class="support">
		<p>
			<?php echo __('Your browser is not supported. <br />Please use one of the browsers listed below.'); ?>
		</p>
		<table class="browsers">
			<tr>
				<td class="icon"><img src="images/browsers/safari.png" alt="Safari" /></td>
				<td class="icon"><img src="images/browsers/firefox.png" alt="Firefox" /></td>
				<td class="icon"><img src="images/browsers/chrome.png" alt="Chrome" /></td>
				<td class="icon"><img src="images/browsers/opera.png" alt="Opera" /></td>
			</tr>
			<tr>
				<td class="name">Safari</td>
				<td class="name">Firefox</td>
				<td class="name">Chrome</td>
				<td class="name">Opera</td>
			</tr>
		</table>
	</div>
</body>
