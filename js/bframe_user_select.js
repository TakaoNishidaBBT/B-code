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
		var cancel_button = document.getElementById('cancel-button');

		var left = new selectable_table(left_table);
		var right = new selectable_table(right_table);

		add_button.addEventListener('click', add);
		del_button.addEventListener('click', del);

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

			function onKeydown(event) {
console.log('onKeydown');
			}

			this.getAllRow = function() {
				var collection = [];

				for(let i=0, index=0; i < table.rows.length; i++) {
					collection[i] = table.rows[i];
				}

				return collection;
			}

			this.getSelectedRow = function() {
				var collection = [];

				for(let i=0, index=0; i < table.rows.length; i++) {
					if(table.rows[i].classList.contains('selected')) {
						collection[index++] = table.rows[i];
					}
				}

				return collection;
			}

			this.addRow = function(collection) {
				for(let i=0; i < collection.length; i++) {
					for(var j=0, index=0; j < table.rows.length; j++) {
						if(collection[i].cells[1].innerHTML == table.rows[j].cells[1].innerHTML) {
							break;
						}
					}
					if(j == table.rows.length) {
						var tr = collection[i].cloneNode(true);
						tr.addEventListener('click', onClick);
						table.appendChild(tr);
					}
				}
			}

			this.removeSelectedRow = function() {
				var collection = [];

				for(let i=0; i < table.rows.length;) {
					if(table.rows[i].classList.contains('selected')) {
						table.deleteRow(i);
						continue;
					}
					i++;
				}
			}
		}

		function add(event) {
			var collection = right.getSelectedRow();
			left.addRow(collection);
			bframe.stopPropagation(event);
		}

		function del(event) {
			left.removeSelectedRow();
			bframe.stopPropagation(event);
		}

		this.getUserName = function() {
			var user_name = '';
			var collection = left.getAllRow();

			for(let i=0; i < collection.length; i++) {
				user_name += '<span>' + collection[i].cells[0].innerHTML + '</span>';
			}

			return user_name;
		}

		this.getUser = function() {
			var user = '';
			var collection = left.getAllRow();

			for(let i=0; i < collection.length; i++) {
				if(user) user += '/';
				user += collection[i].cells[1].innerHTML;
			}

			return user;
		}
	}
