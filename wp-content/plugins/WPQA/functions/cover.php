<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Get user cover url */
if (!function_exists('wpqa_get_user_cover_url')) :
	function wpqa_get_user_cover_url ($your_cover,$size,$user_name) {
		$cover_num = false;
		if (isset($your_cover) && is_array($your_cover) && isset($your_cover["id"])) {
			$your_cover = $your_cover["id"];
			$cover_num = true;
		}

		if (isset($your_cover) && $your_cover != "" && is_numeric($your_cover) && $your_cover > 0) {
			$cover_num = true;
		}else {
			$get_attachment_id = wpqa_get_attachment_id($your_cover);
			if (isset($get_attachment_id) && $get_attachment_id != "" && is_numeric($get_attachment_id) && $your_cover > 0) {
				$cover_num = true;
				$your_cover = $get_attachment_id;
			}
		}
		
		if ($your_cover > 0 && $cover_num == true) {
			$cover = wpqa_get_aq_resize_img_url($size,$size,"",$your_cover,"",$user_name);
		}else {
			$cover = wpqa_get_aq_resize_url($your_cover,$size,$size);
		}
		return $cover;
	}
endif;
/* Get user cover image */
if (!function_exists('wpqa_get_user_cover_image')) :
	function wpqa_get_user_cover_image ($your_cover,$size,$user_name) {
		$cover = "<img class='cover-".$size." photo' alt='".esc_attr($user_name)."' title='".esc_attr($user_name)."' width='".$size."' height='".$size."' src='".wpqa_get_user_cover_url($your_cover,$size,$user_name)."'>";
		return $cover;
	}
endif;
/* Get user cover link */
if (!function_exists('wpqa_get_user_cover_link')) :
	function wpqa_get_user_cover_link ($args = array()) {
		$defaults = array(
			'user_id'   => '',
			'size'      => '',
			'user_name' => '',
			'user'      => '',
			'post'      => '',
			'comment'   => '',
		);
		
		$args = wp_parse_args($args,$defaults);
		
		$user_id   = $args['user_id'];
		$size      = $args['size'];
		$user_name = $args['user_name'];
		$user      = $args['user'];
		$post      = $args['post'];
		$comment   = $args['comment'];
		
		$user_name = ($user_name == "" && $user_id > 0?get_the_author_meta('display_name',$user_id):$user_name);
		
		if (!empty($comment)) {
			$user_name = ($user_id > 0?$user_name:$comment->comment_author);
		}

		$user_meta_cover = wpqa_options("user_meta_cover");
		$user_meta_cover = apply_filters("wpqa_user_meta_cover",$user_meta_cover);
		$user_meta_cover = ($user_meta_cover != ""?$user_meta_cover:"you_cover");
		
		$your_cover = get_the_author_meta($user_meta_cover,$user_id);
		if ((($your_cover && !is_array($your_cover)) || (is_array($your_cover) && isset($your_cover["id"]) && $your_cover["id"] != 0)) && $user_id > 0) {
			$cover = wpqa_get_user_cover_url($your_cover,$size,$user_name);
		}else {
			$default_cover_active = wpqa_options("default_cover_active");
			if ($default_cover_active == "on") {
				$default_cover = wpqa_image_url_id(wpqa_options("default_cover"));
				$default_cover_females = wpqa_image_url_id(wpqa_options("default_cover_females"));
				if ($default_cover_females != "") {
					$gender = get_the_author_meta('gender',$user_id);
					$default_cover = ($gender == "Female" || $gender == 2?$default_cover_females:$default_cover);
				}
				
				if ($default_cover_active == "on" && $default_cover != "") {
					$cover = wpqa_get_aq_resize_url($default_cover,$size,$size,"on");
				}
			}
		}
		if (isset($cover)) {
			return $cover;
		}
	}
endif;
/* Get user cover */
if (!function_exists('wpqa_get_user_cover')) :
	function wpqa_get_user_cover ($args = array()) {
		$defaults = array(
			'user_id'   => '',
			'size'      => '',
			'user_name' => '',
			'user'      => '',
			'post'      => '',
			'comment'   => '',
		);
		
		$args = wp_parse_args($args,$defaults);

		$size      = $args['size'];
		$user_name = $args['user_name'];
		
		$cover = "<img class='cover-".$size." photo' alt='".esc_attr($user_name)."' title='".esc_attr($user_name)."' width='".$size."' height='".$size."' src='".wpqa_get_user_cover_link($args)."'>";
		return $cover;
	}
endif;
add_action("wpqa_cover_image","wpqa_cover_image");
function wpqa_cover_image () {
	$cover_image = wpqa_options("cover_image");
	if ($cover_image == "on" && wpqa_is_user_profile() && !wpqa_is_user_edit_profile()) {
		$user_id = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
		$display_name = get_the_author_meta("display_name",$user_id);
		$profile_credential = get_the_author_meta('profile_credential',$user_id);
		$ask_question_to_users = wpqa_options("ask_question_to_users");
		$cover_fixed = wpqa_options("cover_fixed");
		$owner = wpqa_is_user_owner();
		$following_you = get_user_meta($user_id,"following_you",true);
		$add_questions = (int)wpqa_count_posts_by_user($user_id,"question");
		$active_points_category = wpqa_options("active_points_category");
		if ($active_points_category == "on") {
			$categories_user_points = get_user_meta($user_id,"categories_user_points",true);
			if (is_array($categories_user_points) && !empty($categories_user_points)) {
				foreach ($categories_user_points as $category) {
					$points_category_user[$category] = (int)get_user_meta($user_id,"points_category".$category,true);
				}
				arsort($points_category_user);
				$first_category = key($points_category_user);
				$first_points = reset($points_category_user);
			}
		}
		echo "<div class='wpqa-profile-cover".($cover_fixed == "fixed"?" wpqa-cover-fixed":"")."'>
		<div class='wpqa-cover-background".($cover_fixed == "fixed"?" discy-container":"")."'>
			<div class='cover-opacity'></div>
			<div class='wpqa-cover-inner".($cover_fixed == "fixed"?"":" discy-container")."'>
				<div class='wpqa-cover-content'>
					<div class='post-section user-area user-advanced user-cover'>
						<div class='post-inner'>
							<div class='user-head-area'>
								".wpqa_get_avatar_link(array("user_id" => $user_id,"size" => 84,"span" => "span"))."
							</div>
							<div class='user-content'>
								<div class='user-inner'>
									<h4><a href='".esc_url(wpqa_profile_url($user_id))."'>".$display_name."</a>".wpqa_verified_user($user_id)."</h4>";
									if (isset($first_category)) {
										echo apply_filters("wpqa_cover_before_badge",false,$first_category);
									}
									echo wpqa_get_badge($user_id,"",(isset($first_points) && $first_points != ""?$first_points:""));
									echo "<span class='profile-credential'>".esc_html($profile_credential)."</span>
								</div>
							</div>
						</div>
					</div>
					<div class='wpqa-cover-right'>";
						if ($owner) {
							echo "<div class='question-list-details'>
								<i class='icon-dot-3'></i>
								<ul class='question-link-list'>
									<li><a href='".wpqa_get_profile_permalink($user_id,"edit")."'><i class='icon-cog'></i>".esc_html__("Edit profile","wpqa")."</a></li>
								</ul>
							</div>";
						}
						echo wpqa_following($user_id,"style_4",$owner).
						($ask_question_to_users == "on" && $owner == false?"<div class='ask-question'><a href='".esc_url(wpqa_add_question_permalink("user"))."' class='button-default ask-question-user'>".esc_html__("Ask","wpqa")." ".$display_name."</a></div>":"").
						wpqa_message_button($user_id,"text",$owner)."
						<div class='wpqa-cover-buttons'><i class='icon-users'></i><span class='cover-count follow-cover-count'>".(is_array($following_you)?wpqa_count_number(count($following_you)):0)."</span>".esc_html__("Followers","wpqa")."</div>
						<div><a class='wpqa-cover-buttons' href='".wpqa_get_profile_permalink($user_id,"questions")."'><i class='icon-book-open'></i><span class='cover-count'>".wpqa_count_number($add_questions)."</span>".esc_html__("Questions","wpqa")."</a></div>
					</div>
				</div>
				<div class='clearfix'></div>
			</div>
		</div>
		</div><!-- End wpqa-profile-cover -->";
	}
}?>