<?php
/**
 * Meta box for WP Simple Voting 
 */

add_action('add_meta_boxes', 'wsv_meta_box_voting');
function wsv_meta_box_voting() {
    $allowed_post_types = get_option('_wsv_enable_voting_cpt');
    $is_page_allowed = get_option('_wsv_enable_voting_page');
    $allowed_post_types = unserialize($allowed_post_types);
    if (!$allowed_post_types)
        $allowed_post_types = array();
    array_push($allowed_post_types, 'post');
    if ($is_page_allowed == "on") :
        array_push($allowed_post_types, 'page');
    endif;



    foreach ($allowed_post_types as $pt) :
        add_meta_box('wsv_mb_voting', 'WP Simple Voting', 'wsv_mb_voting_cb', $pt, 'side', 'high');
    endforeach;
}

// Callback function for voting meta box
function wsv_mb_voting_cb() {
    global $post;
    $value = get_post_meta($post->ID, '_wsv_voting_disabled', TRUE);
    if (isset($value) && esc_attr($value == "on")) $check = ' checked="checked"';
    
    wp_nonce_field('wsv_mb_voting_nonce', 'wsv_mb_nonce_value');
    
    echo '<div class="voting-select-meta">';
    echo '<input type="checkbox" name="voting_disable_cb" id="voting_disable_cb" value="on"'.$check.' />';
    echo '<label class="selectit wsv" for="voting_disable_cb">Disable Voting for this post?</label>';
    echo '</div>';
}


add_action('save_post', 'wsv_meta_box_voting_save');
function wsv_meta_box_voting_save($post_id) {
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if(!isset($_POST['wsv_mb_nonce_value']) || !wp_verify_nonce($_POST['wsv_mb_nonce_value'], 'wsv_mb_voting_nonce')) return;
    if(!current_user_can('edit_post')) return;
    
    // Saving vote option
    $chk_value = (isset($_POST['voting_disable_cb']) && $_POST['voting_disable_cb']) ? 'on' : 'off';
    update_post_meta($post_id, '_wsv_voting_disabled', $chk_value);
}

