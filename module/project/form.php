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
			$this->input_control_config = $input_control_config;
			$this->delete_control_config = $delete_control_config;
			$this->confirm_control_config = $confirm_control_config;
			$this->result_control_config = $result_control_config;
			$this->result_config = $result_config;

			$this->df = new B_DataFile(B_PROJECT_DATA, 'project');

			// Set mode to HTML
			$obj = $this->form->getElementByName('mode');
			$obj->setValue($this->request);
		}

		function select() {
			switch($this->mode) {
			case 'insert':
				$this->control = new B_Element($this->input_control_config, $this->user_auth);
				$this->form->setFilterValue('select');
				break;

			case 'update':
				$row = $this->df->get($this->request['project_id']);
				$this->form->setValue($row);
				$this->session['init_value'] = $row;

				$this->control = new B_Element($this->input_control_config, $this->user_auth);
				$this->form->setFilterValue('select');
				break;

			case 'delete':
				$row = $this->df->get($this->request['project_id']);
				$this->form->setValue($row);
				$this->session['post'] = $row;
				$this->display_mode = 'confirm';

				$this->control = new B_Element($this->delete_control_config, $this->user_auth);
				break;
			}
		}

		function confirm() {
			$this->form->setValue($this->post);

			if(!$this->checkAlt($this->post)) {
				$this->control = new B_Element($this->input_control_config, $this->user_auth);
				return;
			}

			if(!$this->form->validate()) {
				$this->control = new B_Element($this->input_control_config, $this->user_auth);
				return;
			}

			$this->form->getValue($post_value);
			$this->session['post'] = $post_value;
			$this->control = new B_Element($this->confirm_control_config, $this->user_auth);

			// Set display mode
			$this->display_mode = 'confirm';
		}

		function _validate_callback($param) {
			return true;
		}

		function _validate_callback2($param) {
			// Check the user id in built-in user
			global $g_auth_users;
			foreach($g_auth_users as $value) {
				if($value['user_id'] == $param['value']) {
					return false;
				}
			}
			return true;
		}

		function checkAlt($value) {
			if($this->request['mode'] == 'update') {
/*
				$row = $this->table->selectByPk($value);
				if($this->session['init_value']['update_datetime'] < $row['update_datetime']) {
					$error_message = __('Another user has updated this record');
					$this->form->setValue($this->session['init_value']);
					$this->form->checkAlt($row, $error_message);
					$this->form->setValue($value);
					$this->control = new B_Element($this->input_control_config, $this->user_auth);

					return false;
				}
*/
			}

			return true;
		}

		function back() {
			$this->form->setValue($this->session['post']);
			$this->control = new B_Element($this->input_control_config, $this->user_auth, $this->mode);
		}

		function register() {
			$ret = $this->_register($message);
			if($ret) $this->df->save();

			$this->result = new B_Element($this->result_config, $this->user_auth);
			$this->result_control = new B_Element($this->result_control_config, $this->user_auth);

			$param['user_id'] = $this->session['post']['user_id'];
			$param['action_message'] = $message;
			$this->result->setValue($param);

			$this->setView('result_view');
		}

		function _register(&$message) {
			if(!$this->checkAlt($this->session['post'])) {
				$message = __('Another user has updated this record');
				return false;
			}

			switch($this->mode) {
			case 'insert':
				$ret = $this->insert();
				if($ret) {
					$message = __('was saved.');
				}
				else {
					$message = __('was faild to register.');
				}
				break;

			case 'update':
				$ret = $this->update($param);
				if($ret) {
					$message = __('was updated.');
				}
				else {
					$message = __('was faild to update.');
				}
				break;

			case 'delete':
				$ret = $this->delete($param);
				if($ret) {
					$message = __('was deleted.');
				}
				else {
					$message = __('was faild to delete.');
				}
				break;
			}

			return $ret;
		}

		function insert() {
			$param = $this->session['post'];

			$param['del_flag'] = '0';
			$param['create_user'] = $this->user_id;
			$param['create_datetime'] = time();
			$param['update_user'] = $this->user_id;
			$param['update_datetime'] = time();

			$this->df->insert($param);
			$this->createThumbnail($param['name'], $param['directory']);

			return true;
		}

		function update() {
			$param = $this->df->get($param['id'], $param);
			$directory_old = $param['directory'];
			$param = $this->session['post'];

			$param['update_user'] = $this->user_id;
			$param['update_datetime'] = time();

			$this->df->update($param['id'], $param);
			if($param['directory'] != $directory_old) {
				$this->createThumbnail($param['name'], $param['directory']);
			}

			return true;
		}

		function delete() {
			$param = $this->session['post'];
			$this->df->delete($param['id']);

			define('B_UPLOAD_THUMBDIR', B_THUMBDIR . $param['name'] . '/');
			$this->removeThumbnail();
			if(file_exists(B_UPLOAD_THUMBDIR)) rmdir(B_UPLOAD_THUMBDIR);

			return true;
		}

		function createThumbnail($name, $directory) {
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

			require_once('./view/view_form.php');

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

		function result_view() {
			// Start buffering
			ob_start();

			require_once('./view/view_result.php');

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
