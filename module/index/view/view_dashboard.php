<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="terminal_id" content="<?php echo TERMINAL_ID ?>">
<meta name="source_module" content="index">
<meta name="source_page" content="index">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="stylesheet" href="css/common.css">
<link rel="stylesheet" href="css/header.css">
<link rel="stylesheet" href="css/dashboard.css">
<script src="js/bstudio.js"></script>
<script src="js/identicon/sha256.js"></script>
<script src="js/identicon/identicon.js"></script>
<title><?php echo $this->title ?></title></head>
<body>
	<div id="title-header">
		<div class="title"><img src="images/common/bcode-logo.png" alt="B-code" /></div>
		<ul class="login-user">
			<li id="small-identicon"></li>
			<li id="user-name"><?php echo $this->user_name ?></li>
			<li id="user-settings"><a href="<?php echo DISPATCH_URL ?>&amp;module=siteadmin&amp;page=form" target="main"><img src="images/common/gear_white.png" alt="user settings" /></a></li>
			<li id="user-id" style="display:none"><?php echo $this->user_id ?></li>
			<li id="logout"><a href="<?php echo DISPATCH_URL ?>&amp;module=index&amp;page=logout" target="_top"><?php echo __('Log out'); ?><img class="logout" src="images/common/logout.png" alt="logout" /></a></li>
		</ul>
	</div>
	<div class="container">
		<div id="left-container">
			<div class="user">
				<span id="big-identicon"></span>
				<span class="user-name"><?php echo $this->user_name ?></span>
			</div>
			<div class="logo">
				<img src="images/common/b-code.png" alt="B-code" />
			</div>
			<?php if($this->menu) echo $this->menu->gethtml(); ?>
		</div>
		<iframe id="main" name="main" class="bframe_adjustwindow" src="<?php echo $this->initial_page; ?>"></iframe>
	</div>
	<script type="text/javascript">
		bstudio.identicon();
	</script>
</body>
