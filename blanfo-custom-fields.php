<?php

/**
 * Plugin Name:       Blanfo Custom Fields
 * Plugin URI:        https://blanfo.ir
 * Description:       Custom Fields on Woocommerce
 * Version:           1.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Ali Zeinodini
 * Author URI:        https://alizeinodini.ir
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       bcf
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {exit;}


define('SOCIAL_IMPACT_VERSION', '1.0.0' );
define('BCF_PATH',plugin_dir_path(__FILE__));
define('BCF_URL',plugin_dir_url(__FILE__));
require_once BCF_PATH.'includes/BlanfoCustomFields.php';
$BCF = new BlanfoCustomFields();