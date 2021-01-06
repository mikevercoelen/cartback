<?php

defined('ABSPATH') || exit;

use \DrewM\MailChimp\MailChimp;

$MailChimp = new MailChimp($MC_API_KEY);

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
  $member = cartback_mc_get_member($subscriber_hash);
  $member_status = $member['status'];

  if ($member_status == $MC_MEMBER_STATUS_NOT_FOUND) {
    return false
  }

  return true
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
