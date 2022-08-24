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
use Josantonius\Hook\Hook;

$data = View::getOption();
?>
<br>
<form method="post">
  <span style="color: #0073aa;">SEARCH RESULTS PAGE URL:</span> <br><input type="text" name="results-url" value="<?php echo $data['results-url']; ?>"><br><br>
  <span style="color: #0073aa;">IGNORED WORDS:</span> <br><input value="<?php echo $data['ignored-words']; ?>" name="ignored-words"><br><br>
  <button style="margin-left: 4px;
	padding: 4px 8px;
	position: relative;
	top: -3px;
	text-decoration: none;
	border: none;
	border: 1px solid #ccc;
	border-radius: 2px;
	background: #f7f7f7;
	text-shadow: none;
	font-weight: 600;
	font-size: 13px;
	line-height: normal;
	color: #0073aa;
	cursor: pointer;
	outline: 0;" type="submit" value="Submit" class="components-button editor-post-publish-panel__toggle is-button is-primary">SAVE</button>
</form>
