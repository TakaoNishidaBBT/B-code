<?php
/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	class editor_editor extends B_AdminModule {
		function __construct() {
			parent::__construct(__FILE__);

			$this->dir = B_FILE_ROOT_DIR;

			require_once('./config/editor_config.php');
			$this->editor = new B_Element($editor_config);
		}

		function open() {
			if($this->request['node_id']) {
				$file_path = B_Util::getPath($this->dir , $this->request['node_id']);
				if(!file_exists(mb_convert_encoding($file_path, B_SYSTEM_FILENAME_ENCODE, 'utf8'))) {
					$this->view_file = './view/view_not_found.php';
					return;
				}
				$info = pathinfo($file_path);
				$update_datetime = filemtime($file_path);
				$obj = $this->editor->getElementByName('contents');

				$contents = file_get_contents(mb_convert_encoding($file_path, B_SYSTEM_FILENAME_ENCODE, 'utf8'));
				if($contents) {
					$encoding = mb_detect_encoding($contents);
					$obj->value = mb_convert_encoding($contents, 'UTF-8', B_MB_DETECT_ORDER);
				}

				switch(strtolower($info['extension'])) {
				case 'js':
					$obj->attr = str_replace('%SYNTAX%', 'data-syntax="javascript"', $obj->attr);
					break;

				case 'css':
					$obj->attr = str_replace('%SYNTAX%', 'data-syntax="css"', $obj->attr);
					break;

				default:
					$obj->attr = str_replace('%SYNTAX%', '', $obj->attr);
					break;
				}

				$obj = $this->editor->getElementByName('node_id');
				if($obj) $obj->value = $this->request['node_id'];

				$obj = $this->editor->getElementByName('file_path');
				if($obj) $obj->value = $file_path;

				$obj = $this->editor->getElementByName('extension');
				if($obj) $obj->value = $info['extension'];

				$obj = $this->editor->getElementByName('update_datetime');
				if($obj) $obj->value = $update_datetime;

				$this->setTitle($info['basename']);

				$this->view_file = './view/view_editor.php';
			}
		}

		function refresh() {
			$status = true;
			$file_path = B_Util::getPath($this->dir , $this->post['node_id']);
			$file_path = mb_convert_encoding($file_path, B_SYSTEM_FILENAME_ENCODE, 'utf8');
			$contents = file_get_contents($file_path);
			if($contents) {
				$encoding = mb_detect_encoding($contents);
				$contents = mb_convert_encoding($contents, 'UTF-8', B_MB_DETECT_ORDER);
			}

			$response['status'] = $status;
			$response['mode'] = $mode;
			$response['message'] = 'refreshed';
			$response['values'] = array(
				'contents' => $contents,
				'update_datetime' => filemtime($file_path)
			);

			header('Content-Type: application/x-javascript charset=utf-8');
			echo json_encode($response);
			exit;
		}

		function register() {
			$status = true;
			$file_path = B_Util::getPath($this->dir , $this->post['node_id']);

			if(!file_exists($file_path)) {
				$status = false;
				$mode = 'alert';
				$message = __("Error. \nNo such file or directory");
			}
			else if($this->post['mode'] == 'confirm' && filemtime($file_path) > $this->post['update_datetime']) {
				$mode = 'confirm';
				$message = __("Another user has updated this file\nAre you sure you want to overwrite?");
			}
			else {
				if($this->post['encoding'] == 'ASCII' || $this->post['encoding'] == 'UTF-8') {
					$contents = $this->post['contents'];
				}
				else {
					$contents = mb_convert_encoding($this->post['contents'], $this->post['encoding'], 'UTF-8');
				}
				file_put_contents($file_path, $contents, LOCK_EX);

				$message = __('Saved');
			}

			$response['status'] = $status;
			$response['mode'] = $mode;
			$response['message_obj'] = 'message';
			$response['message'] = $message;
			if($mode != 'confirm') {
				$response['values'] = array('update_datetime' => time());
			}

			header('Content-Type: application/x-javascript charset=utf-8');
			echo json_encode($response);
			exit;
		}

		function view() {
			// Start buffering
			ob_start();

			require_once($this->view_file);

			// Get buffer
			$contents = ob_get_clean();

			// Send HTTP header
			$this->sendHttpHeader();

			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/editor.css">');
			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/texteditor.css">');
			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/selectbox.css">');
			$this->html_header->appendProperty('script', '<script src="js/bframe_edit_check.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/bframe_texteditor.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/bframe_selectbox.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/ace/ace.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/ace/ext-split.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/ace/ext-language_tools.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/ace/theme-twilight.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/ace/mode-html.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/ace/mode-css.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/ace/mode-php.js"></script>');

			// Show HTML header
			$this->showHtmlHeader();

			// Show HTML body
			echo $contents;
		}
	}
