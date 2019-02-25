<?php
/*
 * B-frame : php web application framework
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	// -------------------------------------------------------------------------
	// class B_DataFile
	// 
	// -------------------------------------------------------------------------
	class B_DataFile {
		function __construct($file_name, $table_name) {
			global $g_data_set, ${$g_data_set};
//$this->log = new B_Log(B_LOG_FILE);

			$this->file_name = $file_name;

			if(file_exists($this->file_name)) {
				$this->fp = fopen($this->file_name, 'rb');
			    $serializedString = fread($this->fp, 200000);
	    		$this->data = unserialize($serializedString);
				fclose($this->fp);
				unset($this->fp);
			}

			$this->table = $table_name;
			$this->config = ${$g_data_set}['table'][$this->table];
			$this->max = 0;
			if(is_array($this->data) && count($this->data)) $this->max = max(array_keys($this->data));
		}

		function get($index) {
			return $this->data[$index];
		}

		function getAll() {
			return $this->data;
		}

		function select($field, $value) {
			foreach($this->data as $key => $row) {
				if($row[$field] == $value) return $row;
			}
		}

		function selectByPk($index) {
			return $this->data[$index];
		}

		function selectByKeyword($fields, $keyword) {
			foreach($this->data as $key => $row) {
				foreach($fields as $field) {
					if(preg_match('/' . $keyword . '/', $row[$field])) {
						$collection[] = $row;
						break;
					}
				}
			}

			return $collection;
		}

		function insert($value) {
			if($param = $this->getInsertValue($value)) {
				$this->max++;
				$param['rowid'] = $this->max;
				$this->data[$this->max] = $param;
			}

			return $this->max;
		}

		function updateByPk($index, $value) {
			$this->data[$index] = $this->getUpdateValue($index, $value);
		}

		function deleteByPk($index) {
			unset($this->data[$index]);
		}

		function getInsertValue($value) {
			foreach($this->config as $key => $val) {
				if(array_key_exists($key, $value)) {
					$param[$key] = $value[$key];
				}
				else {
					$param[$key] = '';
				}
			}
			return $param;
		}

		function getUpdateValue($index, $value) {
			foreach($this->config as $key => $val) {
				if(array_key_exists($key, $value) && $key != 'rowid') {
					$param[$key] = $value[$key];
				}
				else {
					$param[$key] = $this->data[$index][$key];
				}
			}
			$param['rowid'] = $this->data[$index]['rowid'];
			return $param;
		}

		function save() {
			$this->fp = fopen($this->file_name, 'w');
	        fwrite($this->fp, serialize($this->data));
			fclose($this->fp);
		}
	}
