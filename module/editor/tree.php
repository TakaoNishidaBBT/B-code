<?php
/*
 * B-code : Online Editor
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	class editor_tree extends B_AdminModule {
		function __construct() {
			parent::__construct(__FILE__);

			$this->dir = $this->session['project']['doc_root'];
			$this->directory = $this->session['project']['directory'];
			$this->scheme = $this->session['project']['scheme'];
			$this->domain = $this->session['project']['domain'];
			$this->project = $this->session['project']['name'];
			$this->project_dir = $this->session['project']['project_dir'];
			$this->storage = B_TREE_STORAGE_PREIX . $this->project;

			define('B_UPLOAD_THUMBDIR', B_THUMBDIR . $this->project . '/');

			require_once('./config/tree_config.php');
			$this->tree_config = $tree_config;
			$this->tree = new B_FileNode($this->dir, '');

			$this->tree->setConfig($this->tree_config);

			if(!$this->session['sort_order']) $this->session['sort_order'] = 'asc';
			if(!$this->session['sort_key']) $this->session['sort_key'] = 'file_name';

			$this->status = true;

			// project directory does not exist.
			if($this->session['project'] && !file_exists($this->directory)) {
				$this->setView('viewError');
			}
		}

		function open() {
			$this->session['mode'] = 'open';
			$this->session['current_node'] = '';

			$this->setProject($this->request['project']);

			if(!$this->checkUserAuth()) {
				$this->redirect();
			}
		}

		function checkUserAuth() {
			$users = explode('/', $this->session['project']['user']);
			if($this->user_auth == 'super_admin' || $this->user_auth == 'admin' || array_search($this->user_id, $users) !== false) {
				return true;
			}
		}

		function setProject($project) {
			$this->df = new B_DataFile(B_PROJECT_DATA, 'project');
			$this->session['project'] = $this->df->select('name', $project);
			if($this->session['project']['doc_root'] == $this->session['project']['directory']) {
				$this->session['project']['doc_root'] = dirname($this->session['project']['doc_root']);
				$this->session['project']['project_dir'] = basename($this->session['project']['directory']);
			}
			else {
				$this->session['project']['project_dir'] = str_replace($this->session['project']['doc_root'], '', $this->session['project']['directory']);
			}
			if(substr($this->session['project']['project_dir'], 0, 1) == '/') $this->session['project']['project_dir'] = substr($this->session['project']['project_dir'], 1);
			if(!$this->session['project']['project_dir']) $this->session['project']['project_dir'] = 'root';
		}

		function select() {
			$this->session['selected_node'][] = $this->request['node_id'];
			$this->response($this->session['selected_node'], 'select');

			exit;
		}

		function getNodeList() {
			if(!$this->directory) {
				$this->status = false;
				$this->message = __("Please reload your browser.");
				$this->errorResponse();
				exit;
			}
			if(!file_exists($this->directory)) {
				$this->status = false;
				$this->message = __("Can't open the Project Directory.\nIt might have been moved, renamed or deleted.");
				$this->errorResponse();
				exit;
			}

			$this->session['selected_node'] = array();

			if($this->request['sort_key']) {
				if($this->request['node_id'] == $this->session['current_node'] && $this->session['sort_key'] == $this->request['sort_key']) {
					$this->session['sort_order'] = $this->session['sort_order'] == 'asc' ? 'desc' : 'asc';
				}
				
				$this->session['sort_key'] = $this->request['sort_key'];
			}

			if($this->request['current_node']) {
				$this->session['current_node'] = $this->request['current_node'];
			}

			// set open_mode
			if($this->request['mode'] == 'open') {
				$this->open_node = true;
			}

			// set open_nodes from request
			$this->session['open_nodes'] = array();
			if(is_array($this->request['open_nodes'])) {
				foreach($this->request['open_nodes'] as $node) {
					$this->session['open_nodes'][$node] = true;
				}
			}

			if(!$this->session['current_node']) {
				$this->session['current_node'] = $this->project_dir;
			}

			$this->response($this->session['current_node'], 'select');

			exit;
		}

		function pasteNode() {
			$dest = new B_FileNode($this->dir, $this->request['destination_node_id'], null, null, 1);
			if(!file_exists($dest->fullpath)) {
				$this->message = __('Another user has updated this record');
				$this->status = false;
				$this->response('', '');
				exit;
			}

			switch($this->request['mode']) {
			case 'copy':
				// Set time limit to 5 minutes
				set_time_limit(300);

				$this->total_copy_nodes = 0;
				$this->copy_nodes = 0;

				foreach($this->request['source_node_id'] as $node_id) {
					$source = new B_FileNode($this->dir, $node_id, null, null, 'all');
					if(!file_exists($source->fullpath)) {
						$this->message = __('Another user has updated this record');
						$this->status = false;
						break;
					}
					if($source->isMyChild($dest->fullpath)) {
						$this->message = $this->getErrorMessage(1);
						$this->status = false;
						break;
					}
					$this->total_copy_nodes += $source->nodeCount(); 
					$source_node[] = $source;
				}
				if(!$this->status) break;

				if(10 < $this->total_copy_nodes) {
					// send progress
					header('Content-Type: application/octet-stream');
					header('Transfer-encoding: chunked');
					flush();
					ob_flush();

					// Send start message
					$response['status'] = 'show';
					$response['message'] = 'Copying ...';
					$response['progress'] = 0;
					$this->progress = 0;
					$this->sendChunk(json_encode($response));
					$this->show_progress = true;

					usleep(1000);
				}

				$this->session['selected_node'] = array();

				foreach($source_node as $source) {
					if($dest->node_type == 'folder' || $dest->node_type == 'root') {
						if($this->show_progress) $callback = array('obj' => $this, 'method' => '_copy_callback');
						$ret = $source->copy($dest->path, $new_node_name, true, $callback);
					}
					if($ret) {
						$this->session['selected_node'][] = $dest->path . '/' . $new_node_name;
					}
					else {
						$this->status = false;
					}
				}
				if(!$this->status) {
					$this->message = $this->getErrorMessage($source->getErrorNo());
				}

				if($this->show_progress) {
					if($this->status) {
						$response['status'] = 'finished';
						$response['progress'] = 100;
						$this->sendChunk(',' . json_encode($response));
						$this->sendChunk();	// terminate
					}
					else {
						$response['status'] = 'error';
						$response['message'] = $this->getErrorMessage($source->getErrorNo());
						$this->sendChunk(',' . json_encode($response));
						sleep(1);
					}
				}
				break;

			case 'cut':
				foreach($this->request['source_node_id'] as $node_id) {
					$source = new B_FileNode($this->dir, $node_id, null, null, 'all');

					if(!file_exists($source->fullpath)) {
						$this->message = __('Another user has updated this record');
						$this->status = false;
						break;
					}
					if(!file_exists($dest->fullpath)) {
						$this->message = __('Another user has updated this record');
						$this->status = false;
						break;
					}
					else if(file_exists($dest->fullpath . '/' . $source->file_name)) {
						$this->message = __('Already exists');
						$this->status = false;
						break;
					}
					$source_node[] = $source;
				}
				if(!$this->status) break;

				foreach($source_node as $source) {
					if($dest->node_type == 'folder' || $dest->node_type == 'root') {
						$ret = $dest->move($source);
					}
					if($ret) {
						$move_status = true;
						if($this->session['current_node'] == $node_id) {
							$this->session['current_node'] = $source->node_id;
						}
					}
					else {
						$this->status = false;
					}
				}
				if($this->status) {
					$this->session['open_nodes'][$this->request['destination_node_id']] = true;
				}
				else if(!$this->message) {
					$this->message = $this->getErrorMessage($dest->getErrorNo());
				}
				break;
			}

			if(!$response) $this->response('', '');
			exit;
		}

		function _copy_callback($file_node) {
			$this->copy_nodes++;

			$response['status'] = 'progress';
			$response['progress'] = round($this->copy_nodes / $this->total_copy_nodes * 100);
			if($this->progress != $response['progress']) {
				$this->sendChunk(',' . json_encode($response));
				$this->progress = $response['progress'];
			}
		}

		function createNode() {
			$this->session['open_nodes'][$this->request['destination_node_id']] = true;
			$node = new B_FileNode($this->dir, $this->request['destination_node_id']);

			if($this->request['node_type'] == 'folder') {
				$ret = $node->createFolder('newFolder', $new_node_id);
			}
			else {
				$ret = $node->createFile('newFile.txt', $new_node_id);
			}

			if($ret) {
				$this->status = true;
				$this->session['selected_node'] = array();
				$this->session['open_nodes'][$this->request['destination_node_id']] = true;
			}
			else {
				$this->status = false;
				$this->message = __('An error has occurred');
			}
			$this->response($new_node_id, 'new_node');
			exit;
		}

		function deleteNode() {
			if($this->request['delete_node_id'] && $this->request['delete_node_id'] != 'null') {
				foreach($this->request['delete_node_id'] as $node_id) {
					$node = new B_FileNode($this->dir, $node_id, null, null, 'all');
					if(!file_exists($node->fullpath)) {
						$this->message = __('Another user has updated this record');
						$this->status = false;
					}
					else {
						$ret = $node->remove();
						if($ret) {
							$this->status = true;
							if($node->isMyChild(__getPath($this->dir, $this->session['current_node']))) {
								$this->session['current_node'] = $node->parentPath();
							}
						}
						else {
							$this->status = false;
							$this->message = __('An error has occurred');
							break;
						}
					}
				}
			}

			$this->response($this->request['node_id'], 'select');
			exit;
		}

		function saveName() {
			if($this->request['node_id'] && $this->request['node_id'] != 'null') {
				$file_info = pathinfo($this->request['node_id']);
				$node_name = trim($this->request['node_name']);
				$new_node_id = __getPath($file_info['dirname'], $node_name);
				$source = __getPath($this->dir , $this->request['node_id']);
				$dest = __getPath($this->dir , $new_node_id);

				if($this->checkFileName($source, $dest, $node_name, $file_info)) {
					$node = new B_FileNode($this->dir, $this->request['node_id'], null, null, 1);
					if($node) {
						$ret = $node->rename($this->request['node_id'], $new_node_id);
					}
					if($ret) {
						$this->status = true;
						$this->session['selected_node'] = array();
						$this->session['selected_node'][0] = $new_node_id;
						if($this->session['current_node'] == $this->request['node_id']) {
							$this->session['current_node'] = $new_node_id;
						}
						$this->replaceOpenNodes($this->request['node_id'], $new_node_id);
					}
					else {
						$this->message = $node->getMessage();
					}
				}
			}
			$this->response($this->session['current_node'], 'select');
			exit;
		}

		function replaceOpenNodes($before, $after) {
			foreach($this->session['open_nodes'] as $key => $value) {
				if(strstr($key, $before)) {
					$key = $after . substr($key, strlen($before));
				}
				$open_nodes[$key] = true;
			}
			$this->session['open_nodes'] = $open_nodes;
		}

		function checkFileName($source, $dest, $file_name, $file_info) {
			if(preg_match('/[\\\\:\/\*\?\"\'<>\|\s]/', $file_name)) {
				$this->message = __('The following characters cannot be used in file or folder names (\ / : * ? " \' < > | space)');
				return false;
			}
			if(strlen($file_name) != mb_strlen($file_name)) {
				$this->message = __('Multi-byte characters cannot be used');
				return false;
			}
			if(!file_exists(__getPath($this->dir, $file_info['path']))) {
				$this->message = __('Another user has updated this record');
				return false;
			}

			$node_type = is_dir($source) ? 'folder' : 'file';

			if(!strlen(trim($file_name))) {
				$this->message = __('Please enter a name for the %ITEM%');
				$this->message = str_replace('%ITEM%', __($node_type), $this->message);
				return false;
			}
			if(file_exists($dest) && strtolower($file_info['basename']) != strtolower($file_name)) {
				$this->message = __('A %ITEM% with this name already exists. Please enter a different name.');
				$this->message = str_replace('%ITEM%', __($node_type), $this->message);
				return false;
			}
			return true;
		}

		function download() {
			if($this->request['mode'] == 'download') {
				$this->downloadFile($this->request['file_name'], $this->request['file_path'], $this->request['remove']);
			}
			else {
				$this->createFile();
			}
		}

		function createFile() {
			if($this->request['download_node_id'] && $this->request['download_node_id'] != 'null') {
				foreach($this->request['download_node_id'] as $node_id) {
					$nodes[] = new B_FileNode($this->dir, $node_id, null, null, 'all');
				}
				if(count($nodes) == 1 && $nodes[0]->node_type == 'file') {
					$info = pathinfo($nodes[0]->file_name);

					// finish
					$response['status'] = 'download';
					$response['remove'] = false;
					$response['file_name'] = $nodes[0]->file_name;
					$response['file_path'] = $nodes[0]->fullpath;
					header('Content-Type: application/x-javascript charset=utf-8');
					echo json_encode($response);
				}
				else {
					if(!class_exists('ZipArchive')) exit;

					ignore_user_abort(true);

					// set time limit to 5 minutes
					set_time_limit(300);

					// send progress
					header('Content-Type: application/octet-stream');
					header('Transfer-encoding: chunked');
					flush();
					ob_flush();

					// Send start message
					$response['status'] = 'show';
					$response['progress'] = 0;
					$response['message'] = 'Creating zip file';
					$progress = 0;
					$this->sendChunk(json_encode($response));

					if(count($nodes) == 1) {
						if($this->request['download_node_id'][0] == 'root') {
							$file_name = 'root.zip';
						}
						else {
							$file_name = $nodes[0]->file_name . '.zip';
						}
					}
					else {
						$file_name = 'files.zip';
					}

					$file_path = B_DOWNLOAD_DIR . $this->user_id . time() . $file_name;

					$cmdline = 'php "' . B_DOC_ROOT . B_CURRENT_ROOT . 'module/editor/archive.php"';
					$cmdline .= ' "' . $_SERVER['SERVER_NAME'] . '"';
					$cmdline .= ' "' . $_SERVER['DOCUMENT_ROOT'] . '"';
					$cmdline .= ' "' . $this->dir . '"';
					$cmdline .= ' "' . $file_path . '"';

					$escape = '"';
					foreach($this->request['download_node_id'] as $node_id) {
						$cmdline .= ' ' . $escape . $node_id . $escape;
					}

					// kick as a background process
					B_Util::fork($cmdline);

					for($total_file_size=0, $i=0; $i<count($nodes); $i++) {
						$total_file_size+= $nodes[$i]->fileSize();
					}

					// send progress 
					if($total_file_size) {
						for($cnt=0 ;; $cnt++) {
							usleep(40000);
							if(file_exists($file_path)) {
								$response['status'] = 'progress';
								$response['progress'] = 100;
								$this->sendChunk(',' . json_encode($response));
								usleep(300000);

								$response['status'] = 'complete';
								$response['progress'] = 100;
								$response['message'] = 'Complete!';
								$this->sendChunk(',' . json_encode($response));
								sleep(1);

								break;
							}

							if($cnt%4 == 0) {
								unset($dots);
								for($i=0; $i<($cnt/4%8); $i++) {
									$dots.= '.';
								}
								$response['status'] = 'message';
								$response['message'] = "Creating zip file {$dots}";

								$this->sendChunk(',' . json_encode($response));
							}

							usleep(40000);

							$response['status'] = 'progress';
							$response['progress'] = round($cnt / $total_file_size * 100 * 1300000);
							if($response['progress'] > 99) $response['progress'] = 99;

							if($progress != $response['progress']) {
								$this->sendChunk(',' . json_encode($response));
								$progress = $response['progress'];
							}
						}
					}

					// finish
					$response['status'] = 'download';
					$response['remove'] = true;
					$response['file_name'] = $file_name;
					$response['file_path'] = $file_path;
					$this->sendChunk(',' . json_encode($response));
					$this->sendChunk();	// terminate
					if(connection_status()) {
						unlink($file_path);
					}
				}
			}
			exit;
		}

		function downloadFile($file_name, $file_path, $remove) {
			// Download
			header('Pragma: cache;');
			header('Cache-Control: public');

			$info = pathinfo($file_name);
			switch(strtolower($info['extension'])) {
			case 'swf':
				header('Content-type: application/x-shockwave-flash');
				break;

			case 'css':
				header('Content-Type: text/css; charset=' . B_CHARSET);
				break;

			case 'js':
				header('Content-type: application/x-javascript');
				break;

				case 'zip':
				header('Content-type: application/x-zip-dummy-content-type');

			default:
				header('Content-Type: image/' . strtolower($info['extension']));
				break;
			}

			header('Content-Disposition: attachment; filename=' . $file_name);

			ob_end_clean();
			readfile($file_path);
			if($remove === 'true') unlink($file_path);

			exit;
		}

		function preview() {
			if($this->request['node_id'] && $this->request['node_id'] != 'null') {
				// Redircet to top page
				$path = $this->scheme . __getPath($this->domain, $this->request['node_id']);
				header("Location:$path");
			}

			exit;
		}

		function margeOpenCurrentNodes() {
			$current = explode('/', $this->session['current_node']);

			foreach($current as $value) {
				if(!$value) continue;

				if($dir) $dir .= '/';
				$dir .= $value;

				$nodes[$dir] = true;
			}

			if(is_array($nodes) && is_array($this->session['open_nodes'])) {
				$nodes = array_merge($nodes, $this->session['open_nodes']);
			}

			return $nodes;
		}

		function response($node_id, $category) {
			$response['status'] = $this->status;
			if($this->message) {
				$response['message'] = $this->message;
			}
			$root_node = new B_FileNode($this->dir, $this->project_dir, $this->margeOpenCurrentNodes(), null, 1);
			$current_node = $root_node->getNodeById($this->session['current_node']);
			if(!$current_node) {
				$current_node = $root_node;
				$this->session['current_node'] = $this->project_dir;
				$response['forced_current_node'] = $this->project_dir;
			}

			if($current_node && $this->session['sort_key']) {
				$current_node->setSortKey($this->session['sort_key']);
				$current_node->setSortOrder($this->session['sort_order']);
			}

			$list[] = $root_node->getNodeList($node_id, $category);

			$response['current_node'] = $this->session['current_node'];

			if($this->session['selected_node']) {
				$response['selected_node'] = $this->session['selected_node'];
			}
			if($list) {
				$response['node_info'] = $list;
			}
			if($this->session['sort_key']) {
				$response['sort_key'] = $this->session['sort_key'];
				$response['sort_order'] = $this->session['sort_order'];
			}
			if($this->session['mode'] == 'open') {
				$response['open'] = true;
				$this->session['mode'] = '';
			}
			if($this->open_node) {
				$response['open_node'] = true;
			}

			// create thmubnail if necessary
			if($this->total_files = $current_node->missing_thumbnails()) {
				if($this->total_files < 50) {
					$current_node->createthumbnail();
				}
				else {
					$this->createthumbnail($current_node);
					$this->sendChunk(',' . json_encode($response));
					$this->sendChunk();		// terminate
					exit;
				}
			}

			header('Content-Type: application/x-javascript charset=utf-8');
			echo json_encode($response);
		}

		function errorResponse() {
			$response['status'] = $this->status;
			if($this->message) {
				$response['message'] = $this->message;
			}
			header('Content-Type: application/x-javascript charset=utf-8');
			echo json_encode($response);
		}

		function createThumbnail($current_node) {
			// Set time limit to 3 minutes
			set_time_limit(180);

			// send progress
			header('Content-Type: application/octet-stream');
			header('Transfer-encoding: chunked');
			flush();
			ob_flush();

			// Send start message
			$response['status'] = 'show';
			$response['progress'] = 0;
			$response['message'] = 'Creating Thumbnail files';
			$this->sendChunk(json_encode($response));

			$this->createTumbnail_files = 0;
			$this->progress = 0;
			$current_node->createthumbnail($this->except, array('obj' => $this, 'method' => 'createThumbnail_callback'));

			sleep(1);
		}

		function createThumbnail_callback($node) {
			$this->createTumbnail_files++;
			$response['status'] = 'progress';
			$response['progress'] = round($this->createTumbnail_files / $this->total_files * 100);
			if($this->progress != $response['progress']) {
				$this->sendChunk(',' . json_encode($response));
				$this->progress = $response['progress'];
			}
		}

		function getErrorMessage($error) {
			global $g_data_set, ${$g_data_set};

			return ${$g_data_set}['node_error'][$error];
		}

		function view() {
			// Start buffering
			ob_start();

			require_once('./view/view_index.php');

			// Get buffer
			$contents = ob_get_clean();

			// Send HTTP header
			$this->sendHttpHeader();

			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/filemanager_tree.css">');
			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/filemanager.css">');
			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/upload.css">');
			$this->html_header->appendProperty('script', '<script src="js/bframe_tree.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/bframe_dialog.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/bframe_splitter.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/bframe_progress_bar.js"></script>');

			// Show HTML header
			$this->showHtmlHeader();

			// Show HTML body
			echo $contents;
		}

		function viewError() {
			// Start buffering
			ob_start();

			$this->message = __("<dt>Can't open the Directory.</dt><dd>It might have been moved, renamed or deleted.");

			require_once('./view/view_not_found.php');

			// Get buffer
			$contents = ob_get_clean();

			// Send HTTP header
			$this->sendHttpHeader();

			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/filemanager.css">');

			// Show HTML header
			$this->showHtmlHeader();

			// Show HTML body
			echo $contents;
		}
	}
