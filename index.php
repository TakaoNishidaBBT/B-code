<?php
/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

	require_once('global/b_global_function.php');

	// Set directory information
	$doc_root = str_replace('\\' , '/', realpath($_SERVER['DOCUMENT_ROOT']));
	if(substr($doc_root, -1) != '/') $doc_root.= '/';

	$current_dir = dirname(str_replace('\\' , '/', __DIR__));
	if(substr($current_dir, -1) != '/') $current_dir.= '/';

	$current_path = str_replace(strtolower($doc_root), '', strtolower($current_dir));
	if(substr($current_path, 1) != '/') $current_path = '/' . $current_path;

	$current = str_replace('.', '-', basename($current_dir));

	// Start admin session
	$ses = new B_Session;
	$ses->start('nocache', 'bcode-admin-session', $current_path);

	require_once('./config/config.php');
$log = new B_Log(B_LOG_FILE);
	// Check logedin
	$auth = new B_AdminAuth;
	$ret = $auth->getUserInfo($user_id, $user_name, $user_auth, $language);
	if($ret && $_REQUEST['url']) {
		$file = B_Util::pathinfo($_REQUEST['url']);

		// To get real url
		$url = $_SERVER['REQUEST_URI'];
		$url = preg_replace('"^' . B_CURRENT_ROOT . '"', '', $url);
		$url = preg_replace('/\?.*/', '', $url);
		$url = urldecode($url);
		$_REQUEST['url'] = $url;
		$thumbnail = B_UPLOAD_THUMBDIR . str_replace('/', '-', $url);
$log->write('$thumbnail', $thumbnail);
		if(file_exists($thumbnail)) {
			switch($file['extension']) {
			case 'avi':
			case 'flv':
			case 'mov':
			case 'mp4':
			case 'mpg':
			case 'mpeg':
			case 'wmv':
				header('Content-Type: image/jpg');
				break;
			default:
				header('Content-Type: image/' . strtolower($file['extension']));
				break;
			}
			readfile($thumbnail);
			exit;
		}
		else {
			header('HTTP/1.1 404 Not Found');
			exit;
		}
	}

	// Set TERMINAL_ID
	if($_REQUEST['terminal_id']) {
		define('TERMINAL_ID', $_REQUEST['terminal_id']);
	}
	else {
		define('TERMINAL_ID', __getRandomText(12));
	}

	// Set DISPATCH_URL
	define('DISPATCH_URL', 'index.php?terminal_id=' . TERMINAL_ID);

	require_once('./controller/controller.php');
