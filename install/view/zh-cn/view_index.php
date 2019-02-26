<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
<head>
<meta charset="utf-8" />
<link rel="stylesheet" href="css/install.css" type="text/css" media="all" />
<link rel="stylesheet" href="../css/selectbox.css" type="text/css" media="all" />
<script src="../js/bframe.js" type="text/javascript"></script>
<script src="../js/bframe_context_menu.js" type="text/javascript"></script>
<script src="../js/bframe_popup.js" type="text/javascript"></script>
<script src="../js/bframe_selectbox.js" type="text/javascript"></script>
<title>安装 B-code</title>
</head>
<body>

	<h1>安装 B-code</h1>

	<?php
		if($error_message) {
			echo '<p class="error-message-top">' . $error_message . '</p>';
		}
	?>

	<form method="post" action=".">
		<div id="select-language">
			<?php echo $select_language->getHtml(); ?>
			<input name="action" value="select-language" type="hidden" />
		</div>
	</form>

	<form method="post" action=".">

		<p>管理员信息。</p>

		<h2>管理页面 Basic 认证</h2>

		<p>设置管理页面 Basic 认证。这是防止未经授权的访问所必需的。</p>
		<fieldset>
			<legend>管理页面 Basic 认证</legend>
			<?php echo $admin_basic_auth_form->getHtml(); ?>
		</fieldset>

		<h2>网站管理员</h2>

		<p>请输入网站管理员信息。</p>
		<fieldset>
			<legend>网站管理员</legend>
			<?php echo $admin_user_form->getHtml(); ?>
		</fieldset>

		<h2>htaccess</h2>

		<p>安装 B-studio 时,根目录所生成的 htaccess 文本内容</p>
		<fieldset>
			<legend>htaccess</legend>
			<?php echo $root_htaccess->getHtml(); ?>
		</fieldset>

		<h2>确认安装文件权限</h2>
			<?php echo $perm_message; ?>

		<h2>确认安装内容</h2>

		<div class="confirm">
			<input name="action" value="confirm" type="hidden" />
			<input type="submit" class="button" value="　确　认　" />
		</div>

	</form>
</body>
</html>