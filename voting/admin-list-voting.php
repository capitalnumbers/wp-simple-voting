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
            
            $this->sql_vote     = "SELECT *, count(v.vote_count) as total_votes FROM {$this->tbl_posts} AS p LEFT JOIN {$this->tbl_vote} AS v ON p.ID = v.post_id";
            $this->sql_vote    .= " WHERE p.post_type = 'post' AND p.post_status = 'publish' GROUP BY p.ID ORDER BY {$obtext} {$otext}";

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

?>

<div class="wrap">
    <h2><?php echo 'Voting List'; ?></h2>
    <span style="display:block; padding:10px;">
        <?php echo wsv_voting_reset_button('all'); ?>
    </span>
    <?php
        $votingListTable->prepare_items(20);
        $votingListTable->display();
    ?>
</div>

