<?php
/**
 * Supplier List view WP-Admin
 */

if(!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


class WSV_Voting_List_Table extends WP_List_Table {

	public      $item_data;
	protected   $tbl_vote;
    protected   $tbl_posts;
    protected   $sql_vote;
	protected   $vote_list;

        function __construct() {
            parent::__construct(array(
                'singular' => 'vote',
                'plural' => 'votes',
                'ajax' => false
            ));
	}
        
        function get_vote_list() {
            global $wpdb;
            global $wsv_plugin_prefix;
            
            $this->item_data    = array();
            $this->tbl_vote     = $wpdb->prefix.$wsv_plugin_prefix.'user_votes';
            $this->tbl_posts    = $wpdb->prefix.'posts';
            
            $orderby = sanitize_text_field($_GET['orderby']);
            $order = sanitize_text_field($_GET['order']);
            
            $posttype = sanitize_text_field($_GET['posttype']);
            $posttype = empty($posttype) ? 'post' : $posttype;
            
            if ($posttype == "all") :
                // To be added in class method later
                $allowed_post_types_tbl = wsv_get_allowed_post_types();
                for ($i=0; $i<count($allowed_post_types_tbl); $i++) :
                    $allowed_post_types_tbl[$i] = "'".$allowed_post_types_tbl[$i]."'";
                endfor;
                $posttype = implode(",", $allowed_post_types_tbl);
                // END
            else :
                $posttype = "'".$posttype."'";
            endif;

            switch ($orderby) {
                case 'post_title' :
                    $obtext = "p.post_title";
                    break;
                case 'vote_count' :
                default :
                    $obtext = "total_votes";
                    break;
            }
            
            switch ($order) {
                case 'asc' :
                    $otext ="ASC";
                    break;
                case 'desc' :
                default :
                    $otext = "DESC";
                    break;
            }
            
            $this->sql_vote     = "SELECT *, count(v.vote_count) as total_votes FROM {$this->tbl_posts} AS p LEFT JOIN {$this->tbl_vote} AS v ON p.ID = v.post_id WHERE p.post_type IN ({$posttype}) AND p.post_status = 'publish' GROUP BY p.ID ORDER BY {$obtext} {$otext}";
            $this->vote_list    = $wpdb->get_results($this->sql_vote);
        }


        function set_item_data() {
            for ($i = 0; $i < count($this->vote_list); $i++) {
                $this->item_data[$i]['reset_action'] = '';
                $this->item_data[$i]['post_title'] = '<a target="_blank" href="'.get_permalink($this->vote_list[$i]->ID).'">'.$this->vote_list[$i]->post_title.'</a>';
                $this->item_data[$i]['vote_count'] = (int) wsv_get_vote_count($this->vote_list[$i]->ID);
                if (get_post_meta($this->vote_list[$i]->ID, '_wsv_voting_disabled', TRUE) == "on") { // If voting is DISABLED
                    $this->item_data[$i]['reset_action'] = '<p class="wsv_vote_disabled">Voting is disabled for this post</p>';
                }
                $this->item_data[$i]['reset_action'] .= wsv_voting_reset_button('single', $this->vote_list[$i]->ID);
            }
        }
	
	function get_columns(){
            $columns = array(
                'post_title' => 'Post Name',
                'vote_count' => 'Total Votes',
                'reset_action' => 'Action'
            );
            return $columns;
        }

	function column_default( $item, $column_name ) {
            switch( $column_name ) {
                case 'post_title':
                case 'vote_count':
                case 'reset_action':
                return $item[ $column_name ];
                default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
            }
        }

	function get_sortable_columns() {
            $sortable_columns = array(
                'post_title' => array('post_title', false),
                'vote_count' => array('vote_count', false)
            );
            return $sortable_columns;
        }
	
	/**
	 * Method to prepare items for view
	 */
	function prepare_items($show_per_page) {
            $this->get_vote_list();
            $this->set_item_data();
            $per_page = $show_per_page;
            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array($columns, $hidden, $sortable);

            $current_page = $this->get_pagenum();
            $total_items = count($this->item_data);

            $this->found_data = array_slice($this->item_data,(($current_page-1)*$per_page),$per_page);
            $this->set_pagination_args(array(
                'total_items' => $total_items,
                'per_page' => $per_page
            ));
            $this->items = $this->found_data;
        }
}

$votingListTable = new WSV_Voting_List_Table;

$allowed_post_types = wsv_get_allowed_post_types();
$listPostType = sanitize_text_field($_GET['posttype']);
$listPostType = empty($listPostType) ? "post" : $listPostType;

?>

<div class="wrap">
    <h2><?php echo 'Voting List'; ?></h2>
    <div class="wsv_list_sbox">
        <label for="wsv_select_post_type_list">Select a Post Type: </label>
        <select name="wsv_select_post_type_list" onchange="WsvRedirectPostType(this.value);">
            <option value="">-</option>
            <?php
                foreach ($allowed_post_types as $pt) :
                    $cpt_obj = get_post_type_object($pt);
                    $select_txt = ($listPostType == $pt) ? 'selected="selected"' : '';
                    echo '<option value="'.$pt.'"'.$select_txt.'>'.$cpt_obj->labels->singular_name.'</option>';
                endforeach;
            ?>
            <option value="all"<?php if ($listPostType == "all") echo ' selected="selected"'; ?>>All Post Types</option>
        </select>
        <input type="hidden" name="wsv_redirect_post_type_url" value="<?php echo esc_attr(admin_url('admin.php?page=wsv-view-voting')); ?>">
    </div>
    <span style="display:block; padding:10px;">
        <?php echo wsv_voting_reset_button('all'); ?>
    </span>
    <?php
        $votingListTable->prepare_items(20);
        $votingListTable->display();
    ?>
</div>

