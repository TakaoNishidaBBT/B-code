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
$this->log = new B_Log(B_LOG_FILE);
			$this->file_name = $file_name;
			if(file_exists($this->file_name)) {
				$this->fp = fopen($this->file_name, 'rb');
			    $serializedString = fread($this->fp, 200000);
	    		$this->value = unserialize($serializedString);
				fclose($this->fp);
				unset($this->fp);
			}
			if(is_array($this->value) && array_keys($this->value)) $this->max_key = max(array_keys($this->value));
			if(!$this->max_key) $this->max_key = 0;
			$this->max_key++;

			$this->table = $table_name;
			$this->config = ${$g_data_set}['table'][$this->table];
		}

		function get($key) {
$this->log->write('key', $key, $this->value[$key]);
			return $this->value[$key];
		}

		function getAll() {
			return $this->value;
		}

		function select($field, $value) {
			foreach($this->value as $key => $row) {
				if($row[$field] == $value) return $row;
			}
		}

		function insert($value) {
			if($param = $this->checkInsertValue($value)) {
				$this->value[$this->max_key] = $param;
				$this->max_key++;
				return param;
			}
		}

		function update($key, $value) {
			$this->value[$key] = $value;
		}

		function delete($key) {
			unset($this->value[$key]);
		}

		function checkInsertValue($value) {
			foreach($this->config as $key => $val) {
				if($val[2] == '1') {
					$param[$key] = str_pad($this->max_key, $val[1], '0', STR_PAD_LEFT);
					continue;
				}
				if(array_key_exists($key, $value) && $val[2] != '1') {
					$param[$key] = $value[$key];
				}
				else {
					$param[$key] = '';
				}
			}
			return $param;
		}

		function setAll($array) {
			foreach($array as $key => $row) {
				$this->value[$key] = $row;
			}
		}

		function save() {
			$this->fp = fopen($this->file_name, 'w');
	        fwrite($this->fp, serialize($this->value));
			fclose($this->fp);
		}
	}
