<?php

/*
  Plugin Name:  CartBack
  Plugin URI:   https://github.com/mikevercoelen/cartback
  Description:  Abandoned cart Mailchimp automation for WooCommerce
  Version:      0.1
  Author:       Benbodhi
  Author URI:   https://benbodhi.com
  Text Domain:  cartback
  Domain Path:  /languages
  License:      GPLv2 or later
  License URI:  http://www.gnu.org/licenses/gpl-2.0.html
  Copyright 2021 and beyond | Benbodhi (email : wp@benbodhi.com)
*/

defined('ABSPATH') || exit;

define('CARTBACK_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CARTBACK_PLUGIN_PATH', plugin_dir_path(__FILE__));

include(CARTBACK_PLUGIN_PATH . 'admin/settings.php');

// $CB_SETTINGS = get_option('cartback_setting');
// $MC_API_KEY = $CB_SETTINGS['api_key'];
// $MC_LIST_ID = $CB_SETTINGS['list_id'];

use \DrewM\MailChimp\MailChimp;

// if ( !empty($MC_API_KEY && $MC_LIST_ID) ) {
  include(CARTBACK_PLUGIN_PATH . 'vendor/MailChimp.php');
  include(CARTBACK_PLUGIN_PATH . 'includes/tag.php');

  add_action('wp_enqueue_scripts', 'cartback_checkout_page');
  add_action( 'wp_enqueue_scripts', 'cartback_thankyou_page' );

  function cartback_checkout_page() {
    if (class_exists('woocommerce') && is_checkout()) {
      wp_enqueue_script('cartback-checkout', CARTBACK_PLUGIN_URL . '/public/cartback.min.js', array(), false, true);
    }
  }

  function cartback_thankyou_page() {
    if (class_exists('woocommerce') && is_order_received_page()) {
      global $MC_TAG_STATUS_INACTIVE;
      global $MC_TAG;

      $order_id  = absint( get_query_var('order-received') );
      $order = new WC_Order($order_id);
      $email = $order->get_billing_email();

      $subscriber_hash = MailChimp::subscriberHash($email);
      $is_subscribed = cartback_mc_is_subscribed($email);

      if (!$is_subscribed) {
        return false;
      }

      cartback_mc_add_tags($subscriber_hash, array(
        [
          'name' => $MC_TAG,
          'status' => $MC_TAG_STATUS_INACTIVE
        ]
      ));
    }
  }
// }
