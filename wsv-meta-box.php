<?php
/**
 * Meta box for WP Simple Voting 
 */

add_action('add_meta_boxes', 'wsv_meta_box_voting');
function wsv_meta_box_voting() {
    //$allowed_post_types = array('post');
    add_meta_box('wsv_mb_voting', 'WP Simple Voting', 'wsv_mb_voting_cb', 'post', 'side', 'high');
}

// Callback function for voting meta box
function wsv_mb_voting_cb() {
    global $post;
    $values = get_post_custom( $post->ID ); var_dump($values);
    $check = isset( $values['my_meta_box_check'] ) ? esc_attr( $values['my_meta_box_check'] ) : '';
    
    wp_nonce_field('wsv_mb_voting_nonce', 'wsv_mb_nonce_value');
    
    echo '<div class="voting-select-meta">';
    echo '<input type="checkbox" name="voting_select_checkbox" id="voting_select_checkbox" valye="on" />';
    echo '<label class="selectit wsv" for="voting_select_checkbox">Enable Voting?</label>';
    echo '</div>';
}


add_action('save_post', 'wsv_meta_box_voting_save');
function wsv_meta_box_voting_save($post_id) {
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if(!isset($_POST['wsv_mb_nonce_value']) || !wp_verify_nonce($_POST['wsv_mb_nonce_value'], 'wsv_mb_voting_nonce')) return;
    if(!current_user_can('edit_post')) return;
    
    echo 'ss';die;

    // This is purely my personal preference for saving check-boxes
    $chk_value = isset($_POST['voting_select_checkbox']) && $_POST['voting_select_checkbox'] ? 'on' : 'off';
    update_post_meta($post_id, '_wsv_voting_enabled', $chk_value);
}

