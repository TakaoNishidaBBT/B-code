<?php
/*
 * B-code : Online Editor
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	class user_list extends B_AdminModule {
		function __construct() {
			parent::__construct(__FILE__);

			// Create list header
			require_once('./config/list_header_config.php');
			$this->header = new B_Element($list_header_config, $this->user_auth);

			// Data File
			$this->df = new B_DataFile(B_USER_DATA, 'user');

			// Create DataGrid
			require_once('./config/list_config.php');
			$this->dg = new B_DataGrid($this->df, $list_config);

			// Set call back
			$this->dg->setCallBack($this, '_list_callback');
		}

		function func_default() {
			$this->init();
		}

		function init() {
			$this->session = array();

			$this->setProperty();
			$this->setHeader();
			$this->setData();
		}

		function select() {
			$this->setRequest();
			$this->setProperty();
			$this->setHeader();
			$this->setData();
		}

		function back() {
			$this->setProperty();
			$this->setHeader();
			$this->setData();
		}

		function jump() {
			$this->_setRequest('page_no');
			$this->setProperty();
			$this->setHeader();
			$this->setData();
		}

		function sort() {
			if($this->request['sort_key']) {
				if(isset($this->session['sort_key']) && 
					$this->session['sort_key'] == $this->request['sort_key']) {
					if($this->session['order'] == 'asc') {
						$this->session['order'] = 'desc';
					}
					else {
						$this->session['order'] = 'asc';
					}
				}
				else {
					$this->session['sort_key'] = $this->request['sort_key'];
					$this->session['order'] = 'asc';
				}
			}
			$this->setProperty();
			$this->setHeader();
			$this->setData();
		}

		function setRequest() {
			$this->_setRequest('page_no');
			$this->_setRequest('keyword');
			$this->_setRequest('row_per_page');
		}

		function setProperty() {
			$this->default_row_per_page = 20;

			$this->_setProperty('keyword', '');
			$this->_setProperty('page_no', 1);
			$this->_setProperty('row_per_page', $this->default_row_per_page);
			$this->_setProperty('sort_key', 'user_id');
			$this->_setProperty('order', ' asc');
		}

		function setHeader() {
			// Set header
			if($this->session) {
				$this->header->setValue($this->session);
			}
		}

		function setData() {
			if($this->sort_key) {
				$this->dg->setSortKey($this->sort_key);
				$this->dg->setSortOrder($this->order);

				$this->df->setSortKey($this->sort_key);
				$this->df->setSortOrder($this->order);
			}

			$data = $this->df->selectByKeyword(array('user_id', 'user_name', 'notes'), $this->keyword);
			$this->dg->bind($data);
		}

		function _list_callback($array) {
			$row = $array['row'];

			$user_status = $row->getElementByName('user_status');
			if($user_status->value == '9') {
				$row->start_html = $row->start_html_invalid;
			}

			$user_id = $row->getElementByName('user_id');
			$identicon = $row->getElementByName('identicon');
			$identicon->value = B_Util::identicon($user_id->value);
		}

		function view() {
			// Start buffering
			ob_start();

			require_once('./view/view_list.php');

			// Get buffer
			$contents = ob_get_clean();

			// Send HTTP header
			$this->sendHttpHeader();

			// Set css and javascript
			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/user.css">');
			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/selectbox.css">');
			$this->html_header->appendProperty('script', '<script src="js/bframe_selectbox.js"></script>');

			// Show HTML header
			$this->showHtmlHeader();

			// Show HTML body
			echo $contents;
		}
	}
