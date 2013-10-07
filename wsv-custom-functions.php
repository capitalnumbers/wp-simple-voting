<?php

/**
 * Custom Functions for wsv plugin 
 */


/**********************************************************************
 * wsv Session Management
 **********************************************************************/
function wsv_session() {
    if (!session_id()) {
        session_start();
    }
    
    if (!is_array($_SESSION['wsv_has_voted'])) {
        $_SESSION['wsv_has_voted'] = array();
    }
}
add_action('init', 'wsv_session');


/**********************************************************************
 * Include ajax scripts
 **********************************************************************/
include_once 'wsv-ajax-functions.php';


/**********************************************************************
 * Include widgets
 **********************************************************************/
//include_once 'widgets/team-details.php';


/**********************************************************************
 * Include voting functions and pages
 **********************************************************************/
include_once 'voting/voting-functions.php';
include_once 'voting/settings.php';


/**********************************************************************
 * Set siteurl, noconflict variable in js
 **********************************************************************/
add_action('wp_head', 'wsv_front_head_scripts');
add_action('admin_head', 'wsv_front_head_scripts');
function wsv_front_head_scripts() {
?>
<script>
    var $ = jQuery.noConflict();
    var siteurl = "<?php echo esc_js(site_url()); ?>";
    var themeurl = "<?php echo esc_js(get_bloginfo('stylesheet_directory')); ?>";
    var pluginurl = "<?php echo esc_js(plugin_dir_url(__FILE__)); ?>";
</script>
<?php
}


/**********************************************************************
 * Get page ID by slug
 **********************************************************************/
function wsv_get_ID_by_slug($page_slug, $return = 'ID') {
    $page = get_page_by_path($page_slug);
    if ($page) {
        if ($return == "ID")
            return $page->ID;
        if ($return == "name")
            return $page->post_title;
    } else {
        return null;
    }
}



// CUSTOM POST TYPES FOR TESTING
// Post type - Office
function qc_custom_post_office() {
    $labels = array(
        'name'               => _x('Offices', 'post type general name'),
        'singular_name'      => _x('Office', 'post type singular name'),
        'add_new'            => _x('Add New', 'office'),
        'add_new_item'       => __('Add New Office'),
        'edit_item'          => __('Edit Office'),
        'new_item'           => __('New Office'),
        'all_items'          => __('All Offices'),
        'view_item'          => __('View Office'),
        'search_items'       => __('Search Offices'),
        'not_found'          => __('No offices found'),
        'not_found_in_trash' => __('No offices found in the Trash'),
        'parent_item_colon'  => '',
        'menu_name'          => 'Offices'
    );
    
    $args = array(
        'labels'        => $labels,
        'description'   => 'Holds our offices and office specific data',
        'public'        => true,
        /*'menu_position' => 5,*/
        'supports'      => array('title', 'editor', 'thumbnail', 'excerpt'),
        'has_archive'   => true,
    );
    register_post_type('office', $args);    
}
add_action('init', 'qc_custom_post_office');


// Post type - News
function qc_custom_post_news() {
    $labels = array(
        'name'               => _x('News', 'post type general name'),
        'singular_name'      => _x('News', 'post type singular name'),
        'add_new'            => _x('Add New', 'award'),
        'add_new_item'       => __('Add New News'),
        'edit_item'          => __('Edit News'),
        'new_item'           => __('New News'),
        'all_items'          => __('All News'),
        'view_item'          => __('View News'),
        'search_items'       => __('Search News'),
        'not_found'          => __('No news found'),
        'not_found_in_trash' => __('No news found in the Trash'),
        'parent_item_colon'  => '',
        'menu_name'          => 'News'
    );
    
    $args = array(
        'labels'        => $labels,
        'description'   => 'Holds news information',
        'public'        => true,
        /*'menu_position' => 5,*/
        'supports'      => array('title', 'editor', 'thumbnail', 'excerpt'),
        'has_archive'   => true,
    );
    register_post_type('news', $args);  
}
add_action('init', 'qc_custom_post_news');


// Post type - Contact
function qc_custom_post_contact() {
    $labels = array(
        'name'               => _x('Contacts', 'post type general name'),
        'singular_name'      => _x('Contacts', 'post type singular name'),
        'add_new'            => _x('Add New', 'Contact'),
        'add_new_item'       => __('Add New Contact'),
        'edit_item'          => __('Edit Contacts'),
        'new_item'           => __('New Contacts'),
        'all_items'          => __('All Contacts'),
        'view_item'          => __('View Contacts'),
        'search_items'       => __('Search Contacts'),
        'not_found'          => __('No contacts found'),
        'not_found_in_trash' => __('No contacts found in the Trash'),
        'parent_item_colon'  => '',
        'menu_name'          => 'Contacts'
    );
    
    $args = array(
        'labels'        => $labels,
        'description'   => 'Holds contact information',
        'public'        => true,
        /*'menu_position' => 5,*/
        'supports'      => array('title', 'thumbnail'),
        'has_archive'   => true,
        'taxonomies'    =>array('post_tag')
    );
    register_post_type('contact', $args);   
}
add_action('init', 'qc_custom_post_contact');

