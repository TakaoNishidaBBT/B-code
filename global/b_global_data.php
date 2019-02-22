<?php
/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	${$g_data_set}['language'] =
		array(
			'en'	=> __('English'),
			'ja'	=> __('Japanese'),
			'zh-cn'	=> __('Chinese'),
		);

	${$g_data_set}['row_per_page'] =
		array(
			'1'		=> __('1 line'),
			'10'	=> __('10 lines'),
			'20'	=> __('20 lines'),
			'50'	=> __('50 lines'),
			'100'	=> __('100 lines'),
		);

	${$g_data_set}['user_auth'] =
		array(
			'admin'		=> __('Admin'),
			'editor'	=> __('Editor'),
			'preview'	=> __('Preview'),
		);

	${$g_data_set}['user_status'] =
		array(
			'1'		=> __('Enabled'),
			'9'		=> __('Disabled'),
		);

	${$g_data_set}['record_status_list'] =
		array(
			''		=> '',
			'0'		=> '',
			'1'		=> '<img src="images/common/square.png" alt="publicationed" />',
		);

	${$g_data_set}['publication_status'] =
		array(
			''		=> '',
			'0'		=> '',
			'1'		=> '<img src="images/common/square.png" alt="publicationed" />',
			'2'		=> '<img src="images/common/star.png" alt="reserved" />',
		);

	${$g_data_set}['publication'] =
		array(
			'1'		=> __('Published'),
			'2'		=> __('Preview'),
			'3'		=> __('Closed'),
		);

	${$g_data_set}['node_status'] =
		array(
			''		=> __('Published'),
			'9'		=> __('Private'),
		);

	${$g_data_set}['description_flag'] =
		array(
			'1'		=> __('On'),
			'2'		=> __('Off'),
		);

	${$g_data_set}['external_link'] =
		array(
			''		=> __('Off'),
			'1'		=> __('On'),
		);

	${$g_data_set}['datetime_error_message'] =
		array(
			'1'		=> __(' (out of range)'),
			'2'		=> __(' (invalid time)'),
			'3'		=> __(' (invalid date)'),
			'4'		=> __(' (format error)'),
		);

	${$g_data_set}['node_error'] =
		array(
			'0'		=> __('DB error'),
			'1'		=> __('The destination folder is a subfolder of the selected folder'),
			'2'		=> __('The number of nodes are different. Please sort in the right pane.'),
			'3'		=> __('Another user has updated this record'),
		);

	${$g_data_set}['template_node_error'] =
		array(
			'0'		=> __('DB error'),
			'1'		=> __('The destination template is a subtemplate of the selecter template'),
			'2'		=> __('The number of nodes are different'),
			'3'		=> __('Another user has updated this record'),
		);

	${$g_data_set}['mail_type_settings'] =
		array(
			'contact_reply'		=> __('Contact Auto Reply'),
			'contact_notice'	=> __('Contact Notice'),
		);

	${$g_data_set}['table']['settings'] =
		array(							// Data Types			Length	PK		Auto-Increment
			'site_title'				=> array('text', 		'', 	'', 	''),
			'admin_site_title'			=> array('text', 		'', 	'', 	''),
			'notes'						=> array('text', 		'', 	'', 	''),
			'reserve1'					=> array('text', 		'', 	'', 	''),
			'reserve2'					=> array('text', 		'', 	'', 	''),
			'reserve3'					=> array('text', 		'', 	'', 	''),
			'create_user'				=> array('text', 		'', 	'',		''),
			'create_datetime'			=> array('text', 		'', 	'',		''),
			'update_user'				=> array('text', 		'', 	'',		''),
			'update_datetime'			=> array('text', 		'', 	'',		''),
		);

	${$g_data_set}['table']['project'] =
		array(							// Data Types			Length	PK		Auto-Increment
			'name'						=> array('text', 		'', 	'', 	''),
			'directory'					=> array('text', 		'', 	'', 	''),
			'path'						=> array('text', 		'', 	'', 	''),
			'url'						=> array('text', 		'', 	'', 	''),
			'user'						=> array('text', 		'', 	'', 	''),
			'notes'						=> array('text', 		'', 	'', 	''),
			'del_flag'					=> array('char', 		'1', 	'', 	''),
			'reserve1'					=> array('text', 		'', 	'', 	''),
			'reserve2'					=> array('text', 		'', 	'', 	''),
			'reserve3'					=> array('text', 		'', 	'', 	''),
			'create_user'				=> array('text', 		'', 	'',		''),
			'create_datetime'			=> array('text', 		'', 	'',		''),
			'update_user'				=> array('text', 		'', 	'',		''),
			'update_datetime'			=> array('text', 		'', 	'',		''),
		);

	${$g_data_set}['table']['user'] =
		array(							// Data Types			Length	PK		Auto-Increment
			'user_id'					=> array('char', 		'30', 	'', 	''),
			'pwd'						=> array('char', 		'30', 	'', 	''),
			'user_status'				=> array('text', 		'', 	'', 	''),
			'user_auth'					=> array('text', 		'', 	'', 	''),
			'language'					=> array('text', 		'', 	'', 	''),
			'user_name'					=> array('text', 		'', 	'', 	''),
			'email'						=> array('text', 		'', 	'', 	''),
			'notes'						=> array('text', 		'', 	'', 	''),
			'del_flag'					=> array('char', 		'1', 	'', 	''),
			'reserve1'					=> array('text', 		'', 	'', 	''),
			'reserve2'					=> array('text', 		'', 	'', 	''),
			'reserve3'					=> array('text', 		'', 	'', 	''),
			'create_user'				=> array('text', 		'', 	'',		''),
			'create_datetime'			=> array('text', 		'', 	'',		''),
			'update_user'				=> array('text', 		'', 	'',		''),
			'update_datetime'			=> array('text', 		'', 	'',		''),
		);
