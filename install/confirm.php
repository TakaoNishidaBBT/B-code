<?php
/*
 * B-code : Online Editor
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_STRICT);
	ini_set('display_errors', 'On');
	set_error_handler('exception_error_handler');

	// Charset
	define('B_CHARSET', 'UTF-8');
	mb_internal_encoding(B_CHARSET);

	// Global function
	require_once('../global/b_global_function.php');

	// Start session
	define('SESSION_DIR', dirname($_SERVER['SCRIPT_NAME']));

	$ses = new B_Session;
	$ses->start('nocache', 'bc-install', SESSION_DIR);

	if(!$_SESSION['install_index_status']) {
		$path = '.';
		header("Location:$path");
		exit;
	}

	// Form config
	require_once('config/_form_config.php');

	$admin_basic_auth_form = new B_Element($admin_basic_auth_config);
	$admin_user_form = new B_Element($admin_user_form_config);
	$root_htaccess = new B_Element($root_htaccess_config);

	$admin_basic_auth_form->setValue($_SESSION['param']);
	$admin_user_form->setValue($_SESSION['param']);
	$root_htaccess->setValue($_SESSION['param']);

	if($_POST['action'] == 'install') {
		$status = install($admin_basic_auth_form, $admin_user_form, $root_htaccess, $error_message);
		if($status) {
			$_SESSION['install_complete'] = true;
			$path = 'complete.php';
			header("Location:$path");
			exit;
		}
	}

	// Send HTTP header
	header('Cache-Control: no-cache, must-revalidate'); 
	header('Content-Language: ' . $_SESSION['language']);
	header('Content-Type: text/html; charset=UTF-8');

	// Show HTML
	$view_folder = getViewFolder();
	include('./view/' . $view_folder . 'view_confirm.php');
	exit;

	function getViewFolder() {
		switch($_SESSION['language']) {
		case 'ja':
			return 'ja/';

		case 'zh-cn':
			return 'zh-cn/';

		default:
			return;
		}
	}

	function install($admin_basic_auth_form, $admin_user_form, $root_htaccess, &$error_message) {
		$admin_basic_auth_form->getValue($param);
		$admin_user_form->getValue($param);

		try {
			// Set time limit to 3 minutes
			set_time_limit(180);

			// Set up htaccess
			$obj = $root_htaccess->getElementByName('htaccess');
			file_put_contents('../.htaccess', $obj->value);

			// Set up password
			$password = $param['basic_auth_id'] . ':' . htpasswd($param['basic_auth_pwd']);
			file_put_contents('../.htpassword', $password);

			// Set up built-in admin user file
			$contents = file_get_contents('./config/_users.php');
			$contents = str_replace('%USER_NAME%',  $param['admin_user_name'], $contents);
			$contents = str_replace('%USER_ID%',  $param['admin_user_id'], $contents);
			$contents = str_replace('%PASSWORD%', md5($param['admin_user_pwd']), $contents);
			$contents = str_replace('%LANGUAGE%', $_SESSION['language'], $contents);
			file_put_contents('../user/users.php', $contents);
		}
		catch(Exception $e) {
			$error_message = '<p class="error-message">' . $e->getMessage() . '</p>';
			return false;
		}

		return true;
	}

	function htpasswd($plainpasswd) {
		$salt = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);
		$len = strlen($plainpasswd);
		$text = $plainpasswd . '$apr1$' . $salt;
		$bin = pack('H32', md5($plainpasswd . $salt . $plainpasswd));

		for($i=$len; $i>0; $i-=16) {
			$text.= substr($bin, 0, min(16, $i));
		}
		for($i=$len; $i>0; $i>>=1) {
			$text.= ($i & 1) ? chr(0) : $plainpasswd{0};
		}

		$bin = pack('H32', md5($text));

		for($i=0; $i<1000; $i++) {
			$new = ($i&1) ? $plainpasswd : $bin;
			if($i%3) $new.= $salt;
			if($i%7) $new.= $plainpasswd;
			$new.= ($i&1) ? $bin : $plainpasswd;
			$bin = pack('H32', md5($new));
		}

		for($i=0; $i<5; $i++) {
			$k = $i+6;
			$j = $i+12;
			if($j == 16) $j = 5;
			$tmp = $bin[$i] . $bin[$k] . $bin[$j] . $tmp;
		}

		$tmp = chr(0).chr(0).$bin[11].$tmp;
		$tmp = strtr(strrev(substr(base64_encode($tmp), 2)),
		'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/',
		'./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');

		return '$apr1$' . $salt . '$' . $tmp;
	}

	function exception_error_handler($errno, $errstr, $errfile, $errline) {
		if(!(error_reporting() & $errno)) {
			// error_reporting, unexpected error has occurred
			return;
		}

		throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
