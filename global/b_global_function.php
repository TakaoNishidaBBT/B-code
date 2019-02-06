<?php
/*
 * B-square : Event Management and Registration System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
*/
	// global function

	function __getRandomText($length) {
		$base = 'abcdefghijkmnprstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ2345678';
		for($i=0; $i<$length; $i++) {
			$pwd.= $base{mt_rand(0, strlen($base)-1)};
		}
		return $pwd;
	}

	function __($text) {
		if($_SESSION['language'] == 'en') return $text;

		global $texts;

		return $texts[$text] ? $texts[$text] : $text;
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

	// class auto loader
	spl_autoload_register(function($class_name) {
		$admin_dir = dirname(str_replace('\\' , '/', __DIR__));

		$file_path = $admin_dir . '/class/' . $class_name . '.php';
		if(file_exists($file_path)) {
			require_once($file_path);
		}
	});
