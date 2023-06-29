<?php
/*
 * B-code : Online Editor
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	// global function

	function __getRandomText($length) {
		$base = 'abcdefghijkmnprstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ2345678';
		for($i=0; $i<$length; $i++) {
			$pwd.= $base{mt_rand(0, strlen($base)-1)};
		}
		return $pwd;
	}

	function __($text, $lang=null) {
		if($_SESSION['language'] == 'en' || $lang == 'en') return $text;

		global $texts;

		if(!$lang) $lang = $_SESSION['language'];

		return $texts[$lang][$text] ? $texts[$lang][$text] : $text;
	}

	function __project($url) {
		$current_dir = dirname(str_replace('\\', '/', __DIR__));

		$dir_array = explode('/', $url);

		$handle = opendir($current_dir);
		while(false !== ($file_name = readdir($handle))){
			if($file_name == '.' || $file_name == '..') continue;

			if($dir_array[0] == $file_name) return;
		}
		return $dir_array[0];
	}

	function __getPath() {
		return preg_replace('/(?<!:)\/+/', '/', implode('/', array_filter(array_map('trim', func_get_args()), 'strlen')));
	}

	// class auto loader
	spl_autoload_register(function($class_name) {
		$admin_dir = dirname(str_replace('\\' , '/', __DIR__));

		$file_path = $admin_dir . '/class/' . $class_name . '.php';
		if(file_exists($file_path)) {
			require_once($file_path);
		}
	});
