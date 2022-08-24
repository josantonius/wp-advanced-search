<?php
 /**
  * This file is part of https://github.com/josantonius/wp-advanced-search repository.
  *
  * (c) Josantonius <hello@josantonius.dev>
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

namespace WAS\Controller;

use Eliasis\Framework\App;
use Eliasis\Framework\Controller;

class Shortcode extends Controller {


	public function load() {
		$shortcodes = App::WAS()->getOption( 'shortcodes' );

		foreach ( $shortcodes as $shortcode ) {
			$this->add( $shortcode );
		}
		return false;
	}

	public function add( $shortcode ) {
		$instance = App::WAS()->getControllerInstance( $shortcode['class'], $shortcode['namespace'] );
		$shortcode['scripts'] ? $instance->add_scripts() : false;
		$shortcode['styles'] ? $instance->add_styles() : false;
		add_shortcode( $shortcode['id'], [ $instance, 'init' ] );
		return true;
	}

	public function register_ajax_methods() {
		$shortcodes = App::WAS()->getOption( 'shortcodes' );
		foreach ( $shortcodes as $shortcode ) {
			$instance = App::WAS()->getControllerInstance( $shortcode['class'] );
			$methods  = $shortcode['ajax-methods'];
			foreach ( $methods as $method ) {
				add_action( 'wp_ajax_' . $method, [ $instance, $method ] );
				add_action( 'wp_ajax_nopriv_' . $method, [ $instance, $method ] );
			}
		}
	}
}
