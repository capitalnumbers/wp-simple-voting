<?php

class WSVShowMostVotedWidget extends WP_Widget
{
	public function __construct() {
        parent::__construct(
            'wsv-show-most-voted-widget', // ID
            'Most Votes - WP Simple Voting', // Widget name
            array( // Args
                'description' => __('Shows the most voted posts/pages/custom post types')
            )
        );
	}

	public function showMostVoted($title, $count, $post_type, $show_post_type) {
        $voting_list = wsv_get_top_voted($count, $post_type);

        // Widget default wrapper
        echo '<aside class="widget masonry-brick">';

        if ($title != '') :
            echo '<h3 class="widget-title">'.esc_attr($title).'</h3>';
        else :
            echo '<h3 class="widget-title">Most Voted Posts</h3>';
        endif;

        if ($voting_list === FALSE) :
            echo '<p>Oops! Something went wrong while getting the data.</p>';
        elseif (empty($voting_list)) :
            echo '<p>No posts has been voted yet</p>';
        else :
            echo '<ul>';
            foreach ($voting_list as $vdata) :
                $vtext = ($vdata->total_vote > 1) ? 'votes' : 'vote';
                echo '<li>';
                echo '<a href="'.get_permalink($vdata->post_id).'">'.$vdata->post_title.'</a>';
                if ($post_type == "all" && $show_post_type == "on") :
                    $cptobj = get_post_type_object($vdata->post_type);
                    echo ' ('.$cptobj->labels->singular_name.')';
                endif;
                echo ' - '.$vdata->total_vote.' '.$vtext;
                echo '</li>';
            endforeach;
            echo '</ul>';
        endif;


        //Widget default wrapper close
        echo '</aside>';
	}

	function widget($args, $instance) {
        if ($instance['wsv_widget_most_voted_count'] != '') {
            $this->showMostVoted($instance['wsv_widget_most_voted_title'], $instance['wsv_widget_most_voted_count'], $instance['wsv_widget_most_voted_cpt'], $instance['wsv_widget_most_voted_show_cpt']);
        }
	}
	
	public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['wsv_widget_most_voted_count']  = strip_tags($new_instance['wsv_widget_most_voted_count']);
        $instance['wsv_widget_most_voted_title']  = strip_tags($new_instance['wsv_widget_most_voted_title']);
        $instance['wsv_widget_most_voted_cpt']  = strip_tags($new_instance['wsv_widget_most_voted_cpt']);
        $instance['wsv_widget_most_voted_show_cpt']  = strip_tags($new_instance['wsv_widget_most_voted_show_cpt']);
        return $instance;
	}
	
	public function form($instance) {
        if (!$instance['wsv_widget_most_voted_count']) {
            $instance['wsv_widget_most_voted_count'] = 5;
        }
        if (!$instance['wsv_widget_most_voted_cpt']) {
            $instance['wsv_widget_most_voted_cpt'] = 'post';
        }
        $allowed_post_types = wsv_get_allowed_post_types();
    ?>
        <p>
            <label for="<?php echo $this->get_field_id('wsv_widget_most_voted_title'); ?>"><?php _e('Title:'); ?></label>
            <br>
            <input id="<?php echo $this->get_field_id('wsv_widget_most_voted_title'); ?>" name="<?php echo $this->get_field_name('wsv_widget_most_voted_title'); ?>" class="widefat" type="text" value="<?php echo esc_attr($instance['wsv_widget_most_voted_title']); ?>" />
        </p>

        <!-- SELECT BOX TO CHOOSE A POST TYPE -->
        <p>
            <label for="<?php echo $this->get_field_id('wsv_widget_most_voted_cpt'); ?>"><?php _e('Select a post type:'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('wsv_widget_most_voted_cpt'); ?>" name="<?php echo $this->get_field_name('wsv_widget_most_voted_cpt'); ?>">
                <option value="">-</option>
                <?php
                    foreach ($allowed_post_types as $pt) :
                        $cpt_obj = get_post_type_object($pt);
                        $select_txt = ($instance['wsv_widget_most_voted_cpt'] == $pt) ? ' selected="selected"' : '';
                        echo '<option value="'.$pt.'"'.$select_txt.'>'.$cpt_obj->labels->singular_name.'</option>';
                    endforeach;
                ?>
                <option value="all"<?php if ($instance['wsv_widget_most_voted_cpt'] == "all") echo ' selected="selected"'; ?>>All Post Types</option>
            </select>
        </p>
        <!-- END SELECT BOX -->

        <script>
            var $ = jQuery.noConflict();
            $(function() {
                $("#<?php echo $this->get_field_id('wsv_widget_most_voted_cpt'); ?>").change(function() {
                    if ($(this).val() == "all") {
                        $("p#wsv_widget_mv_show_post_type").css("display", "block");
                    } else {
                        $("p#wsv_widget_mv_show_post_type").css("display", "none");
                    }
                })
            })
        </script>

        <p id="wsv_widget_mv_show_post_type"<?php if ($instance['wsv_widget_most_voted_cpt'] != "all") echo ' style="display:none;"'; ?>>
            <label for="<?php echo $this->get_field_id('wsv_widget_most_voted_show_cpt'); ?>"><?php _e('Show post type?'); ?></label>
            <input id="<?php echo $this->get_field_id('wsv_widget_most_voted_show_cpt'); ?>" name="<?php echo $this->get_field_name('wsv_widget_most_voted_show_cpt'); ?>" type="checkbox" value="on"<?php if ($instance['wsv_widget_most_voted_show_cpt'] == "on") echo ' checked="checked"'; ?>/>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('wsv_widget_most_voted_count'); ?>"><?php _e('Number of posts to show:'); ?></label>
            <input id="<?php echo $this->get_field_id('wsv_widget_most_voted_count'); ?>" name="<?php echo $this->get_field_name('wsv_widget_most_voted_count'); ?>" size="3" type="text" value="<?php echo esc_attr($instance['wsv_widget_most_voted_count']); ?>" />
        </p>
    <?php
    }
}

// Register the widget
add_action('widgets_init', create_function('','register_widget("WSVShowMostVotedWidget");'));

?>