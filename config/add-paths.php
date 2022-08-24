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

$root_path = App::ROOT();

return [

	'path' => [

		'modules'   => $root_path . 'modules/',
		'public'    => $root_path . 'public/',
		'json'      => $root_path . 'public/json/',
		'images'      => $root_path . 'public/images/',
		'layout'    => $root_path . 'src/template/layout/',
		'page'      => $root_path . 'src/template/page/',
		'front-section'     => $root_path . 'src/template/front/section/',
	],
];
