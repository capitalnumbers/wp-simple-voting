<?php
/*
Plugin Name: WP Simple Voting
Plugin URI: #
Description: WP Simple Voting enables a simple voting module through which you can vote the posts and have a ranking system.
Version: 1.0
Author: Debobrata
Author URI: http://www.atdeb.com/
License: GPLv2 or later
*/

/* ========================================================================
 * Plugin globals
 * ======================================================================== */
$wsv_plugin_prefix = 'wsv_';
$wsv_plugin_version = 1.0;


/**********************************************************************
 * Plugin includes
 **********************************************************************/
include_once 'wsv-custom-functions.php';
include_once 'wsv-meta-box.php';
// Widgets include
include_once 'widgets/most-voted.php';


/* ======================================================================
 * Plugin Scripts
 * ====================================================================== */
add_action('admin_enqueue_scripts', 'wsv_js_include');
add_action('wp_enqueue_scripts', 'wsv_js_include');
function wsv_js_include() {
    wp_enqueue_script('jquery');
}


/* ======================================================================
 * Plugin Styles
 * ====================================================================== */
add_action('admin_print_styles', 'wsv_css_include');
add_action('wp_print_styles', 'wsv_css_include');
function wsv_css_include() {
    /* Register the style handle */
    wp_register_style($handle = 'wsv-admin-tpl-css', $src = plugins_url('css/style.css', __FILE__), $deps = array(), $ver = '1.0.0', $media = 'all');
        
    /* Enqueue stylesheet */
    wp_enqueue_style('wsv-admin-tpl-css');
}


/**********************************************************************
 * Creating table for voting after plugin is activated
 **********************************************************************/
register_activation_hook(__FILE__, 'wsv_activation');
function wsv_activation() {
    global $wpdb;
    $wsv_plugin_prefix = 'wsv_';
    $tbl_vote_name = $wpdb->prefix.$wsv_plugin_prefix."user_votes";
    
    $tbl_vote_create_sql = "CREATE TABLE IF NOT EXISTS {$tbl_vote_name} (
                                id BIGINT(20) PRIMARY KEY AUTO_INCREMENT,
                                post_id BIGINT(20),
                                vote_count int(11),
                                user_ip varchar(20)
                            )";
    
    $wpdb->query($tbl_vote_create_sql);
}

