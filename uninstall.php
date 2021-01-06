<?php

if (!defined('WP_UNINSTALL_PLUGIN')) exit;

$CB_SETTINGS = get_option('cartback_setting');
$UNINSTALL_CHECKED = $CB_SETTINGS['uninstall_remove'];

if (!$UNINSTALL_CHECKED) exit;

delete_option('cartback_setting');

if (is_multisite()) {
    global $wpdb;
    $blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);

    if(!empty($blogs))
    {
        foreach($blogs as $blog) 
        {
        switch_to_blog($blog['blog_id']);
            delete_option('cartback_setting');
        }
    }
} else {
    delete_option('cartback_setting');
}