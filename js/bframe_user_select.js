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
		document.body.addEventListener('click', reset);

		function selectable_table(table) {
			var last_row;

			document.addEventListener('keydown', onKeydown);

			for(let i=0; i < table.rows.length; i++) {
				table.rows[i].addEventListener('click', onClick);
			}

			function onClick(event) {
				if(event.button == 2) return;

				resetSelectionToSelected(table);

				for(let i=0; i < table.rows.length; i++) {
					table.rows[i].classList.remove('selected');
				}

				if(event.ctrlKey || event.metaKey) {
					this.classList.toggle('current');
				}
				else if(event.shiftKey) {
					if(last_row != 'undefined' || last_row == '') {
						if(last_row < this.rowIndex) {
							for(let i=last_row; i <= this.rowIndex; i++) {
								table.rows[i].classList.add('current');
							}
						}
						else {
							for(let i=this.rowIndex; i <= last_row; i++) {
								table.rows[i].classList.add('current');
							}
						}
					}
				}
				else {
					for(let i=0; i < table.rows.length; i++) {
						table.rows[i].classList.remove('current');
					}
					last_row = this.rowIndex;
					this.classList.toggle('current');
				}
				last_row = this.rowIndex;
			}

			function onKeydown(event) {
				if(last_row === 'undefined' || last_row === '') return;

				if(window.event) {
					keycode = window.event.keyCode;
				}
				else {
					keycode = event.keyCode;
				}

				switch(keycode) {
				case 38:	// up
					if(last_row > 0) {
						if(!event.shiftKey) resetSelection();
						table.rows[--last_row].classList.add('current');
					}
					break;

				case 40:	// down
					if(last_row < table.rows.length-1) {
						if(!event.shiftKey) resetSelection();
						table.rows[++last_row].classList.add('current');
					}
					break;
				}
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
					if(table.rows[i].classList.contains('current') || table.rows[i].classList.contains('selected')) {
						collection[index++] = table.rows[i];
					}
				}

				return collection;
			}

			this.addRow = function(collection) {
				for(let i=0; i < collection.length; i++) {
					for(var j=0, index=0; j < table.rows.length; j++) {
						if(collection[i].cells[1].innerHTML == table.rows[j].cells[1].innerHTML) {
							table.rows[j].classList.add('selected');
							break;
						}
					}
					if(j == table.rows.length) {
						var tr = collection[i].cloneNode(true);
						tr.addEventListener('click', onClick);
						tr.classList.remove('current');
						tr.classList.add('selected');
						table.appendChild(tr);
					}
				}
			}

			this.removeSelectedRow = function() {
				var collection = [];

				for(let i=0; i < table.rows.length;) {
					if(table.rows[i].classList.contains('current') || table.rows[i].classList.contains('selected')) {
						table.deleteRow(i);
						continue;
					}
					i++;
				}
			}

			this.setSelectionToSelected = function() {
				for(let i=0, index=0; i < table.rows.length; i++) {
					if(table.rows[i].classList.contains('current')) {
						table.rows[i].classList.remove('current');
						table.rows[i].classList.add('selected');
					}
				}
				last_row = '';
			}

			function resetSelection() {
				for(let i=0, index=0; i < table.rows.length; i++) {
					table.rows[i].classList.remove('current');
					table.rows[i].classList.remove('selected');
				}
			}
			this.resetSelection = resetSelection;
		}

		function reset(event) {
			resetSelectionToSelected();
		}

		function resetSelectionToSelected(table) {
			if(!table || table != right_table) {
				right.setSelectionToSelected();
			}
			if(!table || table != left_table) {
				left.setSelectionToSelected();
			}
		}

		function add(event) {
			var collection = right.getSelectedRow();
			left.addRow(collection);

			right.setSelectionToSelected();
			left.setSelectionToSelected();

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
