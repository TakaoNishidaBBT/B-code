<?php
/*
 * B-code : Online Editor
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	class editor_upload extends B_AdminModule {
		function __construct() {
			parent::__construct(__FILE__);

			$this->dir = $this->session['project']['doc_root'];
			$this->project = $this->session['project']['name'];
			$this->project_dir = $this->session['project']['project_dir'];

			define('B_UPLOAD_THUMBDIR', B_THUMBDIR . $this->project . '/');
		}

		function confirm() {
			$status = true;

			try {
	 			// Check file size
				$filesize = $_POST['filesize'];
				$post_max_size = $this->util->decode_human_filesize(ini_get('post_max_size'));
				$upload_max_filesize = $this->util->decode_human_filesize(ini_get('upload_max_filesize'));
				if($filesize > $post_max_size || $filesize > $upload_max_filesize) {
					if($post_max_size < $upload_max_filesize) {
						$limit = ini_get('post_max_size');
					}
					else {
						$limit = ini_get('upload_max_filesize');
					}
					$message = __('The file size is too large. The maximun file upload size is %LIMIT%');
					$message = str_replace('%LIMIT%', $limit, $message);
					throw new Exception($message);
				}

	 			// Check file name
				$file = B_Util::pathinfo($_POST['filename']);
				if(strlen($file['basename']) != mb_strlen($file['basename'])) {
					throw new Exception(__('Multi-byte characters cannot be used'));
				}
				if(preg_match('/[\\\\:\/\*\?<>\|\s]/', $file['basename'])) {
					throw new Exception(__('The following characters cannot be used in file or folder names (\ / : * ? " < > | space)'));
				}

				if($file['extension'] == 'zip') {
					switch($this->request['extract_mode']) {
					case 'confirm':
						$response_mode = 'zipConfirm';
						$message = __('Extract %FILE_NAME% ?');
						$message = str_replace('%FILE_NAME%', $file['basename'], $message);
						break;

					case 'noextract':
						$fullpath = __getPath($this->dir, $this->request['node_id'], $file['basename']);
						if(file_exists($fullpath) && $this->request['mode'] == 'confirm') {
							$response_mode = 'confirm';
							$message = __('%FILE_NAME% already exists.<br />Are you sure you want to overwrite?');
							$message = str_replace('%FILE_NAME%', $file['basename'], $message);
						}
						break;
					}
				}
				else {
					$fullpath = __getPath($this->dir, $this->request['node_id'], $file['basename']);
					if($this->request['mode'] == 'confirm' && file_exists($fullpath)) {
						$response_mode = 'confirm';
						$message = __('%FILE_NAME% already exists.<br />Are you sure you want to overwrite?');
						$message = str_replace('%FILE_NAME%', $file['basename'], $message);
					}
				}
			}
			catch(Exception $e) {
				$status = false;
				$message = $e->getMessage();
			}

			$response['status'] = $status;
			$response['mode'] = $response_mode;
			$response['message'] = $message;

			header('Content-Type: application/x-javascript charset=utf-8');
			echo json_encode($response);
			exit;
		}

		function upload() {
			$status = true;

			try {
				// Get file info
				$file = B_Util::pathinfo($_FILES['Filedata']['name']);

				if(strtolower($file['extension']) == 'zip' && class_exists('ZipArchive') && $this->request['extract_mode'] == 'extract') {
					// Set time limit to 3 minutes
					set_time_limit(180);

					// Continue whether a client disconnect or not
					ignore_user_abort(true);

					$zip_file = B_ADMIN_FILES_DIR . $file['basename'];
					$status = move_uploaded_file($_FILES['Filedata']['tmp_name'], $zip_file);

					// Check Contents of zip file
					$this->checkZipFile($zip_file);

					if($status) {
						usleep(300000);

						// Send progress
						header('Content-Type: application/octet-stream');
						header('Transfer-encoding: chunked');
						flush();
						ob_flush();

						// Send start message
						$response['status'] = 'extracting';
						$response['progress'] = 0;
						$this->sendChunk(json_encode($response));

						$zip = new ZipArchive();
						$zip->open($zip_file);
						$zip->extractTo(B_FILE_EXTRACT_DIR);
						$zip->close();
						unlink($zip_file);

						// Controll extracted files
						$node = new B_FileNode(B_FILE_EXTRACT_DIR, '/', null, null, 'all');

						// except file or folder
						$this->except = array_flip(array('__MACOSX', '.DS_Store', '._' . $file['file_name']));

						// Count extract files
						$this->extracted_files = $node->nodeCount(true, $this->except);
						$this->registerd_files = 0;
						$this->progress = 0;

						// register extract files
						$node->walk($this, 'register_archive');
						$response['status'] = 'extracting';
						$response['progress'] = 100;
						$this->sendChunk(',' . json_encode($response));

						usleep(300000);

						// remove all extract files
						$node->remove();

						$response['status'] = 'creating';
						$response['progress'] = 0;
						$this->sendChunk(',' . json_encode($response));

						usleep(300000);

						$node = new B_FileNode($this->dir, $this->request['node_id'], null, null, 'all');
						$this->createTumbnail_files = 0;
						$this->progress = 0;
						$node->createthumbnail($this->except, array('obj' => $this, 'method' => 'createThumbnail_callback'));

						foreach($this->registered_archive_node as $path) {
							$node = new B_FileNode($this->dir, __getPath($this->request['node_id'], $path), null, null, 0);
							$response['node_info'][] = $node->getNodeList('', '', $this->request['node_id']);
						}

						$response['status'] = $status;
						$response['progress'] = 100;
						$this->sendChunk(',' . json_encode($response));
						$this->sendChunk();	// terminate
						exit;
					}
				}
				else {
					$path = __getPath($this->dir, $this->request['node_id'], $file['basename']);
					$status = move_uploaded_file($_FILES['Filedata']['tmp_name'], $path);
					if($status) {
						chmod($path, 0777);
						$node = new B_FileNode($this->dir, __getPath($this->request['node_id'], $file['basename']), null, null, 1);
						$node->removethumbnail();
						$node->createthumbnail();
						$response['node_info'][] = $node->getNodeList('', '', $this->request['node_id']);
					}
				}
				if(!$status) {
					switch($_FILES['Filedata']['error']) {
					case 1:
						$this->error_message = __('The uploaded file exceeds the upload_max_filesize directive in php.ini.');
						break;

					case 2:
						$this->error_message = __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.');
						break;

					case 3:
						$this->error_message = __('The uploaded file was only partially uploaded.');
						break;

					case 4:
						$this->error_message = __('No file was uploaded.');
						break;

					case 6:
						$this->error_message = __('Missing a temporary folder. Introduced in PHP 5.0.3.');
						break;

					case 7:
						$this->error_message = __('Failed to write file to disk. Introduced in PHP 5.1.0.');
						break;

					case 8:
						$this->error_message = __('A PHP extension stopped the file upload.');
						break;

					default:
						$this->error_message = 'move_uploaded_file error';
						break;
					}

					$this->log->write($this->error_message);
					throw new Exception($this->error_message);
				}
			}
			catch(Exception $e) {
				$status = false;
				$message = $e->getMessage();
			}

			$response['status'] = $status;
			$response['message'] = $message;

			header('Content-Type: application/x-javascript charset=utf-8');
			echo json_encode($response);
			exit;
		}

		function checkZipFile($zip_file) {
			$zip = new ZipArchive();
			$zip->open($zip_file);

			for($i=0; $i < $zip->numFiles; $i++) {
				$stat = $zip->statIndex($i);
				$file_name = mb_convert_encoding($stat['name'], 'UTF-8', B_MB_DETECT_ORDER);
				if(strlen($file_name) != mb_strlen($file_name)) {
					$zip->close();
					unlink($zip_file);
					throw new Exception(__('Multi-byte characters cannot be used in file names. Please check contents of the zip file.'));
				}
			}

			$zip->close();
		}

		function register_archive($node) {
			if(!$node->parent ) return true;

			// except file or folder (stop walking)
			if(array_key_exists($node->file_name, $this->except)) return false;

			if($node->level == 1) {
				$this->registered_archive_node[] = $node->path;
			}

			$dest = __getPath($this->dir, $this->request['node_id'], $node->path);
			if(is_dir($node->fullpath)) {
 				if(!file_exists($dest)) {
					mkdir($dest);
				}
				return true;
			}
			else {
				copy($node->fullpath, __getPath($this->dir, $this->request['node_id'], $node->path));
			}

			$this->registerd_files++;
			$response['status'] = 'extracting';
			$response['progress'] = round($this->registerd_files / $this->extracted_files * 100);
			if($this->progress != $response['progress']) {
				$this->sendChunk(',' . json_encode($response));
				$this->progress = $response['progress'];
			}

			return true;
		}

		function createThumbnail_callback($node) {
			$this->createTumbnail_files++;
			$response['status'] = 'creating';
			$response['progress'] = round($this->createTumbnail_files / $this->extracted_files * 100);
			if($this->progress != $response['progress']) {
				$this->sendChunk(',' . json_encode($response));
				$this->progress = $response['progress'];
			}
		}

		function sendChunk($response='') {
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
