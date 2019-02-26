<?php
/*
 * B-code : Online Editor
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
$menu_config = array(
	'start_html'	=> '<div class="side_menu">',
	'end_html'		=> '</div>',
	array(
		'start_html'	=> '<ul class="bframe_navi">',
		'end_html'		=> '</ul>',
		array(
			'start_html'	=> '<li>',
			'end_html'		=> '</li>',
			'class'			=> 'B_Link',
			'specialchars'	=> 'none',
			'link'			=> DISPATCH_URL . '&amp;module=project&amp;page=list',
			'target'		=> 'main',
			array(
				'value'			=> '<img class="menu-icon" src="images/menu/project.png" alt="project" />',
			),
			array(
				'start_html'	=> '<span>',
				'end_html'		=> '</span>',
				'value'			=>  __('PROJECT'),
			),
			array(
				'value'			=> '<img class="right-arrow" src="images/common/right_arrow_white.png" alt="arrow" />',
			),
		),
		array(
			'auth_filter'	=> 'super_admin/admin',
			'start_html'	=> '<li>',
			'end_html'		=> '</li>',
			'class'			=> 'B_Link',
			'specialchars'	=> 'none',
			'link'			=> DISPATCH_URL . '&amp;module=user&amp;page=list',
			'target'		=> 'main',
			array(
				'value'			=> '<img class="menu-icon" src="images/menu/smile.png" alt="user" />',
			),
			array(
				'start_html'	=> '<span>',
				'end_html'		=> '</span>',
				'value'			=>  __('USER'),
			),
			array(
				'value'			=> '<img class="right-arrow" src="images/common/right_arrow_white.png" alt="arrow" />',
			),
		),
	),
);
