<?php
/*
 * B-frame : php web application framework
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	// -------------------------------------------------------------------------
	// class B_DirNode
	// 
	// -------------------------------------------------------------------------
	class B_DirNode {
		function __construct($dir, $path, $open_nodes=null, $parent=null, $expand_level=0, $level=0, $thumb_info=null) {
			if(!$path) return;

			$this->dir = $dir;
			$this->path = $path == 'root' ? '' : $path;
			$this->node_id = $path == '/' ? 'root' : $path;

			$this->fullpath = __getPath($dir, $this->path);
			$this->file_name = basename($this->fullpath);

			if($parent) {
				$this->parent = $parent;
			}
			else if(!$this->isRoot()) {
				$dir = dirname($this->path) == '.' ? '' : dirname($this->path);
				$this->parent = new B_DirNode($this->dir, str_replace('\\', '/', $dir), null, null);
				$this->parent->addNodes($this);
			}

			$this->level = $level;
			$this->node_count = 0;

			if(!file_exists($this->fullpath)) return;

			$this->update_datetime_u = filemtime($this->fullpath);
			$this->update_datetime_t = date('Y/m/d H:i', filemtime($this->fullpath));

			if(!is_dir($this->fullpath)) {
				return;
			}

			$this->node_class = 'folder';
			if($this->node_id == 'root') {
				$this->node_type = 'root';
			}
			else {
				$this->node_type = 'folder';
			}

			$handle = opendir($this->fullpath);
			if(!$handle) return;

			while(false !== ($file_name = readdir($handle))) {
				if($file_name == '.' || $file_name == '..') continue;

				$this->node_count++;

				if(is_dir(__getPath($this->fullpath, $file_name))) {
					$this->folder_count++;
				}
				else {
					continue;
				}
				if((is_array($open_nodes) && $open_nodes[$this->node_id]) || ($expand_level === 'all' || $level < $expand_level)) {
					$object = new B_DirNode($this->dir, __getPath($this->path, $file_name), $open_nodes, $this, $expand_level, $level+1, $thumb_info);
					$this->addNodes($object);
				}
			}

			// sort by file-name (for tree pain)
			if(is_array($this->node)) usort($this->node, array($this, '_sort_name_callback'));

			closedir($handle);
		}

		function isRoot() {
			$fullpath = $this->fullpath;
			$dir = $this->dir;
			if(substr($fullpath, -1) == '/') $fullpath = substr($fullpath, 0, -1);
			if(substr($dir, -1) == '/') $dir = substr($dir, 0, -1);

			return $fullpath == $dir;
		}

		function addNodes(&$object) {
			$this->node[] = $object;
		}

		function removeNodes(&$object) {
			unset($this->node[array_search($object, $this->node)]);
		}

		function setConfig($config) {
			foreach($config as $key => $value) {
				$this->$key = $value;
			}
		}

		function sort() {
			if(!is_array($this->node)) return;

			uasort($this->node, array($this,'_sort_name_callback'));

			$i=0;
			foreach($this->node as &$value) {
				$value->order = $i++;
			}
			ksort($this->node);
		}

		function _sort_name_callback($a, $b) {
			$key = $this->sort_key ? $this->sort_key : 'file_name';
			$order = $this->sort_order ? $this->sort_order : 'asc';

			if($a->node_type == $b->node_type) {
				if($order == 'asc') {
					$ret = ($a->$key < $b->$key) ? -1 : 1;
				}
				else {
					$ret = ($a->$key >= $b->$key) ? -1 : 1;
				}
			}
			else {
				if($order == 'asc') {
					$ret = ($a->node_type > $b->node_type) ? -1 : 1;
				}
				else {
					$ret = ($a->node_type <= $b->node_type) ? -1 : 1;
				}
			}
			return $ret;
		}

		function setSortKey($sort_key) {
			$this->sort_key = $sort_key;
		}

		function setSortOrder($sort_order) {
			$this->sort_order = $sort_order;
		}

		function getHtml() {
			if(isset($this->start_html)) {
				$html.= $this->start_html;
			}

			if(isset($this->end_html)) {
				$html.= $this->end_html;
			}

			return $html;
		}

		function walk($obj, $method) {
			if(method_exists($obj, $method)) {
				$ret = $obj->$method($this);
			}

			if($ret && is_array($this->node)) {
				foreach(array_keys($this->node) as $key) {
					$this->node[$key]->walk($obj, $method);
				}
			}
			return;
		}

		function getNodeList($node_id='', $category='', $path='', $disp_seq=0) {
			$list = $this->_getNodeList($node_id, $category, $path, $disp_seq);

			if(is_array($this->node)) {
				if($this->sort_key) $this->sort();

				$disp_seq=0;
				foreach(array_keys($this->node) as $key) {
					$child_list[] = $this->node[$key]->getNodeList($node_id, $category, $list['path'], $disp_seq++);
				}
				$list['children'] = $child_list;
			}
			return $list;
		}

		function _getNodeList($node_id, $category, $path, $disp_seq) {
			$list['node_id'] = mb_convert_encoding($this->node_id, 'utf8', B_SYSTEM_FILENAME_ENCODE);
			$list['node_type'] = $this->node_type;
			$list['node_class'] = $this->node_class;
			$list['node_name'] = mb_convert_encoding($this->file_name, 'utf8', B_SYSTEM_FILENAME_ENCODE);
			$list['thumbnail_image_path'] = mb_convert_encoding($this->thumbnail_image_path, 'utf8', B_SYSTEM_FILENAME_ENCODE);
			$list['node_count'] = $this->node_count;
			$list['folder_count'] = $this->folder_count;
			$list['create_datetime_u'] = $this->update_datetime_u;
			$list['create_datetime_t'] = $this->update_datetime_t;
			$list['update_datetime_u'] = $this->update_datetime_u;
			$list['update_datetime_t'] = $this->update_datetime_t;
			if($this->node_type != 'folder') {
				$list['file_size'] = $this->file_size;
				$list['human_file_size'] = B_Util::human_filesize($this->file_size, 'K');
			}
			if($this->image_size) {
				$list['image_size'] = $this->image_size;
				$list['human_image_size'] = $this->human_image_size;
			}
			if($this->node_id == $node_id) {
				$list[$category] = true;
			}
			$list['path'] = mb_convert_encoding($this->path, 'utf8', B_SYSTEM_FILENAME_ENCODE);
			$list['disp_seq'] = $disp_seq;
			$list['order'] = $this->order;
			return $list;
		}

		function rename($old_name, $new_name) {
			if($this->node_id === $old_name) {
				$ret = rename(__getPath($this->dir, $old_name), __getPath($this->dir, $new_name));
				if(!$ret) return false;

				$this->node_id = $new_name;
				$this->path = $new_name;
				$this->thumbnail_image_path = $this->getThumbnailImgPath($this->path);
			}
			else {
				$this->path = __getPath($this->parent->path, $this->file_name);
				$this->node_id = __getPath($this->parent->path, $this->file_name);
				$this->thumbnail_image_path = $this->getThumbnailImgPath($this->path);
			}

			$this->fullpath = __getPath($this->dir, $this->path);

			if(is_array($this->node)) {
				foreach(array_keys($this->node) as $key) {
					$ret = $this->node[$key]->rename($old_name, $new_name);
					if(!$ret) return false;
				}
			}
			return true;
		}

		function copy($dest, &$new_node_name, &$data, &$index, $recursive=false, $callback=null) {
			$destination = __getPath($this->dir, $dest);
			if($this->isMyChild($destination)) {
				$this->error_no = 1;
				return false;
			}
			if(file_exists($this->fullpath)) {
				if(is_dir($this->fullpath)) {
					$new_node_name = $this->getNewNodeName($destination, $this->file_name, 'copy');
					$dest = __getPath($dest, $new_node_name);
					$destination = __getPath($this->dir, $dest);
					if(!file_exists($destination)) {
						mkdir($destination);
						chmod($destination, 0777);
					}
				}
				else {
					$new_node_name = $this->getNewNodeName($destination, $this->file_name, 'copy');
					$dest = __getPath($dest, $new_node_name);
					$destination = __getPath($this->dir, $dest);
					copy($this->fullpath, $destination);
					chmod($destination, 0777);

					// copy thumbnail
					if($this->thumb && file_exists(B_UPLOAD_THUMBDIR . $this->thumb)) {
						$file_info = pathinfo($this->path);
						$index++;
						$thumbnail_file_path = B_UPLOAD_THUMBDIR . str_pad($index, 10, '0', STR_PAD_LEFT) . '.' . $file_info['extension'];
						copy(B_UPLOAD_THUMBDIR . $this->thumb, $thumbnail_file_path);
						chmod($thumbnail_file_path, 0777);
						$thumbnail_image_path = $this->getThumbnailImgPath($dest);
						$info = B_Util::pathinfo($thumbnail_file_path);
						$data[$thumbnail_image_path] = $info['basename'];
					}
				}
				if($callback) {
					$this->callBack($callback);
				}
			}
			if($recursive && is_array($this->node)) {
				foreach(array_keys($this->node) as $key) {
					$this->node[$key]->_copy($dest, $data, $index, $recursive, $callback);
				}
			}

			return true;
		}

		function _copy($dest, &$data, &$index, $recursive=false, $callback=null) {
			$destination = __getPath($this->dir, $dest);
			if(file_exists($this->fullpath)) {
				if(is_dir($this->fullpath)) {
					$dest = __getPath($dest, $this->file_name);
					$destination = __getPath($this->dir, $dest);
					if(!file_exists($destination)) {
						mkdir($destination);
						chmod($destination, 0777);
					}
				}
				else {
					$dest = __getPath($dest, $this->file_name);
					$destination = __getPath($this->dir, $dest);
					copy($this->fullpath, $destination);
					chmod($destination, 0777);

					// copy thumbnail
					if($this->thumb && file_exists(B_UPLOAD_THUMBDIR . $this->thumb)) {
						$file_info = pathinfo($this->path);
						$index++;
						$thumbnail_file_path = B_UPLOAD_THUMBDIR . str_pad($index, 10, '0', STR_PAD_LEFT) . '.' . $file_info['extension'];
						copy(B_UPLOAD_THUMBDIR . $this->thumb, $thumbnail_file_path);
						chmod($thumbnail_file_path, 0777);
						$thumbnail_image_path = $this->getThumbnailImgPath($dest);
						$info = B_Util::pathinfo($thumbnail_file_path);
						$data[$thumbnail_image_path] = $info['basename'];
					}
				}
				if($callback) {
					$this->callBack($callback);
				}
			}
			if($recursive && is_array($this->node)) {
				foreach(array_keys($this->node) as $key) {
					$this->node[$key]->_copy($dest, $data, $index, $recursive, $callback);
				}
			}

			return true;
		}

		function fileCopy($destination, $recursive=false, $callback=null) {
			if(file_exists($this->fullpath)) {
				if(is_dir($this->fullpath)) {
					$destination = __getPath($destination, $this->file_name);
					if(!file_exists($destination)) {
						mkdir($destination);
						chmod($destination, 0777);
					}
				}
				else {
					$destination = __getPath($destination, $this->file_name);
					copy($this->fullpath, $destination);
					chmod($destination, 0777);
				}
				if($callback) {
					$this->callBack($callback);
				}
			}
			if($recursive && is_array($this->node)) {
				foreach(array_keys($this->node) as $key) {
					$this->node[$key]->fileCopy($destination, $recursive, $callback);
				}
			}
			return true;
		}

		function move($source) {
			if($this->isMyParent($source->fullpath)) {
				$this->error_no = 1;
				return false;
			}

			try {
				if(file_exists($this->fullpath)) {
					if(is_dir($this->fullpath)) {
						$info = pathinfo($source->fullpath);
						$source->parent->removeNodes($source);
						$this->addNodes($source);
						$source->parent = $this;
						$this->rename($source->node_id, __getPath($this->path, $source->file_name));
						return true;
					}
				}
			}
			catch(Exception $e) {
				return false;
			}
		}

		function remove() {
			if(is_array($this->node)) {
				foreach(array_keys($this->node) as $key) {
					$this->node[$key]->remove();
				}
			}
			if(is_dir($this->fullpath)) {
				usleep(2000);
				rmdir($this->fullpath);
			}
			else if(file_exists($this->fullpath)) {
				unlink($this->fullpath);
				if($this->dir == B_UPLOAD_DIR && file_exists(B_UPLOAD_THUMBDIR . $this->thumb) && !is_dir(B_UPLOAD_THUMBDIR . $this->thumb)) {
					unlink(B_UPLOAD_THUMBDIR . $this->thumb);
				}
			}

			return true;
		}

		function removeChild() {
			if(is_array($this->node)) {
				foreach(array_keys($this->node) as $key) {
					$this->node[$key]->remove();
				}
			}
			return true;
		}

		function createFolder($node_name, &$new_node_id) {
			if(!is_dir($this->fullpath)) {
				return false;
			}

			for($i=2, $folder = $node_name; file_exists(__getPath($this->fullpath, $folder)); $folder = $node_name . $extend) {
				$extend = '(' . $i++ . ')';
			}
			$folder_name = __getPath($this->fullpath, $folder);
			$ret = mkdir($folder_name);
			chmod($folder_name, 0777);

			$new_node_id = __getPath($this->path, $folder);

			return $ret;
		}

		function getNewNodeName($dir, $default_name, $mode) {
			$info = pathinfo($default_name);

			for($i=2, $node_name = $info['filename'];; $node_name = $prefix . $info['filename'] . $extend) {
				if($info['extension']) {
					if(!file_exists(__getPath($dir, $node_name) . '.' . $info['extension'])) break;
				}
				else {
					if(!file_exists(__getPath($dir, $node_name))) break;
				}
				switch($mode) {
				case 'insert':
					$extend = '(' . $i++ . ')';
					break;

				case 'copy':
					if($prefix) {
						$extend = '(' . $i++ . ')';
					}
					else {
						$prefix.= 'copy_of_';
					}
					break;

				case 'arias':
					$prefix.= 'arias_of_';
					break;
				}
			}
			if($info['extension']) {
				return $node_name . '.' . $info['extension'];
			}
			else {
				return $node_name;
			}
		}

		function isMyChild($path) {
			$path_array = explode('/', $path);

			for($i=0, $dir=$path; $i<count($path_array); $dir = dirname($dir), $i++) {
				if($this->fullpath == $dir) {
					return true;
				}
			}

			return false;
		}

		function isMyParent($path) {
			$path_array = explode('/', $path);

			for($i=0, $dir=$this->fullpath; $i<count($path_array); $dir = dirname($dir), $i++) {
				if($path == $dir) {
					return true;
				}
			}
			return false;
		}

		function parentPath() {
			if($this->parent) return $this->parent;
			$parent_path = dirname($this->path);
			if($parent_path == '\\') $parent_path = 'root';

			return $parent_path;
		}

		function getRootNode() {
			for($node = $this; $node; $node = $node->parent) {
				if($node->isRoot()) return $node;
			}
			return $this;
		}

		function getNodeById($node_id) {
			if($this->node_id == $node_id) {
				return $this;
			}
			if(is_array($this->node)) {
				foreach(array_keys($this->node) as $key) {
					$ret = $this->node[$key]->getNodeById($node_id);
					if($ret) return $ret;
				}
			}
		}

		function nodeCount($file_only=false, $except_array=null) {
			if($this->file_name && is_array($except_array) && array_key_exists($this->file_name, $except_array)) return;

			if(is_array($this->node)) {
				foreach(array_keys($this->node) as $key) {
					$count += $this->node[$key]->nodeCount($file_only, $except_array);
				}
			}
			if(!$file_only || $this->node_type == 'file') {
				$mynode = 1;
			}

			return $count + $mynode;
		}

		function callBack($call_back) {
			if(is_array($call_back)) {
				$obj = $call_back['obj'];
				$method = $call_back['method'];
				if(method_exists($obj, $method)) {
					$ret = $obj->$method($this);
				}
			}
			else {
				$ret = call_user_func_array($call_back, array($this));
			}

			return $ret;
		}

		function getErrorNo() {
			return $this->error_no;
		}
	}
