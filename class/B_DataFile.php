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
	    		$this->value = unserialize($serializedString);
				fclose($this->fp);
				unset($this->fp);
			}

			$this->table = $table_name;
			$this->config = ${$g_data_set}['table'][$this->table];
			$this->getPk();
		}

		function getPk() {
			foreach($this->config as $key => $val) {
				if($val[2] == '1') {
					$this->pk = $key;
					$this->pk_length = $val[1];
				}
			}
		}

		function getNextPkValue($pk) {
			$max = 0;
			if(is_array($this->value)) {
				foreach($this->value as $val) {
					$max = max($val[$pk], $max);
				}
			}
			$max = str_pad($max+1, $this->pk_length, '0', STR_PAD_LEFT);
			return $max;
		}

		function get($key) {
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

		function selectByPk($index) {
			return $this->value[$index];
		}

		function insert($value) {
			$this->max = $this->getNextPkValue($this->pk);
			if($param = $this->getInsertValue($value)) {
				$this->value[$this->max] = $param;
			}

			return $this->max;
		}

		function update($where, $value) {
			if(!is_array($where)) return;

			foreach($this->value as $key => $row) {
				if($row[$where[0]] == $where[1]) {
					$this->value[$key] = $value;
				}
			}
		}

		function updateByPk($index, $value) {
			$this->value[$index] = $this->getUpdateValue($index, $value);
		}

		function deleteByPk($index) {
			unset($this->value[$index]);
		}

		function getInsertValue($value) {
			foreach($this->config as $key => $val) {
				if($val[2] == '1') {
					$param[$key] = str_pad($this->max, $val[1], '0', STR_PAD_LEFT);
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

		function getUpdateValue($index, $value) {
			foreach($this->config as $key => $val) {
				if(array_key_exists($key, $value)) {
					$param[$key] = $value[$key];
				}
				else {
					$param[$key] = $this->value[$index][$key];
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
