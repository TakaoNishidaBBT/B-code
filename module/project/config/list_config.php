<?php
/*
 * B-code : Online Editor
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
$list_config = 
array(
	'start_html'	=> '<ul class="list bcode_project" id="project-list">',
	'end_html'		=> '</ul>',

	'empty_message'	=> '<span class="bold">　' . __('No record found') . '</span>',

	'row'		=>
	array(
		'name'				=> 'data_list',
		'class'				=> 'B_Row',
		'start_html'		=> '<li id="%ID%" class="project">',
		'empty_start_html'	=> '<li class="empty project">',
		'end_html'			=> '</li>',
		array(
			'name'			=> 'rowid',
			'class'			=> 'B_Data',
		),
		array(
			'name'			=> 'name',
			'class'			=> 'B_Text',
			'start_html'	=> '<div class="name">',
			'end_html'		=> '</div>',
		),
		array(
			'start_html'	=> '<div class="open-button">',
			'end_html'		=> '</div>',
			array(
				'name'			=> 'open',
				'class'			=> 'B_Link',
				'link'			=> B_CURRENT_ROOT,
				'attr'			=> 'class="open-button" target="_blank"',
				array(
					'start_html'	=> '<span>',
					'end_html'		=> '</span>',
					'value'			=> __('OPEN'),
				)
			),
		),
		array(
			'auth_filter'	=> 'super_admin/admin',
			'start_html'	=> '<div class="delete">',
			'end_html'		=> '</div>',
			array(
				'name'			=> 'delete',
				'class'			=> 'B_Link',
				'link'			=> 'index.php',
				'fixedparam'	=>
				array(
					'terminal_id'	=> TERMINAL_ID,
					'module'		=> $this->module, 
					'page'			=> 'form', 
					'method'		=> 'select',
					'mode'			=> 'delete',
				),
				'param'		=>
				array(
					'rowid'			=> 'rowid',
				),
				array(
					'start_html'	=> '<span class="sprite">',
					'end_html'		=> '</span>',
					'value'			=> '<img class="delete" src="images/common/delete.png" alt="delete" />',
				),
				array(
					'start_html'	=> '<span>',
					'end_html'		=> '</span>',
					'value'			=> __('Delete'),
				),
			),
		),
		array(
			'auth_filter'	=> 'super_admin/admin',
			'start_html'	=> '<div class="settings">',
			'end_html'		=> '</div>',
			array(
				'name'			=> 'edit',
				'class'			=> 'B_Link',
				'link'			=> 'index.php',
				'fixedparam'	=>
				array(
					'terminal_id'	=> TERMINAL_ID,
					'module'		=> $this->module, 
					'page'			=> 'form', 
					'method'		=> 'select',
					'mode'			=> 'update',
				),
				'param'		=>
				array(
					'rowid'			=> 'rowid',
				),
				array(
					'value'			=> '<img class="gear" src="images/common/gear.png" alt="settings" />',
				),
				array(
					'start_html'	=> '<span>',
					'end_html'		=> '</span>',
					'value'			=> __('Settings'),
				),
			),
		),
	),
);
