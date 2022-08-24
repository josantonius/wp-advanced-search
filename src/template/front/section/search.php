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
?>

 <div class="opacity-on" style="height: 48px; min-width: 290px; width: 100%;">
	<v-app id="app">
	<vuetify-advance-search
	  id="results-list"
	  ref="vuetify_advance_search"
	  :matches="matches"
	  categories='<?php echo $categories; ?>'
	  :custom-needle="customNeedle"
	  :categories-menu-title="'Filter by categories'"
	  :is-paginated="isPaginated"
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
