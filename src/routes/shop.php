<?php
$routes[] = array(
	'name' => 'Shop',
	'path' => '/shop',
	'routes' => array(
		array(
			'name' => 'storefront',
			'path' => '',
		),
		array('name' => 'checkout'),
		array('name' => 'order'),
	),
);
$routes[] = array(
	'name' => 'Cart',
	'path' => '/cart',
	'routes' => array(
		array(
			'name' => 'cart',
			'path' => '',
		),
		array(
			'name' => 'add-item',
			'path' => '/add',
			'method' => 'post',
		),
		array(
			'name' => 'update-item',
			'path' => '/update',
			'method' => 'post',
		),
	),
);
