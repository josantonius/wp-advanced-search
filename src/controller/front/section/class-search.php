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

class Search extends Controller {

	public $slug = 'search';

	public $dom_document = null;

	public $current_index = null;

	public $matches = [];

	public $min_chars = 1;

	public $min_words = 2;

	public $word_matches_counter = 0;

	public $sentence_matches_counter = 0;

	public $matches_words = [];

	public $statements = [];

	public $elements = [
		[
			'index' => 0,
			'tagName' => 'h1',
			'attributes' =>
			[],
			'isParagraph' => false,
			'isHeading' => true,
		],
		[
			'index' => 1,
			'tagName' => 'h2',
			'attributes' =>
			[],
			'isParagraph' => false,
			'isHeading' => true,
		],
		[
			'index' => 2,
			'tagName' => 'h3',
			'attributes' =>
			[],
			'isParagraph' => false,
			'isHeading' => true,
		],
		[
			'index' => 3,
			'tagName' => 'h4',
			'attributes' =>
			[],
			'isParagraph' => false,
			'isHeading' => true,
		],
		[
			'index' => 4,
			'tagName' => 'h5',
			'attributes' =>
			[],
			'isParagraph' => false,
			'isHeading' => true,
		],
		[
			'index' => 5,
			'tagName' => 'h6',
			'attributes' =>
			[],
			'isParagraph' => false,
			'isHeading' => true,
		],
		[
			'index' => 6,
			'tagName' => 'p',
			'attributes' =>
			[],
			'isParagraph' => true,
			'isHeading' => false,
		],
	];

	public $current_element = null;

	public $excluded_tags = [ 'html', 'head', 'body' ];

	public $excluded_attributes = [ 'paragraph' ];

	public $categories = '';

	public $ignored_posts = '';

	public $hierarchy = "
        <h1></h1>
        <h2></h2>
        <h3></h3>
        <h4></h4>
        <h5></h5>
        <h6></h6>
        <p paragraph='true'></p>
    ";

	public $needle = '';

	public $results = [];

	public $array_needle = [];

	public $columns = [
		'post_id' => 'posts.ID',
		'post_title' => 'posts.post_title',
		'post_content' => 'posts.post_content',
		'post_status' => 'posts.post_status',
		'post_type' => 'posts.post_type',
		'post_guid' => 'posts.guid',
		'relationships_object_id' => 'relationships.object_id',
		'relationships_term_taxonomy_id' => 'relationships.term_taxonomy_id',
		'taxonomy_term_taxonomy_id' => 'taxonomy.term_taxonomy_id',
		'taxonomy_term_id' => 'taxonomy.term_id',
	];

	public $relevance = [
		'sentence_match_in_title' => 7,
		'sentence_match_in_content' => 6,
		'all_words_match_in_title' => 5,
		'all_words_match_in_content' => 4,
		'some_words_match_in_title' => 3,
		'some_words_match_in_content' => 2,
	];

	public $options = [
		'search_by_sentence' => true,
		'search_by_words' => true,
		'search_in_title' => true,
		'search_in_headings' => true,
		'search_in_content' => true,
		'search_in_pages' => true,
		'search_in_posts' => true,
		'search_in_all_post_categories' => true,
	];

	public $custom_options = [];

	public function getPosts( $needle, $limit = 9999, $options = [], $categories = '', $ignored_posts = '' ) {
		global $wpdb;

		$this->dom_document = new \DOMDocument();

		$this->customOptions = $options;

		$this->categories = $categories;

		$this->ignoredPosts = $ignored_posts;

		$slug = App::WAS()->getOption( 'slug' );

		$needle = strtolower( $needle );

		$this->needle = preg_replace( '/[^\p{L}\p{N}\s]/u', '', $needle );

		$this->needle = $this->clean( $needle );

		$this->array_needle = array_unique( array_filter( explode( ' ', $this->needle ) ) );

		$ignored_words = explode( ',', get_option( $slug . '-ignored-words' ) );

		if ( $this->getOption( 'search_by_words' ) && $this->getOption( 'search_by_sentence' ) ) {
			if ( count( array_filter( explode( ' ', $this->needle ) ) ) === 1 ) {
				$this->customOptions['search_by_words']    = false;
				$this->customOptions['search_by_sentence'] = true;
			}
		}

		if ( $this->getOption( 'search_by_words' ) ) {
			foreach ( $ignored_words as $word ) {
				$index = array_search( strtolower( $word ), array_map( 'strtolower', $this->array_needle ) );
				if ( false !== $index ) {
					unset( $this->array_needle[$index] );
				}
			}
		}

		$response = [];

		$query = $this->getPreparedQuery( $limit );

		$results = $wpdb->get_results( $wpdb->prepare( $query, $this->statements ) );

		foreach ( $results as $key => $post ) {
			$html       = strip_shortcodes( '<h1>' . $post->post_title . '</h1>' . $post->post_content );
			$response[] = [
				'id' => $post->ID,
				'title' => $this->markWordsMatches( $post->post_title ),
				'subtitle' => '',
				'url' => $post->guid,
				'results' => [],
				'tabs' => 0,
			];
		}

		return $response;
	}

	public function getSegmentedResults( $post_id, $needle, $offset = 0, $options = [] ) {
		$this->dom_document = new \DOMDocument();

		$this->customOptions = $options;

		$slug = App::WAS()->getOption( 'slug' );

		$ignored_words = explode( ',', get_option( $slug . '-ignored-words' ) );

		$needle = strtolower( $needle );

		$this->needle = preg_replace( '/[^\p{L}\p{N}\s]/u', '', $needle );

		$this->needle = $this->clean( $needle );

		$this->array_needle = array_unique( array_filter( explode( ' ', $this->needle ) ) );

		if ( $this->getOption( 'search_by_words' ) ) {
			foreach ( $ignored_words as $word ) {
				$index = array_search( strtolower( $word ), array_map( 'strtolower', $this->array_needle ) );
				if ( false !== $index ) {
					unset( $this->array_needle[$index] );
				}
			}
		}

		$post = get_post( (int) $post_id );
		if ( isset( $post->post_title ) && isset( $post->post_content ) && isset( $post->guid ) ) {
			$html = strip_shortcodes( '<h1>' . $post->post_title . '</h1>' . $post->post_content );
			$this->saveElements( $html, $post->guid, $post->post_title, $offset );
		}
		return $this->results;
	}

	public function getPreparedQuery( $limit ) {
		return 'SELECT ' . $this->getColumnsToSelect() .
			   'FROM ' . $this->getFrom() . ' ' . $this->getJoins() . ' ' .
			   'WHERE ' . $this->getMatchingConditions() . ' ' .
			   'AND ' . $this->getStatusRules() . ' ' .
			   'AND ' . $this->getTypeRules() . ' ' .
						   $this->getCategoriesRules() . ' ' .
						   $this->getExcludedPostsRules() . ' ' .
			   'LIMIT ' . $limit;
	}

	public function getColumnsToSelect() {
		$query  = $this->columns['post_title'] . ', ';
		$query .= $this->columns['post_content'] . ', ';

		$query .= $this->columns['post_guid'] . ', ';

		$query .= $this->columns['post_id'] . ' ';

		return $query;
	}

	public function getMatchingConditions() {
		global $wpdb;
		$this->statements = [];
		$conditions       = '(';

		if ( $this->getOption( 'search_by_sentence' ) ) {
			if ( $this->getOption( 'search_in_title' ) ) {
				$conditions        .= $this->columns['post_title'] . ' RLIKE "%s" OR ';
				$this->statements[] = '[[:<:]]' . $this->needle . '[[:>:]]';
			}
			if ( $this->getOption( 'search_in_content' ) ) {
				$conditions        .= $this->columns['post_content'] . ' RLIKE "%s" OR ';
				$this->statements[] = '[[:<:]]' . $this->needle . '[[:>:]]';
			}
		}

		if ( $this->getOption( 'search_by_words' ) ) {
			foreach ( $this->array_needle as $key => $needle ) {
				if ( $this->getOption( 'search_in_title' ) ) {
					$conditions        .= $this->columns['post_title'] . ' RLIKE "%s" OR ';
					$this->statements[] = '[[:<:]]' . $needle . '[[:>:]]';
				}
				if ( $this->getOption( 'search_in_content' ) ) {
					$conditions        .= $this->columns['post_content'] . ' RLIKE "%s" OR ';
					$this->statements[] = '[[:<:]]' . $needle . '[[:>:]]';
				}
			}
		}
		return rtrim( $conditions, ' OR' ) . ')';
	}

	public function getFrom() {
		global $wpdb;

		return $wpdb->posts . ' posts';
	}

	public function getJoins() {
		if ( $this->getOption( 'search_in_posts' ) && ! empty( $this->categories ) ) {
			global $wpdb;

			$query  = 'LEFT JOIN ' . $wpdb->term_relationships . ' relationships' . ' ';
			$query .= 'ON (' . $this->columns['post_id'] . ' = ' . $this->columns['relationships_object_id'] . ') ';
			$query .= 'LEFT JOIN ' . $wpdb->term_taxonomy . ' taxonomy' . ' ';
			$query .= 'ON (' . $this->columns['relationships_term_taxonomy_id'] . ' = ' . $this->columns['taxonomy_term_taxonomy_id'] . ')';

			return $query;
		}

		return '';
	}

	public function getMatchesRules() {
		$condition = "MATCH(%s) AGAINST('%s' IN BOOLEAN MODE)";

		$needle = $this->needle;

		if ( $this->getOption( 'search_by_sentence' ) && ! $this->getOption( 'search_by_words' ) ) {
			$needle = '"' . $needle . '"';
		}

		return sprintf(
			$condition,
			rtrim( $this->getColumnsToSearch(), ', ' ),
			$needle
		);
	}

	public function getStatusRules() {
		return $this->columns['post_status'] . ' = "publish"';
	}

	public function getTypeRules() {
		$posts = $this->getOption( 'search_in_posts' ) ? '"post", ' : '';
		$pages = $this->getOption( 'search_in_pages' ) ? '"page", ' : '';

		return $this->columns['post_type'] . ' IN (' . rtrim( $posts . $pages, ', ' ) . ')';
	}

	public function getCategoriesRules() {
		if ( $this->getOption( 'search_in_posts' ) && ! empty( $this->categories ) ) {
			$term_id = $this->columns['taxonomy_term_id'];
			return 'AND ( ' .
						$this->columns['post_type'] . ' IN ("post") ' . 'AND ' . $term_id . ' IN (' . $this->categories . ') ' .
						'OR ' . $this->columns['post_type'] . ' = "page"' .
					')';
		}
		return '';
	}

	public function getExcludedPostsRules() {
		if ( empty( $this->ignoredPosts ) ) {
			return '';
		}

		return 'AND ' . $this->columns['post_id'] . ' NOT IN (' . $this->ignoredPosts . ')';
	}

	public function setElementsFromHierachy( string $hierarchy ) {
		$nodes = $this->getElementsByTagName( $hierarchy );

		$count = 0;

		foreach ( $nodes as $key => $node ) {
			if ( $this->isExcludedTag( $node ) || ! $this->isValidNodeType( $node ) ) {
				continue;
			}
			$is_paragraph           = $node->getAttribute( 'paragraph' ) !== '';
			$this->elements[$count] = [
				'index' => $count,
				'tagName' => $node->tagName,
				'attributes' => $this->getNodeAttributes( $node ),
				'isParagraph' => $is_paragraph,
				'isHeading' => ! $is_paragraph,
			];

			unset( $nodes->{$key} );

			$count++;
		}
	}

	public function getElementsByTagName( string $html, string $tag = '*' ) {
		if ( ! @$this->dom_document->loadHTML( $html ) ) {
			return [];
		}
		return $this->dom_document->getElementsByTagName( $tag );
	}

	public function isValidNode( \DOMNode $node ) {
		return $this->isValidNodeType( $node ) &&
			   $this->isValidElement( $node ) &&
			  ! $this->isExcludedTag( $node );
	}

	public function isExcludedTag( \DOMNode $node ) {
		return in_array( $node->tagName, $this->excluded_tags );
	}

	public function isValidNodeType( \DOMNode $node ) {
		return ( $node->nodeType ?? 0 ) === 1;
	}

	public function isValidElement( \DOMNode $node ) {
		$this->setCurrentElement( $node );

		$tag_name = $node->tagName ?? '';
		return in_array( $tag_name, array_keys( $this->elements ) ) && $this->current_element;
	}

	public function getNodeAttributes( \DOMNode $node, $attributes = [] ) {
		if ( $node->hasAttributes() ) {
			foreach ( $node->attributes as $attribute ) {
				$attribute_name = $attribute->nodeName;
				if ( ! $this->isExcludedAttribute( $attribute_name ) ) {
					$attributes[$attribute_name] = $attribute->nodeValue;
				}
			}
		}
		return $attributes;
	}

	public function isExcludedAttribute( string $attribute_name ) {
		return in_array( $attribute_name, $this->excluded_attributes );
	}

	public function setCurrentElement( \DOMNode $node ) {
		$index = $this->getTemplateElementIndex( $node->tagName ?? '' );

		$this->current_element = $this->elements[$index] ?? null;
	}

	public function getTemplateElementIndex( string $name ) {
		$index = array_search( $name, array_column( $this->elements, 'tagName' ) );
		return false !== $index ? $index : -1;
	}

	public function isHeadingElement() {
		return $this->current_element['isHeading'] ?? false;
	}

	public function getMatchesNodes( string $html ) {
		$condition = '';

		$count = count( $this->elements );

		foreach ( $this->elements as $key => $element ) {
			$tag_name = $element['tagName'];
			// $condition .= "name()=\"${tagName}\"";
			if ( ( $this->getOption( 'search_in_headings' ) && $element['isHeading'] ) || ( $this->getOption( 'search_in_content' ) && $element['isParagraph'] ) ) {
				$condition .= "self::${tag_name} or ";
			}
		}
		$condition = rtrim( $condition, ' or ' );

		libxml_use_internal_errors( true );
		@$this->dom_document->loadHTML( $html );
		libxml_clear_errors();
		$xp = new \DOMXPath( $this->dom_document );

		$contains = '';

		$needle = $this->needle;
		if ( $this->getOption( 'search_by_sentence' ) ) {
			$contains .= "
                contains(
                    normalize-space(
                        translate(
                            .,
                            'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
                            'abcdefghijklmnopqrstuvwxyz'
                        )
                    ),
                    '$needle'
                ) or";
		}

		if ( $this->getOption( 'search_by_words' ) && count( $this->array_needle ) > 0 ) {
			$contains .= ' (';
			foreach ( $this->array_needle as $word ) {
				if ( strlen( $word ) >= $this->min_chars ) {
					$contains .= "
                        contains(
                            normalize-space(
                                translate(
                                    .,
                                    'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
                                    'abcdefghijklmnopqrstuvwxyz'
                                )
                            ),
                            '$word'
                        ) or";
				}
			}
			$contains = rtrim( $contains, ' or' ) . ')';
		}
		$contains = rtrim( $contains, ' or' );
		$query    = "//*[${condition}][$contains]";

		return $xp->query( $query );
	}

	public function saveElements( string $html, string $guid, $title, $offset ) {
		$nodes = $this->getMatchesNodes( $html );

		foreach ( $nodes as $index => $node ) {
			if ( 0 !== $index && $index <= $offset ) {
				continue;
			}
			$this->setCurrentElement( $node );
			$element_index = $this->getCurrentElementIndex();
			$value         = $node->nodeValue;
			$needle        = implode( ',', $this->array_needle );

			$search = "?search=$needle";
			if ( \strpos( $guid, '?' ) !== false ) {
				$search = "&search=$needle";
			}

			$url = $guid . $search . "&index=$index" . '&by_sentence=' . $this->getOption( 'search_by_sentence' ) . '&by_words=' . $this->getOption( 'search_by_words' );

			$value = $this->clean( $value );
			$value = '<span>' . $this->markWordsMatches( $value ) . '</span>';

			$search_by_words        = $this->getOption( 'search_by_words' );
			$search_by_sentence     = $this->getOption( 'search_by_sentence' );
			$has_minimum_word_count = $this->word_matches_counter >= $this->min_words;
			$has_match_on_sentence  = $this->sentence_matches_counter > 0;

			if ( $search_by_words && $search_by_sentence ) {
				if ( ! $has_minimum_word_count && ! $has_match_on_sentence ) {
					continue;
				}
			} elseif ( $search_by_words && ! $search_by_sentence ) {
				if ( ! $has_minimum_word_count ) {
					continue;
				}
			} elseif ( ! $search_by_words && $search_by_sentence ) {
				if ( ! $has_match_on_sentence ) {
					continue;
				}
			}

			$tag = $this->current_element['tagName'];

			$items = $this->getPreviousHeading(
				$node,
				$element_index,
				[
					[
						'value' => "<div class='result-heading-$tag'>" . $value . '</div>',
					],
				]
			);

			if ( strtolower( $title ) === strip_tags( strtolower( $items[0]['value'] ) ) ) {
				unset( $items[0]['value'] );
			}

			$items = array_filter( $items );

			if ( count( $items ) > 0 ) {
				$this->results[] = [
					'items' => $items,
					'url' => $url,
				];
			}

			if ( count( $this->results ) >= 10 ) {
				break;
			}
		}
	}

	public function getPreviousHeading( \DOMNode $node, int $index, array $data ) {
		$previous_node = $node->previousSibling ?? null;
		if ( $previous_node ) {
			if ( $this->isValidNode( $previous_node ) && $this->isHeadingElement() ) {
				if ( $this->getCurrentElementIndex() < $index ) {
					$index  = $this->getCurrentElementIndex();
					$value  = $previous_node->nodeValue;
					$value  = $this->clean( $value );
					$value  = '<span>' . $this->markWordsMatches( $value ) . '</span>';
					$tag    = $this->current_element['tagName'];
					$data[] = [
						'value' => "<div class='result-heading-$tag'>" . $value . '</div>',
					];
				}
			}
			return $this->getPreviousHeading( $previous_node, $index, $data );
		}
		$parent_node = $node->parentNode ?? false;

		return $parent_node ? $this->getPreviousHeading( $parent_node, 99, $data ) : array_reverse( $data );
	}

	public function mergeMatches() {
		array_multisort( array_map( 'count', $this->results ), SORT_DESC, $this->results );

		foreach ( $this->results as $current_item ) {
			foreach ( $this->results as $index => $match ) {
				$count = 0;
				if ( $current_item['items'] !== $match['items'] ) {
					foreach ( $match['items'] as $key => $item ) {
						$element = $current_item['items'][$key] ?? null;
						if ( $element && $item['value'] === $element['value'] && $item['index'] === $element['index'] ) {
							$count++;
						}
					}
				}
				if ( count( $match['items'] ) === $count ) {
					unset( $this->results[$index] );
				}
			}
		}
		$this->results = array_values( $this->results );
	}

	public function markWordsMatches( $string ) {
		$this->word_matches_counter = 0;

		if ( $this->getOption( 'search_by_words' ) ) {
			$this->matches_words = [];

			if ( count( $this->array_needle ) > 0 ) {
				$string = preg_replace_callback(
					"/\b(" . implode( "\b|\b", $this->array_needle ) . ")\b/i",
					function ( $matches ) {
						if ( strlen( $matches[0] ) >= $this->min_chars ) {
							$this->matches_words[] = strtolower( $matches[0] );
						}
						return "<span style='font-weight: 600;'>$matches[0]</span>";
					},
					$string
				);
			}

			$this->matches_words = array_unique( $this->matches_words );

			$this->word_matches_counter = count( $this->matches_words );
		}

		if ( $this->getOption( 'search_by_sentence' ) && $this->word_matches_counter < $this->min_words ) {
			return $this->markSentenceMatches( $string );
		}

		return $string;
	}

	public function markSentenceMatches( $string ) {
		$string = strip_tags( $string );

		$this->sentence_matches_counter = 0;

		$needle = [ $this->needle ];

		return preg_replace_callback(
			"/\b(" . implode( "\b|\b", $needle ) . ")\b/i",
			function ( $matches ) {
				if ( strlen( $matches[0] ) >= $this->min_chars ) {
					$this->sentence_matches_counter++;
				}
				return "<span style='font-weight: 600;'>$matches[0]</span>";
			},
			$string
		);
	}

	public function setRelevance() {
		foreach ( $this->results as $key => $element ) {
			$relevance = 0;
			foreach ( array_reverse( $element['items'] ) as $index => $item ) {
				$words_number   = count( $this->array_needle );
				$word_value     = ( $index * 5 ) + 100 / $words_number;
				$value          = strip_tags( $item['value'] );
				$sentence_match = preg_match_all( "/\b$this->needle\b/i", $value );

				if ( $sentence_match > 0 ) {
					$relevance += $word_value * $words_number * 2;
				}
				$matches = 0;
				foreach ( $this->array_needle as $word ) {
					$word_matches = preg_match_all( "/\b$word\b/i", $value );
					if ( $word_matches > 0 ) {
						$matches++;
						if ( strlen( $word ) >= $this->min_chars ) {
							$relevance += $word_matches * $word_value;
						}
					}
				}
				if ( count( $this->array_needle ) === $matches ) {
					$relevance += $word_value * $words_number * 2;
				}
			}
			$this->results[$key]['relevance'] = $relevance;
		}
	}

	public function clean( string $string ) {
		$string = mb_convert_encoding( $string, 'HTML-ENTITIES', 'UTF-8' );
		$string = preg_replace(
			array(
				'/(\r\n\r\n|\r\r|\n\n)(\s+)?/',
				'/\r\n|\r|\n/',
				'/\s{2,}/',
				'/[\t\n]/',
				'/&#?[a-z0-9]{2,8};/i',
			),
			array( ' ', ' ', ' ', ' ', ' ' ),
			$string
		);
		$string = str_replace( "\xc2\xa0", ' ', $string );
		$string = mb_convert_encoding( $string, 'Windows-1252', 'UTF-8' );

		return $string;
	}

	public function getCurrentElementIndex() {
		return $this->current_element['index'] ?? -1;
	}

	public function getOption( $option ) {
		return $this->customOptions[$option] ?? $this->options[$option] ?? null;
	}

	public function init( $params = [] ) {
		ob_start();
		$this->render();
		$output = ob_get_clean();
		return $output;
	}

	public function search_on_posts() {
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'wordpressAdvancedSearchFront' ) ) {
			die( 'Busted!' );
		}

		$cats       = [];
		$offset     = $_POST['offset'];
		$needle     = $_POST['needle'];
		$categories = $_POST['categories'];

		$options = [
			'search_by_words' => 'true' === $_POST['search_by_words'],
			'search_by_sentence' => 'true' === $_POST['search_by_sentence'],
			'search_in_title' => 'true' === $_POST['search_in_title'],
			'search_in_headings' => 'true' === $_POST['search_in_headings'],
			'search_in_content' => 'true' === $_POST['search_in_content'],
			'search_in_pages' => 'true' === $_POST['search_in_pages'],
			'search_in_posts' => 'true' === $_POST['search_in_posts'],
			'search_in_all_post_categories' => 'true' === $_POST['search_in_all_post_categories'],
		];

		$limit = $offset . ',' . ( $offset + 10 );

		$response = $this->getPosts( $needle, $limit, $options, $categories );

		echo json_encode( $response );
		die();
	}

	public function get_segmented_results() {
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'wordpressAdvancedSearchFront' ) ) {
			die( 'Busted!' );
		}

		$id     = (int) $_POST['id'];
		$needle = $_POST['needle'];

		$options = [
			'search_by_words' => 'true' === $_POST['search_by_words'],
			'search_by_sentence' => 'true' === $_POST['search_by_sentence'],
			'search_in_title' => 'true' === $_POST['search_in_title'],
			'search_in_headings' => 'true' === $_POST['search_in_headings'],
			'search_in_content' => 'true' === $_POST['search_in_content'],
			'search_in_pages' => 'true' === $_POST['search_in_pages'],
			'search_in_posts' => 'true' === $_POST['search_in_posts'],
			'search_in_all_post_categories' => 'true' === $_POST['search_in_all_post_categories'],
		];

		$offset = $_POST['offset'];

		$response = $this->getSegmentedResults( $id, $needle, $offset, $options );

		echo json_encode( $response );
		die();
	}

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

	public function get_current_user_id() {
		if ( ! function_exists( 'wp_get_current_user' ) ) {
			return 0;
		}
		$user = wp_get_current_user();
		return ( isset( $user->ID ) ? (int) $user->ID : 0 );
	}

	public function add_styles() {}

	public function add_scripts() {}

	public function render() {
		$slug                = App::WAS()->getOption( 'slug' );
		$data['categories']  = $this->get_categories();
		$data['results-url'] = get_option( $slug . '-results-url' );
		$path                = App::WAS()->getOption( 'path', 'front-section' );
		$this->view->renderizate( $path, $this->slug, $data );
	}
}
