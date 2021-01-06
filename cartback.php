<?php

/*
  Plugin Name: 	CartBack
  Plugin URI:		https://github.com/mikevercoelen/cartback
  Description: 	Abandoned cart Mailchimp automation for WooCommerce
  Version: 		0.1
  Author: 		Benbodhi
  Author URI: 	https://benbodhi.com
  Text Domain: 	cartback
  Domain Path:	/languages
  License: 		GPLv2 or later
  License URI:	http://www.gnu.org/licenses/gpl-2.0.html
	Copyright 2021 and beyond | Benbodhi (email : wp@benbodhi.com)
*/

defined('ABSPATH') || exit;

define('CARTBACK_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CARTBACK_PLUGIN_PATH', plugin_dir_path(__FILE__));

include(CARTBACK_PLUGIN_PATH . 'admin/settings.php');
// include(CARTBACK_PLUGIN_PATH . 'vendor/MailChimp.php');
// include(CARTBACK_PLUGIN_PATH . 'includes/tag.php');
// TODO: make me ...
//  include(CARTBACK_PLUGIN_PATH . 'includes/untag.php');

add_action('wp_enqueue_scripts', 'cartback_enqueue_scripts_styles');

function cartback_enqueue_scripts_styles() {
  if (class_exists('woocommerce') && is_checkout()) {
    wp_enqueue_script('cartback-checkout', CARTBACK_PLUGIN_URL . '/public/cartback.min.js', array(), false, true);
  }
}

$MC_API_KEY = get_option('cartback_setting', 'api_key');
$MC_LIST_ID = get_option('cartback_setting', 'list_id');
$MC_TAG = get_option('cartback_setting', 'mailchimp_tag');

function cartback_untag() {
  global $MC_TAG;
  global $MC_API_KEY;
  global $MC_LIST_ID;

  var_dump([
    'apiKey' => $MC_API_KEY,
    'listId' => $MC_LIST_ID,
    'tag' => $MC_TAG
  ]);
}

add_action('rest_api_init', function () {
  register_rest_route('cartback/v1', '/untag', array(
    'methods' => 'GET',
    'callback' => 'cartback_untag'
  ));
});
