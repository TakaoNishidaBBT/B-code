/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	if(typeof bstudio == 'undefined' || !bstudio) {
		var bstudio = {};
	}

	// -------------------------------------------------------------------------
	// class bstudio
	// 
	// -------------------------------------------------------------------------
	bstudio.activateModalWindow = function(a, w, h, func) {
		var p = 'width:' + w + ',height:' + h;
		a.setAttribute('data-param', p);

		top.bframe.modalWindow.activate(a, window);
		if(func) top.bframe.modalWindow.registerCallBackFunction(func);
	}

	bstudio.setDirectory = function() {
		var nodes = bframe_tree.getCurrentNodes();
		var node_id = nodes[0].id.substr(1);

		if(node_id == 'root') node_id = '/';

		bstudio.insertValue(window.frameElement.opener, 'directory', node_id);
		window.frameElement.deactivate();
	}

	bstudio.insertValue = function(opener, target_id, target_value) {
		opener.bstudio._insertValue(target_id, target_value);
	}

	bstudio._insertValue = function(target_id, target_value) {
		var target = document.getElementById(target_id);
		if(!target) return;

		target.value = target_value;
	}

	bstudio.clearText = function(target_id1, target_id2) {
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

	bstudio.reloadTree = function() {
		if(typeof bframe_tree !== 'undefined') bframe_tree.reload();
	}

	bstudio.refreshEditor = function(fname, module, page, method, mode, nocheck) {
		bframe.ajaxSubmit.clearCallBackFunctionAfter();
		bframe.ajaxSubmit.registerCallBackFunctionAfter(bstudio.updateEditor);
		bframe.ajaxSubmit.registerCallBackFunctionAfter(bstudio.resetEditFlag);
		bframe.ajaxSubmit.submit(fname, module, page, method, mode, nocheck);
	}

	bstudio.registerEditor = function(fname, module, page, method, mode, nocheck) {
		bframe.ajaxSubmit.clearCallBackFunctionAfter();
		bframe.ajaxSubmit.registerCallBackFunctionAfter(bstudio.resetEditFlag);
		bframe.ajaxSubmit.submit(fname, module, page, method, mode, nocheck);
	}

	bstudio.setEditFlag = function() {
		var opener = window.frameElement.opener;
		if(typeof opener.bframe_tree !== 'undefined') opener.bframe_tree.setEditFlag();
	}

	bstudio.resetEditFlag = function() {
		var opener = window.frameElement.opener;
		if(typeof opener.bframe_tree !== 'undefined') opener.bframe_tree.resetEditFlag();
	}

	bstudio.changeFileName = function(value) {
		var node_id = document.getElementsByName('node_id');
		if(node_id[0]) {
			var arr = node_id[0].value.split('/');
			arr[arr.length-1] = value;
			node_id[0].value = arr.join('/');

			return node_id[0].value;
		} 
	}

	bstudio.replaceFilePath = function(before, after) {
		var node_id = document.getElementsByName('node_id');
		if(node_id[0]) {
			node_id[0].value = node_id[0].value.split(before).join(after);
			return node_id[0].value;
		} 
	}

	bstudio.setFileName = function(f_name) {
		top.bstudio._setFileName('file_name', f_name);
	}

	bstudio._setFileName = function(target_id, target_value) {
		var target = document.getElementById(target_id);
		if(!target) return;

		target.innerHTML = target_value;
	}

	bstudio.setProperty = function(module) {
		bframe.ajaxSubmit.registerCallBackFunctionAfter(window.frameElement.deactivate);
		bframe.ajaxSubmit.submit('F1', module, 'property', 'register', '', true);
	}

	bstudio.identicon = function() {
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
		big_identicon.innerHTML = small_identicon.innerHTML = '<img src="data:image/svg+xml;utf8,' + data + '">';
	}
