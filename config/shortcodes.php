<?php
 /**
  * This file is part of https://github.com/josantonius/wp-advanced-search repository.
  *
  * (c) Josantonius <hello@josantonius.dev>
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

return [
	'shortcodes' => [
		[
			'id'                => 'wp-advanced-search',
			'class'             => 'Search',
			'namespace'         => 'front-section',
			'scripts'           => true,
			'styles'            => true,
			'get-credits'       => false,
			'only-users-logged' => false,
			'ajax-methods'      => [
				'search_on_posts',
				'get_segmented_results',
			],
		],
		[
			'id'                => 'wp-advanced-search-results',
			'class'             => 'Results',
			'namespace'         => 'front-section',
			'scripts'           => true,
			'styles'            => true,
			'get-credits'       => false,
			'only-users-logged' => false,
			'ajax-methods'      => [],
		],
	],
];
