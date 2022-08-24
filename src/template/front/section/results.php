<?php
 /**
  * This file is part of https://github.com/josantonius/wp-advanced-search repository.
  *
  * (c) Josantonius <hello@josantonius.dev>
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

use Eliasis\Framework\View;

$data       = View::getOption();
$categories = json_encode( $data['categories'] );
$url        = $data['results-url'];
$needle     = $_GET['search'] ?? '';
$options    = json_encode(
	[
		'search_by_sentence' => $_GET['search_by_sentence'] ?? true,
		'search_by_words' => $_GET['search_by_words'] ?? true,
		'search_in_headings' => $_GET['search_in_headings'] ?? true,
		'search_in_content' => $_GET['search_in_content'] ?? true,
		'search_in_posts' => $_GET['search_in_posts'] ?? true,
		'search_in_pages' => $_GET['search_in_pages'] ?? true,
		'search_in_title' => $_GET['search_in_title'] ?? true,
		'search_in_all_post_categories' => $_GET['search_in_all_post_categories'] ?? true,
	]
);

?>
<div>
	<v-app id="results">
	  <vuetify-advance-search
		  id="results-list"
		  ref="vuetify_advance_search"
		  :matches="matches"
		  categories='<?php echo $categories; ?>'
		  options='<?php echo $options; ?>'
		  custom-needle="<?php echo $needle; ?>"
		  :categories-menu-title="'Filter by categories'"
		  :is-paginated="true"
		  all-results-url="<?php echo $url; ?>"
		  :page-number="pageNumber"
		  :size="size"
		  @on-change-matches="onChangeMatches"
		  @on-change-needle="onChangeNeedle"
		  @on-change-options="onChangeOptions"
		  @on-change-categories="onChangeCategories"
		  @on-expand-result="onExpandResult"
		  @on-next-page="onNextPage"
		  @on-click-last-tab="onClickLastTab"
	  ></vuetify-advance-search>
</v-app>
</div>
