<?php
/*
 * B-code : Online Editor
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
$list_header_config = array(
	array(
		'start_html'	=> '<div class="list-header">',
		'end_html'		=> '</div>',
		array(
			'start_html'	=> '<form name="header_form" id="header_form" method="post" action="index.php" target="main">',
			'end_html'		=> '</form>',
			array('class'	=> 'B_Hidden', 'name'=> 'default_row_per_page'),
			array(
				'start_html'	=> '<ul class="search">',
				'end_html'		=> '</ul>',
				array(
					'start_html'	=> '<li class="search">',
					'end_html'		=> '</li>',
					array(
						'class'			=> 'B_InputText',
						'name'			=> 'keyword',
						'attr'			=> 'class="keyword" maxlength="100" placeholder="keyword"',
					),
					array(
						'id'			=> 'search-button',
						'name'			=> 'search-button',
						'class'			=> 'B_Submit',
						'attr'			=> 'class="search-button" onclick="bframe.submit(\'header_form\', \'' . $this->module . '\', \'list\', \'select\')"',
						'value'			=> __('Search'),
					),
				),
				array(
					'auth_filter'	=> 'super_admin',
					'start_html'	=> '<li>',
					'end_html'		=> '</li>',
					array(
						'name'			=> 'add-button',
						'start_html'	=> '<span id="add-button" class="add-button" onclick="bframe.submit(\'header_form\', \'' . $this->module . '\', \'form\', \'select\', \'insert\')">',
						'end_html'		=> '</span>',
						'value'			=> __('Add '),
					),
				),
			),
		),
	),
);
