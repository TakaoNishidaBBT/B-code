<?php
/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	// System Name
	define('B_SYSTEM_NAME', 'B-code');

	// Charset
	define('B_CHARSET', 'UTF-8');

	// Charset for Header
	define('B_CHARSET_HEADER', 'UTF-8');

	// Charset for XML Header
	define('B_CHARSET_XML_HEADER', 'UTF-8');

	//HTTP HOST
	define('B_HTTP_HOST', $_SERVER['SERVER_NAME']);

	//Document Root Directory
	$doc_root = str_replace('\\' , '/', realpath($_SERVER['DOCUMENT_ROOT']));
	if(substr($doc_root, -1) == '/') $doc_root = substr($doc_root, 0, -1);

	define('B_DOC_ROOT', $doc_root);

	$current_dir = dirname(str_replace('\\' , '/', __DIR__));
	$current_path = str_replace(strtolower(B_DOC_ROOT), '', strtolower($current_dir));
	$site_name = basename($current_path);

	// Site Name
	define('B_SITE_NAME', $site_name);

	// Current Root Directory (Top Directory of This System)
	define('B_CURRENT_ROOT', $current_path . '/');
	define('B_CURRENT_DIR', B_DOC_ROOT . B_CURRENT_ROOT);

	// Admin Session Name
	define('B_ADMIN_SESSION_NAME', 'bcode-admin-session');

	// URL of Top Page
	define('B_SITE_ROOT', 'http://' . B_HTTP_HOST . B_CURRENT_ROOT);
	define('B_SITE_ROOT_SSL', 'https://' . B_HTTP_HOST . B_CURRENT_ROOT);

	// Site Base Directory (for Base Tag)
	if(empty($_SERVER['HTTPS']) === true || $_SERVER['HTTPS'] !== 'on') {
		define('B_SITE_BASE', B_SITE_ROOT);
	}
	else {
		define('B_SITE_BASE', B_SITE_ROOT_SSL);
	}

	// Log Files
	// Access Log File
	define('B_ACCESS_LOG_FILE', B_CURRENT_DIR . 'log/access.log');

	// Log File
	define('B_LOG_FILE', B_CURRENT_DIR . 'log/log.txt');

	// Language Files Directory
	define('B_LNGUAGE_DIR', B_CURRENT_DIR . 'language/');

	// Download Directory
	define('B_DOWNLOAD_DIR', B_CURRENT_DIR . 'download/');

	// User File Directory for Resource
	define('B_ADMIN_FILES', 'bs-admin-files/');
	define('B_ADMIN_FILES_DIR', B_DOC_ROOT . B_CURRENT_ROOT . B_ADMIN_FILES);
	define('B_RESOURCE_WORK_DIR', B_ADMIN_FILES_DIR);
	define('B_RESOURCE_EXTRACT_DIR', B_ADMIN_FILES_DIR . 'extract/');

	// Thunbnail
	define('B_THUMB_PREFIX', 'thumb_');
	define('B_THUMB_MAX_SIZE', '100');

	// File Information
	define('B_CACHE_DIR', B_CURRENT_DIR . 'cache/');
	define('B_FILE_INFO_THUMB', B_CACHE_DIR . 'file_info_thumb.txt');
	define('B_FILE_INFO_THUMB_SEMAPHORE', B_CACHE_DIR . 'file_info_thumb_semaphore.txt');

	// ffmpeg
	if(substr(PHP_OS, 0, 3) === 'WIN') {
		define('FFMPEG', B_ADMIN_DIR . 'class/ffmpeg/ffmpeg_for_windows.exe');
	}
	else if(substr(PHP_OS, 0, 5) === 'Linux') {
		define('FFMPEG', B_ADMIN_DIR . 'class/ffmpeg/ffmpeg_for_linux');
	}
	else {
		define('FFMPEG', B_ADMIN_DIR . 'class/ffmpeg/ffmpeg_for_mac');
	}

	// Test Server Settings
	if(preg_match('/www.test-server.com/', B_HTTP_HOST)) {
		define('B_TITLE_PREFIX', __('(Test)'));
		define('B_ARCHIVE_LOG_MODE', 'DEBUG');
	}
	else if(preg_match('/localhost/', B_HTTP_HOST)) {
		define('B_TITLE_PREFIX', __('(Test)'));
		define('B_ARCHIVE_LOG_MODE', 'DEBUG');
	}
	else {
		define('B_TITLE_PREFIX', '');
		define('B_ARCHIVE_LOG_MODE', '');
	}

	// Debaug Mode
	define('B_DEBUG_MODE', 'OFF');

	// Language config
	require_once(B_CURRENT_DIR . 'config/lang_config.php');
	require_once(B_CURRENT_DIR . 'language/language.php');

	// Globa Data File
	$g_data_set = 'b_global_data';
	require_once(B_CURRENT_DIR . 'global/' . $g_data_set . '.php');

	// Built-in User File
	require_once(B_CURRENT_DIR . 'user/users.php');

	// Edit Root Directory
	define('B_FILE_ROOT', 'target/');
	define('B_FILE_ROOT_DIR', B_DOC_ROOT . '/' . B_FILE_ROOT);
	define('B_UPLOAD_THUMBDIR', B_ADMIN_FILES_DIR . 'thumbs/');
	define('B_FILE_ROOT_URL', B_FILE_ROOT);


	// PHP Display Errors
	ini_set('display_errors','Off');
	ini_set('log_errors','On');
	ini_set('error_log', B_CURRENT_DIR . 'log/system.log');
