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

	// Global Function
	require_once('../global/b_global_function.php');

	// Start Session
	define('SESSION_DIR', dirname($_SERVER['SCRIPT_NAME']));

	$ses = new B_Session;
	$ses->start('nocache', 'bc-install', SESSION_DIR);

	// Set $_SESSION['language']
	define('LANG', 'en');
	if(!$_SESSION['language']) $_SESSION['language'] = LANG;

	if($_POST['action'] == 'select-language' && function_exists('mb_internal_encoding')) {
		$_SESSION['language'] = $_POST['language'];
	}

	// Config
	require_once('../config/config.php');

	// Confirm timezone
	date('Ymd');

	require_once('config/_form_config.php');
	$select_language = new B_Element($select_language_config);
	$admin_basic_auth_form = new B_Element($admin_basic_auth_config);
	$admin_user_form = new B_Element($admin_user_form_config);
	$root_htaccess = new B_Element($root_htaccess_config);

	if($_POST['action'] == 'confirm') {
		$_SESSION['install_confirm'] = true;

		if(confirm($admin_basic_auth_form, $admin_user_form, $root_htaccess, $perm_message, $error_message)) {
			$_SESSION['install_index_status'] = true;
			$admin_basic_auth_form->getValue($_SESSION['param']);
			$admin_user_form->getValue($_SESSION['param']);
			$root_htaccess->getValue($_SESSION['param']);
			$path = 'confirm.php';
			header("Location:$path");
			exit;
		}
	}
	else if($_SESSION['install_confirm']) {
		back($admin_basic_auth_form, $admin_user_form, $root_htaccess, $perm_message);
	}
	else {
		setHtaccess($root_htaccess);
		confirmPermission($perm_message);
	}

	$_SESSION['install_index_status'] = false;

	// Set value to language selectbox
	if(function_exists('mb_internal_encoding')) {
		$select_language->setValue(array('language' => $_SESSION['language']));
	}
	else {
		$error_message = __('Please enable mbstring module');
	}
	if(!session_save_path()) {
		$error_message = __('Please set session.save_path');
	}
	if(!function_exists('gd_info')) {
		$error_message = __('Please enable GD library');
	}
	if(!function_exists('exif_read_data')) {
		$error_message = __('Please enable exif library');
	}
	if(!function_exists('simplexml_load_file')) {
		$error_message = __('Please enable SimpleXML library');
	}
	if(!class_exists('ZipArchive')) {
		$error_message = __('ZipArchive is necessary');
	}

	// Send HTTP header
	header('Cache-Control: no-cache, must-revalidate'); 
	header('Content-Language: ' . $_SESSION['language']);
	header('Content-Type: text/html; charset=UTF-8');

	// Show HTML
	$view_folder = getViewFolder();
	include('./view/' . $view_folder . 'view_index.php');
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

	function setHtaccess($root_htaccess) {
		// Set up htaccess
		$contents = file_get_contents('./config/_htaccess.txt');
		$contents = str_replace('%REWRITE_BASE%', B_CURRENT_ROOT, $contents);

		// Set up htaccess basic authentication
		$current_dir = dirname(str_replace('\\' , '/', __DIR__));
		if(substr($current_dir, -1) != '/') $current_dir.= '/';

		$contents = str_replace('%AUTH_USER_FILE%', $current_dir . '.htpassword', $contents);

		$param['htaccess'].= $contents;
		$root_htaccess->setValue($param);
	}

	function confirmPermission(&$message) {
		$status  = checkWritePermission(B_CURRENT_DIR . '.htaccess', $message);
		$status &= checkWritePermission(B_CURRENT_DIR . '.htpassword', $message);
		$status &= checkWritePermission(B_CURRENT_DIR . 'config/lang_config.php', $message);
		$status &= checkWritePermission(B_CURRENT_DIR . 'download', $message);
		$status &= checkWritePermission(B_CURRENT_DIR . 'log', $message);
		$status &= checkWritePermission(B_CURRENT_DIR . 'user/users.php', $message);
		$status &= checkWritePermission(B_CURRENT_DIR . 'bs-admin-files', $message);
		$status &= checkExecutePermission(FFMPEG, $message);
		return $status;
	}

	function checkWritePermission($path, &$message) {
		if(!file_exists($path)) {
			$message.= '<span class="status_ok">' . $path  . __(' write permission granted. ') .  '(file not exist)</span><br />';
			return true;
		}
		else {
			$perms = fileperms($path);
			if(is_writable($path)) {
				$message.= '<span class="status_ok">' . $path  . __(' : write permission granted. ') . '(permission:' . substr(sprintf('%o',$perms), -3) . ')</span><br />';
				return true;
			}
			else {
				$message.= '<span class="status_ng">' . $path  . __(' : write permission not set. ') . '(permission:' . substr(sprintf('%o',$perms), -3) . ')</span><br />';
				return false;
			}
		}
	}

	function checkExecutePermission($path, &$message) {
		if(!file_exists($path)) {
			$message.= '<span class="status_ng">' . $path  .  '(file not exist)</span><br />';
			return false;
		}
		else {
			$perms = fileperms($path);
			if(is_executable($path)) {
				$message.= '<span class="status_ok">' . $path  . __(' : execute permission granted. ') . '(permission:' . substr(sprintf('%o',$perms), -3) . ')</span><br />';
				return true;
			}
			else {
				$message.= '<span class="status_ng">' . $path  . __(' : execute permission not set. ') . '(permission:' . substr(sprintf('%o',$perms), -3) . ')</span><br />';
				return false;
			}
		}
	}

	function confirm($admin_basic_auth_form, $admin_user_form, $root_htaccess, &$perm_message, &$error_message) {
		// Confirm directory permission
		$status = confirmPermission($perm_message);

		// Set value from $_POST
		$admin_basic_auth_form->setValue($_POST);
		$admin_user_form->setValue($_POST);
		$root_htaccess->setValue($_POST);
		$status&= $admin_basic_auth_form->validate();
		$status&= $admin_user_form->validate();
		$status&= $root_htaccess->validate();

		return $status;
	}

	function back($admin_basic_auth_form, $admin_user_form, $root_htaccess, &$perm_message) {
		$admin_basic_auth_form->setValue($_SESSION['param']);
		$admin_user_form->setValue($_SESSION['param']);
		$root_htaccess->setValue($_SESSION['param']);
		confirmPermission($perm_message);
	}

	function exception_error_handler($errno, $errstr, $errfile, $errline ) {
		if(!(error_reporting() & $errno)) {
			// Error_reporting, unexpected error has occurred
			return;
		}

		throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
