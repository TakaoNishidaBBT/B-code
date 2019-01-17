<?php
$list_config =
array(
	'start_html'	=> '<ul class="event">',
	'end_html'		=> '</ul>',

	'empty_message'	=> '',

	'row'		=>
	array(
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		array(
			'name'			=> 'event_link',
			'class'			=> 'B_Link',
			array(
				'start_html'	=> '<div class="date">',
				'end_html'		=> '</div>',
				'name'			=> 'event_date',
			),
			array(
				'start_html'	=> '<div class="place">',
				'end_html'		=> '</div>',
				'name'			=> 'event_place',
			),
			array(
				'start_html'	=> '<div class="title">',
				'end_html'		=> '</div>',
				array(
					'name'			=> 'event_title',
				),
			),
		),
		array(
			'name'			=> 'node_name',
			'class'			=> 'B_Data',
		),
		array(
			'name'			=> 'event_id',
			'class'			=> 'B_Data',
		),
	),
);
