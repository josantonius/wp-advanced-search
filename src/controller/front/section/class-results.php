<?php
 /**
  * This file is part of https://github.com/josantonius/wp-advanced-search repository.
  *
  * (c) Josantonius <hello@josantonius.dev>
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

namespace WAS\Controller\Front\Section;

use Eliasis\Framework\App;
use Eliasis\Framework\Controller;

class Results extends Controller {

	public $slug = 'results';

	public function init( $params = [] ) {
		ob_start();
		$this->render();
		$output = ob_get_clean();
		return $output;
	}

	public function add_scripts() {}

	public function search_terms() {
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'wordpressAdvancedSearchFront' ) ) {
			die( 'Busted!' );
		}

		global $wp_query;

		$cats            = [];
		$needle          = $_POST['needle'];
		$categories      = explode( ',', $_POST['categories'] );
		$search_in_pages = $_POST['searchInPages'];
		$search_in_posts = $_POST['searchInPosts'];

		$response  = [];
		$post_type = [];

		foreach ( $categories as $key => $item ) {
			$term   = get_term_by( 'name', $item, 'category' );
			$cats[] = $term->term_id;
		}

		if ( $search_in_posts ) {
			$post_type[] = 'post';
		}
		if ( $search_in_pages ) {
			$post_type[] = 'page';
		}

		if ( $post_type ) {
			$args = array(
				's' => $needle,
				'cat' => $cats,
				'post_type' => $post_type,
				'posts_per_page' => -1,
			);

			$wp_query = new \WP_Query( $args );

			if ( $wp_query->post_count > 0 ) {
				foreach ( $wp_query->posts as $key => $post ) {
					$data                   = [];
					$data['html']           = '<h1>' . $post->post_title . '</h1>';
					$data['html']          .= $post->post_content;
					$data['nodeTargetUrl'] .= $post->guid;
					$data['allResultsUrl']  = get_search_link( $needle );
					$response[]             = $data;
				}
			}
		}
		echo json_encode( $response );
		die();
	}

	public function get_current_user_id() {
		if ( ! function_exists( 'wp_get_current_user' ) ) {
			return 0;
		}
		$user = wp_get_current_user();
		return ( isset( $user->ID ) ? (int) $user->ID : 0 );
	}

	public function add_styles() {}

	public function get_categories() {
		$categories = get_categories();

		$list = [];

		foreach ( $categories as $key => $item ) {
			if ( $item->name ) {
				$list[] = [
					'id' => $item->cat_ID,
					'value' => $item->name,
				];
			}
		}
		return $list;
	}

	public function render() {
		$slug                = App::WAS()->getOption( 'slug' );
		$data['categories']  = $this->get_categories();
		$data['results-url'] = get_option( $slug . '-results-url' );
		$path                = App::WAS()->getOption( 'path', 'front-section' );
		$this->view->renderizate( $path, $this->slug, $data );
	}
}
