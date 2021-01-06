<?php

defined('ABSPATH') || exit;

$CB_SETTINGS = get_option('cartback_setting');
$MC_API_KEY = $CB_SETTINGS['api_key'];
$MC_LIST_ID = $CB_SETTINGS['list_id'];
$MC_TAG = $CB_SETTINGS['mailchimp_tag'];

use \DrewM\MailChimp\MailChimp;

$MailChimp = new MailChimp($MC_API_KEY);

$MC_MEMBER_STATUS_NOT_FOUND = '404';
$MC_MEMBER_STATUS_UNSUBSCRIBED = 'unsubscribed';
$MC_TAG_STATUS_ACTIVE = 'active';

function cartback_mc_get_member($subscriber_hash) {
  global $MailChimp;
  global $MC_LIST_ID;

  return $MailChimp->get("lists/$MC_LIST_ID/members/$subscriber_hash");
}

function cartback_mc_subscribe($email) {
  global $MailChimp;
  global $MC_MEMBER_STATUS_UNSUBSCRIBED;
  global $MC_LIST_ID;

  return $MailChimp->post("lists/" . $MC_LIST_ID . "/members", [
    'email_address' => $email,
    'status' => $MC_MEMBER_STATUS_UNSUBSCRIBED
  ]);
}

function cartback_mc_add_tag($subscriber_hash) {
  global $MailChimp;
  global $MC_TAG;
  global $MC_TAG_STATUS_ACTIVE;
  global $MC_LIST_ID;

  return $MailChimp->post("lists/$MC_LIST_ID/members/$subscriber_hash/tags", [
    'tags' => array(
      [
        'name' => $MC_TAG,
        'status' => $MC_TAG_STATUS_ACTIVE
      ]
    )
  ]);
}

function cartback_handle_mailchimp($email) {
  global $MC_MEMBER_STATUS_NOT_FOUND;

  $subscriber_hash = MailChimp::subscriberHash($email);
  $member = cartback_mc_get_member($subscriber_hash);
  $member_status = $member['status'];

  if ($member_status === $MC_MEMBER_STATUS_NOT_FOUND) {
    cartback_mc_subscribe($email);
  }

  cartback_mc_add_tag($subscriber_hash);
}

function cartback_get_email($request) {
  if (is_user_logged_in()) {
    $current_user = wp_get_current_user();
    return $current_user->user_email;
  }

  $body = json_decode($request->get_body());
  return $body->email;
}

function cartback_tag($request) {
  $email = cartback_get_email($request);
  cartback_handle_mailchimp($email);
  return true;
}

add_action('rest_api_init', function () {
  register_rest_route('cartback/v1', '/tag', array(
    'methods' => 'POST',
    'callback' => 'cartback_tag'
  ));
});
