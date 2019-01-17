<?php
/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
$form_config = array(
	array('class' => 'B_Hidden', 'name' => 'mode'),
	array('class' => 'B_Hidden', 'name' => 'id'),

	// Required message
	array(
		'class'			=> 'B_Guidance',
		'start_html'	=> '<p>',
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
					'config_filter'		=> 'insert',
					'name'				=> 'name',
					'class'				=> 'B_InputText',
					'attr'				=> 'class="textbox ime_off" maxlength="10" ',
					'validate'			=>
					array(
						array(
							'type' 			=> 'required',
							'error_message'	=> __('Please enter project name'),
						),
						array(
							'type' 			=> 'pattern',
							'pattern'		=> '^[a-zA-Z0-9\_\-]+$',
							'error_message'	=> __('Please enter project name using only alphanumeric, hyphen(-) and underbar(_)'),
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
					'config_filter'		=> 'update/delete',
					'class'				=> 'B_Text',
					'name'				=> 'name',
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
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'	=> '<th>',
				'end_html'		=> '</th>',
				'value'			=> __('Directory'),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'			=> 'directory',
					'class'			=> 'B_InputText',
					'attr'			=> 'class="textbox ime-off" readonly="readonly"',
				),
				array(
					'filter'		=> 'select',
					'name'			=> 'open_select',
					'class'			=> 'B_Link',
					'link'			=> 'index.php',
					'attr'			=> 'title="' . __('Directory') . '" class="settings-button" onclick="top.bframe.modalWindow.activate(this, window, \'directory\'); return false;" data-param="width:350,height:400"',
					'fixedparam'	=>
					array(
						'terminal_id'	=> TERMINAL_ID,
						'module'		=> 'select', 
						'page'			=> 'tree',
					),
					'specialchars'	=> 'none',
					'value'			=> '<img alt="' . __('Directory') . '" src="images/common/gear.png" />',
				),
				array(
					'filter'		=> 'select',
					'class'			=> 'B_Link',
					'link'			=> '#',
					'attr'			=> 'title="' . __('Clear') . '" class="clear-button" onclick="bstudio.clearText(\'directory\'); return false;" ',
					'specialchars'	=> 'none',
					'value'			=> '<img alt="' . __('Clear') . '" src="images/common/clear.png" />',
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
$input_control_config = array(
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
				'value'			=> __('Back'),
			),
		),
	),
	array(
		'name'			=> 'confirm',
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		array(
			'start_html'	=> '<span class="right-button" onclick="bframe.submit(\'F1\', \'' . $this->module . '\', \'form\', \'confirm\', \'\', true)">',
			'end_html'		=> '</span>',
			array(
				'start_html'	=> '<span class="text">',
				'end_html'		=> '</span>',
				'value'			=> __('Confirm'),
			),
			array(
				'start_html'	=> '<span class="img-cover">',
				'end_html'		=> '</span>',
				'value'			=> '<img src="images/common/right_arrow.png" alt="right arow" />',
			),
		),
	),
);

//confirm control
$confirm_control_config = array(
	'start_html'	=> '<ul class="control">',
	'end_html'		=> '</ul>',
	array(
		'name'			=> 'back',
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		array(
			'start_html'	=> '<span class="left-button" onclick="bframe.submit(\'F1\', \'' . $this->module . '\', \'form\', \'back\', \'\')">',
			'end_html'		=> '</span>',
			array(
				'start_html'	=> '<span class="img-cover">',
				'end_html'		=> '</span>',
				'value'			=> '<img src="images/common/left_arrow.png" alt="left arow" />',
			),
			array(
				'start_html'	=> '<span class="text">',
				'end_html'		=> '</span>',
				'value'			=> __('Back'),
			),
		),
	),
	array(
		'name'			=> 'register',
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		array(
			'start_html'	=> '<span class="right-button" onclick="bframe.submit(\'F1\', \'' . $this->module . '\', \'form\', \'register\', \'\')">',
			'end_html'		=> '</span>',
			array(
				'start_html'	=> '<span class="text">',
				'end_html'		=> '</span>',
				'value'			=> __('Save'),
			),
			array(
				'start_html'	=> '<span class="img-cover">',
				'end_html'		=> '</span>',
				'value'			=> '<img src="images/common/right_arrow.png" alt="right arow" />',
			),
		),
	),
);

// control
$delete_control_config = array(
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
				'value'			=> __('Back'),
			),
		),
	),
	array(
		'name'			=> 'register',
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		array(
			'start_html'	=> '<span class="right-button" onclick="return bframe.confirmSubmit(\'' . __('Are you sure you want to delete?') . '\', \'F1\', \'' . $this->module . '\', \'form\', \'register\', \'delete\')">',
			'end_html'		=> '</span>',
			array(
				'start_html'	=> '<span class="text">',
				'end_html'		=> '</span>',
				'value'			=> __('Delete'),
			),
			array(
				'start_html'	=> '<span class="img-cover">',
				'end_html'		=> '</span>',
				'value'			=> '<img src="images/common/right_arrow.png" alt="right arow" />',
			),
		),
	),
);

//control
$result_control_config = array(
	'start_html'	=> '<ul class="control">',
	'end_html'		=> '</ul>',
	array(
		'name'			=> 'backToList',
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
				'value'			=> __('Back to list'),
			),
		),
	),
);

//result
$result_config = array(
	array(
		'start_html'	=> '<form name="F1" method="post" action="index.php">',
		'end_html'		=> '</form>',
		array(
			'start_html'	=> '<p>',
			'end_html'		=> '</p>',
			array(
				array(
					'start_html'	=> '<span class="user-id">',
					'end_html'		=> '</span>',
					array(
						'value'			=> __('User ID: '),
					),
					array(
						'name'			=> 'user_id',
						'class'			=> 'B_Text',
						'start_html'	=> '<span class="bold">',
						'end_html'		=> '</span>',
					),
				),
				array(
					'name'		=> 'action_message',
				),
			),
		),
	),
);
