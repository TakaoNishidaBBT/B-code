<?php
/*
 * B-square : Event Management and Registration System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
*/
	class project_list extends B_AdminModule {
		function __construct() {
			parent::__construct(__FILE__);

			require_once('config/list_config.php');
			$this->list = new B_DataGrid(null, $list_config, $this->user_auth);

			// コールバック設定
//			$this->list->setCallBack($this, '_list_callback');

			// データバインド
			$this->list->setPage(1);
//			$this->list->bind();

			$this->title = __('Project');
		}

		function _list_callback($array) {
			$row = $array['row'];
	
			$a = $row->getElementByName('event_link');
			$node_name = $row->getElementByName('node_name');
			$event_id = $row->getElementByName('event_id');
			$a->link = B_CURRENT_ROOT . $node_name->value . '/' . B_ADMIN;
			$a->target = $node_name->value;
			$a->title = 'event_id : ' . $event_id->value . ' directory : ' . $node_name->value;
		}

		function view() {
			// HTTPヘッダー出力
			$this->sendHttpHeader();

			// HTML ヘッダー出力
			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/project.css" type="text/css">');
			$this->showHtmlHeader();

			require_once('./view/view_list.php');
		}
	}
