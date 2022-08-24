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

$icons  = App::WAS()->getOption( 'url', 'icons' );
$json   = App::WAS()->getOption( 'url', 'json' );
$css    = App::WAS()->getOption( 'url', 'css' );
$js     = App::WAS()->getOption( 'url', 'js' );
$images = App::WAS()->getOption( 'url', 'images' );

return [

	'assets' => [

		'js' => [
			'vuetify' => [
				'name'      => 'vuetify',
				'url'       => $js . 'vuetify.min.js',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.2.1',
				'footer'    => false,
				'params'    => [
					'icons_url' => $icons,
				],
			],
			'vuetifyFront' => [
				'name'      => 'vuetifyFront',
				'url'       => $js . 'vuetify.min.js',
				'place'     => 'front',
				'deps'      => [],
				'version'   => '1.2.1',
				'footer'    => false,
				'params'    => [],
			],
			'wordpressAdvancedSearchScroll' => [
				'name'      => 'wordpressAdvancedSearchScroll',
				'url'       => $js . 'wp-advanced-search-scroll.min.js',
				'place'     => 'front',
				'deps'      => [],
				'version'   => '1.2.1',
				'footer'    => true,
				'params'    => [
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'id' => get_the_ID(),
					'location_error_gif' => $images . 'error.gif',
				],
			],
			'wordpressAdvancedSearchFront' => [
				'name'      => 'wordpressAdvancedSearchFront',
				'url'       => $js . 'wp-advanced-search-front.min.js',
				'place'     => 'front',
				'deps'      => [],
				'version'   => '1.2.1',
				'footer'    => true,
				'params'    => [
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'location_loader_gif' => $images . 'location-loader.gif',
					'location_error_gif' => $images . 'error.gif',
				],
			],
			'wordpressAdvancedSearchAdmin' => [
				'name'      => 'wordpressAdvancedSearchAdmin',
				'url'       => $js . 'wp-advanced-search-admin.min.js',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.2.1',
				'footer'    => true,
				'params'    => [
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				],
			],
			'axios' => [
				'name'      => 'axios',
				'url'       => 'https://unpkg.com/axios/dist/axios.min.js',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.0.0',
				'footer'    => true,
				'params'    => [],
			],
			'axiosFront' => [
				'name'      => 'axiosFront',
				'url'       => 'https://unpkg.com/axios/dist/axios.min.js',
				'place'     => 'front',
				'deps'      => [],
				'version'   => '1.0.0',
				'footer'    => true,
				'params'    => [],
			],
			'vuetifyAdvancedSearchAdmin' => [
				'name'      => 'vuetifyAdvancedSearchAdmin',
				'url'       => $js . 'vuetify-advance-search.min.js',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.0.0',
				'footer'    => true,
				'params'    => [],
			],
			'vuetifyAdvancedSearchFront' => [
				'name'      => 'vuetifyAdvancedSearchFront',
				'url'       => $js . 'vuetify-advance-search.min.js',
				'place'     => 'front',
				'deps'      => [],
				'version'   => '1.0.0',
				'footer'    => true,
				'params'    => [],
			],
		],

		'css' => [
			'vuetify' => [
				'name'      => 'vuetify',
				'url'       => $css . 'vuetify.min.css',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.3.9',
				'media'     => '',
			],
			'vuetifyFront' => [
				'name'      => 'vuetifyFront',
				'url'       => $css . 'vuetify.min.css',
				'place'     => 'front',
				'deps'      => [],
				'version'   => '1.3.9',
				'media'     => '',
			],
			'googleIcons' => [
				'name'      => 'googleIcons',
				'url'       => 'https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.3.9',
				'media'     => '',
			],
			'googleIconsFront' => [
				'name'      => 'googleIconsFront',
				'url'       => 'https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons',
				'place'     => 'front',
				'deps'      => [],
				'version'   => '1.3.9',
				'media'     => '',
			],
			'wordpressAdvancedSearchFront' => [
				'name'      => 'wordpressAdvancedSearchFront',
				'url'       => $css . 'wp-advanced-search-front.min.css',
				'place'     => 'front',
				'deps'      => [],
				'version'   => '1.2.1',
				'media'     => '',
			],
			'wordpressAdvancedSearchAdmin' => [
				'name'      => 'wordpressAdvancedSearchAdmin',
				'url'       => $css . 'wp-advanced-search-admin.min.css',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.2.1',
				'media'     => '',
			],
			'vuetifyAdvancedSearchFront' => [
				'name'      => 'vuetifyAdvancedSearchFront',
				'url'       => $css . 'vuetify-advance-search.min.css',
				'place'     => 'front',
				'deps'      => [],
				'version'   => '1.2.1',
				'media'     => '',
			],
			'vuetifyAdvancedSearchAdmin' => [
				'name'      => 'vuetifyAdvancedSearchAdmin',
				'url'       => $css . 'vuetify-advance-search.min.css',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.2.1',
				'media'     => '',
			],
		],
	],
];
