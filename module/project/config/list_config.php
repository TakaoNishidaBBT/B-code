<?php
/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
$list_config = 
array(
	'start_html'	=> '<ul class="list" id="entry_list">',
	'end_html'		=> '</ul>',

	'empty_message'	=> '<span class="bold">　' . __('No record found') . '</span>',

	'row'		=>
	array(
		'name'					=> 'data_list',
		'start_html'			=> '<li>',
		'empty_start_html'		=> '<li class="empty">',
		'end_html'				=> '</li>',
		'class'					=> 'B_Row',
		array(
			'start_html'	=> '<div class="name">',
			'end_html'		=> '</div>',
			'class'			=> 'B_Text',
			'name'			=> 'name',
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
					'value'			=> __('OPEN'),
					'start_html'	=> '<span>',
					'end_html'		=> '</span>',
				)
			),
		),
		array(
			'auth_filter'	=> 'super_admin',
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
					'project_id'	=> 'id',
				),
				array(
					'value'			=> '<img class="gear" src="images/common/gear.png" alt="settings" />',
				),
				array(
					'value'			=> __('Settings'),
					'start_html'	=> '<span>',
					'end_html'		=> '</span>',
				),
			),
		),
	),

	// pager
//	'pager'		=> $this->pager_config,
);
