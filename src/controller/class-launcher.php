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
use Josantonius\WP_Register\WP_Register;

class Launcher extends Controller {

	public function init() {
		add_action( 'init', [ $this, 'set_language' ] );

		App::WAS()->getControllerInstance( 'Shortcode', 'controller' )->register_ajax_methods();

		$this->options_page = App::WAS()->getControllerInstance( 'Options', 'admin-page' );
		$this->run_ajax();
		if ( is_admin() ) {
			return $this->admin();
		}

		$this->front();
	}

	public function add_front_end_actions() {
		add_action( 'template_redirect', [ $this, 'template_redirect_action' ] );
	}

	public function run_ajax() {
		$methods = [ 'select_centres', 'insert_centre', 'delete_centre' ];

		foreach ( $methods as $method ) {
			add_action( 'wp_ajax_' . $method, [ $this->options_page, $method ] );
			add_action( 'wp_ajax_nopriv_' . $method, [ $this->options_page, $method ] );
		}
	}

	public function activation() {
		$plugin = isset( $_REQUEST['plugin'] ) ? filter_var( wp_unslash( $_REQUEST['plugin'] ), FILTER_SANITIZE_STRING ) : null;

		check_admin_referer( "activate-plugin_$plugin" );

		$this->model->set_options();

		global $wpdb;

		flush_rewrite_rules();
	}

	public function deactivation() {
		$plugin = isset( $_REQUEST['plugin'] ) ? filter_var( wp_unslash( $_REQUEST['plugin'] ), FILTER_SANITIZE_STRING ) : null;

		check_admin_referer( "deactivate-plugin_$plugin" );

		flush_rewrite_rules();
	}

	public function admin() {
		$this->set_menus(
			App::WAS()->getOption( 'pages' ),
			App::WAS()->getOption( 'namespaces', 'admin-page' )
		);
	}

	public function set_language() {
		$slug = App::WAS()->getOption( 'slug' );

		load_plugin_textdomain(
			$slug,
			false,
			$slug . '/languages/'
		);
	}

	public function set_menus( $pages = [], $namespace = '' ) {
		$set_menu = false;

		foreach ( $pages as $page ) {
			$page = $namespace . $page;

			if ( ! class_exists( $page ) ) {
				continue;
			}

			$instance = call_user_func( $page . '::getInstance' );

			if ( method_exists( $instance, 'init' ) ) {
				call_user_func( [ $instance, 'init' ] );
			}

			if ( ! $set_menu && method_exists( $instance, 'set_menu' ) ) {
				$set_menu = true;
				call_user_func( [ $instance, 'set_menu' ] );
			}

			if ( method_exists( $instance, 'set_submenu' ) ) {
				call_user_func( [ $instance, 'set_submenu' ] );
			}
		}
	}

	public function front() {
		$this->add_front_end_actions();
		$this->add_styles();
		$this->add_scripts();
	}

	public function template_redirect_action() {
		if ( ! is_admin() && is_page() ) {
			App::WAS()->getControllerInstance( 'Shortcode', 'controller' )->load();
		}
	}

	public function add_styles() {
		WP_Register::add(
			'style',
			App::WAS()->getOption( 'assets', 'css', 'googleIconsFront' )
		);

		WP_Register::add(
			'style',
			App::WAS()->getOption( 'assets', 'css', 'vuetifyFront' )
		);

		WP_Register::add(
			'style',
			App::WAS()->getOption( 'assets', 'css', 'wordpressAdvancedSearchFront' )
		);

		WP_Register::add(
			'style',
			App::WAS()->getOption( 'assets', 'css', 'vuetifyAdvancedSearchFront' )
		);
	}

	public function add_scripts() {
		WP_Register::add(
			'script',
			App::WAS()->getOption( 'assets', 'js', 'vuetifyFront' )
		);

		WP_Register::add(
			'script',
			App::WAS()->getOption( 'assets', 'js', 'wordpressAdvancedSearchFront' )
		);

		WP_Register::add(
			'script',
			App::WAS()->getOption( 'assets', 'js', 'vuetifyAdvancedSearchFront' )
		);

		WP_Register::add(
			'script',
			App::WAS()->getOption( 'assets', 'js', 'axiosFront' )
		);

		WP_Register::add(
			'script',
			App::WAS()->getOption( 'assets', 'js', 'wordpressAdvancedSearchScroll' )
		);
	}
}
