<?php

class RSHTeamDetailsWidget extends WP_Widget
{
	public function __construct() {
            parent::__construct(
                'rsh-team-details-widget', // ID
                'RSH Single Team Details', // Widget name
                array( // Args
                    'description' => __( 'Shows a team\'s details with image, desc etc for RSH', 'redspottedhanky' )
                )
            );
	}
		
	public function showTeam($name, $img, $desc, $url) {
        ?>
            <?php if ($name && $url) : ?>
            <div class="upper-body-left-panel">
                <div class="team-name-left-panel">
                    <?php if ($img) : ?>
                    <img src="<?php echo rsh_get_crop_image($img, 221, 230); ?>" alt="Team - <?php echo esc_attr($name); ?>" />
                    <?php else : ?>
                    <img src="<?php bloginfo('stylesheet_directory'); ?>/images/team-photo.png" alt="team-photo" />
                    <?php endif; ?>
                    <h2><?php echo esc_attr($name); ?></h2>
                    <?php if ($desc) : ?>
                    <p><?php echo esc_attr($desc); ?></p>
                    <?php endif; ?>
                    <a href="<?php echo esc_attr($url); ?>" class="readmore"></a>
                </div>
            </div>
            <?php endif; ?>
        <?php
	}
  
	function widget($args, $instance) {
            if ($instance['team_name'] != '' && $instance['team_url'] != '') {
                
                $this->showTeam($instance['team_name'], $instance['team_img'], $instance['team_desc'], $instance['team_url']);
            }
	}
	
	public function update( $new_instance, $old_instance ) {
            $instance = array();
            
            $instance['team_name']  = strip_tags($new_instance['team_name']);
            $instance['team_img']   = strip_tags($new_instance['team_img']);
            $instance['team_desc']  = strip_tags($new_instance['team_desc']);
            $instance['team_url']   = strip_tags($new_instance['team_url']);
            
            return $instance;
	}
	
	public function form( $instance ) {
            if (!$instance['team_title']) {
                $instance['team_title'] = "Other Teams";
            }
        ?>
        
        <?php if ($instance['team_img'] != '') { ?>
        <span class="widget-team-details"><img src="<?php echo rsh_get_crop_image($instance['team_img'], 140, 220); ?>"></span>
        <?php } ?>
            
        <p>
            <label for="<?php echo $this->get_field_id('team_name'); ?>"><?php _e('Team Name:'); ?></label> 
            <br/>
            <input id="<?php echo $this->get_field_id('team_name'); ?>" name="<?php echo $this->get_field_name('team_name'); ?>" size="24" type="text" value="<?php echo esc_attr($instance['team_name']); ?>" />
            <br/>
            
            <label for="<?php echo $this->get_field_id('team_img'); ?>"><?php _e('Team Image URL:'); ?></label> 
            <br/>
            <input id="<?php echo $this->get_field_id('team_img'); ?>" name="<?php echo $this->get_field_name('team_img'); ?>" size="24" type="text" value="<?php echo esc_attr($instance['team_img']); ?>" />
            <br/>
            
            <label for="<?php echo $this->get_field_id('team_desc'); ?>"><?php _e('Description:'); ?></label> 
            <br/>
            <textarea rows="10" cols="22" id="<?php echo $this->get_field_id('team_desc'); ?>" name="<?php echo $this->get_field_name('team_desc'); ?>"><?php echo esc_attr($instance['team_desc']); ?></textarea>
            <br/>
            
            <label for="<?php echo $this->get_field_id('team_url'); ?>"><?php _e('Team URL:'); ?></label>
            <br/>
            <input id="<?php echo $this->get_field_id('team_url'); ?>" name="<?php echo $this->get_field_name('team_url'); ?>" size="24" type="text" value="<?php echo esc_attr( $instance['team_url'] ); ?>" />
            <br/>
        </p>
        <?php 
	
        }
        
}

// Register the widget
add_action('widgets_init', create_function('','register_widget("RSHTeamDetailsWidget");'));

?>