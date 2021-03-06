<?php
/*
 * B-code : Online Editor
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
$list_config = 
array(
	'start_html'	=> '<table class="user-list">',
	'end_html'		=> '</table>',

	'empty_message'	=> '<table class="user-list"></table>',

	'row'		=>
	array(
		'start_html'			=> '<tr>',
		'start_html_invalid'	=> '<tr class="invalid">',
		'end_html'				=> '</tr>',
		'class'					=> 'B_Row',
		array(
			'start_html'	=> '<td>',
			'end_html'		=> '</td>',
			'class'			=> 'B_Text',
			'name'			=> 'user_name',
		),
		array(
			'start_html'	=> '<td class="hidden">',
			'end_html'		=> '</td>',
			'class'			=> 'B_Text',
			'name'			=> 'user_id',
		),
	),
);
