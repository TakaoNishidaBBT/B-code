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

	bstudio.openProject = function() {
		var nodes = bframe_tree.getCurrentNodes();
console.log('nodes', nodes, nodes[0].id.substr(1));
		var input = document.getElementById('node_id');
		input.value = nodes[0].id.substr(1);
console.log('input', input);
console.log('bframe.ajaxSubmit', bframe.ajaxSubmit);
		bframe.ajaxSubmit.registerCallBackFunctionAfter(bstudio._openProject);
		bframe.ajaxSubmit.submit('F1', 'project', 'tree', 'open', '', true);
	}

	bstudio._openProject = function() {
console.log('_openProject');
		var frame = bframe.getFrameByName(top, 'main');
		if(typeof frame.bframe_tree !== 'undefined') frame.bframe_tree.reload();
		window.frameElement.deactivate();
	}

	bstudio.reloadTree = function() {
		if(typeof bframe_tree !== 'undefined') bframe_tree.reload();
	}

	bstudio.registerEditor = function(fname, module, page, method, mode, nocheck) {
		var opener = window.frameElement.opener;
		if(typeof opener.bframe_tree !== 'undefined') {
			bframe.ajaxSubmit.removeCallBackFunctionAfter(opener.bframe_tree.reloadTree);
			bframe.ajaxSubmit.registerCallBackFunctionAfter(opener.bframe_tree.reloadTree);
		}
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

	bstudio.setProperty = function(module) {
		bframe.ajaxSubmit.registerCallBackFunctionAfter(window.frameElement.deactivate);
		bframe.ajaxSubmit.submit('F1', module, 'property', 'register', '', true);
	}
