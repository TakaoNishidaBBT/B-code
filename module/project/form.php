<?php
/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	class project_form extends B_AdminModule {
		function __construct() {
			parent::__construct(__FILE__);

			$this->mode = $this->request['mode'];

			require_once('./config/form_config.php');
			$this->form = new B_Element($form_config, $this->user_auth, $this->mode);
			$this->df = new B_DataFile(B_PROJECT_DATA, 'project');

			// Set mode to HTML
			$obj = $this->form->getElementByName('mode');
			$obj->setValue($this->request);
		}

		function select() {
			$this->session['mode'] = $this->request['mode'];

			switch($this->mode) {
			case 'insert':
				$this->view_file = './view/view_form.php';
				break;

			case 'update':
				$row = $this->df->selectByPk($this->request['id']);
				$this->form->setValue($row);
				$this->session['init_value'] = $row;
				$this->view_file = './view/view_form.php';
				break;

			case 'delete':
				$row = $this->df->selectByPk($this->request['id']);
				$this->form->setValue($row);
				$this->display_mode = 'confirm';
				$this->view_file = './view/view_delete.php';
				break;
			}
			$this->form->setFilterValue($this->session['mode']);
		}

		function validate() {
			$this->form->setValue($this->post);

			if(!$this->checkAlt($this->post)) {
				$this->message = __('Another user has updated this record');
				return false;
			}

			if(!$this->form->validate()) {
				$this->message = __('This is an error in your entry');
				return false;
			}

			return true;
		}

		function _validate_callback($param) {
			return true;
		}

		function checkAlt($value) {
			if($this->request['mode'] != 'update') {
				$row = $this->df->selectByPk($value['id']);
				if($this->session['init_value']['update_datetime'] < $row['update_datetime']) {
					$error_message = __('Another user has updated this record');
					return false;
				}
			}

			return true;
		}

		function register() {
$this->log->write('register');
			try {
				if($this->validate()) {
					$status = $this->_register($this->message);
				}
				else {
					$status = false;
				}
			}
			catch(Exception $e) {
				$status = false;
				$mode = 'alert';
				$this->message = $e->getMessage();
			}

			$this->form->setFilterValue($this->session['mode']);

			$response['innerHTML'] = array(
				'user-form'		=> $this->form->getHtml(),
				'hidden-form'	=> $this->form->getHiddenHtml(),
			);

			$response['status'] = $status;
			$response['mode'] = $mode;
			$response['message_obj'] = 'message';
			$response['message'] = $this->message;
$this->log->write('$response', $response);
			header('Content-Type: application/x-javascript charset=utf-8');
			echo json_encode($response);
			exit;
		}

		function _register(&$message) {
$this->log->write('_register');
			switch($this->session['mode']) {
			case 'insert':
				$ret = $this->insert();
				if($ret) {
					$message = __('saved.');
					$this->session['mode'] = 'update';
				}
				else {
					$message = __('faild to register.');
				}
				break;

			case 'update':
				$ret = $this->update();
				if($ret) {
					$message = __('updated.');
				}
				else {
					$message = __('faild to update.');
				}
				break;
			}

			return $ret;
		}

		function insert() {
			$param = $this->post;

			$param['del_flag'] = '0';
			$param['create_user'] = $this->user_id;
			$param['create_datetime'] = time();
			$param['update_user'] = $this->user_id;
			$param['update_datetime'] = time();

			$new_id = $this->df->insert($param);
			$this->df->save();

			$obj = $this->form->getElementByName('id');
			$obj->value = $new_id;
			$obj = $this->form->getElementByName('mode');
			$obj->value = 'update';

			$row = $this->df->selectByPk($new_id);
			$this->session['init_value'] = $row;

			$this->createThumbnail($param['name'], $param['directory']);

			return true;
		}

		function update() {
			$this->form->getValue($param);
$this->log->write('update', $param);
			$param['update_user'] = $this->user_id;
			$param['update_datetime'] = time();

			$this->df->updateByPk($param['id'], $param);
			$this->df->save();
$this->log->write('directory', $param['directory'], $this->session['init_value']['directory']);

			// recreate thumbnail
			if($param['directory'] != $this->session['init_value']['directory']) {
				$this->createThumbnail($param['name'], $param['directory']);
			}

			// rename thmubnail directory
$this->log->write('name', $param['name'], $this->session['init_value']['name']);
			if($param['name'] != $this->session['init_value']['name']) {
				rename(B_THUMBDIR . $this->session['init_value']['name'], B_THUMBDIR . $param['name']);
			}

			$row = $this->df->selectByPk($param['id']);
			$this->session['init_value'] = $row;
$this->log->write('last init_value', $this->session['init_value']);

			return true;
		}

		function delete() {
			$value = $this->df->selectByPk($this->post['id']);
			$this->project_name = $value['name'];

			$this->df->deleteByPk($this->post['id']);
			$this->df->save();

			$this->setView('resultView');

			define('B_UPLOAD_THUMBDIR', B_Util::getPath(B_THUMBDIR, $value['name']) . '/');
$this->log->write('B_UPLOAD_THUMBDIR', B_UPLOAD_THUMBDIR);
			$this->removeThumbnail();
			if(file_exists(B_UPLOAD_THUMBDIR)) rmdir(B_UPLOAD_THUMBDIR);

			$this->setView('resultView');

			return true;
		}

		function createThumbnail($name, $directory) {
			// Set time limit to 3 minutes
			set_time_limit(180);

			define('B_UPLOAD_THUMBDIR', B_THUMBDIR . $name . '/');
			if(file_exists(B_UPLOAD_THUMBDIR)) {
				$this->removeThumbnail();
			}
			else {
				mkdir(B_UPLOAD_THUMBDIR);
				chmod(B_UPLOAD_THUMBDIR, 0777);
			}

			if(substr($directory, -1) != '/') $directory .= '/';
			$node = new B_FileNode(B_FILE_ROOT_DIR, $directory, null, null, 'all');
			$this->createTumbnail_files = 0;
			$this->progress = 0;
			$node->createthumbnail($this->except, array('obj' => $this, 'method' => 'createThumbnail_callback'));
		}

		function removeThumbnail() {
			if(!file_exists(B_UPLOAD_THUMBDIR)) return;

			if($handle = opendir(B_UPLOAD_THUMBDIR)) {
				while(false !== ($file_name = readdir($handle))){
					if($file_name == '.' || $file_name == '..') continue;
					unlink(B_UPLOAD_THUMBDIR . $file_name);
				}
				closedir($handle);
			}
		}

		function view() {
			// Start buffering
			ob_start();

			require_once($this->view_file);

			// Get buffer
			$contents = ob_get_clean();

			// Send HTTP header
			$this->sendHttpHeader();

			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/project.css">');
			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/selectbox.css">');
			$this->html_header->appendProperty('script', '<script src="js/bframe_selectbox.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/bframe_edit_check.js"></script>');

			// Show HTML header
			$this->showHtmlHeader();

			// Show HTML body
			echo $contents;
		}

		function resultView() {
			// Start buffering
			ob_start();

			require_once('./view/view_delete_result.php');

			// Get buffer
			$contents = ob_get_clean();

			// Send HTTP header
			$this->sendHttpHeader();

			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/project.css">');

			// Show HTML header
			$this->showHtmlHeader();

			// Show HTML body
			echo $contents;
		}
	}
