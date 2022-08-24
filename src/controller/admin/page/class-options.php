<?php
 /**
  * This file is part of https://github.com/josantonius/wp-advanced-search repository.
  *
  * (c) Josantonius <hello@josantonius.dev>
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

namespace WAS\Controller\Admin\Page;

use Eliasis\Framework\App;
use Josantonius\WP_Menu\WP_Menu;
use Eliasis\Framework\Controller;
use Josantonius\WP_Register\WP_Register;

class Options extends Controller {

	public $slug = 'wp-advanced-search';

	public $data;

	public function set_menu() {

		WP_Menu::add(
			'menu',
			App::WAS()->getOption( 'menu', 'top-level' ),
			[ $this, 'render' ],
			[ $this, 'add_scripts' ],
			[ $this, 'add_styles' ]
		);
	}

	public function set_submenu() {

		WP_Menu::add(
			'submenu',
			App::WAS()->getOption( 'submenu', 'options' ),
			[ $this, 'render' ]
		);
	}

	public function add_scripts() {
		return;
		WP_Register::add(
			'script',
			App::WAS()->getOption( 'assets', 'js', 'vuetify' )
		);

		WP_Register::add(
			'script',
			App::WAS()->getOption( 'assets', 'js', 'wordpressAdvancedSearchAdmin' )
		);

		WP_Register::add(
			'script',
			App::WAS()->getOption( 'assets', 'js', 'vuetifyAdvancedSearchAdmin' )
		);

		WP_Register::add(
			'script',
			App::WAS()->getOption( 'assets', 'js', 'axios' )
		);
	}

	public function add_styles() {

		WP_Register::add(
			'style',
			App::WAS()->getOption( 'assets', 'css', 'googleIcons' )
		);

		WP_Register::add(
			'style',
			App::WAS()->getOption( 'assets', 'css', 'vuetify' )
		);

		WP_Register::add(
			'style',
			App::WAS()->getOption( 'assets', 'css', 'wordpressAdvancedSearchAdmin' )
		);

		WP_Register::add(
			'style',
			App::WAS()->getOption( 'assets', 'css', 'vuetifyAdvancedSearchAdmin' )
		);
	}

	public function select_centre( $id ) {

		return $this->model->select_centre( $id );
	}

	public function select_centres() {

		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'wordpressAdvancedSearchAdmin' ) ) {
			die( 'Busted!' );
		}

		$response = $this->model->select_centres();

		echo json_encode( $response );

		die();
	}

	public function insert_centre() {

		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'wordpressAdvancedSearchAdmin' ) ) {
			die( 'Busted!' );
		}

		$response = $this->model->insert_centre(
			$_POST['centre'],
			$_POST['activity'],
			$_POST['start_hour'],
			$_POST['end_hour'],
			$_POST['latitude'],
			$_POST['longitude'],
			$_POST['address'],
			$_POST['place_id']
		);

		echo json_encode( $response );

		die();
	}

	public function delete_centre() {

		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'wordpressAdvancedSearchAdmin' ) ) {
			die( 'Busted!' );
		}

		$response = $this->model->delete_centre( $_POST['id'] );

		echo json_encode( $response );

		die();
	}

	public function check_request() {
		if ( isset( $_POST['results-url'] ) && isset( $_POST['ignored-words'] ) ) {
			$slug = App::WAS()->getOption( 'slug' );
			update_option( $slug . '-results-url', $_POST['results-url'] );
			update_option( $slug . '-ignored-words', $_POST['ignored-words'] );
		}
	}

	public function render() {

		$this->check_request();

		$slug = App::WAS()->getOption( 'slug' );

		$words = preg_replace( '/\s+/', '', $_POST['ignored-words'] ?? get_option( $slug . '-ignored-words' ) );
		$url   = preg_replace( '/\s+/', '', $_POST['results-url'] ?? get_option( $slug . '-results-url' ) );

		$data = [
			'results-url' => $url,
			'ignored-words' => trim( $words, ',' ),
		];

		$page   = App::WAS()->getOption( 'path', 'page' );
		$layout = App::WAS()->getOption( 'path', 'layout' );

		$this->view->renderizate( $layout, 'header' );
		$this->view->renderizate( $page, 'options', $data );
	}
}
