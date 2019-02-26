<?php
/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	class siteadmin_form extends B_AdminModule {
		function __construct() {
			parent::__construct(__FILE__);

			if($this->user_auth != 'super_admin') exit;

			require_once('./config/form_config.php');
			$this->form = new B_Element($form_config);
			$this->df = new B_DataFile(B_USER_DATA, 'user');
		}

		function func_default() {
			$this->select();
		}

		function select() {
			global $g_auth_users;

			$param['admin_user_name'] = $g_auth_users[0]['user_name'];
			$param['admin_user_id'] = $g_auth_users[0]['user_id'];
			$param['language'] = $g_auth_users[0]['language'];
			$this->form->setValue($param);
			$this->session['init_value'] = $param;
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

		function checkAlt($value) {
			$row = $this->df->selectByPk($value['id']);
			if($this->session['init_value']['update_datetime'] < $row['update_datetime']) {
				$error_message = __('Another user has updated this record');
				return false;
			}

			return true;
		}

		function _validate_callback($param) {
			// Check user id already exists
			if($this->df->select('user_id', $param['value'])) {
				return false;
			}

			return true;
		}

		function register() {
			try {
				if($this->validate()) {
					$status = $this->_register();
					$this->message = __('saved.');
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

			$response['innerHTML'] = array(
				'admin-form'	=> $this->form->getHtml(),
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

		function _register() {
			global $g_auth_users;

			// Set up admin user file
			$this->form->getValue($param);
			$contents = file_get_contents(B_CURRENT_DIR . 'user/config/_users.php');
			$contents = str_replace('%USER_NAME%',  $param['admin_user_name'], $contents);
			$contents = str_replace('%USER_ID%',  $param['admin_user_id'], $contents);
			$contents = str_replace('%LANGUAGE%',  $param['language'], $contents);
			if($param['admin_user_pwd']) {
				$contents = str_replace('%PASSWORD%', md5($param['admin_user_pwd']), $contents);
			}
			else {
				$contents = str_replace('%PASSWORD%', $g_auth_users[0]['pwd'], $contents);
			}

			file_put_contents(B_CURRENT_DIR . 'user/users.php', $contents);

			return true;
		}

		function view() {
			// Start buffering
			ob_start();

			require_once('./view/view_form.php');

			// Get buffer
			$contents = ob_get_clean();

			// Send HTTP header
			$this->sendHttpHeader();

			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/siteadmin.css">');
			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/selectbox.css">');
			$this->html_header->appendProperty('script', '<script src="js/bframe_selectbox.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/bframe_edit_check.js"></script>');

			// Show HTML header
			$this->showHtmlHeader();

			// Show HTML body
			echo $contents;
		}
	}
