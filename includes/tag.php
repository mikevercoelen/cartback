<?php

defined('ABSPATH') || exit;

$CB_SETTINGS = get_option('cartback_setting');
$MC_API_KEY = $CB_SETTINGS['api_key'];
$MC_LIST_ID = $CB_SETTINGS['list_id'];
$MC_TAG = $CB_SETTINGS['mailchimp_tag'];
$MC_TAG_HARDCODED = '_cartback';

function cartback_handle_tag_mailchimp($email) {
  global $MC_MEMBER_STATUS_NOT_FOUND;
  global $MC_TAG;
  global $MC_TAG_STATUS_ACTIVE;
  global $MC_TAG_HARDCODED;
  global $MC_MEMBER_STATUS_UNSUBSCRIBED;

  $subscriber_hash = MailChimp::subscriberHash($email);
  $is_subscribed = cartback_mc_is_subscribed($subscriber_hash);

  if (!is_subscribed) {
    cartback_mc_subscribe($email, $MC_MEMBER_STATUS_UNSUBSCRIBED);
  }

  cartback_mc_add_tags($subscriber_hash, array(
    [
      'name' => $MC_TAG,
      'status' => $MC_TAG_STATUS_ACTIVE
    ],
    [
      'name': $MC_TAG_HARDCODED,
      'status' => $MC_TAG_STATUS_ACTIVE
    ]
  ));
}

function cartback_tag($request) {
  $email = cartback_get_email($request);
  cartback_handle_tag_mailchimp($email);
  return true;
}

add_action('rest_api_init', function () {
  register_rest_route('cartback/v1', '/tag', array(
    'methods' => 'POST',
    'callback' => 'cartback_tag'
  ));
});
