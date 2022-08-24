<?php
 /**
  * This file is part of https://github.com/josantonius/wp-advanced-search repository.
  *
  * (c) Josantonius <hello@josantonius.dev>
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

use Eliasis\Framework\App;

$icons_url = App::WAS()->getOption( 'url', 'icons' );

return [

	'menu' => [
		'top-level' => [
			'title'      => __( 'WAS', 'wp-advanced-search' ),
			'name'       => __( 'WAS', 'wp-advanced-search' ),
			'capability' => 'manage_options',
			'slug'       => 'wp-advanced-search',
			'function'   => '',
			'icon_url'   => $icons_url . 'wp-advanced-search-menu-admin.png',
			'position'   => 25,
		],
	],
	'submenu' => [
		'places' => [
			'parent'     => 'wp-advanced-search',
			'title'      => __( 'Options', 'wp-advanced-search' ),
			'name'       => __( 'Options', 'wp-advanced-search' ),
			'capability' => 'manage_options',
			'slug'       => 'wp-advanced-search',
			'function'   => '',
		],
	],
];
