<?php
/*
 * B-code : Online Editor
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

	require_once('global/b_global_function.php');

	// Set Directory Information
	$doc_root = str_replace('\\' , '/', realpath($_SERVER['DOCUMENT_ROOT']));
	if(substr($doc_root, -1) != '/') $doc_root.= '/';

	$current_dir = dirname(str_replace('\\' , '/', __DIR__));
	if(substr($current_dir, -1) != '/') $current_dir.= '/';

	$current_path = str_replace(strtolower($doc_root), '', strtolower($current_dir));
	if(substr($current_path, 1) != '/') $current_path = '/' . $current_path;

	$current = str_replace('.', '-', basename($current_dir));

	// Start Admin Session
	$ses = new B_Session;
	$ses->start('nocache', 'bcode-admin-session', $current_path);

	require_once('./config/config.php');

	// Check Logedin
	$auth = new B_AdminAuth;
	$login = $auth->getUserInfo($user_id, $user_name, $user_auth, $language);
	if($login && $_REQUEST['url']) {
		// To Get Real URL
		$url = $_SERVER['REQUEST_URI'];
		$url = preg_replace('"^' . B_CURRENT_ROOT . '"', '', $url);
		$url = preg_replace('/\?.*/', '', $url);
		$url = urldecode($url);
		$_REQUEST['url'] = $url;

		$file = B_Util::pathinfo($_REQUEST['url']);

		if($file['dirname'] && $file['basename']) {
			$dir_array = explode('/', $file['dirname']);
			$folder = $dir_array[0];
			array_shift($dir_array);
			$thumb_dir = B_THUMBDIR . $folder . '/';
			$file['dirname'] = implode('/', $dir_array);
			if($file['dirname']) {
				$url = B_Util::getPath($file['dirname'], $file['basename']);
			}
			else {
				$url = $file['basename'];
			}

			$thumbnail = $thumb_dir . str_replace('/', '-', $url);

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
		}
	}

	switch(strtolower($file['extension'])) {
	case 'swf':
	case 'jpg':
	case 'jpeg':
	case 'gif':
	case 'png':
		header('HTTP/1.1 404 Not Found');
		exit;
	}

	$project = __project($_REQUEST['url']);
	if($project) {
		$_REQUEST['project'] = $project;
	}

	// Set TERMINAL_ID
	if($_REQUEST['terminal_id']) {
		define('TERMINAL_ID', $_REQUEST['terminal_id']);
	}
	else {
		if($project) {
			define('TERMINAL_ID', $project);
		}
		else {
			define('TERMINAL_ID', 'FIXEDTERMINAL_ID');
		}
	}

	define('DISPATCH_URL', 'index.php?terminal_id=' . TERMINAL_ID);

	require_once('./controller/controller.php');
