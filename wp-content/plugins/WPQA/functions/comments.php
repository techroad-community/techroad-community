<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Pre process comment */
add_filter('preprocess_comment','wpqa_comment_question_before');
if (!function_exists('wpqa_comment_question_before')) :
	function wpqa_comment_question_before($commentdata) {
		$get_post_type_comment = "";
		if (!is_admin() && (get_post_type($commentdata["comment_post_ID"]) == "question" || get_post_type($commentdata["comment_post_ID"]) == "post" || get_post_type($commentdata["comment_post_ID"]) == "page")) {
			$the_captcha = 0;
			if (get_post_type($commentdata["comment_post_ID"]) == "question") {
				$the_captcha = wpqa_options("the_captcha_answer");
			}else {
				$the_captcha = wpqa_options("the_captcha_comment");
			}
			$captcha_users = wpqa_options("captcha_users");
			$captcha_style = wpqa_options("captcha_style");
			$captcha_question = wpqa_options("captcha_question");
			$captcha_answer = wpqa_options("captcha_answer");
			$show_captcha_answer = wpqa_options("show_captcha_answer");
			if ($the_captcha == "on" && ($captcha_users == "both" || ($captcha_users == "unlogged" && !is_user_logged_in()))) {
				if (empty($_POST["wpqa_captcha"])) {
					if (defined('DOING_AJAX') && DOING_AJAX)
						die(sprintf(esc_html__("%s ERROR %s: please type a captcha.","wpqa"),"<strong>","</strong>"));
					else
						wp_die(sprintf(esc_html__("%s ERROR %s: please type a captcha.","wpqa"),"<strong>","</strong>"));
					exit;
				}
				if ($captcha_style == "question_answer") {
					if ($captcha_answer != $_POST["wpqa_captcha"]) {
						if (defined('DOING_AJAX') && DOING_AJAX)
							die(esc_html__('The captcha is incorrect, Please try again.','wpqa'));
						else
							wp_die(esc_html__('The captcha is incorrect, Please try again.','wpqa'));
						exit;
					}
				}else {
					if(!session_id()) session_start();
					if (isset($_SESSION["wpqa_code_captcha_comment"]) && $_SESSION["wpqa_code_captcha_comment"] != $_POST["wpqa_captcha"]) {
						if (defined('DOING_AJAX') && DOING_AJAX)
							die(esc_html__('The captcha is incorrect, Please try again.','wpqa'));
						else
							wp_die(esc_html__('The captcha is incorrect, Please try again.','wpqa'));
						exit;
					}
				}
			}

			$answer_video = wpqa_options("answer_video");
			if ($answer_video == "on" && isset($_POST['video_answer_description']) && $_POST['video_answer_description'] == "on" && empty($_POST['video_answer_id'])) {
				wp_die(esc_html__("There are required fields (Video ID).","wpqa"));
				exit;
			}
			
			if (isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name'])) :
				require_once(ABSPATH . 'wp-admin/includes/image.php');
				require_once(ABSPATH . 'wp-admin/includes/file.php');
				$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/gif","image/tiff","image/ico");
				if (!in_array($_FILES['featured_image']['type'],$types)) :
					wp_die(esc_html__("Attachment Error, Please upload image only.","wpqa"));
					exit;
				endif;
			endif;
		}
		return $commentdata;
	}
endif;
/* Comment question */
add_action ('comment_post','wpqa_comment_question');
if (!function_exists('wpqa_comment_question')) :
	function wpqa_comment_question($comment_id) {
		$get_comment = get_comment($comment_id);
		$post_id = $get_comment->comment_post_ID;
		$get_post = get_post($post_id);
		$comment_user_id = $get_comment->user_id;
		if ($get_post->post_type == "question") {
			add_comment_meta($comment_id,'comment_type',"question");
			add_comment_meta($comment_id,'comment_vote',0);
			$question_user_id = get_post_meta($post_id,"user_id",true);
			if ($question_user_id != "" && $question_user_id > 0) {
				add_comment_meta($comment_id,"answer_question_user","answer_question_user");
			}
			
			if (isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) :
				require_once(ABSPATH . 'wp-admin/includes/image.php');
				require_once(ABSPATH . 'wp-admin/includes/file.php');
				$comment_attachment = wp_handle_upload($_FILES['attachment'],array('test_form'=>false),current_time('mysql'));
				if (isset($comment_attachment['error'])) :
					wp_die('Attachment Error: ' . $comment_attachment['error']);
					exit;
				endif;
				$comment_attachment_data = array(
					'post_mime_type' => $comment_attachment['type'],
					'post_title'	 => preg_replace('/\.[^.]+$/','',basename($comment_attachment['file'])),
					'post_content'   => '',
					'post_status'	=> 'inherit',
					'post_author'	=> ($comment_user_id != "" || $comment_user_id != 0?$comment_user_id:0)
				);
				$comment_attachment_id = wp_insert_attachment($comment_attachment_data,$comment_attachment['file'],$post_id);
				$comment_attachment_metadata = wp_generate_attachment_metadata($comment_attachment_id,$comment_attachment['file']);
				wp_update_attachment_metadata($comment_attachment_id, $comment_attachment_metadata);
				add_comment_meta($comment_id,'added_file',$comment_attachment_id);
			endif;
			
			if (isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name'])) :
				require_once(ABSPATH . 'wp-admin/includes/image.php');
				require_once(ABSPATH . 'wp-admin/includes/file.php');
				$comment_featured_image = wp_handle_upload($_FILES['featured_image'],array('test_form'=>false),current_time('mysql'));
				if (isset($comment_featured_image['error'])) :
					wp_die(esc_html__("Attachment Error:","wpqa").' '.$comment_featured_image['error']);
					exit;
				endif;
				$get_current_user_id = get_current_user_id();
				$comment_featured_image_data = array(
					'post_mime_type' => $comment_featured_image['type'],
					'post_title'	 => preg_replace('/\.[^.]+$/','',basename($comment_featured_image['file'])),
					'post_content'   => '',
					'post_status'	 => 'inherit',
					'post_author'	 => ($get_current_user_id > 0?$get_current_user_id:"")
				);
				$comment_featured_image_id = wp_insert_attachment($comment_featured_image_data,$comment_featured_image['file'],$get_comment->comment_post_ID);
				$comment_featured_image_metadata = wp_generate_attachment_metadata($comment_featured_image_id,$comment_featured_image['file']);
				wp_update_attachment_metadata($comment_featured_image_id, $comment_featured_image_metadata);
				add_comment_meta($comment_id,'featured_image',$comment_featured_image_id);
			endif;

			$answer_video = wpqa_options("answer_video");
			if ($answer_video == "on") {
				if (isset($_POST['video_answer_description']))
					update_comment_meta($comment_id,'video_answer_description',esc_html($_POST['video_answer_description']));
				
				if (isset($_POST['video_answer_type']))
					update_comment_meta($comment_id,'video_answer_type',esc_html($_POST['video_answer_type']));
					
				if (isset($_POST['video_answer_id']))
					update_comment_meta($comment_id,'video_answer_id',esc_html($_POST['video_answer_id']));
			}
			
			if ($get_comment->comment_approved == 1) {
				if ($comment_user_id > 0) {
					wpqa_notifications_activities($comment_user_id,"","",$post_id,$comment_id,"add_answer","activities","","answer","","answer");
				}
				update_comment_meta($comment_id,'comment_approved_before',"yes");
				update_post_meta($post_id,"comment_count",$get_post->comment_count);
				wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Your answer was successfully added.","wpqa").'</p></div>','wpqa_session');
				wpqa_notifications_add_answer($get_comment,$get_post);
			}else {
				wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Your answer was successfully added, It's under review.","wpqa").'</p></div>','wpqa_session');
				if ($comment_user_id > 0) {
					wpqa_notifications_activities($comment_user_id,"","","","","approved_answer","activities","","answer","","answer");
				}
			}
		}else {
			if ($get_comment->comment_approved == 1) {
				if ($comment_user_id > 0) {
					wpqa_notifications_activities($comment_user_id,"","",$post_id,$comment_id,"add_comment","activities");
				}
			}else {
				if ($comment_user_id > 0) {
					wpqa_notifications_activities($comment_user_id,"","","","","approved_comment","activities");
				}
			}
		}
	}
endif;
/* Approve comment callback */
add_action('transition_comment_status','wpqa_approve_comment_callback',10,3);
if (!function_exists('wpqa_approve_comment_callback')) :
	function wpqa_approve_comment_callback($new_status,$old_status,$comment) {
		if ($old_status != $new_status) {
			$get_post = get_post($comment->comment_post_ID);
			if ($new_status == 'approved') {
				$comment_approved_before = get_comment_meta($comment->comment_ID,'comment_approved_before',true);
				if ($comment_approved_before != "yes") {
					if ($get_post->post_type == "question") {
						$comment_user_id = $comment->user_id;
						if ($comment_user_id > 0) {
							wpqa_notifications_activities($comment_user_id,"","",$comment->comment_post_ID,$comment->comment_ID,"approved_answer","notifications","","answer");
						}
						wpqa_notifications_add_answer($comment,$get_post);
					}else {
						if ($comment->user_id > 0) {
							wpqa_notifications_activities($comment->user_id,"","",$comment->comment_post_ID,$comment->comment_ID,"approved_comment","notifications");
						}
					}
				}
				update_comment_meta($comment->comment_ID,'comment_approved_before',"yes");
			}
		}
	}
endif;
/* Before delete comment */
add_action('delete_comment','wpqa_before_delete_comment');
if (!function_exists('wpqa_before_delete_comment')) :
	function wpqa_before_delete_comment($comment_id) {
		$remove_best_answer_stats = wpqa_options("remove_best_answer_stats");
		$active_points = wpqa_options("active_points");
		if ($remove_best_answer_stats == "on") {
			$best_answer_comment = get_comment_meta($comment_id,"best_answer_comment",true);
			$get_comment = get_comment($comment_id);
			$user_id = $get_comment->user_id;
			if ($user_id > 0 && $active_points == "on") {
				$point_best_answer = (int)wpqa_options("point_best_answer");
				$point_best_answer = ($point_best_answer > 0?$point_best_answer:5);
				$point_add_comment = (int)wpqa_options("point_add_comment");
				$point_add_comment = ($point_add_comment > 0?$point_add_comment:2);
				wpqa_add_points($user_id,$point_add_comment,"-","delete_answer");
			}
			
			if (isset($best_answer_comment) && isset($comment_id) && $best_answer_comment == "best_answer_comment") {
				if ($user_id > 0 && $active_points == "on") {
					wpqa_add_points($user_id,$point_best_answer,"-","delete_best_answer");
				}
				
				$point_back_option = wpqa_options("point_back");
				$user_author = get_post_field('post_author',$get_comment->comment_post_ID);
				if ($point_back_option == "on" && $active_points == "on" && $user_id != $user_author) {
					$point_back_number = (int)wpqa_options("point_back_number");
					$point_back = get_post_meta($post_id,"point_back",true);
					$what_point = (int)get_post_meta($post_id,"what_point",true);
					
					if ($point_back_number > 0) {
						$what_point = $point_back_number;
					}
					
					if ($point_back == "yes" && $user_author > 0) {
						$what_point = (int)($what_point > 0?$what_point:wpqa_options("question_points"));
						wpqa_add_points($user_author,$what_point,"-","point_removed");
					}
					
					if ($user_author > 0) {
						wpqa_notifications_activities($user_author,"","","","","point_removed","notifications");
					}
				}
			}
		}
	}
endif;
/* Edit comment */
if (!function_exists('wpqa_edit_comment')) :
	function wpqa_edit_comment($edit) {
		if ($_POST && isset($edit) && $edit == "edit_comment") :
			$return = wpqa_process_edit_comments();
			if (is_wp_error($return)) :
	   			return '<div class="wpqa_error">'.$return->get_error_message().'</div>';
	   		else :
   				$comment_approved = wpqa_options("comment_approved");
   				if ($comment_approved == "on" || is_super_admin(get_current_user_id())) {
   					wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Has been edited successfully.","wpqa").'</p></div>','wpqa_session');
   				}else {
   					wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Your answer was successfully added, It's under review.","wpqa").'</p></div>','wpqa_session');
   				}
	   			wp_redirect(get_comment_link($return));
	   			exit;
	   		endif;
		endif;
	}
endif;
add_filter('wpqa_edit_comment','wpqa_edit_comment');
/* Process edit comments */
if (!function_exists('wpqa_process_edit_comments')) :
	function wpqa_process_edit_comments() {
		global $edit_comment_post;
		set_time_limit(0);
		$errors = new WP_Error();
		$edit_comment_post = array();
		
		$fields = array(
			'wpqa_comment_nonce','comment_id','comment','video_answer_description','video_answer_type','video_answer_id'
		);
		
		foreach ($fields as $field) :
			if (isset($_POST[$field])) $edit_comment_post[$field] = $_POST[$field]; else $edit_comment_post[$field] = '';
		endforeach;

		/* Validate Required Fields */

		if (!isset($edit_comment_post['wpqa_comment_nonce']) || !wp_verify_nonce($edit_comment_post['wpqa_comment_nonce'],'wpqa_comment_nonce')) {
			$errors->add('required-1','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are an error, Please try again.","wpqa"));
		}
		
		$comment_id = (isset($edit_comment_post['comment_id'])?(int)$edit_comment_post['comment_id']:0);
		$comment_content = (isset($edit_comment_post["comment"])?wpqa_kses_stip_wpautop($edit_comment_post["comment"]):"");
		
		$get_comment = get_comment($comment_id);
		$comment_user_id = $get_comment->user_id;
		$get_current_user_id = get_current_user_id();
		$get_post = array();
		if (isset($comment_id) && $comment_id != 0 && is_object($get_comment)) {
			$get_post = get_post($get_comment->comment_post_ID);
		}
		
		if (isset($comment_id) && $comment_id != 0 && $get_post) {
			$can_edit_comment = wpqa_options("can_edit_comment");
			if ($can_edit_comment != "on" && !is_super_admin($get_current_user_id) && $comment_user_id != $get_current_user_id) {
				$errors->add('required-2','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("You are not allowed to edit this comment.","wpqa"));
			}
		}else {
			$errors->add('required-3','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("Sorry no comment has you select or not found.","wpqa"));
		}
		
		if (empty($comment_content)) $errors->add('required-4','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (comment).","wpqa"));
		//if (isset($comment_content) && $comment_content == $get_comment->comment_content) $errors->add('required-5','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("You don't modify anything this is the same comment!.","wpqa"));

		$answer_video = wpqa_options("answer_video");
		if ($answer_video == "on" && isset($edit_comment_post['video_answer_description']) && $edit_comment_post['video_answer_description'] == "on" && empty($edit_comment_post['video_answer_id'])) {
			$errors->add('required-5','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (Video ID).","wpqa"));
		}

		$attachment_answer = wpqa_options("attachment_answer");
		$featured_image_answer = wpqa_options("featured_image_answer");
		if (($attachment_answer == "on" && isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) || ($featured_image_answer == "on" && isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name']))) {
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			require_once(ABSPATH . 'wp-admin/includes/file.php');
		}

		if ($attachment_answer == "on" && isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) {
			$comment_attachment = wp_handle_upload($_FILES['attachment'],array('test_form'=>false),current_time('mysql'));
			if (isset($comment_attachment['error'])) :
				$errors->add('Attachment Error: ' . $comment_attachment['error']);
			endif;
		}

		if ($featured_image_answer == "on" && isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name'])) :
			$comment_featured_image = '';
			$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/gif","image/tiff","image/ico");
			if (!in_array($_FILES['featured_image']['type'],$types)) :
				$errors->add('upload-error',esc_html__("Attachment Error, Please upload image only.","wpqa"));
			endif;
			
			$comment_featured_image = wp_handle_upload($_FILES['featured_image'],array('test_form'=>false),current_time('mysql'));
			
			if (isset($comment_featured_image['error'])) :
				$errors->add('upload-error',esc_html__("Attachment Error: ","wpqa") . $comment_featured_image['error']);
			endif;
			
		endif;
		
		/* Edit comment */
		$comment_approved = wpqa_options("comment_approved");
		$data['comment_ID'] = $comment_id;
		if ($comment_approved == "on" || is_super_admin($get_current_user_id)) {
			$data['comment_approved'] = 1;
		}else {
			$data['comment_approved'] = 0;
		}
		$data['comment_content']  = $comment_content;
		
		wp_update_comment($data);
		
		update_comment_meta($comment_id,"edit_comment","edited");

		if ($answer_video == "on") {
			if ($edit_comment_post['video_answer_description'] && $edit_comment_post['video_answer_description'] != "") {
				update_comment_meta($comment_id,'video_answer_description',esc_html($edit_comment_post['video_answer_description']));
			}else {
				delete_comment_meta($comment_id,'video_answer_description');
			}
			
			if ($edit_comment_post['video_answer_type']) {
				update_comment_meta($comment_id,'video_answer_type',esc_html($edit_comment_post['video_answer_type']));
			}
				
			if ($edit_comment_post['video_answer_id']) {
				update_comment_meta($comment_id,'video_answer_id',esc_html($edit_comment_post['video_answer_id']));
			}
		}

		/* Attachment */

		if (isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) :
			$comment_attachment_data = array(
				'post_mime_type' => $comment_attachment['type'],
				'post_title'	 => preg_replace('/\.[^.]+$/','',basename($comment_attachment['file'])),
				'post_content'   => '',
				'post_status'	=> 'inherit',
				'post_author'	=> ($comment_user_id != "" || $comment_user_id != 0?$comment_user_id:0)
			);
			$comment_attachment_id = wp_insert_attachment($comment_attachment_data,$comment_attachment['file'],$post_id);
			$comment_attachment_metadata = wp_generate_attachment_metadata($comment_attachment_id,$comment_attachment['file']);
			wp_update_attachment_metadata($comment_attachment_id, $comment_attachment_metadata);
			update_comment_meta($comment_id,'added_file',$comment_attachment_id);
		endif;

		/* Featured image */
		
		if ($featured_image_answer == "on" && $comment_featured_image) {
			$comment_featured_image_data = array(
				'post_mime_type' => $comment_featured_image['type'],
				'post_title'	 => preg_replace('/\.[^.]+$/','',basename($comment_featured_image['file'])),
				'post_content'   => '',
				'post_status'	 => 'inherit',
				'post_author'	 => ($comment_user_id > 0?$comment_user_id:0)
			);
			$comment_featured_image_id = wp_insert_attachment($comment_featured_image_data,$comment_featured_image['file'],$get_comment->comment_post_ID);
			$comment_featured_image_metadata = wp_generate_attachment_metadata($comment_featured_image_id,$comment_featured_image['file']);
			wp_update_attachment_metadata($comment_featured_image_id, $comment_featured_image_metadata);
			update_comment_meta($comment_id,'featured_image',$comment_featured_image_id);
		}
	
		do_action('wpqa_edit_comments',$comment_id);
		
		/* Successful */
		return $comment_id;
	}
endif;
/* Comment columns */
if (!function_exists('wpqa_comment_columns')) :
	function wpqa_comment_columns ($columns) {
		return array_merge( $columns, array(
			'answers'     => esc_html__('Answer/Comment','wpqa'),
			'best_answer' => esc_html__('Best answer?','wpqa')
		));
	}
endif;
add_filter('manage_edit-comments_columns','wpqa_comment_columns');
if (!function_exists('wpqa_comment_column')) :
	function wpqa_comment_column ($column,$comment_ID) {
		switch ( $column ) {
			case 'answers':
				$comment_type = get_comment_meta($comment_ID,"comment_type",true);
				if (isset($comment_type) && $comment_type == "question") {
					echo apply_filters("wpqa_answer_language",esc_html__('Answer','wpqa'));
				}else {
					echo esc_html__('Comment','wpqa');
				}
			break;
			case 'best_answer':
				$best_answer_comment = get_comment_meta($comment_ID,"best_answer_comment",true);
				if (isset($best_answer_comment) && $best_answer_comment == "best_answer_comment") {
					echo esc_html__('Best answer','wpqa');
				}else {
					echo '<span aria-hidden="true">-</span><span class="screen-reader-text">'.esc_html__("Not best answer","wpqa").'</span>';
				}
			break;
		}
	}
endif;
add_filter('manage_comments_custom_column','wpqa_comment_column',10,2);
/* Edit comment admin */
add_action ('edit_comment','wpqa_edit_comment_admin');
if (!function_exists('wpqa_edit_comment_admin')) :
	function wpqa_edit_comment_admin($comment_id) {
		if (isset($_POST["delete_reason"]) && $_POST["delete_reason"] != "") {
			update_comment_meta($comment_id,"delete_reason",esc_attr($_POST["delete_reason"]));
		}
	}
endif;
/* Meta boxes comment */
add_action('add_meta_boxes_comment','wpqa_meta_boxes_comment');
if (!function_exists('wpqa_meta_boxes_comment')) :
	function wpqa_meta_boxes_comment($comment) {
		$answer_question = get_post_type($comment->comment_post_ID);
		if ($answer_question == "question" || $answer_question == "post") {?>
			<div class="stuffbox">
				<div class="inside">
					<fieldset>
						<legend class="edit-comment-author"><?php esc_html_e('Reason if you need to remove it.','wpqa')?></legend>
						<table class="form-table editcomment">
							<tbody>
								<tr>
									<td class="first" style="width: 10px;"><label for="delete_reason"><?php esc_html_e('Reason:','wpqa')?></label></td>
									<td>
										<input id="delete_reason" name="delete_reason" class="code" type="text" value="<?php echo esc_attr(get_comment_meta($comment->comment_ID,"delete_reason",true));?>" style="width: 98%;">
									</td>
								</tr>
							</tbody>
						</table>
						<br>
						<div class="submitbox"><a href="#" class="submitdelete delete-comment-answer" data-div-id="delete_reason" data-id="<?php echo esc_attr($comment->comment_ID);?>" data-action="wpqa_delete_comment_answer" data-nonce="<?php echo wp_create_nonce("wpqa_delete_nonce")?>" data-location="<?php echo esc_url(($answer_question == "question"?admin_url( 'edit-comments.php?comment_status=all&amp;answers=1'):admin_url('edit-comments.php?comment_status=all&amp;comments=1')))?>"><?php esc_html_e('Delete?','wpqa')?></a></div>
					</fieldset>
				</div>
			</div>
		<?php }
	}
endif;
/* Comments exclude */
add_action('current_screen','wpqa_comments_exclude',10,2);
if (!function_exists('wpqa_comments_exclude')) :
	function wpqa_comments_exclude($screen) {
		if ($screen->id != 'edit-comments')
			return;
		if (isset($_GET['answers'])) {
			add_action('pre_get_comments','wpqa_list_answers',10,1);
		}else if (isset($_GET['comments'])) {
			add_action('pre_get_comments','wpqa_list_comments',10,1);
		}
		add_filter('comment_status_links','wpqa_new_answers_page_link');
	}
endif;
if (!function_exists('wpqa_list_comments')) :
	function wpqa_list_comments($clauses) {
		$clauses->query_vars['post_type'] = "post";
	}
endif;
if (!function_exists('wpqa_list_answers')) :
	function wpqa_list_answers($clauses) {
		$clauses->query_vars['post_type'] = "question";
	}
endif;
if (!function_exists('wpqa_new_answers_page_link')) :
	function wpqa_new_answers_page_link($status_links) {
		$count = wpqa_all_comments_of_post_type("question");
		$count_posts = wpqa_all_comments_of_post_type("post");
		$status_links['comments'] = '<a href="edit-comments.php?comment_status=all&amp;comments=1"'.(isset($_GET['comments'])?' class="current"':'').'>'.esc_html__('Comments','wpqa').' ('.$count_posts.')</a>';
		$status_links['answers'] = '<a href="edit-comments.php?comment_status=all&amp;answers=1"'.(isset($_GET['answers'])?' class="current"':'').'>'.esc_html__('Answers','wpqa').' ('.$count.')</a>';
		return $status_links;
	}
endif;
/* Comment data */
add_filter("comment_form_field_comment","wpqa_comment_data");
if (!function_exists('wpqa_comment_data')) :
	function wpqa_comment_data($comment) {
		return apply_filters('wpqa_comment_fields',false,"add").apply_filters('wpqa_comment_extra_fields',false).$comment;
	}
endif;
/* Comment attachment */
add_filter("wpqa_comment_fields","wpqa_comment_attachment",1,2);
add_filter("wpqa_edit_comment_fields","wpqa_comment_attachment",1,4);
if (!function_exists('wpqa_comment_attachment')) :
	function wpqa_comment_attachment($out,$type,$comment_id = 0,$get_post = object) {
		if (isset($get_post) && isset($get_post->ID)) {
			$post = $get_post;
		}
		if (is_singular("question") || (isset($post) && isset($post->post_type) && 'question' == $post->post_type)) {
			$attachment_answer = wpqa_options("attachment_answer");
			if ($attachment_answer == "on") {
				if ($type == "edit") {
					$added_file = get_comment_meta($comment_id,"added_file",true);
					if ($added_file != "") {
						$out .= '<ul class="wpqa-delete-attachment"><li><a target="_blank" href="'.wp_get_attachment_url($added_file).'"><i class="icon-attach"></i>'.esc_html__('Attachment','wpqa').'</a> <a class="wpqa-remove-image" data-name="added_file" data-type="comment_meta" data-id="'.$comment_id.'" data-nonce="'.wp_create_nonce("wpqa_remove_image").'" href="#"><i class="icon-trash"></i>'.esc_html__('Delete','wpqa').'</a><div class="loader_2 loader_4"></div></li></ul>';
					}
				}
			    $out .= '<div class="wpqa_form">
			    	<label for="attachment">'.esc_html__('Attachment','wpqa').'</label>
				    <div class="fileinputs">
				    	<input type="file" name="attachment" id="attachment">
				    	<div class="fakefile">
				    		<button type="button">'.esc_html__('Select file','wpqa').'</button>
				    		<span>'.esc_html__('Browse','wpqa').'</span>
				    	</div>
				    	<i class="icon-camera"></i>
				    </div>
			    </div>';
			}
			
			$featured_image_answer = wpqa_options("featured_image_answer");
		    if ($featured_image_answer == "on") {
		    	if ($type == "edit") {
					$featured_image = get_comment_meta($comment_id,"featured_image",true);
					if ($featured_image != "") {
						$out .= '<div class="clearfix"></div>
						<div class="wpqa-delete-image">
							<span class="wpqa-delete-image-span">'.wpqa_get_aq_resize_img(250,250,"",$featured_image,"no","").'</span>
							<div class="clearfix"></div>
							<div class="button-default wpqa-remove-image" data-name="featured_image" data-type="comment_meta" data-id="'.$comment_id.'" data-nonce="'.wp_create_nonce("wpqa_remove_image").'">'.esc_html__("Delete","wpqa").'</div>
							<div class="loader_2 loader_4"></div>
						</div>';
					}
				}
		    	$out .= '<div class="wpqa_form">
			        <label for="featured_image">'.esc_html__('Featured image','wpqa').'</label>
			        <div class="fileinputs">
			        	<input type="file" name="featured_image" id="featured_image">
			        	<div class="fakefile">
			        		<button type="button">'.esc_html__('Select file','wpqa').'</button>
			        		<span>'.esc_html__('Browse','wpqa').'</span>
			        	</div>
			        	<i class="icon-camera"></i>
			        </div>
		        </div>';
		    }
		}
		if ($comment_id > 0) {
			return $out;
		}else {
			echo ($out);
		}
	}
endif;
/* Comment video */
add_filter("wpqa_comment_fields","wpqa_comment_video",2,4);
add_filter("wpqa_edit_comment_fields","wpqa_comment_video",2,4);
if (!function_exists('wpqa_comment_video')) :
	function wpqa_comment_video($out,$type,$comment_id = 0,$get_post = object) {
		if (isset($get_post) && isset($get_post->ID)) {
			$post = $get_post;
		}else {
			global $post;
		}

		$posted_video = array();
		
		$fields = array(
			'video_answer_description','video_answer_type','video_answer_id'
		);
		
		foreach ($fields as $field) :
			if (isset($_POST[$field])) $posted_video[$field] = $_POST[$field]; else $posted_video[$field] = '';
		endforeach;

		$answer_video = wpqa_options("answer_video");
		if (isset($post) && isset($post->post_type) && 'question' == $post->post_type && $answer_video == "on") {
			if ($type == "edit") {
				$video_answer_description = get_comment_meta($comment_id,"video_answer_description",true);
				$video_answer_type = get_comment_meta($comment_id,"video_answer_type",true);
				$video_answer_id = get_comment_meta($comment_id,"video_answer_id",true);
			}
			
			$out .= '<div class="wpqa_form">
				<p class="wpqa_checkbox_p">
					<label for="video_answer_description">
						<span class="wpqa_checkbox"><input type="checkbox" id="video_answer_description" class="video_answer_description_input" name="video_answer_description" value="on"'.($type == "add" && isset($posted_video['video_answer_description']) && $posted_video['video_answer_description'] == "on"?" checked='checked'":($type == "edit" && ((isset($posted_video['video_answer_description']) && $posted_video['video_answer_description'] == "on") || (empty($posted_video['video_answer_description']) && $video_answer_description == "on"))?" checked='checked'":"")).'></span>
						<span class="wpqa_checkbox_span">'.esc_html__("Add a video to describe the problem better.","wpqa").'</span>
					</label>
				</p>

				<div class="video_answer_description wpqa_hide"'.($type == "add" && isset($posted_video['video_answer_description']) && $posted_video['video_answer_description'] == "on"?" style='display:block;'":($type == "edit" && ((isset($posted_video['video_answer_description']) && $posted_video['video_answer_description'] == "on") || $video_answer_description == "on")?" style='display:block;'":"")).'>
					<p>
						<label for="video_answer_type">'.esc_html__("Video type","wpqa").'</label>
						<span class="styled-select">
							<select id="video_answer_type" name="video_answer_type">
								<option value="youtube"'.($type == "add" && isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "youtube"?' selected="selected"':($type == "edit"?((isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "youtube") || (isset($video_answer_type) && $video_answer_type == "youtube")?' selected="selected"':''):'')).'>Youtube</option>
								<option value="vimeo"'.($type == "add" && isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "vimeo"?' selected="selected"':($type == "edit"?((isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "vimeo") || (isset($video_answer_type) && $video_answer_type == "vimeo")?' selected="selected"':''):'')).'>Vimeo</option>
								<option value="daily"'.($type == "add" && isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "daily"?' selected="selected"':($type == "edit"?((isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "daily") || (isset($video_answer_type) && $video_answer_type == "daily")?' selected="selected"':''):'')).'>Dialymotion</option>
								<option value="facebook"'.($type == "add" && isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "facebook"?' selected="selected"':($type == "edit"?((isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "facebook") || (isset($video_answer_type) && $video_answer_type == "facebook")?' selected="selected"':''):'')).'>Facebook</option>
							</select>
						</span>
						<i class="icon-video"></i>
						<span class="form-description">'.esc_html__("Choose from here the video type.","wpqa").'</span>
					</p>
					
					<p>
						<label for="video_answer_id">'.esc_html__("Video ID","wpqa").'</label>
						<input name="video_answer_id" id="video_answer_id" class="video_answer_id" type="text" value="'.esc_attr($type == "add" && isset($posted_video['video_answer_id'])?$posted_video['video_answer_id']:($type == "edit"?(isset($posted_video['video_answer_id']) && $posted_video['video_answer_id'] != ""?$posted_video['video_answer_id']:$video_answer_id):"")).'">
						<i class="icon-play"></i>
						<span class="form-description">'.esc_html__("Put here the video id : https://www.youtube.com/watch?v=sdUUx5FdySs Ex: 'sdUUx5FdySs'.","wpqa").'</span>
					</p>
				</div>
			</div>';
		}
		if ($comment_id > 0) {
			return $out;
		}else {
			echo ($out);
		}
	}
endif;
/* Comment captcha */
add_filter("wpqa_comment_fields","wpqa_comment_captcha",3);
if (!function_exists('wpqa_comment_captcha')) :
	function wpqa_comment_captcha() {
		global $post;
		$the_captcha = wpqa_options("the_captcha_".(isset($post) && isset($post->post_type) && 'question' == $post->post_type?"answer":"comment"));
		echo '<div class="wpqa_error"></div>'.wpqa_add_captcha($the_captcha,wpqa_options("captcha_style"),wpqa_options("captcha_question"),wpqa_options("captcha_answer"),wpqa_options("show_captcha_answer"),(isset($post) && isset($post->post_type) && 'question' == $post->post_type?"answer":"comment"),rand(0000,9999),"comment");
	}
endif;
/* Pre comment approved */
add_filter('pre_comment_approved','wpqa_pre_comment_approved','99',2);
if (!function_exists('wpqa_pre_comment_approved')) :
	function wpqa_pre_comment_approved($approved,$commentdata) {
		if (!is_user_logged_in() && $approved != "spam") {
			$comment_unlogged = wpqa_options("comment_unlogged");
			$approved = ($comment_unlogged == "draft"?0:1);
		}
		return $approved;
	}
endif;
/* Notifications add answer */
if (!function_exists('wpqa_notifications_add_answer')) :
	function wpqa_notifications_add_answer($comment,$get_post) {
		$comment_id = $comment->comment_ID;
		$post_id = $comment->comment_post_ID;
		$comment_user_id = $comment->user_id;
		$post_author = $get_post->post_author;
		$post_title = $get_post->post_title;
		$remember_answer = get_post_meta($post_id,"remember_answer",true);
		$user_id_question = get_post_meta($post_id,"user_id",true);
		
		if ($remember_answer == "on" && $post_author != $comment_user_id) {
			if ($post_author > 0) {
				$the_mail = get_the_author_meta("user_email",$post_author);
				$the_author = get_the_author_meta("display_name",$post_author);
			}else {
				$anonymously_user     = get_post_meta($post_id,"anonymously_user",true);
				$anonymously_question = get_post_meta($post_id,"anonymously_question",true);
				if (($anonymously_question == "on" || $anonymously_question == 1) && $anonymously_user != "") {
					$the_mail = get_the_author_meta("user_email",$anonymously_user);
					$the_author = get_the_author_meta("display_name",$anonymously_user);
				}else {
					$the_mail = get_post_meta($post_id,'question_email',true);
					$the_author = get_post_meta($post_id,'question_username',true);
				}
			}
			
			$send_text = wpqa_send_email(wpqa_options("email_notified_answer"),"",$post_id,$comment_id);
			$last_message_email = wpqa_email_code($send_text);
			$email_title = wpqa_options("title_notified_answer");
			$email_title = ($email_title != ""?$email_title:esc_html__("Answer to your question","wpqa"));
			wpqa_sendEmail(wpqa_options("email_template"),get_bloginfo('name'),$the_mail,$the_author,$email_title,$last_message_email);
		}
		
		$question_follow = wpqa_options("question_follow");
		$following_questions = get_post_meta($post_id,"following_questions",true);
		if ($question_follow == "on" && isset($following_questions) && is_array($following_questions)) {
			$send_text = wpqa_send_email(wpqa_options("email_follow_question"),"",$post_id,$comment_id);
			$last_message_email = wpqa_email_code($send_text);
			$email_title = wpqa_options("title_follow_question");
			$email_title = ($email_title != ""?$email_title:esc_html__("Hi there","wpqa"));
			foreach ($following_questions as $user) {
				if ($user_id_question != $user) {
					$author_user_email = get_the_author_meta("user_email",$user);
					$author_display_name = get_the_author_meta("display_name",$user);
					if ($user > 0 && $comment_user_id != $user) {
						wpqa_notifications_activities($user,$comment_user_id,($comment_user_id == 0?$comment->comment_author:0),$post_id,$comment_id,"answer_question_follow","notifications","","answer");
						wpqa_sendEmail(wpqa_options("email_template"),get_bloginfo('name'),esc_html($author_user_email),esc_html($author_display_name),$email_title,$last_message_email);
					}
				}
			}
		}
		
		$active_points = wpqa_options("active_points");
		if ($comment_user_id != 0) {
			if ($comment_user_id != $post_author && $active_points == "on") {
				$point_add_comment = (int)wpqa_options("point_add_comment");
				$point_add_comment = ($point_add_comment > 0 ?$point_add_comment:2);
				wpqa_add_points($comment_user_id,$point_add_comment,"+","answer_question",$post_id,$comment_id);
			}
			
			$add_answer = get_user_meta($comment_user_id,"add_answer_all",true);
			$add_answer_m = get_user_meta($comment_user_id,"add_answer_m_".date_i18n('m_Y',current_time('timestamp')),true);
			$add_answer_d = get_user_meta($comment_user_id,"add_answer_d_".date_i18n('d_m_Y',current_time('timestamp')),true);
			if ($add_answer_d == "" || $add_answer_d == 0) {
				update_user_meta($comment_user_id,"add_answer_d_".date_i18n('d_m_Y',current_time('timestamp')),1);
			}else {
				update_user_meta($comment_user_id,"add_answer_d_".date_i18n('d_m_Y',current_time('timestamp')),$add_answer_d+1);
			}
			
			if ($add_answer_m == "" || $add_answer_m == 0) {
				update_user_meta($comment_user_id,"add_answer_m_".date_i18n('m_Y',current_time('timestamp')),1);
			}else {
				update_user_meta($comment_user_id,"add_answer_m_".date_i18n('m_Y',current_time('timestamp')),$add_answer_m+1);
			}
			
			if ($add_answer == "" || $add_answer == 0) {
				update_user_meta($comment_user_id,"add_answer_all",1);
			}else {
				update_user_meta($comment_user_id,"add_answer_all",$add_answer+1);
			}
		}
		
		$user_is_comment = get_post_meta($post_id,"user_is_comment",true);
		$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
		if (($post_author > 0 && $comment_user_id != $post_author) || ($anonymously_user > 0 && $comment_user_id != $anonymously_user)) {
			wpqa_notifications_activities(($post_author > 0?$post_author:$anonymously_user),$comment_user_id,($comment_user_id == 0?$comment->comment_author:0),$post_id,$comment_id,"answer_question","notifications","","answer");
		}
		if ($user_id_question != "") {
			if ($user_id_question != $comment_user_id) {
				wpqa_notifications_activities($user_id_question,$comment_user_id,($comment_user_id == 0?$comment->comment_author:0),$post_id,$comment_id,"answer_asked_question","notifications","","answer");
			}
			if ($user_is_comment != true && $user_id_question == $comment_user_id) {
				update_post_meta($post_id,"user_is_comment",true);
			}
		}
	}
endif;
/* Remove private answers from API */
add_filter('rest_prepare_comment','wpqa_remove_user_answers',10,3);
if (!function_exists('wpqa_remove_user_answers')) :
	function wpqa_remove_user_answers($data,$post,$request) {
		$_data = $data->data;
		$params = $request->get_params();
		if (!isset($params['id'])) {
			$answer_question_user    = get_comment_meta($_data['id'],"answer_question_user",true);
			$answer_question_private = get_comment_meta($_data['id'],"answer_question_private",true);
			if ($answer_question_user != "" || $answer_question_private != "") {
				unset($_data);
			}
		}
		$data->data = $_data;
		return $data;
	}
endif;
/* Action delete comment */
add_action('wpqa_init','wpqa_delete_comment');
if (!function_exists('wpqa_delete_comment')) :
	function wpqa_delete_comment() {
		if (isset($_GET['wpqa_delete_nonce']) && wp_verify_nonce($_GET['wpqa_delete_nonce'],'wpqa_delete_nonce') && !is_admin() && isset($_GET["delete_comment"]) && $_GET["delete_comment"] != "") {
			$comment_id  = (int)$_GET["delete_comment"];
			$get_comment = get_comment($comment_id);
			if (isset($get_comment) && $comment_id > 0 && isset($get_comment->comment_approved) && $get_comment->comment_approved == 1) {
				$comment_user_id    = $get_comment->user_id;
				$user_id            = get_current_user_id();
				$comment_type       = get_comment_meta($comment_id,"comment_type",true);
				$can_delete_comment = wpqa_options("can_delete_comment");
				$delete_comment     = wpqa_options("delete_comment");
				if (($comment_user_id > 0 && $comment_user_id == $user_id && $can_delete_comment == "on") || current_user_can('edit_comment',$comment_id) || is_super_admin($user_id)) {
					if ($user_id > 0) {
						wpqa_notifications_activities($user_id,"","","","","delete_".($comment_type == "question"?"answer":"comment"),"activities","",($comment_type == "question"?"answer":"comment"));
					}
					if ($comment_user_id > 0 && $user_id != $comment_user_id) {
						wpqa_notifications_activities($comment_user_id,"","","","","delete_".($comment_type == "question"?"answer":"comment"),"notifications","",($comment_type == "question"?"answer":"comment"));
					}
					wp_delete_comment($comment_id,($delete_comment == "trash"?false:true));
					wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Has been deleted successfully.","wpqa").'</p></div>','wpqa_session');
					$protocol    = is_ssl() ? 'https' : 'http';
					$redirect_to = esc_url(remove_query_arg(array('delete_comment','wpqa_delete_nonce'),wp_unslash( $protocol.'://'.wpqa_server('HTTP_HOST').wpqa_server('REQUEST_URI'))));
					$redirect_to = (isset($_GET["page"]) && esc_attr($_GET["page"]) != ""?esc_attr($_GET["page"]):$redirect_to);
					wp_redirect($redirect_to);
					exit;
				}
			}
		}
	}
endif;
/* Remove the HTML codes */
add_filter('comment_text','wpqa_remove_html_codes');
if (!function_exists('wpqa_remove_html_codes')) :
	function wpqa_remove_html_codes($comment_text) {
		$comment_text = wpqa_kses_stip($comment_text);
		return $comment_text;
	}
endif;
/* Comment limit */
if (!function_exists('wpqa_comment_limit')) :
	function wpqa_comment_limit() {
		$comment_limit = (int)$_POST["comment_limit"];
		$comment_min_limit = (int)$_POST["comment_min_limit"];
		$comment_text = $_POST["comment_text"];
		$comment_text = str_replace('<p>','',$comment_text);
		$comment_text = str_replace('</p>','',$comment_text);
		$comment_text = str_replace('<br>','',$comment_text);
		$comment_text = str_replace('<br data-mce-bogus="1">','',$comment_text);
		if ($comment_min_limit > 0 && strlen($comment_text) < $comment_min_limit) {
			echo "wpqa_min_error";
		}
		if ($comment_limit > 0 && strlen($comment_text) > $comment_limit) {
			echo "wpqa_error";
		}
		die();
	}
endif;
add_action("wp_ajax_nopriv_wpqa_comment_limit","wpqa_comment_limit");
add_action("wp_ajax_wpqa_comment_limit","wpqa_comment_limit");
/* Private answer */
if (!function_exists('wpqa_private_answer')) :
	function wpqa_private_answer($comment_id,$first_user,$second_user) {
		$yes_private_answer = 0;
		$private_answer = wpqa_options("private_answer");
		$get_private_answer = get_comment_meta($comment_id,'private_answer',true);
		
		if ($private_answer == "on") {
			if (($get_private_answer == 1 && isset($first_user) && $first_user > 0 && $first_user == $second_user) || $get_private_answer != 1) {
				$yes_private_answer = 1;
			}
		}else {
			$yes_private_answer = 1;
		}
		
		if (is_super_admin($second_user)) {
			$yes_private_answer = 1;
		}
		return $yes_private_answer;
	}
endif;
/* Add loader after submit button */
add_filter("comment_form_submit_button","wpqa_comment_form_submit_button");
if (!function_exists('wpqa_comment_form_submit_button')) :
	function wpqa_comment_form_submit_button($submit_field) {
		return $submit_field.'<span class="clearfix"></span><span class="load_span"><span class="loader_2"></span></span>';
	}
endif;?>