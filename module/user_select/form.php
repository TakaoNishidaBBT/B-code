<?php
/*
 * B-code : Online Editor
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	class user_select_form extends B_AdminModule {
		function __construct() {
			parent::__construct(__FILE__);

			$this->df = new B_DataFile(B_USER_DATA, 'user');

			// Create DataGrid
			require_once('./config/list_config.php');
			$this->dg_left = new B_DataGrid($this->df, $list_config);
			$this->dg_right = new B_DataGrid($this->df, $list_config);
		}

		function open() {
			$this->setData();
		}

		function setData() {
			$data = $this->df->getAll();
			$this->dg_right->bind($data);

			$this->dg_left->bind($data);
		}

		function view() {
			// Start buffering
			ob_start();

			require_once('./view/view_form.php');

			// Get buffer
			$contents = ob_get_clean();

			// Send HTTP header
			$this->sendHttpHeader();

			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/user_select.css">');

			// Show HTML header
			$this->showHtmlHeader();

			// Show HTML body
			echo $contents;
		}
	}
