<?php
/*
 * B-code : Online Editor
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	$lang = LANG;
	if($_SESSION['language']) {
		$lang = $_SESSION['language'];
	}

	mb_language('uni');

	// Language file
	switch($lang) {
	case 'ja':
		define('B_MB_DETECT_ORDER', 'UTF-8, EUC-JP, SJIS');
		mb_detect_order(B_MB_DETECT_ORDER);
		break;

	case 'zh-cn':
		define('B_MB_DETECT_ORDER', 'UTF-8, GB18030, EUC-CN');
		mb_detect_order(B_MB_DETECT_ORDER);
		break;

	default:
		define('B_MB_DETECT_ORDER', 'UTF-8, EUC-JP, SJIS, GB18030, EUC-CN');
		break;
	}

	require_once(B_LNGUAGE_DIR . 'lang/ja.php');
	require_once(B_LNGUAGE_DIR . 'lang/zh-cn.php');
