<?php
/* Users */
add_action( 'widgets_init', 'widget_users_widget' );
function widget_users_widget() {
	register_widget( 'Widget_Users' );
}

class Widget_Users extends WP_Widget {

	function __construct() {
		global $wpqa;
		$widget_ops = array( 'classname' => 'users-widget' );
		$control_ops = array( 'id_base' => 'users-widget' );
		parent::__construct( 'users-widget',$wpqa->super_plugin_name().' - Users', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		global $wpdb;
		$active_points = wpqa_options("active_points");
		extract( $args );
		$title       = apply_filters('widget_title', $instance['title'] );
		$user_number = esc_attr($instance['user_number']);
		$user_sort   = esc_attr($instance['user_sort']);
		$user_order  = esc_attr($instance['user_order']);
		$user_group  = $instance['user_group'];
		$category    = (isset($instance['points_categories'])?$instance['points_categories']:"");
		$active_points_category = wpqa_options("active_points_category");
		if ($category !== "on" || ($category === "on" && $active_points_category == "on" && is_tax("question-category"))) {
			if ($category === "on" && $active_points_category == "on" && is_tax("question-category")) {
				$user_sort = "points";
			}
			echo ($before_widget);
				if ($title) {
					echo ($title == "empty"?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_attr($title)).$after_title).($title == "empty"?"</div>":"");
				}else {
					echo "<h3 class='screen-reader-text'>".esc_html__("Users","wpqa")."</h3>";
				}?>
				<div class="widget-wrap">
					<?php echo "<div class='user-section user-section-small row user-not-normal'>";
						$meta_key_array = array();
						$implode_array  = "";
						$capabilities   = $wpdb->get_blog_prefix(1) . 'capabilities';
						if (!empty($user_group)) {
							foreach ($user_group as $role => $name) {
								if ($name != "0") {
									$all_role_array[] = $name;
									$meta_key_array[] = "( $wpdb->usermeta.meta_key = '$capabilities'
									AND CAST($wpdb->usermeta.meta_value AS CHAR) RLIKE '$name' )";
								}else {
									unset($user_group[$role]);
								}
							}
							if (!empty($meta_key_array)) {
								$implode_array = "AND (".implode(" OR ",$meta_key_array).")";
							}
						}
						$user_sort = (isset($user_sort) && $user_sort != ""?$user_sort:"user_registered");
						if ($user_sort == "the_best_answer" || $user_sort == "post_count" || $user_sort == "question_count" || $user_sort == "answers" || $user_sort == "comments") {
							if ($user_sort == "the_best_answer") {
								$query = $wpdb->prepare("SELECT DISTINCT SQL_CALC_FOUND_ROWS $wpdb->users.ID FROM $wpdb->users INNER JOIN $wpdb->usermeta ON ( $wpdb->users.ID = $wpdb->usermeta.user_id ) LEFT OUTER JOIN ( SELECT user_id, COUNT(*) as total FROM $wpdb->comments INNER JOIN $wpdb->commentmeta ON ( $wpdb->comments.comment_id = $wpdb->commentmeta.comment_id ) WHERE $wpdb->comments.comment_approved = 1 AND $wpdb->commentmeta.meta_key = 'best_answer_comment'  GROUP BY user_id ) c ON ($wpdb->users.ID = c.user_id) WHERE %s=1".$implode_array." ORDER BY total $user_order LIMIT 0,$user_number",1);
							}if ($user_sort == "post_count" || $user_sort == "question_count") {
								$query = $wpdb->prepare("SELECT DISTINCT SQL_CALC_FOUND_ROWS $wpdb->users.ID FROM $wpdb->users INNER JOIN $wpdb->usermeta ON ( $wpdb->users.ID = $wpdb->usermeta.user_id ) LEFT OUTER JOIN ( SELECT post_author, COUNT(*) as post_count FROM $wpdb->posts WHERE ( ( post_type = '".($user_sort == "question_count"?"question":"post")."' AND ( post_status = 'publish' OR post_status = 'private' ) ) ) GROUP BY post_author ) p ON ($wpdb->users.ID = p.post_author) WHERE %s=1 ".$implode_array." ORDER BY post_count $user_order limit 0,$user_number",1);
							}else {
								$query = $wpdb->prepare("SELECT DISTINCT SQL_CALC_FOUND_ROWS $wpdb->users.ID FROM $wpdb->users INNER JOIN $wpdb->usermeta ON ( $wpdb->users.ID = $wpdb->usermeta.user_id ) LEFT OUTER JOIN ( SELECT user_id, COUNT(*) as total FROM $wpdb->comments INNER JOIN $wpdb->posts ON ( $wpdb->comments.comment_post_ID = $wpdb->posts.ID ) WHERE $wpdb->posts.post_type = '".($user_sort == "answers"?"question":"post")."' AND ( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private' ) GROUP BY user_id ) c ON ($wpdb->users.ID = c.user_id) WHERE %s=1 ".$implode_array." ORDER BY total $user_order limit 0,$user_number",1);
							}
							$query = $wpdb->get_results($query);
						}else if ($user_sort == "points" && $active_points == "on") {
							if ($category === "on" && $active_points_category == "on" && is_tax("question-category")) {
								$category  = (int)get_query_var('discy_term_id');
								$user_sort = "points_category".$category;
							}
							$args = array(
								'role__in'    => (isset($user_group) && is_array($user_group)?$user_group:array()),
								'meta_query'  => array(array("key" => $user_sort,"value" => 0,"compare" => ">")),
								'orderby'     => 'meta_value_num',
								'order'       => $user_order,
								'number'      => $user_number,
								'fields'      => 'ID',
								'count_total' => false,
							);
							
							$query = new WP_User_Query($args);
							$get_results = true;
						}else {
							if ($user_sort != "user_registered" && $user_sort != "display_name" && $user_sort != "ID") {
								$user_sort = "user_registered";
							}
							$args = array(
								'role__in'    => (isset($user_group) && is_array($user_group)?$user_group:array()),
								'orderby'     => $user_sort,
								'order'       => $user_order,
								'number'      => $user_number,
								'fields'      => 'ID',
								'count_total' => false,
							);
							
							$query = new WP_User_Query($args);
							$get_results = true;
						}
						
						if (isset($query)) {
							$query = (isset($get_results)?$query->get_results():$query);
							foreach ($query as $user) {
								$user = (isset($user->ID)?$user->ID:$user);
								$owner_widget = false;
								if (get_current_user_id() == $user) {
									$owner_widget = true;
								}
								echo "<div class='col col12'>".wpqa_author($user,"small",$owner_widget,($user_sort == "post_count" || $user_sort == "comments"?"post":$user_sort),"widget","","",(isset($category) && $category !== ""?$category:""))."</div>";
							}
						}?>
					</div>
				</div>
			<?php echo ($after_widget);
		}
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>