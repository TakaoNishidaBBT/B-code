<?php
/*
 * B-code : Online Editor
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
$language = array(
	'en'	=> __('English'),
	'ja'	=> __('Japanese'),
	'zh-cn'	=> __('Chinese'),
);

$select_language_config = array(
	array('class' => 'B_Hidden', 'name' => 'select_language', 'value' => 1),
	array(
		'start_html'	=> '<label for="language">',
		'end_html'		=> '</label>',
		'value'			=> __('Select language: '),
	),
	array(
		'name'			=> 'language',
		'class'			=> 'B_Selectbox',
		'data_set'		=> $language,
		'local'			=> true,
		'value'			=> 'en',
		'attr'			=> 'class="bframe_selectbox white" onchange=submit()',
	),
);

$admin_basic_auth_config = array(
	array(
		// Table
		'start_html'	=> '<table class="form"><tbody>',
		'end_html'		=> '</tbody></table>',

		// User name
		array(
			'error_group'	=> 'true',
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'			=> '<th>',
				'invalid_start_html'	=> '<th class="error">',
				'end_html'				=> '</th>',
				'value'					=> __('Username'),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'			=> 'basic_auth_id',
					'class'			=> 'B_InputText',
					'attr'			=> 'class="textbox ime-off" size="40" maxlength="100" ',
					'validate'		=>
					array(
						array(
							'type' 			=> 'required',
							'error_message'	=> __('Please enter username'),
						),
						array(
							'type' 			=> 'pattern',
							'pattern'		=> '^[a-zA-Z0-9\_\-]+$',
							'error_message'	=> __('Please enter username using only alphanumeric, hyphen(-) and underbar(_)'),
						),
					),
				),
				array(
					'name'			=> 'error_message',
					'class'			=> 'B_ErrMsg',
					'start_html'	=> '<span class="error-message">',
					'end_html'		=> '</span>',
				),
			),
		),

		// Password
		array(
			'error_group'	=> 'true',
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'			=> '<th>',
				'invalid_start_html'	=> '<th class="error">',
				'end_html'				=> '</th>',
				'value'					=> __('Password'),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'				=> 'basic_auth_pwd',
					'class'				=> 'B_Password',
					'attr'				=> 'class="textbox ime-off" size="40" maxlength="100" ',
					'confirm_message'	=> __('(Set password)'),
					'validate'			=>
					array(
						array(
							'type' 			=> 'required',
							'error_message'	=> __('Please enter password'),
						),
						array(
							'type' 			=> 'pattern',
							'pattern'		=> '^[a-zA-Z0-9\_\-]+$',
							'error_message'	=> __('Please enter password using only alphanumeric, hyphen(-) and underbar(_)'),
						),
					),
				),
				array(
					'name'			=> 'error_message',
					'class'			=> 'B_ErrMsg',
					'start_html'	=> '<span class="error-message">',
					'end_html'		=> '</span>',
				),
			),
		),

		// Password (Re-entry)
		array(
			'error_group'	=> 'true',
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			'confirm_mode'	=> 'none',
			array(
				'start_html'			=> '<th>',
				'invalid_start_html'	=> '<th class="error">',
				'end_html'				=> '</th>',
				'value'					=> __('Password (Re-entry)'),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'			=> 'basic_auth_pwd2',
					'class'			=> 'B_Password',
					'attr'			=> 'class="textbox ime-off" size="40" maxlength="100" ',
					'validate'		=>
					array(
						array(
							'type' 			=> 'required',
							'error_message'	=> __('Please re-enter password'),
						),
						array(
							'type' 			=> 'match',
							'target'		=> 'basic_auth_pwd',
							'error_message'	=> __('Password does not match'),
						),
					),
				),
				array(
					'name'			=> 'error_message',
					'class'			=> 'B_ErrMsg',
					'start_html'	=> '<span class="error-message">',
					'end_html'		=> '</span>',
				),
			),
		),
	),
);

$admin_user_form_config = array(
	array(
		// Table
		'start_html'	=> '<table class="form"><tbody>',
		'end_html'		=> '</tbody></table>',

		// Username
		array(
			'error_group'	=> 'true',
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'			=> '<th>',
				'invalid_start_html'	=> '<th class="error">',
				'end_html'				=> '</th>',
				'value'					=> __('Username'),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'			=> 'admin_user_name',
					'class'			=> 'B_InputText',
					'attr'			=> 'class="textbox" size="40" maxlength="100" ',
					'validate'		=>
					array(
						array(
							'type' 			=> 'required',
							'error_message'	=> __('Please enter username'),
						),
					),
				),
				array(
					'name'			=> 'error_message',
					'class'			=> 'B_ErrMsg',
					'start_html'	=> '<span class="error-message">',
					'end_html'		=> '</span>',
				),
			),
		),

		// Login ID
		array(
			'error_group'	=> 'true',
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'			=> '<th>',
				'invalid_start_html'	=> '<th class="error">',
				'end_html'				=> '</th>',
				'value'					=> __('Login ID'),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'			=> 'admin_user_id',
					'class'			=> 'B_InputText',
					'attr'			=> 'class="textbox ime-off" size="40" maxlength="100" ',
					'validate'		=>
					array(
						array(
							'type' 			=> 'required',
							'error_message'	=> __('Please enter login ID'),
						),
						array(
							'type' 			=> 'pattern',
							'pattern'		=> '^[a-zA-Z0-9\_\-]+$',
							'error_message'	=> __('Please enter login ID using alphanumeric, hyphen(-) and underbar(_)'),
						),
					),
				),
				array(
					'name'			=> 'error_message',
					'class'			=> 'B_ErrMsg',
					'start_html'	=> '<span class="error-message">',
					'end_html'		=> '</span>',
				),
			),
		),

		// Password
		array(
			'error_group'	=> 'true',
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'			=> '<th>',
				'invalid_start_html'	=> '<th class="error">',
				'end_html'				=> '</th>',
				'value'					=> __('Password'),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'				=> 'admin_user_pwd',
					'class'				=> 'B_Password',
					'attr'				=> 'class="textbox ime-off" size="40" maxlength="100" ',
					'confirm_message'	=> __('(Set password)'),
					'validate'			=>
					array(
						array(
							'type' 			=> 'required',
							'error_message'	=> __('Please enter password'),
						),
						array(
							'type' 			=> 'pattern',
							'pattern'		=> '^[a-zA-Z0-9\_\-]+$',
							'error_message'	=> __('Please enter password using alphanumeric, hyphen(-) and underbar(_)'),
						),
					),
				),
				array(
					'name'			=> 'error_message',
					'class'			=> 'B_ErrMsg',
					'start_html'	=> '<span class="error-message">',
					'end_html'		=> '</span>',
				),
			),
		),

		// Password (Re-entry)
		array(
			'error_group'	=> 'true',
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			'confirm_mode'	=> 'none',
			array(
				'start_html'			=> '<th>',
				'invalid_start_html'	=> '<th class="error">',
				'end_html'				=> '</th>',
				'value'					=> __('Password (Re-entry)'),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'			=> 'admin_user_pwd2',
					'class'			=> 'B_Password',
					'attr'			=> 'class="textbox ime-off" size="40" maxlength="100" ',
					'validate'		=>
					array(
						array(
							'type' 			=> 'required',
							'error_message'	=> __('Please re-enter password'),
						),
						array(
							'type' 			=> 'match',
							'target'		=> 'admin_user_pwd',
							'error_message'	=> __('Password is not matched'),
						),
					),
				),
				array(
					'name'			=> 'error_message',
					'class'			=> 'B_ErrMsg',
					'start_html'	=> '<span class="error-message">',
					'end_html'		=> '</span>',
				),
			),
		),
	),
);

$root_htaccess_config = array(
	array(
		'name'		=> 'htaccess',
		'class'		=> 'B_TextArea',
		'attr'		=> 'class="htaccess"',
	),
);
