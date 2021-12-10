<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action('load-nav-menus.php','wpqa_admin_wp_nav_menu_meta_box');
function wpqa_admin_wp_nav_menu_meta_box() {
	if (!class_exists("WPQA")) {
		return;
	}
	add_meta_box('add-wpqa-nav-menu',esc_html__('WPQA - Custom Links','wpqa'),'wpqa_nav_menu_meta_box','nav-menus','side','core');
	add_action('admin_print_footer_scripts','wpqa_nav_menu_script');
}
function wpqa_nav_menu_meta_box() {
	global $nav_menu_selected_id;
	
	$walker      = new WPQA_Walker_Nav_Menu_Checklist(false);
	$args        = array('walker' => $walker);
	$tabs        = array();
	$current_tab = 'all';
	
	$tabs['profile']['label'] = esc_html__('Profile links','wpqa');
	$tabs['profile']['pages'] = wpqa_nav_menu_get_profile_links();
	
	$removed_args = array('action','customlink-tab','edit-menu-item','menu-item','page-tab','_wpnonce');?>
	
	<div id="wpqa-menu" class="posttypediv">
		<ul id="wpqa-menu-tabs" class="wpqa-menu-tabs add-menu-item-tabs">
			<li <?php echo ('all' == $current_tab?' class="tabs"':'')?>>
				<a class="nav-tab-link" data-type="tabs-panel-wpqa-menu-all" href="<?php echo ($nav_menu_selected_id?esc_url(add_query_arg('wpqa-menu-tab','all',remove_query_arg($removed_args))):"")?>#tabs-panel-wpqa-menu-all">
					<?php esc_html_e('View All')?>
				</a>
			</li>
		</ul>
		<div id="tabs-panel-wpqa-menu-all" class="tabs-panel tabs-panel-active">
			<ul id="wpqa-menu-checklist-profile" class="categorychecklist form-no-clear">
				<?php echo walk_nav_menu_tree(array_map('wp_setup_nav_menu_item',$tabs['profile']['pages']),0,(object)$args);?>
			</ul>
		</div>
		
		<p class="button-controls">
			<span class="list-controls">
				<a href="<?php echo esc_url(add_query_arg(array('wpqa-tab' => 'all','selectall' => 1),remove_query_arg($removed_args)));?>#wpqa-menu" class="select-all"><?php esc_html_e('Select All','wpqa')?></a>
			</span>
			<span class="add-to-menu">
				<input type="submit"<?php if (function_exists('wp_nav_menu_disabled_check')):wp_nav_menu_disabled_check($nav_menu_selected_id);endif;?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu','wpqa')?>" name="add-custom-menu-item" id="submit-wpqa-menu" />
				<span class="spinner"></span>
			</span>
		</p>
	</div>
	<?php
}
function wpqa_nav_menu_script() {?>
	<script type="text/javascript">
	jQuery('#menu-to-edit').on('click','a.item-edit',function() {
		var settings  = jQuery(this).closest('.menu-item-bar').next('.menu-item-settings');
		var css_class = settings.find('.edit-menu-item-classes');
		
		if (css_class.val().indexOf('wpqa-menu') == 0) {
			css_class.attr('readonly','readonly');
			settings.find('.field-url').css('display','none');
		}
	});
	</script>
<?php
}
function wpqa_nav_menu_get_profile_links() {
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Login','wpqa'),
		'slug' => 'login',
		'link' => "#wpqa-login",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Login Pop Up','wpqa'),
		'slug' => 'login-popup',
		'link' => "#wpqa-login-popup",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Signup','wpqa'),
		'slug' => 'signup',
		'link' => "#wpqa-signup",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Signup Pop Up','wpqa'),
		'slug' => 'signup-popup',
		'link' => "#wpqa-signup-popup",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Lost Password','wpqa'),
		'slug' => 'lost-password',
		'link' => "#wpqa-lost-password",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Lost Password Pop Up','wpqa'),
		'slug' => 'lost-password-popup',
		'link' => "#wpqa-lost-password-popup",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Polls','wpqa'),
		'slug' => 'poll',
		'link' => "#wpqa-poll",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Ask Question','wpqa'),
		'slug' => 'add-question',
		'link' => "#wpqa-add-question",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Ask Question Pop Up','wpqa'),
		'slug' => 'add-question-popup',
		'link' => "#wpqa-add-question-popup",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Add Post','wpqa'),
		'slug' => 'add-post',
		'link' => "#wpqa-add-post",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Add Post Pop Up','wpqa'),
		'slug' => 'add-post-popup',
		'link' => "#wpqa-add-post-popup",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Buy Points','wpqa'),
		'slug' => 'buy-points',
		'link' => "#wpqa-buy-points",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Profile','wpqa'),
		'slug' => 'profile',
		'link' => "#wpqa-profile",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Edit Profile','wpqa'),
		'slug' => 'edit-profile',
		'link' => "#wpqa-edit-profile",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Followers','wpqa'),
		'slug' => 'followers',
		'link' => "#wpqa-followers",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Following','wpqa'),
		'slug' => 'following',
		'link' => "#wpqa-following",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Notifications','wpqa'),
		'slug' => 'notifications',
		'link' => "#wpqa-notifications",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Activities','wpqa'),
		'slug' => 'activities',
		'link' => "#wpqa-activities",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Messages','wpqa'),
		'slug' => 'messages',
		'link' => "#wpqa-messages",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Points','wpqa'),
		'slug' => 'points',
		'link' => "#wpqa-points",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Questions','wpqa'),
		'slug' => 'questions',
		'link' => "#wpqa-questions",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Polls','wpqa'),
		'slug' => 'polls',
		'link' => "#wpqa-polls",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Asked Questions','wpqa'),
		'slug' => 'asked-questions',
		'link' => "#wpqa-asked_questions",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Paid Questions','wpqa'),
		'slug' => 'paid-questions',
		'link' => "#wpqa-paid_questions",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Answers','wpqa'),
		'slug' => 'answers',
		'link' => "#wpqa-answers",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Best Answers','wpqa'),
		'slug' => 'best-answers',
		'link' => "#wpqa-best_answers",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Followed','wpqa'),
		'slug' => 'followed',
		'link' => "#wpqa-followed",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Favorites','wpqa'),
		'slug' => 'favorites',
		'link' => "#wpqa-favorites",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Posts','wpqa'),
		'slug' => 'posts',
		'link' => "#wpqa-posts",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Comments','wpqa'),
		'slug' => 'comments',
		'link' => "#wpqa-comments",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Followers Questions','wpqa'),
		'slug' => 'followers-questions',
		'link' => "#wpqa-followers_questions",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Followers Answers','wpqa'),
		'slug' => 'followers-answers',
		'link' => "#wpqa-followers_answers",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Followers Posts','wpqa'),
		'slug' => 'followers-posts',
		'link' => "#wpqa-followers_posts",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Followers Comments','wpqa'),
		'slug' => 'followers-comments',
		'link' => "#wpqa-followers_comments",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Logout','wpqa'),
		'slug' => 'logout',
		'link' => "#wpqa-logout",
	);

	$wpqa_menu_items = apply_filters("wpqa_menu_items",$wpqa_menu_items);
	
	if (count($wpqa_menu_items) < 1) {
		return false;
	}
	
	$page_args = array();
	foreach ($wpqa_menu_items as $wpqa_item) {
		$page_args[$wpqa_item['slug']] = (object) array(
			'ID'             => -1,
			'post_title'     => $wpqa_item['name'],
			'post_author'    => 0,
			'post_date'      => 0,
			'post_excerpt'   => $wpqa_item['slug'],
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'comment_status' => 'closed',
			'guid'           => $wpqa_item['link']
		);
	}
	
	return $page_args;
}

class WPQA_Walker_Nav_Menu_Checklist extends Walker_Nav_Menu {
	public function __construct($fields = false) {
		if ($fields) {
			$this->db_fields = $fields;
		}
	}
	
	public function start_lvl(&$output,$depth = 0,$args = array()) {
		$indent = str_repeat("\t",$depth);
		$output .= "\n$indent<ul class='children'>\n";
	}
	
	public function end_lvl(&$output,$depth = 0,$args = array()) {
		$indent = str_repeat("\t",$depth);
		$output .= "\n$indent</ul>";
	}
	
	function start_el(&$output,$item,$depth = 0,$args = array(),$id = 0) {
		global $_nav_menu_placeholder;
		
		$_nav_menu_placeholder = (0 > $_nav_menu_placeholder)?intval($_nav_menu_placeholder) - 1:-1;
		$possible_object_id = isset($item->post_type) && 'nav_menu_item' == $item->post_type?$item->object_id:$_nav_menu_placeholder;
		$possible_db_id = (!empty($item->ID)) && (0 < $possible_object_id)?(int)$item->ID:0;
		
		$indent = ($depth)?str_repeat("\t",$depth):'';
		
		$output .= $indent.'<li>';
		$output .= '<label class="menu-item-title">';
		$output .= '<input type="checkbox" class="menu-item-checkbox';
		
		if (property_exists($item,'label')) {
			$title = $item->label;
		}
		
		$output .= '" name="menu-item['.$possible_object_id.'][menu-item-object-id]" value="'.esc_attr($item->object_id).'" />';
		$output .= isset($title)?esc_html($title):esc_html($item->title);
		$output .= '</label>';
		
		if (empty($item->url)) {
			$item->url = $item->guid;
		}
		
		if (!in_array(array('wpqa-menu','wpqa-'.$item->post_excerpt.'-nav'),$item->classes)) {
			$item->classes[] = 'wpqa-menu';
			$item->classes[] = 'wpqa-'.$item->post_excerpt.'-nav';
		}
		
		// Menu item hidden fields.
		$output .= '<input type="hidden" class="menu-item-db-id" name="menu-item['.$possible_object_id.'][menu-item-db-id]" value="'.$possible_db_id.'" />';
		$output .= '<input type="hidden" class="menu-item-object" name="menu-item['.$possible_object_id.'][menu-item-object]" value="'.esc_attr($item->object).'" />';
		$output .= '<input type="hidden" class="menu-item-parent-id" name="menu-item['.$possible_object_id.'][menu-item-parent-id]" value="'.esc_attr($item->menu_item_parent).'" />';
		$output .= '<input type="hidden" class="menu-item-type" name="menu-item['.$possible_object_id.'][menu-item-type]" value="custom" />';
		$output .= '<input type="hidden" class="menu-item-title" name="menu-item['.$possible_object_id.'][menu-item-title]" value="'.esc_attr($item->title).'" />';
		$output .= '<input type="hidden" class="menu-item-url" name="menu-item['.$possible_object_id.'][menu-item-url]" value="'.esc_attr($item->url).'" />';
		$output .= '<input type="hidden" class="menu-item-target" name="menu-item['.$possible_object_id.'][menu-item-target]" value="'.esc_attr($item->target).'" />';
		$output .= '<input type="hidden" class="menu-item-attr_title" name="menu-item['.$possible_object_id.'][menu-item-attr_title]" value="'.esc_attr($item->attr_title).'" />';
		$output .= '<input type="hidden" class="menu-item-classes" name="menu-item['.$possible_object_id.'][menu-item-classes]" value="'.esc_attr(implode(' ',$item->classes)).'" />';
		$output .= '<input type="hidden" class="menu-item-xfn" name="menu-item['.$possible_object_id.'][menu-item-xfn]" value="'.esc_attr($item->xfn).'" />';
	}
}
// Add Menu Class to FrontEnd
add_filter('nav_menu_css_class','wpqa_menu_class',10,2);
function wpqa_menu_class( $classes, $item ) {
	if (isset($item->url) && $item->url != "" && is_user_logged_in() && ($item->url == "#wpqa-profile" || $item->url == "#wpqa-edit-profile" || $item->url == "#wpqa-followers" || $item->url == "#wpqa-following" || $item->url == "#wpqa-notifications" || $item->url == "#wpqa-activities" || $item->url == "#wpqa-messages" || $item->url == "#wpqa-points" || $item->url == "#wpqa-questions" || $item->url == "#wpqa-polls" || $item->url == "#wpqa-asked_questions" || $item->url == "#wpqa-paid_questions" || $item->url == "#wpqa-answers" || $item->url == "#wpqa-best_answers" || $item->url == "#wpqa-followed" || $item->url == "#wpqa-favorites" || $item->url == "#wpqa-posts" || $item->url == "#wpqa-comments" || $item->url == "#wpqa-followers_questions" || $item->url == "#wpqa-followers_answers" || $item->url == "#wpqa-followers_posts" || $item->url == "#wpqa-followers_comments" || $item->url == "#wpqa-logout")) {
		if ($item->url == "#wpqa-profile") {
			$classes[] = 'li-profile';
		}else if ($item->url == "#wpqa-edit-profile") {
			$classes[] = 'li-edit-profile';
		}else if ($item->url == "#wpqa-followers") {
			$classes[] = 'li-followers';
		}else if ($item->url == "#wpqa-following") {
			$classes[] = 'li-following';
		}else if ($item->url == "#wpqa-notifications") {
			$classes[] = 'li-notifications';
		}else if ($item->url == "#wpqa-activities") {
			$classes[] = 'li-activities';
		}else if ($item->url == "#wpqa-messages") {
			$classes[] = 'li-messages';
		}else if ($item->url == "#wpqa-points") {
			$classes[] = 'li-points';
		}else if ($item->url == "#wpqa-questions") {
			$classes[] = 'li-questions';
		}else if ($item->url == "#wpqa-polls") {
			$classes[] = 'li-polls';
		}else if ($item->url == "#wpqa-asked_questions") {
			$classes[] = 'li-asked-questions';
		}else if ($item->url == "#wpqa-paid_questions") {
			$classes[] = 'li-paid-questions';
		}else if ($item->url == "#wpqa-answers") {
			$classes[] = 'li-answers';
		}else if ($item->url == "#wpqa-best_answers") {
			$classes[] = 'li-best-answers';
		}else if ($item->url == "#wpqa-followed") {
			$classes[] = 'li-followed';
		}else if ($item->url == "#wpqa-favorites") {
			$classes[] = 'li-favorites';
		}else if ($item->url == "#wpqa-posts") {
			$classes[] = 'li-posts';
		}else if ($item->url == "#wpqa-comments") {
			$classes[] = 'li-comments';
		}else if ($item->url == "#wpqa-followers_questions") {
			$classes[] = 'li-followers-questions';
		}else if ($item->url == "#wpqa-followers_answers") {
			$classes[] = 'li-followers-answers';
		}else if ($item->url == "#wpqa-followers_posts") {
			$classes[] = 'li-followers-posts';
		}else if ($item->url == "#wpqa-followers_comments") {
			$classes[] = 'li-followers-comments';
		}else if ($item->url == "#wpqa-logout") {
			$classes[] = 'li-logout';
		}
		
		if (($item->url == "#wpqa-profile" && wpqa_is_home_profile()) || ($item->url == "#wpqa-edit-profile" && wpqa_is_user_edit_profile()) || ($item->url == "#wpqa-followers" && wpqa_is_user_followers()) || ($item->url == "#wpqa-following" && wpqa_is_user_following()) || ($item->url == "#wpqa-notifications" && wpqa_is_user_notifications()) || ($item->url == "#wpqa-activities" && wpqa_is_user_activities()) || ($item->url == "#wpqa-messages" && wpqa_is_user_messages()) || ($item->url == "#wpqa-questions" && wpqa_is_user_questions()) || ($item->url == "#wpqa-answers" && wpqa_is_user_answers()) || ($item->url == "#wpqa-best_answers" && wpqa_is_best_answers()) || ($item->url == "#wpqa-points" && wpqa_is_user_points()) || ($item->url == "#wpqa-polls" && wpqa_is_user_polls()) || ($item->url == "#wpqa-asked_questions" && wpqa_is_asked_questions()) || ($item->url == "#wpqa-paid_questions" && wpqa_is_paid_questions()) || ($item->url == "#wpqa-followed" && wpqa_is_user_followed()) || ($item->url == "#wpqa-favorites" && wpqa_is_user_favorites()) || ($item->url == "#wpqa-posts" && wpqa_is_user_posts()) || ($item->url == "#wpqa-comments" && wpqa_is_user_comments()) || ($item->url == "#wpqa-followers_questions" && wpqa_is_followers_questions()) || ($item->url == "#wpqa-followers_answers" && wpqa_is_followers_answers()) || ($item->url == "#wpqa-followers_posts" && wpqa_is_followers_posts()) || ($item->url == "#wpqa-followers_comments" && wpqa_is_followers_comments())) {
			$classes[] = 'current-menu-item current_page_item';
		}
	}else if (isset($item->url) && $item->url != "" && ($item->url == "#wpqa-buy-points" || $item->url == "#wpqa-poll" || $item->url == "#wpqa-login" || $item->url == "#wpqa-signup" || $item->url == "#wpqa-lost-password" || $item->url == "#wpqa-add-question" || $item->url == "#wpqa-add-post" || $item->url == "#wpqa-login-popup" || $item->url == "#wpqa-signup-popup" || $item->url == "#wpqa-lost-password-popup" || $item->url == "#wpqa-add-question-popup" || $item->url == "#wpqa-add-post-popup")) {
		if ($item->url == "#wpqa-poll") {
			$classes[] = 'li-poll';
		}
		if ($item->url == "#wpqa-login") {
			$classes[] = 'li-login';
		}
		if ($item->url == "#wpqa-login-popup") {
			$classes[] = 'li-login-popup login-panel';
		}
		if ($item->url == "#wpqa-signup") {
			$classes[] = 'li-signup';
		}
		if ($item->url == "#wpqa-signup-popup") {
			$classes[] = 'li-signup-popup signup-panel';
		}
		if ($item->url == "#wpqa-lost-password") {
			$classes[] = 'li-lost-password';
		}
		if ($item->url == "#wpqa-lost-password-popup") {
			$classes[] = 'li-lost-password-popup lost-password';
		}
		if ($item->url == "#wpqa-add-question") {
			$classes[] = 'li-add-question';
		}
		if ($item->url == "#wpqa-add-question-popup") {
			$classes[] = 'li-add-question-popup wpqa-question';
		}
		if ($item->url == "#wpqa-add-post") {
			$classes[] = 'li-add-post';
		}
		if ($item->url == "#wpqa-add-post-popup") {
			$classes[] = 'li-add-post-popup wpqa-post';
		}
		if ($item->url == "#wpqa-buy-points") {
			$classes[] = 'li-buy-points';
		}
		
		if (($item->url == "#wpqa-buy-points" || wpqa_is_buy_points()) || ($item->url == "#wpqa-poll" && is_post_type_archive("question") && isset($_GET["type"]) && $_GET["type"] == "poll") || ($item->url == "#wpqa-add-question" && wpqa_is_add_questions()) || ($item->url == "#wpqa-login" && wpqa_is_login()) || ($item->url == "#wpqa-signup" && wpqa_is_signup()) || ($item->url == "#wpqa-lost-password" && wpqa_is_lost_password()) || ($item->url == "#wpqa-add-post" && wpqa_is_add_posts())) {
			$classes[] = 'current-menu-item current_page_item';
		}
	}

	$classes = apply_filters("wpqa_menu_classes",$classes,$item);

    return $classes;
}
// Menu FrontEnd
add_filter( 'walker_nav_menu_start_el', 'wpqa_walker_nav_menu_start_el', 10, 4 );
function wpqa_walker_nav_menu_start_el( $item_output, $item, $depth, $args ) {
	// link attributes
	$attributes  = ! empty($item->attr_title)? ' title="'.esc_attr($item->attr_title).'"' : '';
	$attributes .= ! empty($item->target)    ? ' target="'.esc_attr($item->target  ).'"' : '';
	$attributes .= ! empty($item->xfn)       ? ' rel="'  .esc_attr($item->xfn     ).'"' : '';
	$attributes  = apply_filters("wpqa_menu_attributes",$attributes,$item);
	
	if (isset($item->url) && $item->url != "" && is_user_logged_in() && ($item->url == "#wpqa-profile" || $item->url == "#wpqa-edit-profile" || $item->url == "#wpqa-followers" || $item->url == "#wpqa-following" || $item->url == "#wpqa-notifications" || $item->url == "#wpqa-activities" || $item->url == "#wpqa-messages" || $item->url == "#wpqa-points" || $item->url == "#wpqa-questions" || $item->url == "#wpqa-polls" || $item->url == "#wpqa-asked_questions" || $item->url == "#wpqa-paid_questions" || $item->url == "#wpqa-answers" || $item->url == "#wpqa-best_answers" || $item->url == "#wpqa-followed" || $item->url == "#wpqa-favorites" || $item->url == "#wpqa-posts" || $item->url == "#wpqa-comments" || $item->url == "#wpqa-followers_questions" || $item->url == "#wpqa-followers_answers" || $item->url == "#wpqa-followers_posts" || $item->url == "#wpqa-followers_comments" || $item->url == "#wpqa-logout")) {
		$wpqa_user_id = get_current_user_id();
		if ($item->url == "#wpqa-profile") {
			$attributes .= ' href="'.esc_url(wpqa_profile_url($wpqa_user_id)).'"';
		}else if ($item->url == "#wpqa-edit-profile") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"edit")).'"';
		}else if ($item->url == "#wpqa-followers") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"followers")).'"';
		}else if ($item->url == "#wpqa-following") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"following")).'"';
		}else if ($item->url == "#wpqa-notifications") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"notifications")).'"';
		}else if ($item->url == "#wpqa-activities") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"activities")).'"';
		}else if ($item->url == "#wpqa-messages") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"messages")).'"';
		}else if ($item->url == "#wpqa-points") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"points")).'"';
		}else if ($item->url == "#wpqa-questions") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"questions")).'"';
		}else if ($item->url == "#wpqa-polls") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"polls")).'"';
		}else if ($item->url == "#wpqa-asked_questions") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"asked_questions")).'"';
		}else if ($item->url == "#wpqa-paid_questions") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"paid_questions")).'"';
		}else if ($item->url == "#wpqa-answers") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"answers")).'"';
		}else if ($item->url == "#wpqa-best_answers") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"best_answers")).'"';
		}else if ($item->url == "#wpqa-followed") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"followed")).'"';
		}else if ($item->url == "#wpqa-favorites") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"favorites")).'"';
		}else if ($item->url == "#wpqa-posts") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"posts")).'"';
		}else if ($item->url == "#wpqa-comments") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"comments")).'"';
		}else if ($item->url == "#wpqa-followers_questions") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"followers_questions")).'"';
		}else if ($item->url == "#wpqa-followers_answers") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"followers_answers")).'"';
		}else if ($item->url == "#wpqa-followers_posts") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"followers_posts")).'"';
		}else if ($item->url == "#wpqa-followers_comments") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"followers_comments")).'"';
		}else if ($item->url == "#wpqa-logout") {
			$protocol = is_ssl() ? 'https' : 'http';
			$attributes .= ' href="'.wp_logout_url(wp_unslash( $protocol.'://'.wpqa_server('HTTP_HOST').wpqa_server('REQUEST_URI'))).'"';
		}
	}else if (isset($item->url) && $item->url != "" && ($item->url == "#wpqa-buy-points" || $item->url == "#wpqa-poll" || $item->url == "#wpqa-login" || $item->url == "#wpqa-signup" || $item->url == "#wpqa-lost-password" || $item->url == "#wpqa-add-question" || $item->url == "#wpqa-add-post" || $item->url == "#wpqa-login-popup" || $item->url == "#wpqa-signup-popup" || $item->url == "#wpqa-lost-password-popup" || $item->url == "#wpqa-add-question-popup" || $item->url == "#wpqa-add-post-popup")) {
		if ($item->url == "#wpqa-poll") {
			$attributes .= ' href="'.esc_url(add_query_arg(array("type" => "poll"),get_post_type_archive_link("question"))).'"';
		}else if ($item->url == "#wpqa-login") {
			$attributes .= ' href="'.wpqa_login_permalink().'"';
		}else if ($item->url == "#wpqa-login-popup") {
			$attributes .= ' href="'.wpqa_login_permalink().'"';
		}else if ($item->url == "#wpqa-signup") {
			$attributes .= ' href="'.wpqa_signup_permalink().'"';
		}else if ($item->url == "#wpqa-signup-popup") {
			$attributes .= ' href="'.wpqa_signup_permalink().'"';
		}else if ($item->url == "#wpqa-lost-password") {
			$attributes .= ' href="'.wpqa_lost_password_permalink().'"';
		}else if ($item->url == "#wpqa-lost-password-popup") {
			$attributes .= ' href="'.wpqa_lost_password_permalink().'"';
		}else if ($item->url == "#wpqa-add-question") {
			$attributes .= ' href="'.wpqa_add_question_permalink().'"';
		}else if ($item->url == "#wpqa-add-question-popup") {
			$attributes .= ' href="'.wpqa_add_question_permalink().'"';
		}else if ($item->url == "#wpqa-add-post") {
			$attributes .= ' href="'.wpqa_add_post_permalink().'"';
		}else if ($item->url == "#wpqa-add-post-popup") {
			$attributes .= ' href="'.wpqa_add_post_permalink().'"';
		}else if ($item->url == "#wpqa-buy-points") {
			$attributes .= ' href="'.wpqa_buy_points_permalink().'"';
		}
	}else {
		$attributes .= ' href="'.esc_url($item->url).'"';
	}
	
	$item_output = '';
	if (isset($args->before)) {
		$item_output = $args->before;
	}
	
	$item_output .= '<a class="" '.$attributes.'>';
	$item_output .= apply_filters('the_title',$item->title,$item->ID);
	$item_output .= '</a>';
	
	if (isset($args->after)) {
		$item_output .= $args->after;
	}
	
	return $item_output;
}