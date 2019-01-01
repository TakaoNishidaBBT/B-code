<?php
/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
$editor_config = array(
	array('class' => 'B_Hidden', 'name' => 'file_path'),
	array('class' => 'B_Hidden', 'name' => 'update_datetime'),
	array(
		'start_html'	=> '<div class="editor_container bframe_adjustwindow" data-param="margin:0">',
		'end_html'		=> '</div>',
		array(
			'start_html'	=> '<div id="text_editor" class="text_editor bframe_adjustparent" data-param="margin:0">',
			'end_html'		=> '</div>',
			array(
				'name'			=> 'contents',
				'class'			=> 'B_TextArea',
				'attr'			=> 'class="textarea bframe_adjustparent bframe_texteditor" data-param="margin:32" %SYNTAX% style="width:100%"',
				'no_trim'		=> true,
			),
		),
	),
	array(
		'start_html'	=> '<div style="display:none">',
		'end_html'		=> '</div>',
		array(
			'name'			=> 'register',
			'start_html'	=> '<span id="register" class="register-button" onclick="bstudio.registerEditor(\'F1\', \'' . $this->module . '\', \'editor\', \'register\', \'confirm\', true)">',
			'end_html'		=> '</span>',
			'value'			=> '<img src="images/common/save.png" alt="Save" />' . __('Save'),
		),
	),
);
