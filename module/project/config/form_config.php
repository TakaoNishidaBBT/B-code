<?php
/*
 * B-code : Online Editor
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
$form_config = array(
	array('class' => 'B_Hidden', 'name' => 'mode'),
	array('class' => 'B_Hidden', 'name' => 'rowid'),

	// Required message
	array(
		'class'			=> 'B_Guidance',
		'start_html'	=> '<p class="require-guidance">',
		'end_html'		=> '</p>',
		array(
			'class'			=> 'B_Guidance',
			'start_html'	=> '<span class="require">',
			'end_html'		=> '</span>',
			'value'			=> __('*'),
		),
		array(
			'class'			=> 'B_Guidance',
			'value'			=> __(' Indicates required field'),
		),
	),

	array(
		// Table
		'start_html'	=> '<table class="form"><tbody>',
		'end_html'		=> '</tbody></table>',

		// Name
		array(
			'error_group'	=> true,
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'		=> '<th>',
				'end_html'			=> '</th>',
				'invalid_start_html'=> '<th class="error">',
				array(
					'value'			=> __('Name'),
				),
				array(
					'class'			=> 'B_Guidance',
					'value'			=> '<span class="require">' . __('*') . '</span>',
				),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'				=> 'name',
					'class'				=> 'B_InputText',
					'attr'				=> 'class="textbox ime_off" maxlength="100" ',
					'validate'			=>
					array(
						array(
							'type' 			=> 'required',
							'error_message'	=> __('Please enter project name'),
						),
						array(
							'type' 			=> 'pattern',
							'pattern'		=> '^[a-zA-Z0-9\.\_\-]+$',
							'error_message'	=> __('Please enter project name using only alphanumeric, dot(.), hyphen(-) and underbar(_)'),
						),
						array(
							'type'			=> 'callback',
							'obj'			=> $this,
							'method'		=> '_validate_callback',
							'error_message'	=> __('This name is already in use'),
						),
					),
				),
				array(
					'name'				=> 'error_message',
					'class'				=> 'B_ErrMsg',
					'start_html'		=> '<span class="error-message">',
					'end_html'			=> '</span>',
				),
			),
		),

		// Domain
		array(
			'error_group'	=> true,
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'		=> '<th>',
				'end_html'			=> '</th>',
				'invalid_start_html'=> '<th class="error">',
				array(
					'value'			=> __('Domain'),
				),
				array(
					'class'			=> 'B_Guidance',
					'value'			=> '<span class="require">' . __('*') . '</span>',
				),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'			=> 'scheme',
					'class'			=> 'B_SelectBox',
					'data_set'		=> 'scheme',
					'attr'			=> 'class="bframe_selectbox white"',
				),
				array(
					'name'				=> 'domain',
					'class'				=> 'B_InputText',
					'attr'				=> 'class="textbox ime_off" maxlength="100" ',
					'validate'			=>
					array(
						array(
							'type' 			=> 'required',
							'error_message'	=> __('Please enter domain name'),
						),
						array(
							'type' 			=> 'pattern',
							'pattern'		=> '^[a-zA-Z0-9\.\-]+$',
							'error_message'	=> __('Please enter project name using only alphanumeric, dot(.) and hyphen(-)'),
						),
					),
				),
				array(
					'name'				=> 'error_message',
					'class'				=> 'B_ErrMsg',
					'start_html'		=> '<span class="error-message">',
					'end_html'			=> '</span>',
				),
			),
		),

		// Doc Root
		array(
			'error_group'	=> true,
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'		=> '<th>',
				'end_html'			=> '</th>',
				'invalid_start_html'=> '<th class="error">',
				array(
					'value'			=> __('Doc Root'),
				),
				array(
					'class'			=> 'B_Guidance',
					'value'			=> '<span class="require">' . __('*') . '</span>',
				),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'			=> 'doc_root',
					'class'			=> 'B_InputText',
					'attr'			=> 'class="textbox ime-off" readonly="readonly"',
					'validate'		=>
					array(
						array(
							'type' 			=> 'required',
							'error_message'	=> __('Please set doc root'),
						),
					),
				),
				array(
					'filter'		=> 'insert/update',
					'name'			=> 'open_select',
					'class'			=> 'B_Link',
					'link'			=> 'index.php',
					'attr'			=> 'title="' . __('Doc Root') . '" class="settings-button" onclick="top.bframe.modalWindow.activate(this, window, \'doc_root\'); return false;" data-param="width:350,height:400"',
					'fixedparam'	=>
					array(
						'terminal_id'	=> TERMINAL_ID,
						'module'		=> 'select', 
						'page'			=> 'tree',
						'method'		=> 'openDocRoot',
					),
					'specialchars'	=> 'none',
					'value'			=> '<img alt="' . __('Doc Root') . '" src="images/common/gear.png" />',
				),
				array(
					'filter'		=> 'insert/update',
					'class'			=> 'B_Link',
					'link'			=> '#',
					'attr'			=> 'title="' . __('Clear') . '" class="clear-button" onclick="bcode.clearText(\'doc_root\'); return false;" ',
					'specialchars'	=> 'none',
					'value'			=> '<img alt="' . __('Clear') . '" src="images/common/clear.png" />',
				),
				array(
					'name'				=> 'error_message',
					'class'				=> 'B_ErrMsg',
					'start_html'		=> '<span class="error-message">',
					'end_html'			=> '</span>',
				),
			),
		),

		// Directory
		array(
			'error_group'	=> true,
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'		=> '<th>',
				'end_html'			=> '</th>',
				'invalid_start_html'=> '<th class="error">',
				array(
					'value'			=> __('Directory'),
				),
				array(
					'class'			=> 'B_Guidance',
					'value'			=> '<span class="require">' . __('*') . '</span>',
				),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'			=> 'directory',
					'class'			=> 'B_InputText',
					'attr'			=> 'class="textbox ime-off" readonly="readonly"',
					'validate'		=>
					array(
						array(
							'type' 			=> 'required',
							'error_message'	=> __('Please set directory'),
						),
						array(
							'type' 			=> 'callback',
							'obj'			=> $this,
							'method'		=> '_directory_validate_callback',
							'error_message'	=> __('Directory must be under Doc Root'),
						),
					),
				),
				array(
					'filter'		=> 'insert/update',
					'name'			=> 'open_select',
					'class'			=> 'B_Link',
					'link'			=> 'index.php',
					'attr'			=> 'title="' . __('Directory') . '" class="settings-button" onclick="top.bframe.modalWindow.activate(this, window, \'doc_root\', \'directory\'); return false;" data-param="width:350,height:400"',
					'fixedparam'	=>
					array(
						'terminal_id'	=> TERMINAL_ID,
						'module'		=> 'select', 
						'page'			=> 'tree',
						'method'		=> 'openDirectory',
					),
					'specialchars'	=> 'none',
					'value'			=> '<img alt="' . __('Directory') . '" src="images/common/gear.png" />',
				),
				array(
					'filter'		=> 'insert/update',
					'class'			=> 'B_Link',
					'link'			=> '#',
					'attr'			=> 'title="' . __('Clear') . '" class="clear-button" onclick="bcode.clearText(\'directory\'); return false;" ',
					'specialchars'	=> 'none',
					'value'			=> '<img alt="' . __('Clear') . '" src="images/common/clear.png" />',
				),
				array(
					'name'				=> 'error_message',
					'class'				=> 'B_ErrMsg',
					'start_html'		=> '<span class="error-message">',
					'end_html'			=> '</span>',
				),
			),
		),

		// User
		array(
			'error_group'	=> true,
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'	=> '<th>',
				'end_html'		=> '</th>',
				array(
					'value'			=> __('User'),
				),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'config_filter'	=> 'insert/update',
					'name'			=> 'user_name',
					'start_html'	=> '<div id="user_name" class="user-name">',
					'end_html'		=> '</div>',
				),
				array(
					'config_filter'	=> 'delete',
					'name'			=> 'user_name',
					'start_html'	=> '<div id="user_name" class="user-name delete">',
					'end_html'		=> '</div>',
				),
				array(
					'class'			=> 'B_Hidden',
					'name'			=> 'user',
				),
				array(
					'filter'		=> 'insert/update',
					'name'			=> 'open_select',
					'class'			=> 'B_Link',
					'link'			=> 'index.php',
					'attr'			=> 'title="' . __('User Select') . '" class="settings-button" onclick="top.bframe.modalWindow.activate(this, window, \'user\'); return false;" data-param="width:400,height:300"',
					'fixedparam'	=>
					array(
						'terminal_id'	=> TERMINAL_ID,
						'module'		=> 'user', 
						'page'			=> 'select',
						'method'		=> 'open',
					),
					'specialchars'	=> 'none',
					'value'			=> '<img alt="' . __('User') . '" src="images/common/gear.png" />',
				),
				array(
					'filter'		=> 'insert/update',
					'class'			=> 'B_Link',
					'link'			=> '#',
					'attr'			=> 'title="' . __('Clear') . '" class="clear-button" onclick="bcode.clearText(\'user_name\', \'user\'); return false;" ',
					'specialchars'	=> 'none',
					'value'			=> '<img alt="' . __('Clear') . '" src="images/common/clear.png" />',
				),
				array(
					'name'				=> 'error_message',
					'class'				=> 'B_ErrMsg',
					'start_html'		=> '<span class="error-message">',
					'end_html'			=> '</span>',
				),
			),
		),

		// Notes
		array(
			'error_group'	=> true,
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'		=> '<th>',
				'invalid_start_html'=> '<th class="error">',
				'end_html'			=> '</th>',
				'value'				=> __('Notes'),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'		=> 'notes',
					'class'		=> 'B_TextArea',
					'attr'		=> 'class="textarea bframe_textarea" cols="78" rows="5"',
				),
			),
		),
	),
);

// control
$back_button_config = array(
	'start_html'	=> '<ul class="control">',
	'end_html'		=> '</ul>',
	array(
		'name'			=> 'back',
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		array(
			'start_html'	=> '<span class="left-button" onclick="bframe.submit(\'F1\', \'' . $this->module . '\', \'list\', \'back\', \'\')">',
			'end_html'		=> '</span>',
			array(
				'start_html'	=> '<span class="img-cover">',
				'end_html'		=> '</span>',
				'value'			=> '<img src="images/common/left_arrow.png" alt="left arow" />',
			),
			array(
				'start_html'	=> '<span class="text">',
				'end_html'		=> '</span>',
				'value'			=> __('Back To List'),
			),
		),
	),
);
$submit_button_config = array(
	'start_html'	=> '<ul class="control">',
	'end_html'		=> '</ul>',
	array(
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		array(
			'start_html'	=> '<span class="right-button" onclick="bframe.submit(\'F1\', \'' . $this->module . '\', \'form\', \'register\', \'\', true)">',
			'end_html'		=> '</span>',
			array(
				'start_html'	=> '<span class="text">',
				'end_html'		=> '</span>',
				'value'			=> __('Register'),
			),
			array(
				'start_html'	=> '<span class="img-cover">',
				'end_html'		=> '</span>',
				'value'			=> '<img src="images/common/right_arrow.png" alt="right arow" />',
			),
		),
	),
);
