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
	bstudio.reloadTree = function() {
		if(typeof bframe_tree !== 'undefined') bframe_tree.reload();
	}

	bstudio.registerEditor = function(fname, module, page, method, mode, nocheck) {
		if(window.opener) {
			bframe.ajaxSubmit.removeCallBackFunctionAfter(window.opener.bstudio.reloadTree);
			bframe.ajaxSubmit.registerCallBackFunctionAfter(window.opener.bstudio.reloadTree);
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
