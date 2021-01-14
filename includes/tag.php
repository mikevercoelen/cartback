<?php
defined('ABSPATH') || exit;

use \DrewM\MailChimp\MailChimp;

$CB_SETTINGS = get_option('cartback_setting');
$MC_API_KEY = $CB_SETTINGS['api_key'];
$MC_LIST_ID = $CB_SETTINGS['list_id'];
$MC_TAG = $CB_SETTINGS['mailchimp_tag'];
$MC_TAG_HARDCODED = '_cartback';

$MailChimp = new MailChimp($MC_API_KEY);

function cartback_handle_tag_mailchimp($email) {
  global $MC_TAG;
  global $MC_TAG_STATUS_ACTIVE;
  global $MC_TAG_HARDCODED;
  global $MC_MEMBER_STATUS_UNSUBSCRIBED;

  $subscriber_hash = MailChimp::subscriberHash($email);
  $is_subscribed = cartback_mc_is_subscribed($subscriber_hash);

  if (!$is_subscribed) {
    cartback_mc_subscribe($email, $MC_MEMBER_STATUS_UNSUBSCRIBED);
  }

  cartback_mc_add_tags($subscriber_hash, array(
    [
      'name' => $MC_TAG,
      'status' => $MC_TAG_STATUS_ACTIVE
    ],
    [
      'name' => $MC_TAG_HARDCODED,
      'status' => $MC_TAG_STATUS_ACTIVE
    ]
  ));
}

$MC_MEMBER_STATUS_NOT_FOUND = '404';
$MC_MEMBER_STATUS_UNSUBSCRIBED = 'unsubscribed';
$MC_TAG_STATUS_ACTIVE = 'active';
$MC_TAG_STATUS_INACTIVE = 'inactive';

function cartback_get_email($request) {
  if (is_user_logged_in()) {
    $current_user = wp_get_current_user();
    return $current_user->user_email;
  }

  $body = json_decode($request->get_body());
  return $body->email;
}

function cartback_mc_is_subscribed($subscriber_hash) {
  global $MC_MEMBER_STATUS_NOT_FOUND;

  $member = cartback_mc_get_member($subscriber_hash);
  $member_status = $member['status'];

  if ($member_status == $MC_MEMBER_STATUS_NOT_FOUND) {
    return false;
  }

  return true;
}

function cartback_mc_add_tags($subscriber_hash, $tags) {
  global $MailChimp;
  global $MC_LIST_ID;

  return $MailChimp->post("lists/$MC_LIST_ID/members/$subscriber_hash/tags", [
    'tags' => $tags
  ]);
}

function cartback_mc_subscribe($email, $status) {
  global $MailChimp;
  global $MC_LIST_ID;

  return $MailChimp->post("lists/" . $MC_LIST_ID . "/members", [
    'email_address' => $email,
    'status' => $status
  ]);
}

function cartback_mc_get_member($subscriber_hash) {
  global $MailChimp;
  global $MC_LIST_ID;

  return $MailChimp->get("lists/$MC_LIST_ID/members/$subscriber_hash");
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
