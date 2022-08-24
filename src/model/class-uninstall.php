<?php
 /**
  * This file is part of https://github.com/josantonius/wp-advanced-search repository.
  *
  * (c) Josantonius <hello@josantonius.dev>
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

namespace WAS\Model;

use Eliasis\Framework\App;
use Eliasis\Framework\Model;

class Uninstall extends Model {

	public function remove_all() {

		$slug = App::WAS()->getOption( 'slug' );

		delete_option( $slug . '-version' );

		delete_site_option( $slug . '-version' );
	}
}
