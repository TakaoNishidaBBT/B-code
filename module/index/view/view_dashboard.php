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
		<div class="login-user">
			<ul>
				<li>
					<dl class="login-user">
						<dt id="small-identicon"></dt>
						<dd id="user-name"><?php echo $this->user_name ?></dd>
						<dd id="user-id" style="display:none"><?php echo $this->user_id ?></dd>
					</dl>
				</li>
				<li><a href="<?php echo DISPATCH_URL ?>&amp;module=index&amp;page=logout" target="_top" ><?php echo __('Log out'); ?></a></li>
			</ul>
		</div>
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
