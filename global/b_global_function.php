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
		$path = str_replace('\\', '/', func_get_arg(0));

		for($i=1; $i<func_num_args(); $i++) {
			if(!$file_name = func_get_arg($i)) continue;

			if(substr($path, -1) == '/') {
				$path = substr($path, 0, -1);
			}
			if(substr($file_name, 0, 1) == '/') {
				$file_name = substr($file_name, 1);
			}

			$path.= '/' . $file_name;
		}

		return $path;
	}

	// class auto loader
	spl_autoload_register(function($class_name) {
		$admin_dir = dirname(str_replace('\\' , '/', __DIR__));

		$file_path = $admin_dir . '/class/' . $class_name . '.php';
		if(file_exists($file_path)) {
			require_once($file_path);
		}
	});
