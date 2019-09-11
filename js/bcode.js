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

	bcode.setDocRoot = function() {
		var nodes = bframe_tree.getCurrentNodes();
		var node_id = nodes[0].id.substr(1);

		if(node_id == 'root') node_id = '/';

		bcode.insertValue(window.frameElement.opener, 'doc_root', node_id);
		window.frameElement.deactivate();
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
		if(bframe.fireEvent) bframe.fireEvent(target, 'change');
	}

	bcode.setUser = function() {
		var user_name = bframe.userSelect.getUserName();
		bcode.insertHTML(window.frameElement.opener, 'user_name', user_name);

		var user = bframe.userSelect.getUser();
		bcode.insertValue(window.frameElement.opener, 'user', user);

		window.frameElement.deactivate();
	}

	bcode.insertHTML = function(opener, target_id, target_value) {
		opener.bcode._insertHTML(target_id, target_value);
	}

	bcode._insertHTML = function(target_id, target_value) {
		var target = document.getElementById(target_id);
		if(!target) return;

		target.innerHTML = target_value;
	}

	bcode.clearText = function() {
		// arguments
		for(var i=0; i < arguments.length; i++) {
			var target = document.getElementById(arguments[i]);
			if(target) {
				if(target.value) {
					target.value = '';
				}
				else {
					target.innerHTML = '';
				}
				if(bframe.fireEvent) bframe.fireEvent(target, 'change');
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
