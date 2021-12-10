<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Action user avatar */
add_action("wpqa_user_avatar","wpqa_action_user_avatar");
if (!function_exists('wpqa_action_user_avatar')) :
	function wpqa_action_user_avatar ($args = array()) {
		if (isset($args["size"]) && isset($args["user_id"])) {
			echo wpqa_get_user_avatar(array("user_id" => $args["user_id"],"size" => $args["size"],"user_name" => (isset($args["name"])?$args["name"]:""),"user" => (isset($args["user"])?$args["user"]:""),"post" => (isset($args["post"])?$args["post"]:""),"comment" => (isset($args["comment"])?$args["comment"]:""),"email" => (isset($args["email"])?$args["email"]:"")));
		}
	}
endif;
/* Action user avatar link */
add_action("wpqa_action_avatar_link","wpqa_action_avatar_link");
if (!function_exists('wpqa_action_avatar_link')) :
	function wpqa_action_avatar_link ($args = array()) {
		if (isset($args["size"]) && isset($args["user_id"])) {
			echo wpqa_get_avatar_link(array("user_id" => $args["user_id"],"size" => $args["size"],"user_name" => (isset($args["name"])?$args["name"]:""),"user" => (isset($args["user"])?$args["user"]:""),"pop" => (isset($args["pop"])?$args["pop"]:""),"span" => (isset($args["span"])?$args["span"]:""),"post" => (isset($args["post"])?$args["post"]:""),"comment" => (isset($args["comment"])?$args["comment"]:""),"email" => (isset($args["email"])?$args["email"]:"")));
		}
	}
endif;
/* Get the user avatar */
add_filter('get_avatar','wpqa_avatar',1,5);
if (!function_exists('wpqa_avatar')) :
	function wpqa_avatar($avatar,$id_or_email,$size,$default,$alt) {
		$user = false;
		if (is_numeric($id_or_email)) {
			$id = (int)$id_or_email;
			$user = get_user_by('id',$id);
		}else if (is_object($id_or_email)) {
			if (!empty($id_or_email->user_id)) {
				$id = (int)$id_or_email->user_id;
				$user = get_user_by('id',$id);
			}
		}else {
			$user = get_user_by('email',$id_or_email);	
		}
		
		if (isset($user) && is_object($user) && $user->data->ID > 0) {
			$user_meta_avatar = wpqa_options("user_meta_avatar");
			$user_meta_avatar = apply_filters("wpqa_user_meta_avatar",$user_meta_avatar);
			$user_meta_avatar = ($user_meta_avatar != ""?$user_meta_avatar:"you_avatar");
			$you_avatar = get_the_author_meta($user_meta_avatar,$user->data->ID);
			if ((($you_avatar && !is_array($you_avatar)) || (is_array($you_avatar) && isset($you_avatar["id"]) && $you_avatar["id"] != 0)) && $user->data->ID > 0) {
				$avatar = wpqa_get_user_avatar_image($you_avatar,$size,$alt);
			}
		}
		return $avatar;
	}
endif;
/* Get user avatar url */
if (!function_exists('wpqa_get_user_avatar_url')) :
	function wpqa_get_user_avatar_url ($you_avatar,$size,$user_name) {
		$avatar_num = false;
		if (isset($you_avatar) && is_array($you_avatar) && isset($you_avatar["id"])) {
			$you_avatar = $you_avatar["id"];
			$avatar_num = true;
		}

		if (isset($you_avatar) && $you_avatar != "" && is_numeric($you_avatar) && $you_avatar > 0) {
			$avatar_num = true;
		}else {
			$get_attachment_id = wpqa_get_attachment_id($you_avatar);
			if (isset($get_attachment_id) && $get_attachment_id != "" && is_numeric($get_attachment_id) && $you_avatar > 0) {
				$avatar_num = true;
				$you_avatar = $get_attachment_id;
			}
		}
		
		if ($you_avatar > 0 && $avatar_num == true) {
			$avatar = wpqa_get_aq_resize_img_url($size,$size,"",$you_avatar,"",$user_name);
		}else {
			$avatar = wpqa_get_aq_resize_url($you_avatar,$size,$size);
		}
		return $avatar;
	}
endif;
/* Get user avatar image */
if (!function_exists('wpqa_get_user_avatar_image')) :
	function wpqa_get_user_avatar_image ($you_avatar,$size,$user_name) {
		$avatar = "<img class='avatar avatar-".$size." photo' alt='".esc_attr($user_name)."' title='".esc_attr($user_name)."' width='".$size."' height='".$size."' src='".wpqa_get_user_avatar_url($you_avatar,$size,$user_name)."'>";
		return $avatar;
	}
endif;
/* Get user avatar link */
if (!function_exists('wpqa_get_user_avatar_link')) :
	function wpqa_get_user_avatar_link ($args = array()) {
		$defaults = array(
			'user_id'   => '',
			'size'      => '',
			'user_name' => '',
			'user'      => '',
			'post'      => '',
			'comment'   => '',
			'email'     => '',
		);
		
		$args = wp_parse_args($args,$defaults);
		
		$user_id   = $args['user_id'];
		$size      = $args['size'];
		$user_name = $args['user_name'];
		$user      = $args['user'];
		$post      = $args['post'];
		$comment   = $args['comment'];
		$email     = $args['email'];
		
		$user_name = ($user_name == "" && $user_id > 0?get_the_author_meta('display_name',$user_id):$user_name);
		
		if (!empty($comment)) {
			$user_name = ($user_id > 0?$user_name:$comment->comment_author);
		}
		
		$user_id = ($user_id == 0 && $email != ""?$email:$user_id);
		$user_meta_avatar = wpqa_options("user_meta_avatar");
		$user_meta_avatar = apply_filters("wpqa_user_meta_avatar",$user_meta_avatar);
		$user_meta_avatar = ($user_meta_avatar != ""?$user_meta_avatar:"you_avatar");
		$you_avatar = get_the_author_meta($user_meta_avatar,$user_id);
		if ((($you_avatar && !is_array($you_avatar)) || (is_array($you_avatar) && isset($you_avatar["id"]) && $you_avatar["id"] != 0)) && $user_id > 0) {
			$avatar = wpqa_get_user_avatar_url($you_avatar,$size,$user_name);
		}else {
			$avatar = get_avatar_url((!empty($user)?$user:$user_id),$size,"",$user_name);
			$email = '';
			$out_return = true;
			$default_image_active = wpqa_options("default_image_active");
			if ($default_image_active == "on") {
				$default_image = wpqa_image_url_id(wpqa_options("default_image"));
				$default_image_females = wpqa_image_url_id(wpqa_options("default_image_females"));
				if ($default_image_females != "") {
					$gender = get_the_author_meta('gender',$user_id);
					$default_image = ($gender == "Female" || $gender == 2?$default_image_females:$default_image);
				}
				$id_or_email = (!empty($user)?$user:$user_id);
				if (is_numeric($id_or_email)) {
					$id = (int) $id_or_email;
					$user = get_userdata($id);
					if ($user) {
						$email = $user->user_email;
					}
				}else if (is_object($id_or_email)) {
					$allowed_comment_types = apply_filters('get_avatar_comment_types',array('comment'));
					if (!empty( $id_or_email->comment_type ) && ! in_array( $id_or_email->comment_type,(array)$allowed_comment_types ) ) {
						$out_return = false;
					}
					if (!empty($id_or_email->user_id)) {
						$id = (int) $id_or_email->user_id;
						$user = get_userdata($id);
						if ($user) {
							$email = $user->user_email;
						}
					}else if (!empty($id_or_email->comment_author_email)) {
						$email = $id_or_email->comment_author_email;
					}
				}else {
					$email = $id_or_email;
				}
				
				$hashkey = md5(strtolower(trim($email)));
				$uri = 'https://www.gravatar.com/avatar/'.$hashkey.'?d=404';
				$data = get_transient($hashkey);
				if (false === $data) {
					$response = wp_remote_head($uri);
					if (is_wp_error($response)) {
						$data = 'not200';
					}else {
						$data = $response['response']['code'];
					}
					set_transient($hashkey,$data,60*60*12);
				}
				if ($data == '200') {
					$out_return = true;
				}else if ($default_image_active == "on" && $default_image != "") {
					$out_return = false;
				}
				
				if ($out_return == false) {
					$avatar = wpqa_get_aq_resize_url($default_image,$size,$size,"on");
				}
			}
		}
		return $avatar;
	}
endif;
/* Get user avatar */
if (!function_exists('wpqa_get_user_avatar')) :
	function wpqa_get_user_avatar ($args = array()) {
		$defaults = array(
			'user_id'   => '',
			'size'      => '',
			'user_name' => '',
			'user'      => '',
			'post'      => '',
			'comment'   => '',
			'email'     => '',
		);
		
		$args = wp_parse_args($args,$defaults);
		
		$size      = $args['size'];
		$user_name = $args['user_name'];
		
		$avatar = "<img class='avatar avatar-".$size." photo' alt='".esc_attr($user_name)."' title='".esc_attr($user_name)."' width='".$size."' height='".$size."' src='".wpqa_get_user_avatar_link($args)."'>";
		return $avatar;
	}
endif;
/* Get user avatar with link */
if (!function_exists('wpqa_get_avatar_link')) :
	function wpqa_get_avatar_link($args = array()) {
		$defaults = array(
			'user_id'   => '',
			'size'      => '',
			'user_name' => '',
			'user'      => '',
			'pop'       => '',
			'span'      => '',
			'post'      => '',
			'comment'   => '',
			'email'     => '',
		);
		
		$args = wp_parse_args($args,$defaults);
		
		$user_id   = $args['user_id'];
		$size      = $args['size'];
		$user_name = $args['user_name'];
		$user      = $args['user'];
		$pop       = $args['pop'];
		$span      = $args['span'];
		$post      = $args['post'];
		$comment   = $args['comment'];
		$email     = $args['email'];
		$url       = '';
		
		$avatar = wpqa_get_user_avatar(array("user_id" => $user_id,"size" => $size,"user_name" => $user_name,"user" => $user,"post" => $post,"comment" => $comment,"email" => $email));
		
		$out = ($span == 'span'?'<div class="author-image author-image-'.$size.'">':'');
			if ($user_id > 0) {
				$wpqa_profile_url = wpqa_profile_url($user_id);
				if ($wpqa_profile_url != "" && $wpqa_profile_url != "wpqa_No_site") {
					$out .= '<a href="'.esc_url($wpqa_profile_url).'">';
				}
				$out .= ($span == 'span'?'<span class="author-image-span">':'').$avatar.($span == 'span'?'</span>':'');
				$out = apply_filters("wpqa_get_user_avatar_filter",$out,$user_id);
				if ($wpqa_profile_url != "" && $wpqa_profile_url != "wpqa_No_site") {
					$out .= '</a>';
				}
				$author_image_pop = wpqa_options("author_image_pop");
				if ($pop == "pop" && $author_image_pop == "on") {
					$owner = false;
					if (get_current_user_id() == $user_id) {
						$owner = true;
					}
					$out .= '<div class="author-image-pop-2">
						'.wpqa_author($user_id,"columns_pop",$owner).'
					</div>';
				}
			}else {
				if (!empty($comment)) {
					$url = $comment->comment_author_url;
				}
				$wpqa_profile_url = ($url != ""?$url:"wpqa_No_site");
				if ($wpqa_profile_url != "" && $wpqa_profile_url != "wpqa_No_site") {
					$out .= '<a title="'.$user_name.'" href="'.esc_url($wpqa_profile_url).'">';
				}
				$out .= ($span == 'span'?'<span class="author-image-span">':'').$avatar.($span == 'span'?'</span>':'');
				$out = apply_filters("wpqa_get_user_avatar_filter",$out,$user_id);
				if ($wpqa_profile_url != "" && $wpqa_profile_url != "wpqa_No_site") {
					$out .= '</a>';
				}
			}
		$out .= ($span == 'span'?'</div>':'');
		return $out;
	}
endif;?>