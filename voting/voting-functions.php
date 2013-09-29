<?php

/*
 * Custom functions for voting
 */


/**********************************************************************
 * Get user ip
 **********************************************************************/
function wsv_get_user_ip() {
    if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $ip = $_SERVER["REMOTE_ADDR"];
    } else {
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    if(strpos($ip, ',') !== false) {
        $ip = explode(',', $ip);
        $ip = $ip[0];
    }
    return esc_attr($ip);
}


/**********************************************************************
 * Get user votes of a post
 **********************************************************************/
function wsv_get_votes($post_id, $limit = 1) {
    global $wpdb;
    global $wsv_plugin_prefix;
    
    $user_ip = wsv_get_user_ip();
    $tbl_vote = $wpdb->prefix.$wsv_plugin_prefix."user_votes";
    $sql = $wpdb->prepare("SELECT * FROM {$tbl_vote} WHERE post_id = %d AND user_ip = %s", $post_id, $user_ip);
    $votes = $wpdb->get_results($sql);
    
    if ($votes != NULL && count($votes) >= $limit) {
        return FALSE;
    } else {
        return $votes;
    }
}


/**********************************************************************
 * Get user votes of a post
 **********************************************************************/
function wsv_get_vote_count($post_id) {
    global $wpdb;
    global $wsv_plugin_prefix;
    
    $tbl_vote = $wpdb->prefix.$wsv_plugin_prefix."user_votes";
    $sql = $wpdb->prepare("SELECT count(id) AS total_votes FROM {$tbl_vote} WHERE post_id = %d", $post_id);
    $votes = $wpdb->get_row($sql);

    if ($votes) {
        return $votes->total_votes;
    } else {
        return FALSE;
    }
}

/**********************************************************************
 * Voting function
 **********************************************************************/
function wsv_voting($post_id = '') {
    if ($post_id == '') {
        global $post;
        $post_id = $post->ID;
    }
    
    $vclass = '';
    $click_event = '';
    $vtext = '';
    $voting_disabled = get_post_meta($post_id, '_wsv_voting_disabled', TRUE);
    
    $votes = wsv_get_votes($post_id);
    $votecount = wsv_get_vote_count($post_id);

    if ($votes === FALSE) {
        $vtext = "Voted";
        $vclass = "voted";
    } else {
        $vtext = "Vote";
        $vclass = "vote";
        $click_event = ' onclick="getPostId(this.id)"';
    }
    
    $html  = '<div class="wsv_post">';
    $html .= '<div id="voting_area_outer_'.esc_attr($post_id).'">';
    $html .= '<button id="vote_post_'.esc_attr($post_id).'"'.$click_event.' class="'.$vclass.'" votecount="'.$votecount.'">'.$vtext.'</button>';
    $html .= '</div>';
    $html .= '</div>';
    
    if (strtolower($voting_disabled) != "on") {
        if (!empty($_SESSION['wsv_has_voted']) && is_array($_SESSION['wsv_has_voted'])) {
            $vote_post_arr = $_SESSION['wsv_has_voted'];
            if (in_array($post_id, $vote_post_arr)) {
                echo '<div class="wsv_post">';
                echo '<button class="voted">Voted</span>';
                echo '</div>';
            } else {
                echo $html;
            }
        } else {
            echo $html;
        }
    }
}


/**********************************************************************
 * Add voting shortcode
 * eg. [wsv-vote] / [wsv-vote post_id="$post_id"]
 **********************************************************************/
function sc_wsv_voting($atts) {
    if ($atts['post_id'] == '') {
        global $post;
        $post_id = $post->ID;
    } else {
        $post_id = $atts['post_id'];
    }
    
    $vclass = '';
    $click_event = '';
    $vtext = '';
    $voting_disabled = get_post_meta($post_id, '_wsv_voting_disabled', TRUE);
    
    $votes = wsv_get_votes($post_id);
    
    if ($votes === FALSE) {
        $vtext = "Voted";
        $vclass = "voted";
    } else {
        $vtext = "Vote";
        $vclass = "vote";
        $click_event = ' onclick="getPostId(this.id)"';
    }
    
    $html  = '<div class="wsv_post">';
    $html .= '<div id="voting_area_outer_'.esc_attr($post_id).'">';
    $html .= '<span id="vote_post_'.esc_attr($post_id).'"'.$click_event.' class="'.$vclass.'">'.$vtext.'</span>';
    $html .= '</div>';
    $html .= '</div>';
    
    if (strtolower($voting_disabled) != "on") {
        if (!empty($_SESSION['wsv_has_voted']) && is_array($_SESSION['wsv_has_voted'])) {
            $vote_post_arr = $_SESSION['wsv_has_voted'];
            if (in_array($post_id, $vote_post_arr)) {
                echo '<div class="wsv_post">';
                echo '<span class="voted">Voted</span>';
                echo '</div>';
            } else {
                echo $html;
            }
        } else {
            echo $html;
        }
    }
}
add_shortcode('wsv-vote', 'sc_wsv_voting');


/**********************************************************************
 * Voting Reset Button Styling
 **********************************************************************/
function wsv_voting_reset_button($type = 'single', $reset_post_id = '') {
    $button_class = 'button-primary ';

    if ($type == "single"){
        if ($reset_post_id == "") {
            return FALSE;
        } else {
            $button_name = 'reset_vote_post_'.$reset_post_id;
            $button_id = $button_name;
            $button_text = 'Reset vote';
            $button_class .= 'wsv-reset-button-single';
            $outer_id = 'reset-area-outer-'.$reset_post_id;
        }
    } else if ($type == "all") {
        $button_name = 'reset_vote_post_all';
        $button_id = $button_name;
        $button_text = 'Reset all votes';
        $button_class .= 'wsv-reset-button-all';
        $outer_id = 'reset-area-outer-all';
    } else {
        return FALSE;
    }
    
    $button_html  = '<div id="'.$outer_id.'">';
    $button_html .= '<input type="button" name="'.esc_attr($button_name).'" id="'.esc_attr($button_id).'" class="'.$button_class.'" value="'.$button_text.'" />';
    $button_html .= '</div';
    
    return $button_html;
}


/**********************************************************************
 * Voting Reset Process
 **********************************************************************/
function wsv_do_voting_reset($type = 'single', $reset_post_id = '') {
    global $wpdb;
    global $wsv_plugin_prefix;
    
    $tbl = $wpdb->prefix.$wsv_plugin_prefix."user_votes";
    $reset_post_id = $wpdb->escape($reset_post_id);
    
    if ($type == "single") {
        if ($reset_post_id == "") {
            return FALSE;
        } else {
            $status = $wpdb->delete($tbl, array('post_id' => $reset_post_id));
            if ($status === FALSE) {
                return FALSE;
            } else {
                if (is_array($_SESSION['wsv_has_voted']) && in_array($reset_post_id, $_SESSION['wsv_has_voted'])) {
                    $post_key = array_search($reset_post_id, $_SESSION['wsv_has_voted']);
                    unset($_SESSION['wsv_has_voted'][$post_key]);
                }
                return TRUE;
            }
        }
    } else if ($type == "all") {
        $reset_sql = "TRUNCATE TABLE {$tbl}";
        $status = $wpdb->query($reset_sql);
        if ($status === FALSE) {
            return FALSE;
        } else {
            if (is_array($_SESSION['wsv_has_voted'])) {
                unset($_SESSION['wsv_has_voted']);
            }
            return TRUE;
        }
    } else {
        return FALSE;
    }
}


/**********************************************************************
 * Attach voting buttons with post contents
 **********************************************************************/
add_filter('the_content', 'wsv_add_voting_button_in_posts');
function wsv_add_voting_button_in_posts($content) {
    $voting_btn_html = wsv_voting();
    $vote_count_html = "";
    if (get_option("_wsv_show_vote_count") == "on") {
        $vote_count_html = '<p>Total votes: <strong id="vote-count-'.get_the_ID().'">' . wsv_get_vote_count(get_the_ID()) . '</strong></p>';
    }
    $content = $voting_btn_html . $vote_count_html . $content;
    return $content;
}

