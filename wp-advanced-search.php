<?php
/**
 * WordPress Advanced Search.
 *
 * Plugin Name: WordPress Advanced Search
 * Plugin URI:  https://github.com/josantonius/wp-advanced-search.git
 * Description: WordPress Advanced Search.
 * Version:     1.0.0
 * Author:      Josantonius
 * Author URI:  https://josantonius.com/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wp-advanced-search
 * Domain Path: /languages
 */

use Eliasis\Framework\App;

if ( ! function_exists( 'add_action' ) || ! defined( 'ABSPATH' ) ) {
	echo 'I can do when called directly.';
	die;
}

require 'vendor/autoload.php';

App::run( __DIR__, 'wordpress-plugin', 'WAS' );

$launcher = App::getControllerInstance( 'Launcher', 'controller' );

register_activation_hook( __FILE__, [ $launcher, 'activation' ] );

register_deactivation_hook( __FILE__, [ $launcher, 'deactivation' ] );

$launcher->init();
