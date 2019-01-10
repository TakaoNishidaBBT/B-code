<?php
/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	class project_tree extends B_AdminModule {
		function __construct() {
			parent::__construct(__FILE__);

			$this->dir = B_FILE_ROOT_DIR;

			require_once('./config/tree_config.php');
			$this->tree_config = $tree_config;
			$this->tree = new B_FileNode($this->dir, '');

			$this->tree->setConfig($this->tree_config);

			if($this->request['node_id']) {
				$this->openCurrentNode($this->request['node_id']);
			}
			if($this->request['mode'] == 'open') {
				$this->openCurrentNode($this->session['current_node']);
			}

			$this->status = true;
			if(!$this->session['sort_order']) $this->session['sort_order'] = 'asc';
			if(!$this->session['sort_key']) $this->session['sort_key'] = 'file_name';
		}

		function open() {
			$node_id = $this->request['node_id'];
			$this->global_session['open_project'][$node_id] = true;

			header('Content-Type: application/x-javascript charset=utf-8');
			$response['status'] = true;
			echo json_encode($response);

			exit;
		}

		function openCurrentNode($node_id) {
			$dir = explode('/', $node_id);

			for($i=0; $i<count($dir); $i++) {
				if(!$dir[$i]) continue;
				$path.= '/' . $dir[$i];
				$this->session['open_nodes'][$path] = true;
			}
		}

		function getNodeList() {
			$this->session['selected_node'] = '';

			if($this->request['sort_key']) {
				if($this->request['node_id'] == $this->session['current_node'] && $this->session['sort_key'] == $this->request['sort_key']) {
					$this->session['sort_order'] = $this->session['sort_order'] == 'asc' ? 'desc' : 'asc';
				}
				
				$this->session['sort_key'] = $this->request['sort_key'];
			}
			if($this->request['node_id'] && $this->request['mode'] != 'open') {
				$this->session['current_node'] = $this->request['node_id'];
			}
			if($this->request['node_id']) {
				$this->session['open_nodes'][$this->request['node_id']] = true;
			}
			if(!$this->session['current_node']) {
				$this->session['current_node'] = 'root';
			}
			if(isset($this->request['display_mode'])) {
				$this->session['display_mode'] = $this->request['display_mode'];
			}
			$this->response($this->session['current_node'], 'select');

			exit;
		}

		function closeNode() {
			$this->session['open_nodes'][$this->request['node_id']] = false;
			$this->session['selected_node'] = '';

			header('Content-Type: application/x-javascript charset=utf-8');
			$response['status'] = true;
			echo json_encode($response);

			exit;
		}

		function response($node_id, $category) {
			// If thumb-nail cache file not exists
			if(!file_exists(B_FILE_INFO_THUMB)) {
				if($this->createThumbnailCacheFile()) {
					exit;
				}
			}
			$response['status'] = $this->status;
			if($this->message) {
				$response['message'] = $this->message;
			}

			$root_node = new B_FileNode($this->dir, '/', $this->session['open_nodes'], null, 1);
			$current_node = $root_node->getNodeById($this->session['current_node']);
			if(!$current_node) {
				$current_node = $root_node;
				$this->session['current_node'] = 'root';
			}

			if($this->session['sort_key']) {
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

			header('Content-Type: application/x-javascript charset=utf-8');
			echo json_encode($response);
		}

		function getErrorMessage($error) {
			global $g_data_set, ${$g_data_set};

			return ${$g_data_set}['node_error'][$error];
		}

		function view() {
			// Start buffering
			ob_start();

			require_once('./view/view_project.php');

			// Get buffer
			$contents = ob_get_clean();

			// Send HTTP header
			$this->sendHttpHeader();

			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/project.css">');
			$this->html_header->appendProperty('script', '<script src="js/bframe_tree.js"></script>');

			// Show HTML header
			$this->showHtmlHeader();

			// Show HTML body
			echo $contents;
		}
	}
