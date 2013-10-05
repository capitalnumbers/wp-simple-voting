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

