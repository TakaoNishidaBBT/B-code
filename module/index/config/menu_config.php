<?php
/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
$menu_config = array(
	'start_html'	=> '<div class="pull_down_menu">',
	'end_html'		=> '</div>',
	array(
		'start_html'	=> '<ul>',
		'end_html'		=> '</ul>',
		array(
			'start_html'	=> '<li class="title">',
			'end_html'		=> '</li>',
			array(
				'start_html'	=> '<span class="title">',
				'end_html'		=> '</span>',
				'value'			=> 'B-code',
			),
		),
		array(
			'auth_filter'	=> 'super_admin/admin',
			'start_html'	=> '<li>',
			'end_html'		=> '</li>',
			array(
				'class'			=> 'B_Link',
				'attr'			=> 'class="bframe_menu"',
				'id'			=> 'resource',
				'value'			=> '<img src="images/menu/resource.png" alt="resources"/>' . __('File'),
				'specialchars'	=> 'none',
				'script'		=>
				array(
					'bframe_menu'	=>
					array(
						'context_menu'	=>
						array(
							array(
								'menu'		=> __('Add Project Folder'),
								'param'		=> DISPATCH_URL . '&module=project&page=tree,Add Project Folder,360,460',
								'func'		=> 'popup',
							),
						),
						'context_menu_mark'		=> '　▼',
						'context_menu_frame'	=> 'top',
						'context_menu_width'	=> '120',
					),
				),
			),
		),
		array(
			'auth_filter'	=> 'super_admin',
			'start_html'	=> '<li>',
			'end_html'		=> '</li>',
			array(
				'class'			=> 'B_Link',
				'attr'			=> 'class="bframe_menu"',
				'id'			=> 'setting_menu',
				'value'			=> '<img src="images/menu/settings.png" alt="settings"/>' . __('Settings'),
				'specialchars'	=> 'none',
				'script'		=>
				array(
					'bframe_menu'	=>
					array(
						'context_menu'	=>
						array(
							array(
								'menu'		=> __('Basic Settings'),
								'param'		=> DISPATCH_URL . '&module=settings&page=form&method=select,main',
								'func'		=> 'openUrl',
							),
							array(
								'menu'		=> __('Users'),
								'param'		=> DISPATCH_URL . '&module=user&page=list&method=init,main',
								'func'		=> 'openUrl',
							),
							array(
								'menu'		=> __('Site Admin'),
								'param'		=> DISPATCH_URL . '&module=siteadmin&page=form&method=select,main',
								'func'		=> 'openUrl',
							),
						),
						'context_menu_mark'		=> '　▼',
						'context_menu_frame'	=> 'top',
						'context_menu_width'	=> '140',
					),
				),
			),
		),
		array(
			'auth_filter'	=> 'admin',
			'start_html'	=> '<li>',
			'end_html'		=> '</li>',
			array(
				'class'			=> 'B_Link',
				'attr'			=> 'class="bframe_menu"',
				'id'			=> 'setting_menu',
				'value'			=> '<img src="images/menu/settings.png" alt="settings"/>' . __('Settings'),
				'specialchars'	=> 'none',
				'script'		=>
				array(
					'bframe_menu'	=>
					array(
						'context_menu'	=>
						array(
							array(
								'menu'		=> __('Basic Settings'),
								'param'		=> DISPATCH_URL . '&module=settings&page=form&method=select,main',
								'func'		=> 'openUrl',
							),
							array(
								'menu'		=> __('Users'),
								'param'		=> DISPATCH_URL . '&module=user&page=list&method=init,main',
								'func'		=> 'openUrl',
							),
						),
						'context_menu_mark'		=> '　▼',
						'context_menu_frame'	=> 'top',
						'context_menu_width'	=> '140',
					),
				),
			),
		),
		array(
			'auth_filter'	=> 'editor',
			'start_html'	=> '<li>',
			'end_html'		=> '</li>',
			array(
				'class'			=> 'B_Link',
				'attr'			=> 'class="bframe_menu"',
				'id'			=> 'setting_menu',
				'value'			=> '<img src="images/menu/settings.png" alt="settings"/>' . __('Settings'),
				'specialchars'	=> 'none',
				'script'		=>
				array(
					'bframe_menu'	=>
					array(
						'context_menu'	=>
						array(
							array(
								'menu'		=> __('Basic Settings'),
								'param'		=> DISPATCH_URL . '&module=settings&page=form&method=select,main',
								'func'		=> 'openUrl',
							),
						),
						'context_menu_mark'		=> '　▼',
						'context_menu_frame'	=> 'top',
					),
				),
			),
		),
	),
);
