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
				$fp = fopen($this->file_name, 'rb');
			    $serializedString = fread($fp, 200000);
	    		$this->data = unserialize($serializedString);
				fclose($fp);
				unset($fp);
			}

			$this->table = $table_name;
			$this->config = ${$g_data_set}['table'][$this->table];
			$this->max = 0;
			if(is_array($this->data) && count($this->data)) $this->max = max(array_keys($this->data));
		}

		function setSortKey($key) {
			$this->sort_key = $key;
		}

		function setSortOrder($order) {
			$this->sort_order = $order;
		}

		function get($index) {
			return $this->data[$index];
		}

		function getAll() {
			return $this->data;
		}

		function select($field, $value) {
			if(!is_array($this->data)) return;

			foreach($this->data as $key => $row) {
				if($row[$field] == $value) return $row;
			}
		}

		function selectByPk($index) {
			return $this->data[$index];
		}

		function selectByKeyword($fields, $keyword) {
			if(!is_array($this->data)) return;

			foreach($this->data as $key => $row) {
				foreach($fields as $field) {
					if(preg_match('/' . $keyword . '/', $row[$field])) {
						$collection[] = $row;
						break;
					}
				}
			}

			$this->sort($collection);

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

		function sort(&$data) {
			if(!is_array($data)) return;
			if(!$this->sort_key) return;
			if(!$data[0][$this->sort_key]) return;
			uasort($data, array($this, '_sort_callback'));
		}

		function _sort_callback($a, $b) {
			if($this->sort_order == 'asc') {
				return ($a[$this->sort_key] < $b[$this->sort_key]) ? -1 : 1;
			}
			else {
				return ($a[$this->sort_key] > $b[$this->sort_key]) ? -1 : 1;
			}
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
			if(!file_exists(dirname($this->file_name))) {
				mkdir(dirname($this->file_name));
				chmod(dirname($this->file_name), 0777);
			}

			$fp = fopen($this->file_name, 'w');
	        fwrite($fp, serialize($this->data));
			fclose($fp);
		}
	}
