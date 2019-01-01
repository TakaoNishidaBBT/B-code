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

		function createThumbnailCacheFile() {
			if(file_exists(B_FILE_INFO_THUMB_SEMAPHORE)) return;

			// open semaphore for lock
			if(!$fp_semaphore = fopen(B_FILE_INFO_THUMB_SEMAPHORE, 'x')) return;

			// create thumb-nails
			$node = new B_FileNode(B_FILE_ROOT_DIR, 'root', null, null, 'all');
			$this->total_create_nodes = $node->nodeCount(); 

			if(20 < $this->total_create_nodes) {
				// send progress
				header('Content-Type: application/octet-stream');
				header('Transfer-encoding: chunked');
				flush();
				ob_flush();

				// Send start message
				$response['status'] = 'show';
				$response['progress'] = 0;
				$response['message'] = 'creating thumbnails';
				$progress = 0;
				$this->sendChunk(json_encode($response));

				$this->show_progress = true;

				// set time limit to 5 minutes
				set_time_limit(300);

				// continue whether a client disconnect or not
				ignore_user_abort(true);
			}

			// remove all Thumbnail Cache Files
			$this->removeThumbnailCacheFile();

			if($this->show_progress) $callback = array('obj' => $this, 'method' => '_createThumbnail_callback');
			$index = 0;
			$node->createThumbnail($data, $index, null, $callback);

			// write serialized data into cache file
			$fp = fopen(B_FILE_INFO_THUMB, 'w');
			fwrite($fp, serialize($data));
			fclose($fp);

			// close and unlock semaphore
			fclose($fp_semaphore);
			unlink(B_FILE_INFO_THUMB_SEMAPHORE);

			if($this->show_progress) {
				$response['status'] = 'finished';
				$response['progress'] = 100;
				$this->sendChunk(',' . json_encode($response));
				$this->sendChunk();	// terminate
				return true;
			}
		}

		function _createThumbnail_callback($file_node) {
			$this->create_nodes++;

			$response['status'] = 'progress';
			$response['progress'] = round($this->create_nodes / $this->total_create_nodes * 100);
			if($this->progress != $response['progress']) {
				$this->sendChunk(',' . json_encode($response));
				$this->progress = $response['progress'];
			}
		}

		function removeThumbnailCacheFile() {
			if($handle = opendir(B_UPLOAD_THUMBDIR)) {
				while(false !== ($file_name = readdir($handle))){
					if($file_name == '.' || $file_name == '..') continue;
					unlink(B_UPLOAD_THUMBDIR . $file_name);
				}
				closedir($handle);
			}
		}
/*
		function getImgHTML($img_path, $max_width, $max_height) {
			if(!$img_path) return;
			if(!file_exists($img_path)) return;

			$image_size = getimagesize($img_path);

			if($image_size[0] > $max_width) {
				if(($image_size[0] / $max_width) > ($image_size[1] / $max_height)) {
					$width = $max_width;
					$height = $image_size[1] * $width / $image_size[0];
				}
				else {
					$height = $max_height;
					$width = $image_size[0] * $height / $image_size[1];
				}
			}
			else if($image_size[1] > $max_height) {
				$height = $max_height;
				$width = $image_size[0] * $height / $image_size[1];
			}
			else {
				$width = $image_size[0];
				$height = $image_size[1];
			}

			$html = '<img src="%IMG_URL%" width="%WIDTH%" height="%HEIGHT%" alt="" />';
			$html = str_replace('%IMG_URL%', B_FILE_ROOT_URL . $img_path, $html);
			$html = str_replace('%WIDTH%', $width, $html);
			$html = str_replace('%HEIGHT%', $height, $html);

			return $html;
		}
*/
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
