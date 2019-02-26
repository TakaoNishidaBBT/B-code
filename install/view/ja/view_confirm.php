<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
<head>
<meta charset="utf-8" />
<link rel="stylesheet" href="css/install.css" type="text/css" media="all" />
<title>B-codeのインストール</title>
</head>
<body>

	<form method="post" action="">

		<h1>B-codeのインストール</h1>

		<?php if($error_message) { ?>
			<div class="error">
				<fieldset>
					<legend>エラー</legend>
					<?php echo $error_message; ?>
				</fieldset>
			</div>
		<?php } ?>

		<h2>管理画面ベーシック認証</h2>

		<fieldset>
			<legend>管理画面ベーシック認証</legend>
			<?php echo $admin_basic_auth_form->getHtml('confirm'); ?>
		</fieldset>

		<h2>サイト管理ユーザ</h2>

		<fieldset>
			<legend>サイトの管理者</legend>
			<?php echo $admin_user_form->getHtml('confirm'); ?>
		</fieldset>

		<h2>htaccess</h2>

		<fieldset>
			<legend>htaccess</legend>
			<?php echo $root_htaccess->getHtml('confirm'); ?>
		</fieldset>

		<h2>インストール</h2>


		<fieldset>
			<legend>インストールによって作成されるファイル</legend>
			<ul>
				<li>インストールディレクトリ/.htaccess</li>
				<li>インストールディレクトリ/.htpassword</li>
				<li>インストールディレクトリ/user/users.php</li>
			</ul>
			<p><span class="caution">※</span>ファイルが存在する場合は上書きされます。</p>
		</fieldset>

		<p class="center">「インストール」ボタンをクリックすると、この内容でインストールを開始します。</p>

		<div class="confirm">
			<input name="action" value="install" type="hidden" />
			<input type="button" class="button" value="　戻　る　" onclick="location.href='index.php'"/>
			<input type="submit" class="button" value="インストール" />
		</div>

	</form>
</body>
</html>