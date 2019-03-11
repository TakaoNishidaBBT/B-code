<?php
/*
 * B-code : Online Editor
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
				$obj = $this->form->getElementByName('domain');
				$obj->value = B_HTTP_HOST;
				$obj = $this->form->getElementByName('doc_root');
				$obj->value = B_DOC_ROOT_DIR;
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
				if($this->df->select('name', $param['value'])) {
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
			$this->new_thumbnail = false;

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

			if($this->new_thumbnail) exit;

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
					$message = __('created.');
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

			$this->thumb_dir = B_Util::getPath(B_THUMBDIR, $param['name']) . '/';

			$this->createThumbnailDir($param['name'], $param['directory']);

			return true;
		}

		function update() {
			$this->form->getValue($param);
			$param['update_user'] = $this->user_id;
			$param['update_datetime'] = time();
			$this->df->updateByPk($param['rowid'], $param);
			$this->df->save();

			// recreate thumbnail
			if($param['directory'] != $this->session['init_value']['directory']) {
				$this->thumb_dir = B_Util::getPath(B_THUMBDIR, $this->session['init_value']['name']) . '/';
				$this->removeThumbnail();
			}

			// rename thmubnail directory
			if($param['name'] != $this->session['init_value']['name']) {
				rename(B_THUMBDIR . $this->session['init_value']['name'], B_THUMBDIR . $param['name']);
			}

			$row = $this->df->selectByPk($param['rowid']);
			$this->session['init_value'] = $row;

			return true;
		}

		function delete() {
			$value = $this->df->selectByPk($this->post['rowid']);
			$this->project_name = $value['name'];

			$this->df->deleteByPk($this->post['rowid']);
			$this->df->save();

			$this->setView('resultView');

			$this->thumb_dir = B_Util::getPath(B_THUMBDIR, $value['name']) . '/';

			$this->removeThumbnail();

			if(file_exists($this->thumb_dir)) rmdir($this->thumb_dir);

			$this->setView('resultView');

			return true;
		}

		function createThumbnailDir($name, $directory) {
			if(!file_exists(B_THUMBDIR)) {
				mkdir(B_THUMBDIR);
			}
			if(file_exists($this->thumb_dir)) {
				$this->removeThumbnail();
			}
			else {
				mkdir($this->thumb_dir);
				chmod($this->thumb_dir, 0777);
			}
		}

		function removeThumbnail() {
			if(!file_exists($this->thumb_dir)) return;

			if($handle = opendir($this->thumb_dir)) {
				while(false !== ($file_name = readdir($handle))){
					if($file_name == '.' || $file_name == '..') continue;
					unlink($this->thumb_dir . $file_name);
				}
				closedir($handle);
			}
		}

		function createThumbnail_callback($node) {
			if($node->node_type == 'folder') return true;

			$this->createTumbnail_files++;
			$response['status'] = 'progress';
			$response['progress'] = round($this->createTumbnail_files / $this->total_files * 100);
			if($this->progress != $response['progress']) {
				$this->sendChunk(',' . json_encode($response));
				$this->progress = $response['progress'];
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
