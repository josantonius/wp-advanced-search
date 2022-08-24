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

$namespace = App::WAS()->getOption( 'namespaces', 'admin-page' );

return [
	'hooks' => [],
];
