<?php
	/**
	 * WSV Voting Settings
	 */
?>

<div class="wrap">
	<h2>Settings</h2>

<?php
	$wsv_custom_posts = get_post_types(array('_builtin' => false));
	$wsv_admin_url = admin_url("admin.php?page=wsv-voting-settings");

	// $_POST values
	$wsv_setting_submit_val		= sanitize_text_field($_POST['wsv_setting_submit']);
	$wsv_enable_voting_page_val = sanitize_text_field($_POST['enable_voting_page']);
	$wsv_show_vote_count_val	= sanitize_text_field($_POST['show_vote_count']);
	$wsv_voting_cpt_list		= unserialize(get_option("_wsv_enable_voting_cpt"));
	
	// Storing value for custom post type list
	if (is_array($_POST['enable_voting_cpt'])) {
		foreach ($_POST['enable_voting_cpt'] as $cpt) {
			$cpt_arr[] = sanitize_text_field($cpt);
		}
		$wsv_enable_voting_cpt_val = serialize($cpt_arr);
	}

	// If form is submitted
	if ($wsv_setting_submit_val == "Save Changes") {
		$wsv_enable_voting_page_val = !empty($wsv_enable_voting_page_val) ? "on" : "off";
		$wsv_show_vote_count_val = !empty($wsv_show_vote_count_val) ? "on" : "off";

		// Updating option table
		update_option("_wsv_enable_voting_page", $wsv_enable_voting_page_val);
		update_option("_wsv_show_vote_count", $wsv_show_vote_count_val);
		if (!empty($wsv_enable_voting_cpt_val)) {
			update_option("_wsv_enable_voting_cpt", $wsv_enable_voting_cpt_val);
		} else {
			delete_option("_wsv_enable_voting_cpt");
		}

		// Show update message after options are updated
		echo '<div class="updated below-h2" id="message"><p>Voting settings updated</p></div>';

	}
?>

	<form name="wsv_settings" id="wsv_settings" method="POST" action="<?php echo $wsv_admin_url; ?>">
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="enable_voting_page">Enable voting in pages?</label></th>
				<td><input type="checkbox" value="on" id="enable_voting_page" name="enable_voting_page"<?php if (get_option("_wsv_enable_voting_page") == "on") echo ' checked="checked"'; ?>></td>
			</tr>
			
			<?php if (!empty($wsv_custom_posts)) : ?>
			<tr valign="top">
				<th scope="row"><label for="enable_voting_cpt">Enable voting in custom posts?</label></th>
				<td>
				<?php foreach ($wsv_custom_posts as $k=> $v) : ?>
					<fieldset>
						<legend class="screen-reader-text">
							<span>Enable voting in custom posts?</span>
						</legend>
						<label for="enable_voting_cpt_<?php echo $k; ?>">
							<input type="checkbox" value="<?php echo $k; ?>" id="enable_voting_cpt_<?php echo $k; ?>" name="enable_voting_cpt[]"<?php if (in_array($k, $wsv_voting_cpt_list)) echo ' checked="checked"'; ?>>
							<span><?php echo $v; ?></span>
						</label>
						<br>						
					</fieldset>
				<?php endforeach; ?>
				</td>
			</tr>
			<?php endif; ?>

			<tr valign="top">
				<th scope="row"><label for="show_vote_count">Show vote count?</label></th>
				<td><input type="checkbox" value="on" id="show_vote_count" name="show_vote_count"<?php if (get_option("_wsv_show_vote_count") == "on") echo ' checked="checked"'; ?>></td>
			</tr>

			<tr valign="top">
				<th scope="row">&nbsp;</th>
				<td><input type="submit" value="Save Changes" class="button button-primary" id="wsv_setting_submit" name="wsv_setting_submit"></td>
			</tr>
		</table>
	</form>
</div>