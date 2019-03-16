/*
 * B-frame : php web application framework
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	bframe.addEventListener(window, 'load', bframeUserSelectInit);

	function bframeUserSelectInit() {
		if(!bframe.userSelect) {
			bframe.userSelect = new bframe.user_select();
		}
	}

	// -------------------------------------------------------------------------
	// class bframe.user_select
	// 
	// -------------------------------------------------------------------------
	bframe.user_select = function() {
		var self = this;
		var left_table = document.getElementById('left-table');
		var right_table = document.getElementById('right-table');
		var add_button = document.getElementById('add-button');
		var del_button = document.getElementById('del-button');
		var keyword = document.getElementById('keyword');

	}
