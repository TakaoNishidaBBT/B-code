/*
 * B-frame : php web application framework
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	bframe.addEventListener(window, 'load' , bframeTextEditorInit);

	function bframeTextEditorInit() {
		var objects = document.querySelectorAll('textarea.bframe_texteditor');

		for(var i=0; i < objects.length; i++) {
			bframe_texteditor = new bframe.texteditor(objects[i]);
		}
	}

	// -------------------------------------------------------------------------
	// class bframe.texteditor
	// 
	// -------------------------------------------------------------------------
	bframe.texteditor = function(target) {
		var self = this;
		var container, editor;
		var syntax = target.getAttribute('data-syntax');
		var register_button;
		var widget;
		var ace_editor, theme, Split, split, UndoManager;
		var edit_flag = false;
		var undo_depth = 0;
		var message_field;
		var save_command = {
			name: 'save',
			bindKey: {
				mac: 'Command-S',
				win: 'Ctrl-S'
			},
			exec: function() {
				save()
			}
		}
		var refresh_command = {
			name: 'refresh',
			bindKey: {
				mac: 'Command-Shift-R',
				win: 'alt-R'
			},
			exec: function() {
				refresh()
			}
		}

		var close_command = {
			name: 'close',
			bindKey: {
				mac: 'Command-Shift-W',
				win: 'alt-W'
			},
			exec: function() {
				close()
			}
		}

		if(!parent) {
			var parent = target.parentNode;
			target.style.display = 'none';
		}

		if(!syntax) {
			var syntax = 'php';
		}

		init();

		function createControl() {
			var control, control1, control2, li;
			// control
			control = document.createElement('div');
			control.className = 'control';
			parent.appendChild(control);

			control1 = document.createElement('ul');
			control1.className = 'left-side-control';
			control.appendChild(control1);

			if(bframe.getOS() == 'windows') {
				li = createControlButton('images/editor/undo.png', 'undo (ctrl-z)', undo);
				control1.appendChild(li);
				li = createControlButton('images/editor/redo.png', 'redo (ctrl-y)', redo);
				control1.appendChild(li);
			}
			else {
				li = createControlButton('images/editor/undo.png', 'undo (command-z)', undo);
				control1.appendChild(li);
				li = createControlButton('images/editor/redo.png', 'redo (command-y)', redo);
				control1.appendChild(li);
			}
			li = createControlButton('images/editor/splith.png', 'split horizontal', splith);
			control1.appendChild(li);
			li = createControlButton('images/editor/splitv.png', 'split vertical', splitv);
			control1.appendChild(li);
			li = createControlButton('images/editor/indent_guide.png', 'show indent guide', indentGuide);
			control1.appendChild(li);
			li = createControlButton('images/editor/invisible_object.png', 'show invisibles', invisible);
			control1.appendChild(li);
			if(bframe.getOS() == 'windows') {
				li = createControlButton('images/editor/goto.png', 'go to line (ctrl-l)', goto);
				control1.appendChild(li);
			}
			else {
				li = createControlButton('images/editor/goto.png', 'go to line', goto);
				control1.appendChild(li);
			}
			li = createControlButton('images/editor/auto_complete.png', 'auto complete on', autoComplete);
			control1.appendChild(li);

			widget = bframe.searchNodeByClassName(parent, 'open_widgetmanager');
			if(widget) {
				li = createControlButton('images/editor/gear.png', 'widgets', openWidget);
				control1.appendChild(li);
			}

			message_field = document.createElement('div'); 
			message_field.className = 'message-field'; 
			control.appendChild(message_field);

			control2 = document.createElement('ul');
			control2.className = 'right-side-control';
			control.appendChild(control2);

			if(bframe.getOS() == 'windows') {
				li = createControlButton('images/editor/refresh.png', 'refresh (alt-r)', refresh);
			}
			else {
				li = createControlButton('images/editor/refresh.png', 'refresh (command-shift-r)', refresh);
			}
			li.className = 'refresh';
			control2.appendChild(li);
		}

		function createControlButton(icon_img, title, func) {
			var li = document.createElement('li');

			var a = document.createElement('a');
			a.title = title;
			if(func) {
				bframe.addEventListener(a, 'mousedown', func);
			}
			li.appendChild(a);
			var span = document.createElement('span');
			a.appendChild(span);
			var img = document.createElement('img');
			img.src = icon_img;
			span.appendChild(img);

			return li;
		}

		function init() {
			// register button
			register_button = document.getElementById('register');

			// refresh button
			refresh_button = document.getElementById('refresh');

			// create control
			createControl();

			// container
			container = document.createElement('div');
			container.style.height = (target.offsetHeight) + 'px';
			container.className = 'container';
			container.onSelectStart = function() {return false;};
			var param = target.getAttribute('data-param');
			container.setAttribute('data-param', param);
			parent.appendChild(container);
			var aw = new bframe.adjustparent(container);

			// editor
			editor = document.createElement('div');

			container.appendChild(editor);
			editor.style.height = target.style.height;
			editor.style.position = 'absolute';
			editor.style.top = '32px';
			editor.style.left = '0';
			editor.style.bottom = '0';
			editor.style.right = '0';
			var ap = new bframe.adjustparent(editor);

			theme = require('ace/theme/twilight');
			Split = require('ace/split').Split;
			split = new Split(editor, theme, 1);
			ace_editor = split.getEditor(0);
			ace_editor.getSession().setValue(target.value);
			mode = require('ace/mode/' + syntax).Mode;
			ace_editor.getSession().setMode(new mode());

			UndoManager = require('ace/undomanager').UndoManager;
			var session = split.getEditor(0).session;
			session.setUndoManager(new UndoManager());

			ace_editor.renderer.setHScrollBarAlwaysVisible(false);
			ace_editor.renderer.setShowPrintMargin(false);
			ace_editor.renderer.setShowInvisibles(true);
			ace_editor.getSession().setFoldStyle('markbeginend');
			ace_editor.getSession().setUseWrapMode(true);
			ace_editor.getSession().setUseSoftTabs(false);
			ace_editor.getSession().on('change', setEditFlag);

			var completion = require('ace/ext/language_tools');
			ace_editor.setOptions({
				enableBasicAutocompletion: true,
				enableLiveAutocompletion: true,
				enableSnippets: true,
			});

			ace_editor.setScrollSpeed(2);
			ace_editor.commands.addCommand(save_command);
			ace_editor.commands.addCommand(refresh_command);
			ace_editor.commands.addCommand(close_command);

			target.style.display = 'none';

			bframe.addEventListener(parent,				'focus',	onFocus);
			bframe.addEventListener(parent.parentNode,	'focus',	onFocus);
			bframe.addEventListener(target,				'change',	updateEditor);

			bframe.resize_handler.registerCallBackFunction(onFocus);

			// for submit
			if(bframe.ajaxSubmit) {
				bframe.ajaxSubmit.registerCallBackFunction(updateTarget);
			}

			// for inline editor
			if(bframe.inline) {
				bframe.inline.registerCallBackFunction(updateTarget);
			}

			// for preview
			if(bframe.preview) {
				bframe.preview.registerCallBackFunction(updateTarget);
			}

			// for edit check
			if(bframe.editCheck_handler) {
				bframe.editCheck_handler.registerCallBackFunction(editCheckCallback);
				bframe.editCheck_handler.registerResetCallBackFunction(resetDirtyCallback);
			}
		}

		function undo(event) {
			var session = split.getEditor(0).session;
			var undoManager = session.getUndoManager();
			undoManager.undo();
		}

		function redo(event) {
			ace_editor.redo();
		}

		function openWidget(event) {
			bcode.activateModalWindow(widget, 350, 400, insert);
			bframe.stopPropagation(event);
		}

		function insert(code) {
			if(typeof(code)  == 'string') ace_editor.insert(code);
			ace_editor.focus();
		}

		function splith(event) {
			if(split.getSplits() == 1 || split.getOrientation() != split.BELOW) {
				split.setOrientation(split.BELOW);
				split.setSplits(2);
				var session = split.getEditor(0).session;
				var newSession = split.setSession(session, 1);
				newSession.name = session.name;

				var newEditor = split.getEditor(1);
				newEditor.renderer.setHScrollBarAlwaysVisible(ace_editor.renderer.getHScrollBarAlwaysVisible());
				newEditor.renderer.setShowPrintMargin(ace_editor.renderer.getShowPrintMargin());
				newEditor.renderer.setShowInvisibles(ace_editor.renderer.getShowInvisibles());
				newEditor.setDisplayIndentGuides(ace_editor.getDisplayIndentGuides());
				newEditor.commands.addCommand(save_command);
				newEditor.focus();
			}
			else{
				split.setSplits(1);
				ace_editor.focus();
			}
		}

		function splitv(event) {
			if(split.getSplits() == 1 || split.getOrientation() != split.BESIDE) {
				split.setOrientation(split.BESIDE);
				split.setSplits(2);
				var session = split.getEditor(0).session;
				var newSession = split.setSession(session, 1);
				newSession.name = session.name;

				var newEditor = split.getEditor(1);
				newEditor.renderer.setHScrollBarAlwaysVisible(ace_editor.renderer.getHScrollBarAlwaysVisible());
				newEditor.renderer.setShowPrintMargin(ace_editor.renderer.getShowPrintMargin());
				newEditor.renderer.setShowInvisibles(ace_editor.renderer.getShowInvisibles());
				newEditor.setDisplayIndentGuides(ace_editor.getDisplayIndentGuides());
				newEditor.commands.addCommand(save_command);
				newEditor.focus();
			}
			else {
				split.setSplits(1);
				ace_editor.focus();
			}
		}

		function indentGuide(event) {
			if(ace_editor.getDisplayIndentGuides()) {
				ace_editor.setDisplayIndentGuides(false);
				if(split.getSplits() == 2) {
					split.getEditor(1).setDisplayIndentGuides(false);
				}
			}
			else {
				ace_editor.setDisplayIndentGuides(true);
				if(split.getSplits() == 2) {
					split.getEditor(1).setDisplayIndentGuides(true);
				}
			}
		}

		function invisible(event) {
			if(ace_editor.renderer.getShowInvisibles()) {
				ace_editor.renderer.setShowInvisibles(false);
				if(split.getSplits() == 2) {
					split.getEditor(1).renderer.setShowInvisibles(false);
				}
			}
			else {
				ace_editor.renderer.setShowInvisibles(true);
				if(split.getSplits() == 2) {
					split.getEditor(1).renderer.setShowInvisibles(true);
				}
			}
		}

		function goto(event) {
			line = parseInt(prompt("Enter line number:"), 10);
			if(!isNaN(line)) {
				var cEditor = split.getCurrentEditor();
				cEditor.gotoLine(line);
				cEditor.focus();
			}
		}

		function autoComplete(event) {
			var obj = bframe.getEventSrcElement(event);
			var a = bframe.searchParentByTagName(obj, 'a');
			var img = a.querySelector('img');

			var ret = ace_editor.getOptions({
				enableBasicAutocompletion: false,
				enableLiveAutocompletion: false,
				enableSnippets: false,
			});

			if(ret.enableBasicAutocompletion) {
				var completion = require('ace/ext/language_tools');
				ace_editor.setOptions({
					enableBasicAutocompletion: false,
					enableLiveAutocompletion: false,
					enableSnippets: false,
				});
				a.title = 'auto complete off';
				img.classList.add('disabled');
			}
			else {
				ace_editor.setOptions({
					enableBasicAutocompletion: true,
					enableLiveAutocompletion: true,
					enableSnippets: true,
				});
				a.title = 'auto complete on';
				img.classList.remove('disabled');
			}
		}

		function refresh() {
			if(ace_editor.getSession().getUndoManager().undoDepth() != undo_depth) {
				if(!confirm(top.bframe.message.getProperty('refresh_confirm'))) return;
			}
			bcode.updateEditor = updateEditor;
			message_field.innerHTML = '';
			bframe.fireEvent(refresh_button, 'click');
		}

		function close() {
			bcode.closeCurrentTab();
		}

		function onFocus() {
			split.resize();
			split.focus();
		}

		function updateEditor(response) {
			var session = ace_editor.getSession();
			var cursor = session.selection.getCursor();
			var top = session.getScrollTop();

			ace_editor.selectAll();
			var range = ace_editor.getSelectionRange();
			ace_editor.clearSelection();
			ace_editor.getSession().replace(range, target.value);

			// keep scroll top and cursor position 
			ace_editor.moveCursorToPosition(cursor);
			session.setScrollTop(top);
			ace_editor.focus();
			ace_editor.getSession().getUndoManager().reset();
			undo_depth = ace_editor.getSession().getUndoManager().undoDepth();

			while(message_field.firstChild) {
				message_field.removeChild(message_field.firstChild);
			}

			var span = document.createElement('span');
			span.className = 'fadeout';
			span.innerHTML = response.message;
			message_field.appendChild(span);

			setTimeout(bcode.resetEditFlag, 100);
		}

		function updateTarget() {
			target.value = ace_editor.getSession().getValue();
		}

		function editCheckCallback() {
			if(ace_editor.getSession().getUndoManager().undoDepth() != undo_depth) {
				bframe.editCheck_handler.setEditFlag();
			}
		}

		function setEditFlag() {
			setTimeout(_setEditFlag, 10);
		}

		function _setEditFlag() {
			if(ace_editor.getSession().getUndoManager().undoDepth() != undo_depth) {
				edit_flag = true;
				bcode.setEditFlag();
			}
			else {
				bcode.resetEditFlag();
			}
		}

		function resetDirtyCallback() {
			edit_flag = false;
		}

		function save() {
			if(ace_editor.getSession().getUndoManager().undoDepth() == undo_depth) return;

			bframe.fireEvent(register_button, 'click');
			undo_depth = ace_editor.getSession().getUndoManager().undoDepth();
		}
	}
