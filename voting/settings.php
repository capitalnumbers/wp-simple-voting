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
            $capability = 'manage_options',
            $menu_slug  = 'wsv-view-voting',
            $function   = 'wsv_view_voting',
            $icon_url   = '',
            $position   = 99
    );

    // Show voting list
    $voting_setting_page = add_submenu_page(
            $parent_slug = 'wsv-view-voting',
            $page_title = 'Show voting list',
            $menu_title = 'Voting List',
            $capability = 'manage_options',
            $menu_slug = 'wsv-view-voting',
            $function = 'wsv_view_voting'
    );

    // Voting settings
    $voting_setting_page = add_submenu_page(
            $parent_slug = 'wsv-view-voting',
            $page_title = 'Voting Settings',
            $menu_title = 'Settings',
            $capability = 'manage_options',
            $menu_slug = 'wsv-voting-settings',
            $function = 'wsv_voting_settings'
    );

}

function wsv_view_voting() {
    include_once 'admin-list-voting.php';
}

function wsv_voting_settings() {
    include_once 'voting-settings.php';
}

