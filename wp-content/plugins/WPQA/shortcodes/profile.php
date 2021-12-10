<?php

/* @author    2codeThemes
*  @package   WPQA/shortcodes
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Edit profile */
if (!function_exists('wpqa_edit_profile')) :
	function wpqa_edit_profile($atts, $content = null) {
		$breadcrumbs = wpqa_options("breadcrumbs");
		$user_meta_avatar = wpqa_options("user_meta_avatar");
		$user_meta_avatar = apply_filters("wpqa_user_meta_avatar",$user_meta_avatar);
		$user_meta_avatar = ($user_meta_avatar != ""?$user_meta_avatar:"you_avatar");

		$user_meta_cover = wpqa_options("user_meta_cover");
		$user_meta_cover = apply_filters("wpqa_user_meta_cover",$user_meta_cover);
		$user_meta_cover = ($user_meta_cover != ""?$user_meta_cover:"you_cover");

		$edit_profile_sections = wpqa_options("edit_profile_sections");
		$edit_profile_items_1  = wpqa_options("edit_profile_items_1");
		$edit_profile_items_2  = wpqa_options("edit_profile_items_2");
		$edit_profile_items_3  = wpqa_options("edit_profile_items_3");
		
		$names_required = wpqa_options("names_required");
		$profile_picture_required = wpqa_options("profile_picture_required");
		$profile_cover_required = wpqa_options("profile_cover_required");
		$country_required = wpqa_options("country_required");
		$city_required = wpqa_options("city_required");
		$profile_credential_required = wpqa_options("profile_credential_required");
		$phone_required = wpqa_options("phone_required");
		$gender_required = wpqa_options("gender_required");
		$age_required = wpqa_options("age_required");
		$send_email_question_groups = wpqa_options("send_email_question_groups");
		$out = '';
		if (!is_user_logged_in()) {
			$out .= '<div class="alert-message"><i class="icon-lamp"></i><p>'.esc_html__("Please login to edit profile.","wpqa").'</p></div>'.do_shortcode("[wpqa_login]");
		}else {
			$out .= '<form class="edit-profile-form wpqa_form" method="post" enctype="multipart/form-data">'.apply_filters('wpqa_edit_profile_form','edit_profile');
				$user_id = get_current_user_id();
				$user_info = get_userdata($user_id);
				$profile_credential = get_the_author_meta('profile_credential',$user_info->ID);
				$url = get_the_author_meta('url',$user_info->ID);
				$twitter = get_the_author_meta('twitter',$user_info->ID);
				$facebook = get_the_author_meta('facebook',$user_info->ID);
				$youtube = get_the_author_meta('youtube',$user_info->ID);
				$vimeo = get_the_author_meta('vimeo',$user_info->ID);
				$linkedin = get_the_author_meta('linkedin',$user_info->ID);
				$follow_email = get_the_author_meta('follow_email',$user_info->ID);
				if (isset($follow_email) && $follow_email != "" && $follow_email != 0 && $follow_email != "on") {
					$follow_email = "on";
					update_user_meta( $user_info->ID, 'follow_email', $follow_email );
				}
				$display_name = get_the_author_meta('display_name',$user_info->ID);
				$country = get_the_author_meta('country',$user_info->ID);
				$city = get_the_author_meta('city',$user_info->ID);
				$age = get_the_author_meta('age',$user_info->ID);
				$phone = get_the_author_meta('phone',$user_info->ID);
				$gender = get_the_author_meta('gender',$user_info->ID);
				$instagram = get_the_author_meta('instagram',$user_info->ID);
				$pinterest = get_the_author_meta('pinterest',$user_info->ID);
				$show_point_favorite = get_the_author_meta('show_point_favorite',$user_info->ID);
				$received_email = get_the_author_meta('received_email',$user_info->ID);
				$active_message = wpqa_options("active_message");
				$received_message = get_the_author_meta('received_message',$user_info->ID);
				$you_avatar = get_the_author_meta($user_meta_avatar,$user_info->ID);
				$your_cover = get_the_author_meta($user_meta_cover,$user_info->ID);
				$rand_e = rand(1,1000);
				if ($breadcrumbs != "on" && wpqa_is_user_profile() && wpqa_is_user_owner() && wpqa_is_user_edit_profile()) {
					$out .= '<div class="profile-setting profile-no-breadcrumbs">
						<a href="#edit-profile" data-type="setting"'.(isset($_POST["profile_type"]) && $_POST["profile_type"] == "setting"?" class='active-tab'":(empty($_POST["profile_type"])?" class='active-tab'":"")).'>'.esc_html__("Edit profile","wpqa").'</a>
						<a href="#change-password" data-type="password"'.(isset($_POST["profile_type"]) && $_POST["profile_type"] == "password"?" class='active-tab'":"").'>'.esc_html__("Change Password","wpqa").'</a>
					</div><div class="clearfix"></div>';
				}
				$out .= '
				<div class="form-inputs clearfix">
					<div class="page-sections" id="edit-profile"'.(isset($_POST["profile_type"]) && $_POST["profile_type"] == "setting"?" style='display: block'":(isset($_POST["profile_type"]) && $_POST["profile_type"] == "password"?" style='display: none'":"")).'>';
						if (isset($edit_profile_sections) && is_array($edit_profile_sections)) {
							foreach ($edit_profile_sections as $key_sections => $value_sections) {
								if (isset($value_sections["value"]) && $value_sections["value"] == "basic" && isset($edit_profile_items_1) && is_array($edit_profile_items_1)) {
									$out .= '<div class="page-section">
										<div class="page-wrap-content">
											<h2 class="post-title-2"><i class="icon-vcard"></i>'.esc_html__("Basic Information","wpqa").'</h2>';
											$out .= apply_filters('wpqa_edit_profile_before_email',false,$user_info->ID).
											'<p>
												<label for="email_'.$rand_e.'">'.esc_html__("E-Mail","wpqa").'<span class="required">*</span></label>
												<input type="text" name="email" id="email_'.$rand_e.'" value="'.(isset($_POST["email"])?esc_attr($_POST["email"]):$user_info->user_email).'" autocomplete="off">
												<i class="icon-mail"></i>
											</p>';
											foreach ($edit_profile_items_1 as $key_items_1 => $value_items_1) {
												if ($key_items_1 == "names" && isset($value_items_1["value"]) && $value_items_1["value"] == "names") {
													$out .= '<p class="nickname_field">
														<label for="nickname_'.$rand_e.'">'.esc_html__("Nickname","wpqa").'<span class="required">*</span></label>
														<input name="nickname" id="nickname_'.$rand_e.'" type="text" value="'.(isset($_POST["nickname"])?esc_attr($_POST["nickname"]):esc_attr($user_info->nickname)).'">
														<i class="icon-vcard"></i>
													</p>';
													$out .= '
													<p class="first_name_field">
														<label for="first_name_'.$rand_e.'">'.esc_html__("First Name","wpqa").($names_required == "on"?'<span class="required">*</span>':'').'</label>
														<input name="first_name" id="first_name_'.$rand_e.'" type="text" value="'.(isset($_POST["first_name"])?esc_attr($_POST["first_name"]):esc_attr($user_info->first_name)).'">
														<i class="icon-user"></i>
													</p>
													<p class="last_name_field">
														<label for="last_name_'.$rand_e.'">'.esc_html__("Last Name","wpqa").($names_required == "on"?'<span class="required">*</span>':'').'</label>
														<input name="last_name" id="last_name_'.$rand_e.'" type="text" value="'.(isset($_POST["last_name"])?esc_attr($_POST["last_name"]):esc_attr($user_info->last_name)).'">
														<i class="icon-users"></i>
													</p>
													<p class="display_name_field">
														<label for="display_name_'.$rand_e.'">'.esc_html__("Display name","wpqa").($names_required == "on"?'<span class="required">*</span>':'').'</label>
														<input name="display_name" id="display_name_'.$rand_e.'" type="text" value="'.(isset($_POST["display_name"])?esc_attr($_POST["display_name"]):esc_attr($user_info->display_name)).'">
														<i class="icon-user"></i>
													</p>';
												}else if ($key_items_1 == "image_profile" && isset($value_items_1["value"]) && $value_items_1["value"] == "image_profile") {
													if ($user_info->ID > 0) {
														$out .= '<div class="clearfix"></div>
														<div class="author-image profile-image">
															<span class="author-image-span wpqa-delete-image-span">'.wpqa_get_user_avatar(array("user_id" => $user_info->ID,"size" => 100,"user_name" => $user_info->display_name)).'</span>';
															if (((!is_array($you_avatar) && $you_avatar != "") || (is_array($you_avatar) && isset($you_avatar["id"]) && $you_avatar["id"] != 0))) {
																$out .= '<div class="clearfix"></div>
																<div class="button-default wpqa-remove-image" data-name="'.$user_meta_avatar.'" data-type="user_meta" data-id="'.$user_info->ID.'" data-nonce="'.wp_create_nonce("wpqa_remove_image").'">'.esc_html__("Delete","wpqa").'</div>
																<div class="loader_2 loader_4"></div>';
															}
														$out .= '</div>';
													}
													$out .= '<label for="you_avatar_'.$rand_e.'">'.esc_html__('Profile Picture','wpqa').($profile_picture_required == "on"?'<span class="required">*</span>':'').'</label>
													<div class="fileinputs">
														<input type="file" name="'.$user_meta_avatar.'" id="you_avatar_'.$rand_e.'">
														<div class="fakefile">
															<button type="button">'.esc_html__('Select file','wpqa').'</button>
															<span>'.esc_html__('Browse','wpqa').'</span>
														</div>
														<i class="icon-camera"></i>
													</div>
													<div class="clearfix"></div>';
												}else if ($key_items_1 == "cover" && isset($value_items_1["value"]) && $value_items_1["value"] == "cover") {
													if ($user_info->ID > 0 && ((!is_array($your_cover) && $your_cover != "") || (is_array($your_cover) && isset($your_cover["id"]) && $your_cover["id"] != 0))) {
														$out .= '<div class="clearfix"></div>
														<div class="author-image profile-image">
															<span class="author-image-span wpqa-delete-image-span">'.wpqa_get_user_cover(array("user_id" => $user_info->ID,"size" => 100,"user_name" => $user_info->display_name)).'</span>
																<div class="clearfix"></div>
																<div class="button-default wpqa-remove-image" data-name="'.$user_meta_cover.'" data-type="user_meta" data-id="'.$user_info->ID.'" data-nonce="'.wp_create_nonce("wpqa_remove_image").'">'.esc_html__("Delete","wpqa").'</div>
																<div class="loader_2 loader_4"></div>
														</div>';
													}
													$out .= '<label for="your_cover_'.$rand_e.'">'.esc_html__('Cover Picture','wpqa').($profile_cover_required == "on"?'<span class="required">*</span>':'').'</label>
													<div class="fileinputs">
														<input type="file" name="'.$user_meta_cover.'" id="your_cover_'.$rand_e.'">
														<div class="fakefile">
															<button type="button">'.esc_html__('Select file','wpqa').'</button>
															<span>'.esc_html__('Browse','wpqa').'</span>
														</div>
														<i class="icon-camera"></i>
													</div>
													<div class="clearfix"></div>';
												}else if ($key_items_1 == "country" && isset($value_items_1["value"]) && $value_items_1["value"] == "country") {
													$get_countries = apply_filters('wpqa_get_countries',false);
													$out .= '<p>
														<label for="country_'.$rand_e.'">'.esc_html__("Country","wpqa").($country_required == "on"?'<span class="required">*</span>':'').'</label>
														<span class="styled-select">
															<select name="country" id="country_'.$rand_e.'" '.($country_required == "on"?'class="required-item"':'').'>
																<option value="">'.esc_html__( 'Select a country&hellip;', 'wpqa' ).'</option>';
																	foreach( $get_countries as $key => $value ) {
																		$out .= '<option value="' . esc_attr( $key ) . '"' . selected( (isset($_POST["country"])?esc_attr($_POST["country"]):esc_attr($country)), esc_attr( $key ), false ) . '>' . esc_attr( $value ) . '</option>';
																	}
															$out .= '</select>
														</span>
														<i class="icon-location"></i>
													</p>';
												}else if ($key_items_1 == "city" && isset($value_items_1["value"]) && $value_items_1["value"] == "city") {
													$out .= '<p>
														<label for="city_'.$rand_e.'">'.esc_html__("City","wpqa").($city_required == "on"?'<span class="required">*</span>':'').'</label>
														<input type="text" '.($city_required == "on"?'class="required-item"':'').' name="city" id="city_'.$rand_e.'" value="'.(isset($_POST["city"])?esc_attr($_POST["city"]):$city).'">
														<i class="icon-address"></i>
													</p>';
												}else if ($key_items_1 == "phone" && isset($value_items_1["value"]) && $value_items_1["value"] == "phone") {
													$out .= '<p>
														<label for="phone_'.$rand_e.'">'.esc_html__("Phone","wpqa").($phone_required == "on"?'<span class="required">*</span>':'').'</label>
														<input type="text" '.($phone_required == "on"?'class="required-item"':'').' name="phone" id="phone_'.$rand_e.'" value="'.(isset($_POST["phone"])?esc_attr($_POST["phone"]):$phone).'">
														<i class="icon-phone"></i>
													</p>';
												}else if ($key_items_1 == "gender" && isset($value_items_1["value"]) && $value_items_1["value"] == "gender") {
													$last_gender = (isset($_POST["gender"]) && $_POST["gender"]?$_POST["gender"]:$gender);
													$gender_other = wpqa_options("gender_other");
													$out .= '<p class="wpqa_radio_p"><label>'.esc_html__("Gender","wpqa").($gender_required == "on"?'<span class="required">*</span>':'').'</label></p>
													<div class="wpqa_radio_div">
														<p>
															<span class="wpqa_radio"><input id="gender_male_'.$rand_e.'" name="gender" type="radio" value="1"'.($last_gender == "male" || $last_gender == "1"?' checked="checked"':'').'></span>
															<label for="gender_male_'.$rand_e.'">'.esc_html__("Male","wpqa").'</label>
														</p>
														<p>
															<span class="wpqa_radio"><input id="gender_female_'.$rand_e.'" name="gender" type="radio" value="2"'.($last_gender == "female" || $last_gender == "2"?' checked="checked"':'').'></span>
															<label for="gender_female_'.$rand_e.'">'.esc_html__("Female","wpqa").'</label>
														</p>';
														if ($gender_other == "on") {
															$out .= '<p>
																<span class="wpqa_radio"><input id="gender_other_'.$rand_e.'" name="gender" type="radio" value="3"'.($last_gender == "other" || $last_gender == "3"?' checked="checked"':'').'></span>
																<label for="gender_other_'.$rand_e.'">'.esc_html__("Other","wpqa").'</label>
															</p>';
														}
														$out .= '<div class="clearfix"></div>
													</div>';
												}else if ($key_items_1 == "age" && isset($value_items_1["value"]) && $value_items_1["value"] == "age") {
													$out .= '<p>
														<label for="age_'.$rand_e.'">'.esc_html__("Age","wpqa").($age_required == "on"?'<span class="required">*</span>':'').'</label>
														<input type="text" readonly="readonly" class="age-datepicker" '.($age_required == "on"?'class="required-item"':'').' name="age" id="age_'.$rand_e.'" value="'.(isset($_POST["age"])?esc_attr($_POST["age"]):esc_attr($age)).'">
														<i class="icon-globe"></i>
													</p>';
												}
											}
										$out .= '</div>
										<div class="clearfix"></div>
									</div><!-- End page-section -->';
								}else if (isset($value_sections["value"]) && $value_sections["value"] == "social" && isset($edit_profile_items_2) && !empty($edit_profile_items_2) && is_array($edit_profile_items_2)) {
									$p_count = 0;
									$edit_profile_items_2_keys = array_keys($edit_profile_items_2);
									while ($p_count < count($edit_profile_items_2)) {
										if (isset($edit_profile_items_2[$edit_profile_items_2_keys[$p_count]]["value"]) && $edit_profile_items_2[$edit_profile_items_2_keys[$p_count]]["value"] != "" && $edit_profile_items_2[$edit_profile_items_2_keys[$p_count]]["value"] != "0") {
											$first_profile_one = $p_count;
											break;
										}
										$p_count++;
									}
									if (isset($first_profile_one)) {
										$out .= '<div class="page-section">
											<div class="page-wrap-content">
												<h2 class="post-title-2"><i class="icon-globe"></i>'.esc_html__("Social Profiles","wpqa").'</h2>
												<div class="wpqa_form_2">';
													foreach ($edit_profile_items_2 as $key_items_2 => $value_items_2) {
														if ($key_items_2 == "facebook" && isset($value_items_2["value"]) && $value_items_2["value"] == "facebook") {
															$out .= '<p>
																<label for="facebook_'.$rand_e.'">'.esc_html__("Facebook","wpqa").'  '.esc_html__("(Put the full URL)","wpqa").'</label>
																<input type="text" name="facebook" id="facebook_'.$rand_e.'" value="'.(isset($_POST["facebook"])?esc_url($_POST["facebook"]):esc_url($facebook)).'">
																<i class="icon-facebook"></i>
															</p>';
														}else if ($key_items_2 == "twitter" && isset($value_items_2["value"]) && $value_items_2["value"] == "twitter") {
															$out .= '<p>
																<label for="twitter_'.$rand_e.'">'.esc_html__("Twitter","wpqa").'  '.esc_html__("(Put the full URL)","wpqa").'</label>
																<input type="text" name="twitter" id="twitter_'.$rand_e.'" value="'.(isset($_POST["twitter"])?esc_url($_POST["twitter"]):esc_url($twitter)).'">
																<i class="icon-twitter"></i>
															</p>';
														}else if ($key_items_2 == "youtube" && isset($value_items_2["value"]) && $value_items_2["value"] == "youtube") {
															$out .= '<p>
																<label for="youtube_'.$rand_e.'">'.esc_html__("Youtube","wpqa").'  '.esc_html__("(Put the full URL)","wpqa").'</label>
																<input type="text" name="youtube" id="youtube_'.$rand_e.'" value="'.(isset($_POST["youtube"])?esc_url($_POST["youtube"]):esc_url($youtube)).'">
																<i class="icon-play"></i>
															</p>';
														}else if ($key_items_2 == "vimeo" && isset($value_items_2["value"]) && $value_items_2["value"] == "vimeo") {
															$out .= '<p>
																<label for="vimeo_'.$rand_e.'">'.esc_html__("Vimeo","wpqa").'  '.esc_html__("(Put the full URL)","wpqa").'</label>
																<input type="text" name="vimeo" id="vimeo_'.$rand_e.'" value="'.(isset($_POST["vimeo"])?esc_url($_POST["vimeo"]):esc_url($vimeo)).'">
																<i class="icon-vimeo"></i>
															</p>';
														}else if ($key_items_2 == "linkedin" && isset($value_items_2["value"]) && $value_items_2["value"] == "linkedin") {
															$out .= '<p>
																<label for="linkedin_'.$rand_e.'">'.esc_html__("Linkedin","wpqa").'  '.esc_html__("(Put the full URL)","wpqa").'</label>
																<input type="text" name="linkedin" id="linkedin_'.$rand_e.'" value="'.(isset($_POST["linkedin"])?esc_url($_POST["linkedin"]):esc_url($linkedin)).'">
																<i class="icon-linkedin"></i>
															</p>';
														}else if ($key_items_2 == "instagram" && isset($value_items_2["value"]) && $value_items_2["value"] == "instagram") {
															$out .= '<p>
																<label for="instagram_'.$rand_e.'">'.esc_html__("Instagram","wpqa").'  '.esc_html__("(Put the full URL)","wpqa").'</label>
																<input type="text" name="instagram" id="instagram_'.$rand_e.'" value="'.(isset($_POST["instagram"])?esc_url($_POST["instagram"]):esc_url($instagram)).'">
																<i class="icon-instagrem"></i>
															</p>';
														}else if ($key_items_2 == "pinterest" && isset($value_items_2["value"]) && $value_items_2["value"] == "pinterest") {
															$out .= '<p>
																<label for="pinterest_'.$rand_e.'">'.esc_html__("Pinterest","wpqa").'  '.esc_html__("(Put the full URL)","wpqa").'</label>
																<input type="text" name="pinterest" id="pinterest_'.$rand_e.'" value="'.(isset($_POST["pinterest"])?esc_url($_POST["pinterest"]):esc_url($pinterest)).'">
																<i class="icon-pinterest"></i>
															</p>';
														}
													}
												$out .= '</div>
											</div>
											<div class="clearfix"></div>
										</div><!-- End page-section -->';
									}
								}else if (isset($value_sections["value"]) && $value_sections["value"] == "about" && isset($edit_profile_items_3) && is_array($edit_profile_items_3)) {
									$out .= '<div class="page-section">
										<div class="page-wrap-content">
											<h2 class="post-title-2"><i class="icon-graduation-cap"></i>'.esc_html__("About Me","wpqa").'</h2>';
											foreach ($edit_profile_items_3 as $key_items_3 => $value_items_3) {
												if ($key_items_3 == "profile_credential" && isset($value_items_3["value"]) && $value_items_3["value"] == "profile_credential") {
													$out .= '<p>
														<label for="profile_credential_'.$rand_e.'">'.esc_html__("Add profile credential","wpqa").($profile_credential_required == "on"?'<span class="required">*</span>':'').'</label>
														<input type="text" name="profile_credential" id="profile_credential_'.$rand_e.'" value="'.(isset($_POST["profile_credential"])?esc_html($_POST["profile_credential"]):esc_html($profile_credential)).'">
														<i class="icon-info"></i>
													</p>';
												}else if ($key_items_3 == "website" && isset($value_items_3["value"]) && $value_items_3["value"] == "website") {
													$out .= '<p>
														<label for="url_'.$rand_e.'">'.esc_html__("Website","wpqa").'</label>
														<input type="text" name="url" id="url_'.$rand_e.'" value="'.(isset($_POST["url"])?esc_url($_POST["url"]):esc_url($url)).'">
														<i class="icon-link"></i>
													</p>';
												}else if ($key_items_3 == "bio" && isset($value_items_3["value"]) && $value_items_3["value"] == "bio") {
													$bio_editor = wpqa_options("bio_editor");
													if ($bio_editor == "on") {
														$settings = array("textarea_name" => "description","media_buttons" => true,"textarea_rows" => 10);
														$settings = apply_filters('wpqa_description_editor_setting',$settings);
														ob_start();
														wp_editor((isset($_POST["description"])?wpqa_kses_stip($_POST["description"],"yes"):wpqa_kses_stip($user_info->description,"yes")),"description_".$rand_e,$settings);
														$editor_contents = ob_get_clean();
														$out .= '<div class="the-description wpqa_textarea the-textarea">'.$editor_contents.'</div>';
													}else {
														$out .= '<p>
															<label for="description_'.$rand_e.'">'.esc_html__("Professional Bio","wpqa").'</label>
															<textarea name="description" id="description_'.$rand_e.'" cols="58" rows="8">'.(isset($_POST["description"])?stripslashes(sanitize_textarea_field($_POST["description"])):stripslashes(sanitize_textarea_field($user_info->description))).'</textarea>
															<i class="icon-pencil"></i>
														</p>';
													}
												}else if ($key_items_3 == "follow_email" && isset($value_items_3["value"]) && $value_items_3["value"] == "follow_email") {
													$out .= '<p class="normal_label">
														<label for="follow_email_'.$rand_e.'">
															<span class="wpqa_checkbox"><input type="checkbox" name="follow_email" id="follow_email_'.$rand_e.'" value="on" '.checked((isset($_POST["follow_email"])?esc_attr($_POST["follow_email"]):(!empty($_POST) && empty($_POST["follow_email"])?"":esc_attr($follow_email))),"on",false).'></span>
															<span class="wpqa_checkbox_span">'.esc_html__("Show follow up email?","wpqa").'</span>
														</label>
													</p>';
												}else if ($key_items_3 == "private_pages" && isset($value_items_3["value"]) && $value_items_3["value"] == "private_pages") {
													$out .= '<p class="normal_label">
														<label for="show_point_favorite_'.$rand_e.'">
															<span class="wpqa_checkbox"><input type="checkbox" name="show_point_favorite" id="show_point_favorite_'.$rand_e.'" value="on" '.checked((isset($_POST["show_point_favorite"])?esc_attr($_POST["show_point_favorite"]):(!empty($_POST) && empty($_POST["show_point_favorite"])?"":esc_attr($show_point_favorite))),"on",false).'></span>
															<span class="wpqa_checkbox_span">'.esc_html__("Show your private pages for all the users?","wpqa").'</span><span> '.esc_html__("(Points, favorite and followed pages)","wpqa").'.</span>
														</label>
													</p>';
												}else if ($key_items_3 == "send_emails" && isset($value_items_3["value"]) && $value_items_3["value"] == "send_emails" && is_array($send_email_question_groups) && isset($user_info->roles[0]) && in_array($user_info->roles[0],$send_email_question_groups)) {
													$out .= '<p class="normal_label">
														<label for="received_email_'.$rand_e.'">
															<span class="wpqa_checkbox"><input type="checkbox" name="received_email" id="received_email_'.$rand_e.'" value="on" '.checked((isset($_POST["received_email"])?esc_attr($_POST["received_email"]):(!empty($_POST) && empty($_POST["received_email"])?"":esc_attr($received_email))),"on",false).'></span>
															<span class="wpqa_checkbox_span">'.esc_html__("Received mail when user add a new question","wpqa").'.</span>
														</label>
													</p>';
												}else if ($key_items_3 == "received_message" && isset($value_items_3["value"]) && $value_items_3["value"] == "received_message" && $active_message == "on") {
													$out .= '<p class="normal_label">
														<label for="received_message_'.$rand_e.'">
															<span class="wpqa_checkbox"><input type="checkbox" name="received_message" id="received_message_'.$rand_e.'" value="on" '.checked((isset($_POST["received_message"])?esc_attr($_POST["received_message"]):(!empty($_POST) && empty($_POST["received_message"])?"":esc_attr($received_message))),"on",false).'></span>
															<span class="wpqa_checkbox_span">'.esc_html__("Do you need to receive message from another users?","wpqa").'.</span>
														</label>
													</p>';
												}		
											}
										$out .= '</div>
										<div class="clearfix"></div>
									</div><!-- End page-section -->';
								}
							}
						}
					$out .= '</div><!-- End page-sections -->
					
					<div class="page-sections" id="change-password"'.(isset($_POST["profile_type"]) && $_POST["profile_type"] == "password"?" style='display: block'":(isset($_POST["profile_type"]) && $_POST["profile_type"] == "setting"?" style='display: none'":"")).'>
						<div class="page-section">
							<div class="page-wrap-content">
								<h2 class="post-title-2"><i class="icon-lock"></i>'.esc_html__("Change password","wpqa").'</h2>
								<p class="login-password">
									<label for="newpassword_'.$rand_e.'">'.esc_html__("New Password","wpqa").'<span class="required">*</span></label>
									<input id="newpassword_'.$rand_e.'" class="required-item" type="password" name="pass1" value="">
									<i class="icon-lock-open"></i>
								</p>
								<p class="login-password">
									<label for="newpassword2_'.$rand_e.'">'.esc_html__("Confirm Password","wpqa").'<span class="required">*</span></label>
									<input id="newpassword2_'.$rand_e.'" class="required-item" type="password" name="pass2" value="">
									<i class="icon-lock-open"></i>
								</p>
							</div>
						</div><!-- End page-section -->
					</div><!-- End page-sections -->
				</div>
				
				<p class="form-submit">
					<span class="load_span"><span class="loader_2"></span></span>
					<input type="hidden" name="user_action" value="edit_profile">
					<input type="hidden" name="action" value="update">
					<input type="hidden" name="admin_bar_front" value="1">
					<input type="hidden" name="user_id" id="user_id" value="'.esc_attr($user_info->ID).'">
					<input type="hidden" name="user_login" id="user_login" value="'.esc_attr($user_info->user_login).'">
					<input type="hidden" name="profile_type" id="profile_type" value="'.(isset($_POST["profile_type"]) && $_POST["profile_type"] == "password"?"password":"setting").'">
					<input type="submit" value="'.esc_attr__("Save","wpqa").'" class="button-default button-hide-click login-submit submit">
				</p>
			
			</form>';
		}
		return $out;
	}
endif;
/* Process edit profile form */
if (!function_exists('wpqa_process_edit_profile_form')) :
	function wpqa_process_edit_profile_form() {
		$user_meta_avatar = wpqa_options("user_meta_avatar");
		$user_meta_avatar = apply_filters("wpqa_user_meta_avatar",$user_meta_avatar);
		$user_meta_avatar = ($user_meta_avatar != ""?$user_meta_avatar:"you_avatar");
		$user_meta_cover  = wpqa_options("user_meta_cover");
		$user_meta_cover  = apply_filters("wpqa_user_meta_cover",$user_meta_cover);
		$user_meta_cover  = ($user_meta_cover != ""?$user_meta_cover:"you_cover");

		$edit_profile_items_1 = wpqa_options("edit_profile_items_1");
		$edit_profile_items_3 = wpqa_options("edit_profile_items_3");
		$names_register = (isset($edit_profile_items_1["names"]["value"]) && $edit_profile_items_1["names"]["value"] == "names"?"on":0);
		$profile_picture = (isset($edit_profile_items_1["image_profile"]["value"]) && $edit_profile_items_1["image_profile"]["value"] == "image_profile"?"on":0);
		$profile_cover = (isset($edit_profile_items_1["cover"]["value"]) && $edit_profile_items_1["cover"]["value"] == "cover"?"on":0);
		$country_register = (isset($edit_profile_items_1["country"]["value"]) && $edit_profile_items_1["country"]["value"] == "country"?"on":0);
		$city_register = (isset($edit_profile_items_1["city"]["value"]) && $edit_profile_items_1["city"]["value"] == "city"?"on":0);
		$phone_register = (isset($edit_profile_items_1["phone"]["value"]) && $edit_profile_items_1["phone"]["value"] == "phone"?"on":0);
		$gender_register = (isset($edit_profile_items_1["gender"]["value"]) && $edit_profile_items_1["gender"]["value"] == "gender"?"on":0);
		$age_register = (isset($edit_profile_items_1["age"]["value"]) && $edit_profile_items_1["age"]["value"] == "age"?"on":0);
		$profile_credential_register = (isset($edit_profile_items_3["profile_credential"]["value"]) && $edit_profile_items_3["profile_credential"]["value"] == "profile_credential"?"on":0);
		
		$names_required = wpqa_options("names_required");
		$profile_picture_required = wpqa_options("profile_picture_required");
		$profile_picture_size = (int)wpqa_options("profile_picture_size");
		$profile_cover_required = wpqa_options("profile_cover_required");
		$profile_cover_size = (int)wpqa_options("profile_cover_size");
		$country_required = wpqa_options("country_required");
		$city_required = wpqa_options("city_required");
		$phone_required = wpqa_options("phone_required");
		$gender_required = wpqa_options("gender_required");
		$age_required = wpqa_options("age_required");
		$profile_credential_required = wpqa_options("profile_credential_required");
		$user_id = get_current_user_id();
		$get_you_avatar = get_user_meta($user_id,$user_meta_avatar,true);
		$get_your_cover = get_user_meta($user_id,$user_meta_cover,true);
		
		require_once(ABSPATH . 'wp-admin/includes/user.php');
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		
		$errors = new WP_Error();
		$posted = array(
			'email'               => esc_html($_POST['email']),
			'pass1'               => (isset($_POST['profile_type']) && $_POST['profile_type'] == "password" && isset($_POST['pass1']) && $_POST['pass1'] != ""?esc_html($_POST['pass1']):""),
			'pass2'               => (isset($_POST['profile_type']) && $_POST['profile_type'] == "password" && isset($_POST['pass2']) && $_POST['pass2'] != ""?esc_html($_POST['pass2']):""),
			'first_name'          => (isset($_POST['first_name']) && $_POST['first_name'] != ""?esc_html($_POST['first_name']):""),
			'last_name'           => (isset($_POST['last_name']) && $_POST['last_name'] != ""?esc_html($_POST['last_name']):""),
			'nickname'            => (isset($_POST['nickname']) && $_POST['nickname'] != ""?esc_html($_POST['nickname']):""),
			'display_name'        => (isset($_POST['display_name']) && $_POST['display_name'] != ""?esc_html($_POST['display_name']):""),
			'country'             => (isset($_POST['country']) && $_POST['country'] != ""?esc_html($_POST['country']):""),
			'city'                => (isset($_POST['city']) && $_POST['city'] != ""?esc_html($_POST['city']):""),
			'phone'               => (isset($_POST['phone']) && $_POST['phone'] != ""?esc_html($_POST['phone']):""),
			'gender'              => (isset($_POST['gender']) && $_POST['gender'] != ""?esc_html($_POST['gender']):""),
			'age'                 => (isset($_POST['age']) && $_POST['age'] != ""?esc_html($_POST['age']):""),
			'facebook'            => (isset($_POST['facebook']) && $_POST['facebook'] != ""?esc_url($_POST['facebook']):""),
			'twitter'             => (isset($_POST['twitter']) && $_POST['twitter'] != ""?esc_url($_POST['twitter']):""),
			'youtube'             => (isset($_POST['youtube']) && $_POST['youtube'] != ""?esc_url($_POST['youtube']):""),
			'vimeo'               => (isset($_POST['vimeo']) && $_POST['vimeo'] != ""?esc_url($_POST['vimeo']):""),
			'linkedin'            => (isset($_POST['linkedin']) && $_POST['linkedin'] != ""?esc_url($_POST['linkedin']):""),
			'instagram'           => (isset($_POST['instagram']) && $_POST['instagram'] != ""?esc_url($_POST['instagram']):""),
			'pinterest'           => (isset($_POST['pinterest']) && $_POST['pinterest'] != ""?esc_url($_POST['pinterest']):""),
			'follow_email'        => (isset($_POST['follow_email']) && $_POST['follow_email'] != ""?esc_html($_POST['follow_email']):""),
			'show_point_favorite' => (isset($_POST['show_point_favorite']) && $_POST['show_point_favorite'] != ""?esc_html($_POST['show_point_favorite']):""),
			'received_email'      => (isset($_POST['received_email']) && $_POST['received_email'] != ""?esc_html($_POST['received_email']):""),
			'received_message'    => (isset($_POST['received_message']) && $_POST['received_message'] != ""?esc_html($_POST['received_message']):""),
			'profile_credential'  => (isset($_POST['profile_credential']) && $_POST['profile_credential'] != ""?esc_html($_POST['profile_credential']):""),
			'url'                 => (isset($_POST['url']) && $_POST['url'] != ""?esc_url($_POST['url']):""),
			'description'         => (isset($_POST['description']) && $_POST['description'] != ""?esc_html($_POST['description']):""),
		);
		$posted = apply_filters("wpqa_edit_profile_posted",$posted);
		if (isset($_POST['profile_type']) && $_POST['profile_type'] == "setting") {
			unset($_POST['pass1']);
			unset($_POST['pass2']);
		}
		
		if (empty($_POST['email'])) {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (Email).","wpqa"));
		}
		if (isset($_POST['profile_type']) && $_POST['profile_type'] == "password" && $_POST['pass1'] !== $_POST['pass2']) {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("Password does not match.","wpqa"));
		}
		if (empty($_POST['first_name']) && $names_register === "on" && $names_required == "on") {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (First name).","wpqa"));
		}
		if (empty($_POST['last_name']) && $names_register === "on" && $names_required == "on") {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (Last name).","wpqa"));
		}
		if (empty($_POST['nickname']) && $names_register === "on" && $names_required == "on") {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (Nickname).","wpqa"));
		}
		if (empty($_POST['display_name']) && $names_register === "on" && $names_required == "on") {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (Display name).","wpqa"));
		}
		if (empty($_FILES[$user_meta_avatar]['name']) && empty($get_you_avatar) && $profile_picture === "on" && $profile_picture_required == "on") {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (Profile picture).","wpqa"));
		}
		if (empty($_FILES[$user_meta_cover]['name']) && empty($get_your_cover) && $profile_cover === "on" && $profile_cover_required == "on") {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (Cover picture).","wpqa"));
		}
		if (empty($_POST['country']) && $country_register === "on" && $country_required == "on") {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (Country).","wpqa"));
		}
		if (empty($_POST['city']) && $city_register === "on" && $city_required == "on") {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (City).","wpqa"));
		}
		if (empty($_POST['profile_credential']) && $profile_credential_register === "on" && $profile_credential_required == "on") {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (Profile credential).","wpqa"));
		}
		if (empty($_POST['phone']) && $phone_register === "on" && $phone_required == "on") {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (Phone).","wpqa"));
		}
		if (empty($_POST['gender']) && $gender_register === "on" && $gender_required == "on") {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (Gender).","wpqa"));
		}
		if (empty($_POST['age']) && $age_register === "on" && $age_required == "on") {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (Age).","wpqa"));
		}
		
		if ($errors->get_error_code()) {
			return $errors;
		}
		
		isset($_POST['admin_bar_front']) ? 'true' : 'false';
		if ($names_register !== 'on') {
			$_POST['nickname'] = get_the_author_meta("user_login",$user_id);
		}
		$errors_user = edit_user($user_id);
		if (is_wp_error($errors_user)) return $errors_user;
		do_action('personal_options_update',$user_id);
		
		if (isset($_FILES[$user_meta_avatar]) && !empty($_FILES[$user_meta_avatar]['name'])) :
			$mime = $_FILES[$user_meta_avatar]["type"];
			$file_tmp = $_FILES[$user_meta_avatar]['tmp_name'];
			$size = filesize($file_tmp);
			if (($mime != 'image/jpeg') && ($mime != 'image/jpg') && ($mime != 'image/png')) {
				$errors->add('upload-error', esc_html__('Error type, Please upload: jpg,jpeg,png','wpqa'));
				if ($errors->get_error_code()) return $errors;
			}else if ($profile_picture_size > 0 && $size > ($profile_picture_size*1000)) {
				$errors->add('upload-error', sprintf(esc_html__('Error size, The maximum size is %s MB.','wpqa'),floor($profile_picture_size/1000)));
			}else {
				$you_avatar = wp_handle_upload($_FILES[$user_meta_avatar],array('test_form'=>false),current_time('mysql'));
				if ($you_avatar && isset($you_avatar["url"])) :
					$filename = $you_avatar["file"];
					$filetype = wp_check_filetype( basename( $filename ), null );
					$wp_upload_dir = wp_upload_dir();
					
					$attachment = array(
						'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
						'post_mime_type' => $filetype['type'],
						'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
						'post_content'   => '',
						'post_status'    => 'inherit'
					);
					$attach_id = wp_insert_attachment( $attachment, $filename );
					$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
					wp_update_attachment_metadata( $attach_id, $attach_data );
					update_user_meta($user_id,$user_meta_avatar,$attach_id);
				endif;
				if (isset($you_avatar['error']) && $you_avatar) :
					if (isset($errors->add)) {
						$errors->add('upload-error', esc_html__('Error in upload the image : ','wpqa') . $you_avatar['error']);
						if ($errors->get_error_code()) return $errors;
					}
					return $errors;
				endif;
			}
		else:
			update_user_meta($user_id,$user_meta_avatar,$get_you_avatar);
		endif;

		if (isset($_FILES[$user_meta_cover]) && !empty($_FILES[$user_meta_cover]['name'])) :
			$mime = $_FILES[$user_meta_cover]["type"];
			$file_tmp = $_FILES[$user_meta_cover]['tmp_name'];
			$size = filesize($file_tmp);
			if (($mime != 'image/jpeg') && ($mime != 'image/jpg') && ($mime != 'image/png')) {
				$errors->add('upload-error', esc_html__('Error type, Please upload: jpg,jpeg,png','wpqa'));
				if ($errors->get_error_code()) return $errors;
			}else if ($profile_cover_size > 0 && $size > ($profile_cover_size*1000)) {
				$errors->add('upload-error', sprintf(esc_html__('Error size, The maximum size is %s MB.','wpqa'),floor($profile_cover_size/1000)));
			}else {
				$your_cover = wp_handle_upload($_FILES[$user_meta_cover],array('test_form'=>false),current_time('mysql'));
				if ($your_cover && isset($your_cover["url"])) :
					$filename = $your_cover["file"];
					$filetype = wp_check_filetype( basename( $filename ), null );
					$wp_upload_dir = wp_upload_dir();
					
					$attachment = array(
						'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
						'post_mime_type' => $filetype['type'],
						'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
						'post_content'   => '',
						'post_status'    => 'inherit'
					);
					$attach_id = wp_insert_attachment( $attachment, $filename );
					$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
					wp_update_attachment_metadata( $attach_id, $attach_data );
					update_user_meta($user_id,$user_meta_cover,$attach_id);
				endif;
				if (isset($your_cover['error']) && $your_cover) :
					if (isset($errors->add)) {
						$errors->add('upload-error', esc_html__('Error in upload the image : ','wpqa') . $your_cover['error']);
						if ($errors->get_error_code()) return $errors;
					}
					return $errors;
				endif;
			}
		else:
			update_user_meta($user_id,$user_meta_cover,$get_your_cover);
		endif;

		if (sizeof($errors->errors)>0) return $errors;
		$update_profile = get_user_meta($user_id,"update_profile",true);
		if ($update_profile == "yes") {
			delete_user_meta($user_id,"update_profile");
  			wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Profile has been updated.","wpqa").'</p></div>','wpqa_session');
			wp_safe_redirect(esc_url(home_url('/')));
			exit;
		}
		return;
	}
endif;
/* Edit profile form */
if (!function_exists('wpqa_edit_profile_form')) :
	function wpqa_edit_profile_form($edit) {
		if (isset($_POST["user_action"]) && $_POST["user_action"] == $edit) :
			$return = wpqa_process_edit_profile_form();
			if (is_wp_error($return)) :
	   			return '<div class="wpqa_error">'.$return->get_error_message().'</div>';
	   		else :
	   			return '<div class="wpqa_success">'.esc_html__("Profile has been updated.","wpqa").'</div>';
	   		endif;
		endif;
	}
endif;
add_filter('wpqa_edit_profile_form','wpqa_edit_profile_form');
/* Show profile fields */
add_action( 'show_user_profile', 'wpqa_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'wpqa_show_extra_profile_fields' );
function wpqa_show_extra_profile_fields( $user ) {?>
	<table class="form-table">
		<tr class="form-terms">
			<th colspan="2" scope="row" valign="top">
				<div class="discy_framework">
					<?php if (class_exists('discy_admin_fields_class')) {
						discy_admin_fields_class::discy_admin_fields("author",discy_author,"author",$user->ID,discy_admin_author($user->ID));
					}?>
				</div>
			</th>
		</tr>
	</table>
<?php }
/* Save user's meta */
add_action( 'personal_options_update', 'wpqa_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'wpqa_save_extra_profile_fields' );
function wpqa_save_extra_profile_fields( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) ) return false;
	
	$options = (function_exists('discy_admin_author')?discy_admin_author($user_id):array());
	$get_current_user_id = get_current_user_id();
	
	if (isset($_POST['admin']) && $_POST['admin'] == "save") {
		do_action("wpqa_user_register",$user_id);
		
		if (isset($_POST['user_best_answer'])) {
			$user_best_answer = sanitize_text_field($_POST['user_best_answer']);
			update_user_meta( $user_id, 'user_best_answer', $user_best_answer );
		}
	}
	
	if (isset($_POST['from_admin']) && $_POST['from_admin'] == "yes") {
		$active_points = wpqa_options("active_points");
		if (is_super_admin($get_current_user_id) && $active_points == "on") {
			$add_remove_point = "";
			$the_points = "";
			$the_reason = "";
			if (isset($_POST['add_remove_point'])) {
				$add_remove_point = esc_html($_POST['add_remove_point']);
			}
			if (isset($_POST['the_points'])) {
				$the_points = (int)esc_html($_POST['the_points']);
			}
			if (isset($_POST['the_reason'])) {
				$the_reason = esc_html($_POST['the_reason']);
			}
			if ($the_points > 0) {
				if ($add_remove_point == "remove") {
					$add_remove_point_last = "-";
					$the_reason_last = "admin_remove_points";
				}else {
					$add_remove_point_last = "+";
					$the_reason_last = "admin_add_points";
				}
				$the_reason = (isset($the_reason) && $the_reason != ""?$the_reason:$the_reason_last);
				wpqa_add_points($user_id,$the_points,$add_remove_point_last,$the_reason_last);
				if ($get_current_user_id > 0 && $user_id > 0) {
					wpqa_notifications_activities($user_id,$get_current_user_id,"","","",$the_reason_last,"notifications");
				}
			}
		}
		
		foreach ($options as $value) {
			if (!isset($value['unset']) && $value['type'] != 'heading' && $value['type'] != "heading-2" && $value['type'] != "html" && $value['type'] != 'info' && $value['type'] != 'content') {
				$val = '';
				if (isset($value['std'])) {
					$val = $value['std'];
				}
				
				$field_name = $value['id'];
				if (isset($_POST[$field_name])) {
					$val = $_POST[$field_name];
				}
				
				if (!isset($_POST[$field_name]) && $value['type'] == "checkbox") {
					$val = 0;
				}
				
				if ('' === $val || array() === $val) {
					delete_user_meta($user_id,$field_name);
				}else {
					update_user_meta($user_id,$field_name,$val);
				}
			}
		}
	}else {
		$post_array = array('country','city','phone','gender','age','facebook','twitter','youtube','vimeo','linkedin','instagram','pinterest','follow_email','show_point_favorite','received_email','received_message','profile_credential');
		$post_array = apply_filters("wpqa_edit_profile_post_array",$post_array);
		foreach ($post_array as $field_name) {
			$val = '';
			
			if (isset($_POST[$field_name])) {
				$val = $_POST[$field_name];
			}
			
			if ('' === $val || array() === $val) {
				delete_user_meta($user_id,$field_name);
			}else {
				update_user_meta($user_id,$field_name,$val);
			}
		}
	}
	$nicename_nickname = (isset($_POST['nickname']) && $_POST['nickname'] != ""?sanitize_text_field($_POST['nickname']):sanitize_text_field($_POST['user_name']));
	edit_user($user_id);
	wp_update_user( array ('ID' => $user_id, 'user_nicename' => $nicename_nickname, 'nickname' => $nicename_nickname) ) ;
}?>