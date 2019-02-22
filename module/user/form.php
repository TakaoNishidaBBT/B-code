<?php
/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	class user_form extends B_AdminModule {
		function __construct() {
			parent::__construct(__FILE__);

			require_once('./config/form_config.php');
			$this->form = new B_Element($form_config, $this->user_auth, $this->mode);
			$this->df = new B_DataFile(B_USER_DATA, 'user');

			// Set mode to HTML
			$obj = $this->form->getElementByName('mode');
			$obj->setValue($this->request);
		}

		function select() {
			$this->session['mode'] = $this->request['mode'];

			switch($this->session['mode']) {
			case 'insert':
				$this->view_file = './view/view_form.php';
				break;

			case 'update':
				$row = $this->df->selectByPk($this->request['rowid']);
				$this->form->setValue($row);
				$this->session['init_value'] = $row;
				$this->view_file = './view/view_form.php';
				break;

			case 'delete':
				$row = $this->df->selectByPk($this->request['rowid']);
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
			if($this->session['mode'] == 'insert') {
				if($this->df->select('user_id', $param['value'])) {
					return false;
				}
			}
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
				$row = $this->df->selectByPk($value['id']);
				if($this->session['init_value']['update_datetime'] < $row['update_datetime']) {
					$error_message = __('Another user has updated this record');
					return false;
				}
			}

			return true;
		}

		function register() {
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

			header('Content-Type: application/x-javascript charset=utf-8');
			echo json_encode($response);
			exit;
		}

		function _register(&$message) {
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

			$obj = $this->form->getElementByName('rowid');
			$obj->value = $new_id;
			$obj = $this->form->getElementByName('mode');
			$obj->value = 'update';

			$row = $this->df->selectByPk($new_id);
			$this->session['init_value'] = $row;

			return true;
		}

		function update() {
			$this->form->getValue($param);
			$param['update_user'] = $this->user_id;
			$param['update_datetime'] = time();

			$this->df->updateByPk($param['rowid'], $param);
			$this->df->save();

			$row = $this->df->selectByPk($param['rowid']);
			$this->session['init_value'] = $row;

			return true;
		}

		function delete() {
			$value = $this->df->selectByPk($this->post['rowid']);
			$this->user_name = $value['user_name'];

			$this->df->deleteByPk($this->post['rowid']);
			$this->df->save();

			$this->setView('resultView');
		}

		function view() {
			// Start buffering
			ob_start();

			require_once($this->view_file);

			// Get buffer
			$contents = ob_get_clean();

			// Send HTTP header
			$this->sendHttpHeader();

			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/user.css">');
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

			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/user.css">');

			// Show HTML header
			$this->showHtmlHeader();

			// Show HTML body
			echo $contents;
		}
	}
