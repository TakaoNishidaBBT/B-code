<?php
/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	class B_AdminModule extends B_Module {
		function __construct($file_path) {
			parent::__construct($file_path);

			$auth = new B_AdminAuth;
			$auth->getUserInfo($this->user_id, $this->user_name, $this->user_auth, $this->language);

			// HTML header
			require_once(B_CURRENT_DIR . 'module/common/config/html_header_config.php');
			$this->createHtmlHeader($html_header_config);

			require_once(B_CURRENT_DIR . 'module/common/config/pager_config.php');
			$this->pager_config = $pager_config;

			// bframe_message config
			require_once(B_CURRENT_DIR . 'module/common/config/bframe_message_config.php');
			$this->bframe_message_config = $bframe_message_config;
		}

		function sendChunk($response=null) {
			if($response) {
				$response = $response . str_repeat(' ', 8000);
				echo sprintf("%x\r\n", strlen($response));
				echo $response . "\r\n";
			}
			else {
				echo "0\r\n\r\n";
			}
			flush();
			ob_flush();
		}
	}
