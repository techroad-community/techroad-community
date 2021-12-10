<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Notifications add question */
if (!function_exists('wpqa_notifications_add_question')) :
	function wpqa_notifications_add_question($post_id,$question_username,$user_id,$not_user,$anonymously_user,$get_current_user_id) {
		global $wpdb;
		$send_email_new_question = wpqa_options("send_email_new_question");
		$the_author = 0;
		if ($not_user == 0) {
			$the_author = $question_username;
		}
		if ($user_id == "") {
			$private_question = get_post_meta($post_id,"private_question",true);
			$send_text = wpqa_send_email(wpqa_options("email_new_questions"),"",$post_id);
			$last_message_email = wpqa_email_code($send_text);
			$email_title = wpqa_options("title_new_questions");
			$email_title = ($email_title != ""?$email_title:esc_html__("New question","wpqa"));
			if ($send_email_new_question == "on") {
				$logo_email_template = wpqa_options("logo_email_template");
				$user_group   = wpqa_options("send_email_question_groups");
				$capabilities = $wpdb->get_blog_prefix(1) . 'capabilities';
				$query = $wpdb->prepare("SELECT DISTINCT SQL_CALC_FOUND_ROWS $wpdb->users.ID,$wpdb->users.user_email,$wpdb->users.display_name,$wpdb->usermeta.meta_key,$wpdb->usermeta.meta_value FROM $wpdb->users INNER JOIN $wpdb->usermeta ON ($wpdb->users.ID = $wpdb->usermeta.user_id) WHERE %s=1 AND $wpdb->usermeta.meta_key = 'received_email' AND ($wpdb->usermeta.meta_value = '1' OR $wpdb->usermeta.meta_value = 'on')",1);
				$users = $wpdb->get_results($query);
				if (isset($users) && is_array($users) && !empty($users)) {
					foreach ($users as $key => $value) {
						$another_user_id = $value->ID;
						if (is_super_admin($another_user_id) && ($private_question == "on" || $private_question == 1) && (($another_user_id != $anonymously_user && $anonymously_user > 0) || ($another_user_id != $not_user && $not_user > 0))) {
							wpqa_notifications_activities($another_user_id,$not_user,$the_author,$post_id,"","add_question","notifications","","question");
						}else {
							$get_capabilities = get_user_meta($another_user_id,$capabilities,true);
							if ($not_user != $another_user_id && (empty($user_group) || (is_array($user_group) && is_array($get_capabilities) && in_array(key($get_capabilities),$user_group)))) {
								$yes_private = wpqa_private($post_id,$not_user,$another_user_id);
								if ($yes_private == 1) {
									if ($another_user_id > 0 && $not_user != $another_user_id) {
										wpqa_notifications_activities($another_user_id,$not_user,$the_author,$post_id,"","add_question","notifications","","question");
										wpqa_sendEmail(wpqa_options("email_template"),get_bloginfo('name'),esc_html($value->user_email),esc_html($value->display_name),$email_title,$last_message_email);
									}
								}
							}
						}
					}
				}
			}else if ($private_question == "on" || $private_question == 1) {
				$users = get_users(array("role" => "administrator","blog_id" => 1,"fields" => array("ID","user_email")));
				if (isset($users) && is_array($users) && !empty($users)) {
					foreach ($users as $key => $value) {
						$another_user_id = $value->ID;
						if ($another_user_id > 0 && $not_user != $another_user_id && $anonymously_user != $another_user_id) {
							wpqa_notifications_activities($another_user_id,$not_user,$the_author,$post_id,"","add_question","notifications","","question");
							wpqa_sendEmail(wpqa_options("email_template"),get_bloginfo('name'),esc_html($value->user_email),esc_html($value->display_name),$email_title,$last_message_email);
						}
					}
				}
			}
		}
	}
endif;
/* Private question */
if (!function_exists('wpqa_private')) :
	function wpqa_private($post_id,$first_user,$second_user) {
		$get_private_question = get_post_meta($post_id,"private_question",true);
		$user_id = get_post_meta($post_id,"user_id",true);
		$user_is_comment = get_post_meta($post_id,"user_is_comment",true);
		$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
		$question_category = wp_get_post_terms($post_id,'question-category',array("fields" => "all"));
		
		$yes_private = 0;
		if (is_array($question_category) && isset($question_category[0])) {
			$wpqa_private = get_term_meta($question_category[0]->term_id,prefix_terms."private",true);
		}
		if (is_array($question_category) && isset($question_category[0])) {
			if (isset($question_category[0]) && $wpqa_private == "on") {
				if (isset($first_user) && $first_user > 0 && $first_user == $second_user) {
					$yes_private = 1;
				}
			}else if (isset($question_category[0]) && $wpqa_private == 0 && $question_category[0]->parent == 0) {
				$yes_private = 1;
			}
			
			if (isset($question_category[0]) && $question_category[0]->parent > 0) {
				$wpqa_private_parent = get_term_meta($question_category[0]->parent,prefix_terms."private",true);
				if ($wpqa_private_parent == "on" && isset($first_user) && $first_user > 0 && $first_user == $second_user) {
					$yes_private = 1;
				}else if (isset($question_category[0]) && $wpqa_private_parent == "on" && !isset($first_user)) {
					$yes_private = 0;
				}else if (isset($question_category[0]) && $wpqa_private_parent == 0) {
					$yes_private = 1;
				}
			}
		}else {
			$yes_private = 1;
		}
		
		if ($get_private_question == 1 || $get_private_question == "on" || ($user_id != "" && $user_is_comment != true)) {
			$yes_private = 0;
			if ((isset($first_user) && $first_user > 0 && $first_user == $second_user) || ($user_id > 0 && $user_id == $second_user) || ($anonymously_user > 0 && $anonymously_user == $second_user)) {
				$yes_private = 1;
			}
		}
		
		if (is_super_admin($second_user)) {
			$yes_private = 1;
		}
		return $yes_private;
	}
endif;
/* Count paid question */
if (!function_exists('wpqa_count_paid_question')) :
	function wpqa_count_paid_question( $user_id = "", $post_status = "publish" ) {
		global $wpdb;
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts INNER JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id ) WHERE $wpdb->posts.post_type = 'question' AND $wpdb->posts.post_status = '$post_status' AND $wpdb->posts.post_author = $user_id AND $wpdb->postmeta.meta_key = '_paid_question' AND $wpdb->postmeta.meta_value = 'paid'" );
	  	return $count;
	}
endif;
/* Count asked question */
if (!function_exists('wpqa_count_asked_question')) :
	function wpqa_count_asked_question( $user_id = "", $asked = "=", $post_status = "publish" ) {
		global $wpdb;
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts INNER JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id ) WHERE $wpdb->posts.post_type = 'question' AND $wpdb->posts.post_status = '$post_status' AND $wpdb->posts.comment_count ".$asked." 0 AND $wpdb->postmeta.meta_key = 'user_id' AND $wpdb->postmeta.meta_value = $user_id" );
	  	return $count;
	}
endif;
/* Admin reports */
if (!function_exists('wpqa_add_admin_page_reports')) :
	function wpqa_add_admin_page_reports() {
		$active_reports = wpqa_options("active_reports");
		if ($active_reports == "on") {
			/* Count new reports */
			$wpqa_option_array = get_option("report_option_array");
			if (is_array($wpqa_option_array)) {
				foreach ($wpqa_option_array as $key => $value) {
					$wpqa_one_option = get_option("report_option_".$value);
					$post_no_empty = get_post($wpqa_one_option["post_id"]);
					if (!isset($post_no_empty)) {
						unset($wpqa_one_option);
					}
					if (isset($wpqa_one_option) && $wpqa_one_option["report_new"] == 1) {
						$count_report_new[] = $wpqa_one_option["report_new"];
					}
				}
			}
			/* Count new reports answers */
			$wpqa_option_answer_array = get_option("report_option_answer_array");
			if (is_array($wpqa_option_answer_array)) {
				foreach ($wpqa_option_answer_array as $key => $value) {
					$wpqa_one_option = get_option("report_option_answer_".$value);
					$comment_no_empty = get_comment($wpqa_one_option["comment_id"]);
					if (!isset($comment_no_empty)) {
						unset($wpqa_one_option);
					}
					if (isset($wpqa_one_option) && $wpqa_one_option["report_new"] == 1) {
						$count_report_answer_new[] = $wpqa_one_option["report_new"];
					}
				}
			}
			$count_report_new = (isset($count_report_new) && is_array($count_report_new)?count($count_report_new):0);
			$count_report_answer_new = (isset($count_report_answer_new) && is_array($count_report_answer_new)?count($count_report_answer_new):0);
			$count_lasts = $count_report_new+$count_report_answer_new;
			$wpqa_page = add_menu_page(esc_html__('Reports','wpqa'), esc_html__('Reports','wpqa').' <span class="count_report_new awaiting-mod count-'.$count_lasts.'"><span class="count_lasts">'.$count_lasts.'</span></span>' ,'manage_options', apply_filters('wpqa_filter_r_questions','r_questions') , 'wpqa_r_questions','dashicons-email-alt');
			add_submenu_page( apply_filters('wpqa_filter_r_questions','r_questions'), apply_filters('wpqa_filter_questions_report',esc_html__('Questions','wpqa')), apply_filters('wpqa_filter_questions_report',esc_html__('Questions','wpqa')).' <span class="count_report_new awaiting-mod count-'.$count_report_new.'"><span class="count_report_question_new">'.$count_report_new.'</span></span>', 'manage_options', apply_filters('wpqa_filter_r_questions','r_questions'), 'wpqa_r_questions' );
			add_submenu_page( apply_filters('wpqa_filter_r_questions','r_questions'), esc_html__('Answers','wpqa'), esc_html__('Answers','wpqa').' <span class="count_report_new awaiting-mod count-'.$count_report_answer_new.'"><span class="count_report_answer_new">'.$count_report_answer_new.'</span></span>', 'manage_options', 'r_answers', 'wpqa_r_answers' );
		}
		$pay_ask = wpqa_options("pay_ask");
		if ($pay_ask == "on") {
			$new_payments = (int)get_option("new_payments");
			add_menu_page(esc_html__('Payments','wpqa'), esc_html__('Payments','wpqa').' <span class="count_report_new awaiting-mod count-'.$new_payments.'"><span class="count_lasts">'.$new_payments.'</span></span>' ,'manage_options', 'wpqa_payments' , 'wpqa_payments','dashicons-cart');
		}
	}
endif;
add_action('admin_menu', 'wpqa_add_admin_page_reports');
/* Report questions */
if (!function_exists('wpqa_r_questions')) :
	function wpqa_r_questions () {
		wpqa_reports();
	}
endif;
/* Report answers */
if (!function_exists('wpqa_r_answers')) :
	function wpqa_r_answers () {
		wpqa_reports("_answer");
	}
endif;
/* Reports */
if (!function_exists('wpqa_reports')) :
	function wpqa_reports ($type = "") {
		$ask_me = wpqa_options("ask_me");
		if ($ask_me == "on") {
			$ask_option = get_option("ask_option".$type);
			$ask_option_array = get_option("ask_option".$type."_array");
			if (isset($ask_option) && $ask_option != "") {
				update_option("report_option".$type,$ask_option);
				delete_option("ask_option".$type);
			}
			if (isset($ask_option_array) && !empty($ask_option_array)) {
				update_option("report_option".$type."_array",$ask_option_array);
				delete_option("ask_option".$type."_array");
			}
		}?>
		<div class="wrap">
			<h1>
				<?php if ($type == "_answer") {
					echo esc_html__("Answers","wpqa");
				}else {
					echo apply_filters('wpqa_filter_questions_report',esc_html__('Questions','wpqa'));
				}echo " ".esc_html__("Reports","wpqa");?>
			</h1>
			
			<?php $rows_per_page = get_option("posts_per_page");
			$wpqa_option = get_option("report_option".$type);
			$wpqa_option_array = get_option("report_option".$type."_array");
			if (is_array($wpqa_option_array)) {
				foreach ($wpqa_option_array as $key => $value) {
					if ($ask_me == "on") {
						$discy_item_option = get_option("ask_option".$type."_".$value);
						if (isset($discy_item_option) && !empty($discy_item_option)) {
							update_option("report_option".$type."_".$value,$discy_item_option);
							delete_option("ask_option".$type."_".$value);
						}
					}
					$wpqa_one_option[$value] = get_option("report_option".$type."_".$value);
					if ($type == "_answer") {
						$comment_no_empty = get_comment($wpqa_one_option[$value]["comment_id"]);
						if (!isset($comment_no_empty)) {
							unset($wpqa_one_option[$value]);
						}
					}else {
						$post_no_empty = get_post($wpqa_one_option[$value]["post_id"]);
						if (!isset($post_no_empty)) {
							unset($wpqa_one_option[$value]);
						}
					}
				}
			}?>
				
			<div class="reports-table-items">
				<?php if (isset($wpqa_one_option) && is_array($wpqa_one_option) && !empty($wpqa_one_option)) {
					update_option("new_payments",0);
					$wpqa_reports_option = array_reverse($wpqa_one_option);
					$paged = (isset($_GET["paged"])?(int)$_GET["paged"]:1);
					$current = max(1,$paged);
					$pagination_args = array(
						'base'      => @esc_url(add_query_arg('paged','%#%')),
						'total'     => ceil(sizeof($wpqa_reports_option)/$rows_per_page),
						'current'   => $current,
						'show_all'  => false,
						'prev_text' => '&laquo;',
						'next_text' => '&raquo;',
					);
						
					$start = ($current - 1) * $rows_per_page;
					$end = $start + $rows_per_page;
					$end = (sizeof($wpqa_reports_option) < $end) ? sizeof($wpqa_reports_option) : $end;
					
					if (isset($pagination_args["total"]) && $pagination_args["total"] > 1) {?>
						<div class="tablenav top">
							<div class="tablenav-pages">
								<span class="displaying-num"><?php echo count($wpqa_reports_option)?> <?php esc_html_e("Reports","wpqa")?></span>
								<span class="pagination-links">
									<?php echo (paginate_links($pagination_args) != ""?paginate_links($pagination_args):"")?>
								</span>
							</div>
							<br class="clear">
						</div>
					<?php }else {
						echo "<br>";
					}
				}else {
					echo "<br>";
				}?>
				
				<table class="wp-list-table widefat fixed striped ">
					<thead>
						<tr>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Link","wpqa")?></span></th>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Report","wpqa")?></span></th>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Author","wpqa")?></span></th>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Date","wpqa")?></span></th>
						</tr>
					</thead>
					
					<tbody class="report-table">
						<?php if (isset($wpqa_one_option) && is_array($wpqa_one_option) && !empty($wpqa_one_option)) {
							for ($i=$start;$i < $end ;++$i) {
								$wpqa_reports_option_result = $wpqa_reports_option[$i];?>
								<tr<?php echo ($wpqa_reports_option_result["report_new"] == 1?' class="unapproved"':'')?>>
									<?php if ($type == "_answer") {?>
										<td><a href="<?php echo get_comment_link($wpqa_reports_option_result["comment_id"])?>"><?php echo get_comment_link($wpqa_reports_option_result["comment_id"])?></a></td>
									<?php }else {?>
										<td><a href="<?php echo get_the_permalink($wpqa_reports_option_result["post_id"]);?>"><?php echo get_the_permalink($wpqa_reports_option_result["post_id"]);?></a></td>
									<?php }?>
									<td class="has-row-actions">
										<p>
											<?php echo wpqa_kses_stip_wpautop($wpqa_reports_option_result["value"]);?>
										</p>
										<div class="row-actions">
											<span class="delete"><a class="reports-delete<?php echo ($type == "_answer"?" reports-answers":"")?>" data-nonce="<?php echo wp_create_nonce("wpqa_report_nonce")?>" data-attr="<?php echo esc_attr($wpqa_reports_option_result["item_id_option"])?>" href="#"><?php esc_html_e("Delete","wpqa")?></a></span>
										</div>
									</td>
									<td>
										<strong>
											<?php if ($wpqa_reports_option_result["the_author"] != "") {
												if ($wpqa_reports_option_result["the_author"] == 1) {
													echo "Not user";
												}else {
													echo esc_attr($wpqa_reports_option_result["the_author"]);
												}
											}else {
												?><a href="<?php echo wpqa_profile_url((int)$wpqa_reports_option_result["user_id"]);?>"><?php echo get_the_author_meta("display_name",(int)$wpqa_reports_option_result["user_id"])?></a><?php
											}?>
										</strong>
									</td>
									<td><div><?php echo human_time_diff($wpqa_reports_option_result["the_date"],current_time('timestamp'))." ".esc_html__("ago","wpqa")?></div></td>
								</tr>
								<?php if ($wpqa_reports_option_result["report_new"] == 1) {
									$wpqa_reports_option_result["report_new"] = 0;
									update_option("report_option".$type."_".$wpqa_reports_option_result["item_id_option"],$wpqa_reports_option_result);
								}
							}
						}else {
							echo '<tr class="no-items"><td class="colspanchange" colspan="4">'.esc_html__("There are no reports yet.","wpqa").'</td></tr>';
						}?>
					</tbody>
				
					<tfoot>
						<tr>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Link","wpqa")?></span></th>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Report","wpqa")?></span></th>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Author","wpqa")?></span></th>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Date","wpqa")?></span></th>
						</tr>
					</tfoot>
				</table>
				
				<?php if (isset($pagination_args["total"]) && $pagination_args["total"] > 1) {?>
					<div class="tablenav bottom">
						<div class="tablenav-pages">
							<span class="displaying-num"><?php echo count($wpqa_reports_option)?> <?php esc_html_e("Reports","wpqa")?></span>
							<span class="pagination-links">
								<?php echo (paginate_links($pagination_args) != ""?paginate_links($pagination_args):"")?>
							</span>
						</div>
						<br class="clear">
					</div>
				<?php }?>
			</div>
		</div>
	<?php }
endif;
/* Not show the question */
add_action('wp','wpqa_not_show_questions');
if (!function_exists('wpqa_not_show_questions')) :
	function wpqa_not_show_questions() {
		global $post;
		if (is_singular('question')) {
			$user_get_current_user_id = get_current_user_id();
			$get_question_user_id = get_post_meta($post->ID,"user_id",true);
			$yes_private = wpqa_private($post->ID,$post->post_author,$user_get_current_user_id);
			if (!is_super_admin($user_get_current_user_id) && $yes_private != 1) {
				global $wp_query;
				$wp_query->set_404();
				status_header(404);
			}
		}
	}
endif;
/* Remove private questions from API */
add_filter('rest_prepare_question','wpqa_remove_user_questions',10,3);
if (!function_exists('wpqa_remove_user_questions')) :
	function wpqa_remove_user_questions($data,$post,$request) {
		$_data = $data->data;
		$params = $request->get_params();
		if (!isset($params['id'])) {
			$user_id          = get_post_meta($_data['id'],"user_id",true);
			$user_is_comment  = get_post_meta($_data['id'],"user_is_comment",true);
			$private_question = get_post_meta($_data['id'],"private_question",true);
			 
			if ($private_question == 1 || $private_question == "on" || ($user_id != "" && $user_is_comment != true)) {
				unset($_data);
			}
		}
		$data->data = $_data;
		return $data;
	}
endif;
/* Remove private questions from feed */
add_action('pre_get_posts','wpqa_feed_private_question');
if (!function_exists('wpqa_feed_private_question')) :
	function wpqa_feed_private_question($query) {
		if (is_feed()) {
			$query->set('meta_query',
				array(
					'relation' => 'OR',
					array(
						array("key" => "private_question","compare" => "NOT EXISTS"),
						array("key" => "user_id","compare" => "EXISTS"),
						array("key" => "user_is_comment","compare" => "EXISTS")
					),
					array(
						array("key" => "private_question","compare" => "NOT EXISTS"),
						array("key" => "user_id","compare" => "NOT EXISTS")
					),
				)
			);
		}
	}
endif;
/* Feed request */
add_filter('request','wpqa_feed_request');
if (!function_exists('wpqa_feed_request')) :
	function wpqa_feed_request ($qv) {
		if (isset($qv['feed']) && !isset($qv['post_type'])) {
			$qv['post_type'] = array('post','question');
		}
		return $qv;
	}
endif;
/* Question sticky */
if (!function_exists('wpqa_question_content')) :
	function wpqa_question_sticky($post_id) {
		$end_sticky_time = get_post_meta($post_id,"end_sticky_time",true);
		$question_sticky = "";
		if (is_sticky($post_id)) {
			$question_sticky = "sticky";
			if ($end_sticky_time != "" && $end_sticky_time < strtotime(date("Y-m-d"))) {
				$question_sticky = "";
			}
		}
		
		return $question_sticky;
	}
endif;
/* Question content */
add_action('wpqa_question_content','wpqa_question_content',1,4);
if (!function_exists('wpqa_question_content')) :
	function wpqa_question_content($post_id,$user_id,$anonymously_user,$post_author) {
		$_paid_question = get_post_meta($post_id,"_paid_question",true);
		$end_sticky_time = get_post_meta($post_id,"end_sticky_time",true);
		
		if (is_super_admin($user_id) && ((isset($_paid_question) && $_paid_question == "paid") || is_sticky())) {
			if (isset($_paid_question) && $_paid_question == "paid") {
				$item_transaction = get_post_meta($post_id, 'item_transaction', true);
				$paypal_sandbox = get_post_meta($post_id, 'paypal_sandbox', true);
			}
			
			if (is_sticky()) {
				$item_transaction_sticky = get_post_meta($post_id, 'item_transaction_sticky', true);
				$paypal_sandbox_sticky = get_post_meta($post_id, 'paypal_sandbox_sticky', true);
			}
		}
		
		if ((is_super_admin($user_id) || ($anonymously_user > 0 && $user_id == $anonymously_user) || ($post_author > 0 && $user_id == $post_author)) && (isset($_paid_question) && $_paid_question == "paid")) {
			echo '<div class="alert-message"><i class="icon-lamp"></i><p> '.esc_html__("This is a paid question.","wpqa").((isset($_paid_question) && $_paid_question == "paid" && ((isset($item_transaction) && $item_transaction != "") || (isset($paypal_sandbox) && $paypal_sandbox != "" && $paypal_sandbox = "sandbox"))) || (is_sticky() && ((isset($item_transaction_sticky) && $item_transaction_sticky != "") || (isset($paypal_sandbox_sticky) && $paypal_sandbox_sticky != "" && $paypal_sandbox_sticky = "sandbox")))?' <a href="#" class="paid-details wpqa-open-click" data-class="paid-question-area">'.esc_html__("Paid details","wpqa").' </a>':'').'</p></div>';
		}
		
		if (is_sticky()) {
			if ((is_super_admin($user_id) || ($anonymously_user > 0 && $user_id == $anonymously_user) || ($post_author > 0 && $user_id == $post_author)) && ($end_sticky_time != "" && $end_sticky_time >= strtotime(date("Y-m-d")))) {
				echo '<div class="alert-message"><i class="icon-lamp"></i><p>'.esc_html__("This question will sticky to","wpqa").': '.date("Y-m-d",$end_sticky_time).'</p></div>';
			}
		}
		
		if (is_super_admin($user_id) && ((isset($_paid_question) && $_paid_question == "paid") || is_sticky())) {
			
			if ((isset($_paid_question) && $_paid_question == "paid" && ((isset($item_transaction) && $item_transaction != "") || (isset($paypal_sandbox) && $paypal_sandbox != "" && $paypal_sandbox = "sandbox"))) || (is_sticky() && ((isset($item_transaction_sticky) && $item_transaction_sticky != "") || (isset($paypal_sandbox_sticky) && $paypal_sandbox_sticky != "" && $paypal_sandbox_sticky = "sandbox")))) {
				echo '<div class="paid-question-area wpqa-open-div wpqa_hide">';
					if (isset($_paid_question) && $_paid_question == "paid") {
						if (isset($item_transaction) && $item_transaction != "") {
							echo '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("The transaction id","wpqa").' : '.$item_transaction.'</p></div>';
						}
						if (isset($paypal_sandbox) && $paypal_sandbox != "" && $paypal_sandbox = "sandbox") {
							echo '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("This transaction is from PayPal sandbox.","wpqa").'</p></div>';
						}
					}
					
					if (is_sticky()) {
						if (isset($item_transaction_sticky) && $item_transaction_sticky != "") {
							echo '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("The transaction id for sticky question","wpqa").' : '.$item_transaction_sticky.'</p></div>';
						}
						if (isset($paypal_sandbox_sticky) && $paypal_sandbox_sticky != "" && $paypal_sandbox_sticky = "sandbox") {
							echo '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("This transaction is from PayPal sandbox for sticky question.","wpqa").'</p></div>';
						}
					}
				echo '</div>';
			}
		}
	}
endif;
/* Question content loop */
add_action('wpqa_question_content_loop','wpqa_question_content_loop',1,1);
if (!function_exists('wpqa_question_content_loop')) :
	function wpqa_question_content_loop($post_id) {
		$end_sticky_time  = get_post_meta($post_id,"end_sticky_time",true);
		if ($end_sticky_time != "" && $end_sticky_time < strtotime(date("Y-m-d"))) {
			delete_post_meta($post_id,"start_sticky_time");
			delete_post_meta($post_id,"end_sticky_time");
			delete_post_meta($post_id,'sticky');
			$sticky_questions = get_option('sticky_questions');
			if (is_array($sticky_questions) && in_array($post_id,$sticky_questions)) {
				$sticky_posts = get_option('sticky_posts');
				$sticky_posts = wpqa_remove_item_by_value($sticky_posts,$post_id);
				update_option('sticky_posts',$sticky_posts);
				$sticky_questions = wpqa_remove_item_by_value($sticky_questions,$post_id);
				update_option('sticky_questions',$sticky_questions);
			}
		}
	}
endif;
/* Bump question */
add_action('wpqa_after_question_area','wpqa_question_bump_a',1,5);
if (!function_exists('wpqa_question_bump_a')) :
	function wpqa_question_bump_a($post_id,$user_id,$anonymously_user,$post_author,$comments) {
		$question_bump = wpqa_options("question_bump");
		$active_points = wpqa_options("active_points");
		if (is_user_logged_in() && $question_bump == "on" && $active_points == "on" && empty($comments) && $user_id == $post_author && $post_author != 0) {
			echo '<a href="#" class="bump-question wpqa-open-click color" data-class="bump-question-area">'.esc_html__("Bump your question","wpqa").'</a>';
		}
	}
endif;
/* Sticky area */
add_action('wpqa_after_question_area','wpqa_question_sticky_area',2,4);
if (!function_exists('wpqa_question_sticky_area')) :
	function wpqa_question_sticky_area($post_id,$user_id,$anonymously_user,$post_author) {
		$pay_to_sticky = wpqa_options("pay_to_sticky");
		if ($pay_to_sticky == "on") {
			$end_sticky_time = get_post_meta($post_id,"end_sticky_time",true);
			if (($end_sticky_time == "" || ($end_sticky_time != "" && $end_sticky_time < strtotime(date("Y-m-d"))))) {
				$days_sticky = (int)wpqa_options("days_sticky");
				$days_sticky = ($days_sticky > 0?$days_sticky:7);
				
				$_allow_to_sticky = get_user_meta($user_id,$user_id."_allow_to_sticky",true);
				if ($user_id > 0 && isset($_POST["process"]) && $_POST["process"] == "sticky") {
					/* Pay by points */
					if (isset($_POST["points"]) && $_POST["points"] > 0) {
						$points_price = (int)$_POST["points"];
						$points_user = (int)get_user_meta($user_id,"points",true);
						if ($points_price <= $points_user) {
							update_post_meta($post_id,"sticky_points",$points_price);
							wpqa_add_points($user_id,$points_price,"-","sticky_points");
							wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("You have just stickied the question by points.","wpqa").'</p></div>','wpqa_session');
						}else {
							$buy_points_payment = wpqa_options("buy_points_payment");
							wpqa_session('<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you haven't enough points","wpqa").($buy_points_payment == "on"?', <a href="'.wpqa_buy_points_permalink().'">'.esc_html__("You can buy the points from here.","wpqa").'</a>':'.').'</p></div>','wpqa_session');
							wp_safe_redirect(get_the_permalink($post_id));
							die();
						}
					}
					update_post_meta($post_id,"sticky",1);
					$sticky_posts = get_option('sticky_posts');
					if (is_array($sticky_posts)) {
						if (!in_array($post_id,$sticky_posts)) {
							$array_merge = array_merge($sticky_posts,array($post_id));
							update_option("sticky_posts",$array_merge);
						}
					}else {
						update_option("sticky_posts",array($post_id));
					}
					$sticky_questions = get_option('sticky_questions');
					if (is_array($sticky_questions)) {
						if (!in_array($post_id,$sticky_questions)) {
							$array_merge = array_merge($sticky_questions,array($post_id));
							update_option("sticky_questions",$array_merge);
						}
					}else {
						update_option("sticky_questions",array($post_id));
					}
					update_post_meta($post_id,"start_sticky_time",strtotime(date("Y-m-d")));
					update_post_meta($post_id,"end_sticky_time",strtotime(date("Y-m-d",strtotime(date("Y-m-d")." +$days_sticky days"))));
					wp_safe_redirect(get_the_permalink($post_id));
					die();
				}
				
				if ((($anonymously_user > 0 && $user_id == $anonymously_user) || ($post_author > 0 && $user_id == $post_author)) && (($end_sticky_time != "" && $end_sticky_time < strtotime(date("Y-m-d"))) || (!is_sticky())) && isset($_allow_to_sticky) && (int)$_allow_to_sticky < 1 && $pay_to_sticky == "on") {
					$pay_sticky_payment = $last_payment = floatval(wpqa_options("pay_sticky_payment"));
					echo '<a href="#" class="pay-to-sticky wpqa-open-click color" data-class="pay-to-sticky-area">'.esc_html__("Pay to sticky question","wpqa").'</a>
					<div class="clearfix"></div>
					<div class="pay-to-sticky-area wpqa-open-div'.(isset($_POST["add_coupon"]) && $_POST["add_coupon"] == "submit"?"":" wpqa_hide").'">
						'.wpqa_get_payment_coupons($user_id,false,$post_id,$days_sticky,"sticky").'
					</div>
					<div class="clearfix"></div>';
				}
			}
		}
	}
endif;
/* Bump question */
add_action('wpqa_after_question_area','wpqa_question_bump',3,5);
if (!function_exists('wpqa_question_bump')) :
	function wpqa_question_bump($post_id,$user_id,$anonymously_user,$post_author,$comments) {
		$question_bump = wpqa_options("question_bump");
		$active_points = wpqa_options("active_points");
		if (is_user_logged_in() && $question_bump == "on" && $active_points == "on" && empty($comments) && $user_id == $post_author && $post_author != 0) {
			echo '<div class="clearfix"></div>
			<div class="bump-question-area wpqa-open-div wpqa_hide">
				<input id="input-add-point" name="" type="text" placeholder="'.esc_html__("Question bump points","wpqa").'">
				<a class="button-default" href="#">'.esc_html__("Bump","wpqa").'</a>
				<div class="load_span"><span class="loader_2"></span></div>
			</div>
			<div class="clearfix"></div>';
		}
	}
endif;
/* Vote question */
add_action('wpqa_question_vote','wpqa_question_vote',1,7);
if (!function_exists('wpqa_question_vote')) :
	function wpqa_question_vote($post,$user_id,$anonymously_user,$question_vote,$question_loop_dislike,$question_single_dislike,$class = "") {
		$active_vote_unlogged = wpqa_options("active_vote_unlogged");?>
		<ul class="question-vote<?php echo ($class != ""?" ".$class:"")?>">
			<li class="question-vote-up"><a href="#"<?php echo ((is_user_logged_in() && $post->post_author != $user_id && $anonymously_user != $user_id) || (!is_user_logged_in() && $active_vote_unlogged == "on")?' id="question_vote_up-'.$post->ID.'"':'')?> data-type="question" data-vote-type="up" class="wpqa_vote question_vote_up<?php echo (is_user_logged_in() && $post->post_author != $user_id && $anonymously_user != $user_id?"":(is_user_logged_in() && (($post->post_author > 0 && $post->post_author == $user_id) || ($anonymously_user == $user_id))?" vote_not_allow":($active_vote_unlogged == "on"?"":" vote_not_user"))).((is_user_logged_in() && $post->post_author != $user_id && $anonymously_user != $user_id) || (!is_user_logged_in() && $active_vote_unlogged == "on")?' vote_allow':'')?>" title="<?php esc_attr_e("Like","wpqa");?>"><i class="<?php echo apply_filters('wpqa_vote_up_icon','icon-up-dir');?>"></i></a></li>
			<li class="vote_result" itemprop="upvoteCount"><?php echo ($question_vote != ""?$question_vote:0)?></li>
			<li class="li_loader"><span class="loader_3 fa-spin"></span></li>
			<?php if ((!is_single() && $question_loop_dislike != "on") || (is_single() && $question_single_dislike != "on")) {?>
				<li class="question-vote-down"><a href="#"<?php echo ((is_user_logged_in() && $post->post_author != $user_id && $anonymously_user != $user_id) || (!is_user_logged_in() && $active_vote_unlogged == "on")?' id="question_vote_down-'.$post->ID.'"':'')?> data-type="question" data-vote-type="down" class="wpqa_vote question_vote_down<?php echo (is_user_logged_in() && $post->post_author != $user_id && $anonymously_user != $user_id?"":(is_user_logged_in() && (($post->post_author > 0 && $post->post_author == $user_id) || ($anonymously_user == $user_id))?" vote_not_allow":($active_vote_unlogged == "on"?"":" vote_not_user"))).((is_user_logged_in() && $post->post_author != $user_id && $anonymously_user != $user_id) || (!is_user_logged_in() && $active_vote_unlogged == "on")?' vote_allow':'')?>" title="<?php esc_attr_e("Dislike","wpqa");?>"><i class="<?php echo apply_filters('wpqa_vote_down_icon','icon-down-dir');?>"></i></a></li>
			<?php }?>
		</ul>
	<?php }
endif;
/* Allow pre tag */
add_action('wpqa_init','wpqa_allow_pre');
if (!function_exists('wpqa_allow_pre')) :
	function wpqa_allow_pre () {
		global $allowedtags;
		$allowedtags['pre'] = array();
	}
endif;
/* Question list */
add_action("wpqa_question_list_details","wpqa_question_list_details",1,9);
if (!function_exists('wpqa_question_list_details')) :
	function wpqa_question_list_details($post,$user_id,$anonymously_user,$question_edit,$question_delete,$question_close,$closed_question,$active_reports,$active_logged_reports) {
		echo '<ul class="question-link-list">';
			if (($user_id == $post->post_author && $post->post_author > 0) || ($anonymously_user != "" && $anonymously_user == $user_id) || is_super_admin($user_id)) {
				if ($question_edit == "on") {
					echo '<li><a href="'.esc_url(wpqa_edit_permalink($post->ID)).'"><i class="icon-pencil"></i>'.esc_html__("Edit","wpqa").'</a></li>';
				}
				if ($question_delete == "on" || is_super_admin($user_id)) {
					echo '<li><a class="question-delete" href="'.esc_url(add_query_arg(array("delete" => $post->ID,"wpqa_delete_nonce" => wp_create_nonce("wpqa_delete_nonce")),get_permalink($post->ID))).'"><i class="icon-trash"></i>'.esc_html__("Delete","wpqa").'</a></li>';
				}
				if ($question_close == "on") {
					echo '<li><a class="'.($closed_question == 1?"question-open":"question-close").'" href="#" data-nonce="'.wp_create_nonce("wpqa_open_close_nonce").'" title="'.($closed_question == 1?esc_html__("Open the question","wpqa"):esc_html__("Close the question","wpqa")).'"><i class="icon-lock'.($closed_question == 1?"-open":"").'"></i>'.($closed_question == 1?esc_html__("Open","wpqa"):esc_html__("Close","wpqa")).'</a></li>';
				}
			}
			if ($active_reports == "on" && ((is_user_logged_in() && (($user_id != $post->post_author && $post->post_author > 0) || $post->post_author == 0 || $anonymously_user == 0 || ($anonymously_user != "" && $anonymously_user != $user_id && $user_id > 0))) || (!is_user_logged_in() && $active_logged_reports != "on"))) {
				echo '<li class="report_activated"><a class="report_q" href="'.esc_attr($post->ID).'"><i class="icon-attention"></i>'.esc_html__("Report","wpqa").'</a></li>';
			}
		echo '</ul>';
	}
endif;?>