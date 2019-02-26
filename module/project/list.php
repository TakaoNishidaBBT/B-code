<?php
/*
 * B-code : Online Editor
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	class project_list extends B_AdminModule {
		function __construct() {
			parent::__construct(__FILE__);

			// Create list header
			require_once('./config/list_header_config.php');
			$this->header = new B_Element($list_header_config, $this->user_auth);

			// Data File
			$this->df = new B_DataFile(B_PROJECT_DATA, 'project');

			// Create DataGrid
			require_once('./config/list_config.php');
			$this->dg = new B_DataGrid($this->df, $list_config, $this->user_auth, $this->language);

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
					if($this->session['order'] == ' asc') {
						$this->session['order'] = ' desc';
					}
					else {
						$this->session['order'] = ' asc';
					}
				}
				else {
					$this->session['sort_key'] = $this->request['sort_key'];
					$this->session['order'] = ' asc';
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
			$data = $this->df->selectByKeyword(array('name', 'directory', 'notes'), $this->keyword);
			if(is_array($data)) $data = array_merge($data, array('', '', '', '', ''));
			$this->dg->bind($data);
		}

		function _list_callback(&$array) {
			$row = &$array['row'];

			$open = &$row->getElementByName('open');
			$name = &$row->getElementByName('name');
			if($name->value) {
				$open->link.= $name->value . '/';
			}
			else {
				$li = &$row->getElementByName('data_list');
				$li->start_html = $li->empty_start_html;
				unset($li->elements);
			}
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
			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/project.css">');
			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/selectbox.css">');
			$this->html_header->appendProperty('script', '<script src="js/bframe_selectbox.js"></script>');

			// Show HTML header
			$this->showHtmlHeader();

			// Show HTML body
			echo $contents;
		}
	}
