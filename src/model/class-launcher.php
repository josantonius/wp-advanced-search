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

class Launcher extends Model {

	public function create_tables() {}

	public function remove_tables() {}

	public function set_options() {

		$slug = App::WAS()->getOption( 'slug' );

		$actual_version    = App::WAS()->getOption( 'version' );
		$installed_version = get_option( $slug ) . '-version';

		if ( ! $installed_version ) {
			add_option( $slug . '-version', $actual_version );
			add_option( $slug . '-results-url', '' );
			add_option( $slug . '-ignored-words', 'of,to' );
		} else {
			if ( $installed_version < $actual_version ) {
				add_option( $slug . '-version', $actual_version );
				add_option( $slug . '-results-url', '' );
				add_option( $slug . '-ignored-words', 'of,to' );
			}
		}
	}
}
