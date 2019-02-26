<?php
/*
 * B-code : Online Editor
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	class index_index extends B_AdminModule {
		function __construct() {
			parent::__construct(__FILE__);

			$this->title = B_TITLE_PREFIX . 'B-code';

			// Check browser
			if(!$this->checkBrowser()) {
				$this->view_file = './view/view_support_browsers.php';
				return;
			}

			// Check logedin
			$this->auth = new B_AdminAuth;
			$ret = $this->auth->getUserInfo($this->user_id, $this->user_name, $this->user_auth, $this->language);
			if($ret) {
				$this->admin();
			}
			else {
				$this->login();
			}
		}

		function admin() {
			// Set session for each TERMINAL_ID
			$_SESSION['terminal_id'] = TERMINAL_ID;
			if(!is_array($_SESSION[TERMINAL_ID])) $_SESSION[TERMINAL_ID] = array();

			// bframe_message
			$this->bframe_message = new B_Element($this->bframe_message_config, $this->user_auth);

			$this->user_name = htmlspecialchars($this->user_name, ENT_QUOTES, B_CHARSET);

			if($_REQUEST['project']) {
				$this->title = $_REQUEST['project'] . ' - ' . $this->title;
				$this->initial_page = DISPATCH_URL . '&amp;module=editor&amp;page=tree&amp;method=open&amp;project=' . $_REQUEST['project'];
				$this->view_file = './view/view_index.php';
			}
			else {
				if($this->user_auth == 'super_admin') {
					// Menu
					require_once('./config/menu_config.php');
					$this->menu = new B_Element($menu_config, $this->user_auth);
					$this->admin_profile = '<li id="user-settings"><a href="' . DISPATCH_URL . '&amp;module=siteadmin&amp;page=form" target="main"><img src="images/common/gear_white.png" alt="user settings" /></a></li>';
				}

				$this->initial_page = DISPATCH_URL . '&amp;module=project&amp;page=list';

				$this->view_file = './view/view_dashboard.php';
			}
		}

		function login() {
			$_SESSION['language'] = LANG;

			if($_POST['login']) {
				// Check login
				$this->df = new B_DataFile(B_USER_DATA, 'user');
				$ret = $this->auth->login($this->df, $_POST['user_id'], $_POST['password']);
				if($ret) {
					// Regenerate session id
					session_regenerate_id(true);

					$this->auth->getUserInfo($this->user_id, $this->user_name, $this->user_auth, $this->language);

					// Redirect
					$path = B_SITE_BASE;
					header("Location:$path");
					exit;
				}
				else {
					$this->view_file = './view/view_login_error.php';
				}
			}
			else {
				$this->view_file = './view/view_login.php';
			}
		}

		function checkBrowser() {
			$this->agent = $_SERVER['HTTP_USER_AGENT'];
			if(preg_match('/firefox/i', $_SERVER['HTTP_USER_AGENT'])) return true;
			if(preg_match('/chrome/i', $_SERVER['HTTP_USER_AGENT'])) return true;
			if(preg_match('/safari/i', $_SERVER['HTTP_USER_AGENT'])) return true;
			if(preg_match('/rv:11.0/i', $_SERVER['HTTP_USER_AGENT'])) return true;

			return false;
		}

		function view() {
			// Start buffering
			ob_start();

			require_once($this->view_file);

			// Get buffer
			$contents = ob_get_clean();

			// Send HTTP header
			$this->sendHttpHeader();

			// Show HTML
			echo $contents;
		}
	}
