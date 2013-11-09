<?php

include_once 'voting/voting-functions.php';

/* ========================================= WP AJAX ========================================= */
add_action('init', 'ajax_script_enqueuer');

add_action("wp_ajax_do_post_vote", "do_post_vote");
add_action("wp_ajax_nopriv_do_post_vote", "do_post_vote");

add_action("wp_ajax_do_reset_vote_single", "do_reset_vote_single");
add_action("wp_ajax_nopriv_do_reset_vote_single", "do_reset_vote_single");

add_action("wp_ajax_do_reset_vote_all", "do_reset_vote_all");
add_action("wp_ajax_nopriv_do_reset_vote_all", "do_reset_vote_all");

function ajax_script_enqueuer() {
   wp_register_script('wsv_actions_script', plugins_url('/js/ajax-scripts.js', __FILE__), array('jquery'));
   wp_localize_script('wsv_actions_script', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));

   wp_enqueue_script('jquery');
   wp_enqueue_script('wsv_actions_script');
}

function do_post_vote () {
    global $wpdb;
    global $wsv_plugin_prefix;
    $response = '';
    $tbl_vote = $wpdb->prefix.$wsv_plugin_prefix."user_votes";
    
    // WILL BE IN OPTIONS LATER
    $vote_limit = 1;
    $data = sanitize_text_field($_POST['post_id']);

    if ($data != '') {
        if (get_post_meta($data, '_wsv_voting_disabled', TRUE) != "on") { // If voting is not disabled
            if (!in_array($data, $_SESSION['wsv_has_voted'])) { // If voted post id exists in session
                $user_ip = wsv_get_user_ip();
                $votes = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$tbl_vote} WHERE post_id = %d AND user_ip = %s", $data, $user_ip));

                if ($votes != NULL && count($votes) >= $vote_limit) {
                    $response = json_encode(array('e'));
                } else {
                    $ins_arr = array(
                        'post_id'       => $data,
                        'vote_count'    => 1,
                        'user_ip'       => $user_ip
                    );
                    $vote_ins = $wpdb->insert($tbl_vote, $ins_arr);
                    if ($vote_ins) {
                        array_push($_SESSION['wsv_has_voted'], $data);
                        $response = json_encode(array(
                                'status' => 's',
                                'total_votes' => wsv_get_vote_count($data)
                            ));
                    } else {
                        $response = json_encode(array(
                                'status' => 'e'
                            ));
                    }
                }
            } else {
                $response = json_encode(array(
                        'status' => 'e'
                    ));
            }
        } else {
            $response = json_encode(array(
                    'status' => 'e'
                ));
        }
    } else {
        $response = json_encode(array(
                'status' => 'e'
            ));
    }
    
    echo $response;
    die;
}

function do_reset_vote_single() {
    $post_id = sanitize_text_field($_POST['post_id']);
    $reset_status = wsv_do_voting_reset('single', $post_id);
    
    if ($reset_status === FALSE) {
        $response = array('e');
    } else {
        $response = array('s');
    }
    
    echo json_encode($response);
    die;
}

function do_reset_vote_all() {
    $secure_chk = sanitize_text_field($_POST['called_reset']);
    
    if ($secure_chk == "1") {
        $reset_status = wsv_do_voting_reset('all');

        if ($reset_status === FALSE) {
            $response = array('e');
        } else {
            $response = array('s');
        }
    } else {
        $response = array('e');
    }
    
    echo json_encode($response);
    die;
}

