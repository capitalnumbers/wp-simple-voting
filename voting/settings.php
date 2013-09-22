<?php

/**
 * Voting settings, menu for wp-admin
 */

// Add Voting menu
add_action('admin_menu', 'wsv_create_menu_voting');

function wsv_create_menu_voting() {
    $vote_list_page = add_menu_page(
            $page_title = 'WP Simple Voting',
            $menu_title = 'Voting',
            $capability = 'administrator',
            $menu_slug  = 'wsv-view-voting',
            $function   = 'wsv_view_voting',
            $icon_url   = '',
            $position   = 99
    );
}

function wsv_view_voting() {
    include_once 'admin-list-voting.php';
}

