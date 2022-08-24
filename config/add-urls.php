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

$url = App::PUBLIC_URL();

return [
	'url' => [
		'js'    => $url . 'js/',
		'css'   => $url . 'css/',
		'json'  => $url . 'json/',
		'images'  => $url . 'images/',
		'icons' => $url . 'images/icons/',
	],
];
