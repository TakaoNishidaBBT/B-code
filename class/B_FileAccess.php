<?php
	class B_FileAccess {
		var $fp;
		var $file_name;

		function __construct($file_name) {
			$this->file_name = $file_name;
			if(file_exists($this->file_name)) {
				$this->fp = fopen($this->file_name, 'rb');
			    $serializedString = fread($this->fp, 200000);
	    		$this->value = unserialize($serializedString);
				fclose($this->fp);
				unset($this->fp);
			}
		}

		function get($key) {
			return $this->value[$key];
		}

		function getAll() {
			return $this->value;
		}

		function set($key, $value) {
			$this->value[$key] = $value;
		}

		function setAll($array) {
			foreach($array as $key => $value) {
				$this->value[$key] = $value;
			}
		}

		function save() {
			$this->fp = fopen($this->file_name, 'w');
	        fwrite($this->fp, serialize($this->value));
			fclose($this->fp);
		}
	}
