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
		var left_table = document.querySelector('#left table');
		var right_table = document.querySelector('#right table');
		var add_button = document.getElementById('add-button');
		var del_button = document.getElementById('del-button');
		var keyword = document.getElementById('keyword');

		var left = new selectable_table(left_table);  
		var right = new selectable_table(right_table);  

		function selectable_table(table) {
			var last_row;

			for(let i=0; i < table.rows.length; i++) {
				table.rows[i].addEventListener('click', onClick);
			}

			function onClick(event) {
				if(event.button == 2) return;

				if(event.ctrlKey || event.metaKey) {
					last_row = this.rowIndex;
					this.classList.toggle('selected');
				}
				else if(event.shiftKey) {
					if(last_row != 'undefined') {
						if(last_row < this.rowIndex) {
							for(let i=last_row; i <= this.rowIndex; i++) {
								table.rows[i].classList.add('selected');
							}
						}
						else {
							for(let i=this.rowIndex; i <= last_row; i++) {
								table.rows[i].classList.add('selected');
							}
						}
					}
				}
				else {
					for(let i=0; i < table.rows.length; i++) {
						table.rows[i].classList.remove('selected');
					}
					last_row = this.rowIndex;
					this.classList.toggle('selected');
				}
			}
		}
	}
