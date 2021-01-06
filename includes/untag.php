<?php

defined('ABSPATH') || exit;

function cartback_handle_untag_mailchimp($email) {
  global $MC_TAG_STATUS_INACTIVE;

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
  ))
}

function cartback_untag($request) {
  $email = cartback_get_email($request);
  cartback_handle_untag_mailchimp($email);
  return true;
}

add_action('rest_api_init', function () {
  register_rest_route('cartback/v1', '/untag', array(
    'methods' => 'POST',
    'callback' => 'cartback_untag'
  ));
});
