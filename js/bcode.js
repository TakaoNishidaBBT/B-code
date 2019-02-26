/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	if(typeof bcode == 'undefined' || !bcode) {
		var bcode = {};
	}

	// -------------------------------------------------------------------------
	// class bcode
	// 
	// -------------------------------------------------------------------------
	bcode.activateModalWindow = function(a, w, h, func) {
		var p = 'width:' + w + ',height:' + h;
		a.setAttribute('data-param', p);

		top.bframe.modalWindow.activate(a, window);
		if(func) top.bframe.modalWindow.registerCallBackFunction(func);
	}

	bcode.setDirectory = function() {
		var nodes = bframe_tree.getCurrentNodes();
		var node_id = nodes[0].id.substr(1);

		if(node_id == 'root') node_id = '/';

		bcode.insertValue(window.frameElement.opener, 'directory', node_id);
		window.frameElement.deactivate();
	}

	bcode.insertValue = function(opener, target_id, target_value) {
		opener.bcode._insertValue(target_id, target_value);
	}

	bcode._insertValue = function(target_id, target_value) {
		var target = document.getElementById(target_id);
		if(!target) return;

		target.value = target_value;
	}

	bcode.clearText = function(target_id1, target_id2) {
		var target1 = document.getElementById(target_id1);
		if(target1 && target1.value) {
			target1.value = '';
			if(bframe.fireEvent) bframe.fireEvent(target1, 'change');
		}
		if(target_id2) {
			var target2 = document.getElementById(target_id2);
			if(target2 && target2.value) {
				target2.value = '';
				if(bframe.fireEvent) bframe.fireEvent(target2, 'change');
			}
		}
	}

	bcode.reloadTree = function() {
		if(typeof bframe_tree !== 'undefined') bframe_tree.reload();
	}

	bcode.refreshEditor = function(fname, module, page, method, mode, nocheck) {
		bframe.ajaxSubmit.clearCallBackFunctionAfter();
		bframe.ajaxSubmit.registerCallBackFunctionAfter(bcode.updateEditor);
		bframe.ajaxSubmit.registerCallBackFunctionAfter(bcode.resetEditFlag);
		bframe.ajaxSubmit.submit(fname, module, page, method, mode, nocheck);
	}

	bcode.registerEditor = function(fname, module, page, method, mode, nocheck) {
		bframe.ajaxSubmit.clearCallBackFunctionAfter();
		bframe.ajaxSubmit.registerCallBackFunctionAfter(bcode.resetEditFlag);
		bframe.ajaxSubmit.submit(fname, module, page, method, mode, nocheck);
	}

	bcode.setEditFlag = function() {
		var opener = window.frameElement.opener;
		if(typeof opener.bframe_tree !== 'undefined') opener.bframe_tree.setEditFlag();
	}

	bcode.resetEditFlag = function() {
		var opener = window.frameElement.opener;
		if(typeof opener.bframe_tree !== 'undefined') opener.bframe_tree.resetEditFlag();
	}

	bcode.changeFileName = function(value) {
		var node_id = document.getElementsByName('node_id');
		if(node_id[0]) {
			var arr = node_id[0].value.split('/');
			arr[arr.length-1] = value;
			node_id[0].value = arr.join('/');

			return node_id[0].value;
		} 
	}

	bcode.replaceFilePath = function(before, after) {
		var node_id = document.getElementsByName('node_id');
		if(node_id[0]) {
			node_id[0].value = node_id[0].value.split(before).join(after);
			return node_id[0].value;
		} 
	}

	bcode.setFileName = function(f_name) {
		top.bcode._setFileName('file_name', f_name);
	}

	bcode._setFileName = function(target_id, target_value) {
		var target = document.getElementById(target_id);
		if(!target) return;

		target.innerHTML = target_value;
	}

	bcode.setProperty = function(module) {
		bframe.ajaxSubmit.registerCallBackFunctionAfter(window.frameElement.deactivate);
		bframe.ajaxSubmit.submit('F1', module, 'property', 'register', '', true);
	}

	bcode.identicon = function() {
		var salt = 'bcode';
		var user_id = document.getElementById('user-id');
		var big_identicon = document.getElementById('big-identicon');
		var small_identicon = document.getElementById('small-identicon');

		hash = sha256(user_id.innerHTML + salt);

		options = {
			background: [255, 255, 255, 255],
			margin: 0.2,
			size: 128,
			format: 'svg'
		};
		var data = new Identicon(hash, options).toString(true);
		if(small_identicon) small_identicon.innerHTML = '<img src="data:image/svg+xml;utf8,' + data + '">';
		if(big_identicon) big_identicon.innerHTML = '<img src="data:image/svg+xml;utf8,' + data + '">';
	}

	bcode.mousedownBody = function() {
		bframe.fireEvent(document.body, 'mousedown');
	}

	bcode.setNavi = function() {
		setTimeout(bcode._setNavi, 10);
	}

	bcode._setNavi = function() {
		if(parent.bframe.gnavi && location.search) {
			parent.bframe.gnavi.set(location.search.substr(1));
		}
	}
