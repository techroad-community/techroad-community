<?php if ($post->post_type == 'question') {
	$question_category = wp_get_post_terms($post->ID,'question-category',array("fields" => "all"));
	if (isset($question_category[0])) {
		$discy_new = discy_term_meta("new",$question_category[0]->term_id);
		$discy_special = discy_term_meta("special",$question_category[0]->term_id);
	}
	$closed_question = discy_post_meta("closed_question","",false);
	$custom_permission = discy_options("custom_permission");
	$add_answer = discy_options("add_answer");
	$its_question = "question";
}

$user_id = get_current_user_id();
if (is_user_logged_in()) {
	$user_is_login = get_userdata($user_id);
	$roles = $user_is_login->allcaps;
}

$wpqa_server = apply_filters('wpqa_server','SCRIPT_FILENAME');
if (!empty($wpqa_server) && 'comments.php' == basename($wpqa_server)) :
	die (esc_html__('Please do not load this page directly. Thanks!',"discy"));
endif;

if ( post_password_required() ) : ?>
    <p class="no-comments">
    	<?php if (isset($its_question) && 'question' == $its_question) {
    		esc_html_e("This question is password protected. Enter the password to view answers.","discy");
    	}else {
    		esc_html_e("This post is password protected. Enter the password to view comments.","discy");
    	}?>
    </p>
    <?php return;
endif;

if ( have_comments() ) :
	$k_ad = 1;?>
	<div id="comments" class="post-section">
		<div class="post-inner">
			<?php $filter_show_comments = apply_filters("discy_filter_show_comments",true,$post->post_type);
			if ($filter_show_comments == true) {
				if (isset($its_question) && $its_question == "question") {
					$answers_tabs = discy_options('answers_tabs');
					$answers_tabs = apply_filters("wpqa_answers_tabs",$answers_tabs);
					$answers_tabs_keys = array_keys($answers_tabs);
					if (isset($answers_tabs) && is_array($answers_tabs)) {
						$a_count = 0;
						while ($a_count < count($answers_tabs)) {
							if (isset($answers_tabs[$answers_tabs_keys[$a_count]]["value"]) && $answers_tabs[$answers_tabs_keys[$a_count]]["value"] != "" && $answers_tabs[$answers_tabs_keys[$a_count]]["value"] != "0") {
								$first_one = $a_count;
								break;
							}
							$a_count++;
						}
						
						if (isset($first_one)) {
							$first_one = $answers_tabs[$answers_tabs_keys[$first_one]]["value"];
						}
						
						if (isset($_GET["show"]) && $_GET["show"] != "") {
							$first_one = $_GET["show"];
						}
					}
					if (isset($first_one)) {?>
						<div class="answers-tabs">
					<?php }
				}?>
					<h3 class="section-title"><span><?php comments_number(esc_html__('No',"discy"),esc_html__('1',"discy"), esc_html__('%',"discy"));?> </span><?php comments_number((isset($its_question) && 'question' == $its_question?esc_html__("Answers","discy"):esc_html__("Comments","discy")),(isset($its_question) && 'question' == $its_question?apply_filters("wpqa_answer_language",esc_html__("Answer","discy")):esc_html__("Comment","discy")), (isset($its_question) && 'question' == $its_question?esc_html__("Answers","discy"):esc_html__("Comments","discy")));?></h3>
				<?php if (isset($its_question) && $its_question == "question" && isset($first_one)) {
					$wpqa_answers_tabs_foreach = apply_filters("wpqa_answers_tabs_foreach",true,$answers_tabs,$first_one);
				}
				if (isset($wpqa_answers_tabs_foreach) && $wpqa_answers_tabs_foreach == true && isset($its_question) && $its_question == "question" && isset($first_one)) {?>
						<div class="answers-tabs-inner">
							<ul>
								<?php foreach ($answers_tabs as $key => $value) {
									if ($key == "votes" && isset($answers_tabs["votes"]["value"]) && $answers_tabs["votes"]["value"] == "votes") {?>
										<li<?php echo ((isset($_GET["show"]) && $_GET["show"] === "votes") || $first_one === "votes"?" class='active-tab'":"")?>><a href="<?php echo esc_url(add_query_arg(array("show" => "votes")))?>#comments"><?php esc_html_e("Voted","discy")?></a></li>
									<?php }else if ($key == "oldest" && isset($answers_tabs["oldest"]["value"]) && $answers_tabs["oldest"]["value"] == "oldest") {?>
										<li<?php echo ((isset($_GET["show"]) && $_GET["show"] === "oldest") || $first_one === "oldest"?" class='active-tab'":"")?>><a href="<?php echo esc_url(add_query_arg(array("show" => "oldest")))?>#comments"><?php esc_html_e("Oldest","discy")?></a></li>
									<?php }else if ($key == "recent" && isset($answers_tabs["recent"]["value"]) && $answers_tabs["recent"]["value"] == "recent") {?>
										<li<?php echo ((isset($_GET["show"]) && $_GET["show"] === "recent") || $first_one === "recent"?" class='active-tab'":"")?>><a href="<?php echo esc_url(add_query_arg(array("show" => "recent")))?>#comments"><?php esc_html_e("Recent","discy")?></a></li>
									<?php }
								}?>
							</ul>
						</div><!-- End answers-tabs-inner -->
						<div class="clearfix"></div>
					</div><!-- End answers-tabs -->
				<?php }
				$show_answer = discy_options("show_answer");
				if (empty($its_question) || (isset($its_question) && $its_question == "question" && $custom_permission != "on" || (is_super_admin($user_id)) || (is_user_logged_in() && isset($roles["show_answer"]) && $roles["show_answer"] == 1) || (!is_user_logged_in() && $show_answer == "on"))) {
					if (isset($its_question) && $its_question == "question") {
						if (isset($first_one)) {
							if ($first_one == 'votes') {
								$comments_args = get_comments(array('post_id' => $post->ID,'status' => 'approve','orderby' => 'meta_value_num','meta_key' => 'comment_vote','order' => 'DESC'));
							}else if ($first_one == 'oldest') {
								$comments_args = get_comments(array('post_id' => $post->ID,'status' => 'approve','orderby' => 'comment_date','order' => 'ASC'));
							}else if ($first_one == 'recent') {
								$comments_args = get_comments(array('post_id' => $post->ID,'status' => 'approve','orderby' => 'comment_date','order' => 'DESC'));
							}
						}
					}?>
					<ol class="commentlist clearfix">
					    <?php if (isset($its_question) && $its_question == "question") {
					    	$comments_args = (isset($comments_args)?$comments_args:array());
						    $comments_args = apply_filters("wpqa_comments_args",$comments_args,$first_one,$post->ID);
						}
					    if (isset($comments_args) && is_array($comments_args) && !empty($comments_args)) {
					    	wp_list_comments('callback=discy_comment',$comments_args);
					    }else {
					    	$wpqa_show_comments = apply_filters("wpqa_show_comments",true);
					    	if ($wpqa_show_comments == true) {
						    	wp_list_comments('callback=discy_comment');
						    }
					    }?>
					</ol><!-- End commentlist -->
				<?php }else {
					echo '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry, you do not have a permission to show this answers.","discy").'</p></div>';
				}
			}?>
			<div class="clearfix"></div>
		</div><!-- End post-inner -->
	</div><!-- End post -->
	
	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
		<div class="pagination comments-pagination">
		    <?php paginate_comments_links(array('prev_text' => '<i class="icon-left-open"></i>', 'next_text' => '<i class="icon-right-open"></i>'))?>
		</div><!-- End comments-pagination -->
		<div class="clearfix"></div>
    <?php endif;
endif;

if ( comments_open() ) {
	$yes_new = 1;
	if (have_comments()) {
		if (isset($question_category[0]) && $discy_new == "on") {
			$yes_new = 0;
			if ($post->post_author != $user_id) {
				$yes_new = 1;
			}
			if (is_super_admin($user_id)) {
				$yes_new = 0;
			}
		}else {
			$yes_new = 0;
		}
	}else {
		if (isset($question_category[0]) && $discy_new == "on") {
			if (isset($post->post_author) && $post->post_author > 0 && $post->post_author == $user_id) {
				$yes_new = 1;
			}
			if (isset($post->ID) && $post->post_author > 0 && $post->post_author == $user_id) {
				$yes_new = 1;
			}
		}else if (isset($question_category[0]) && $discy_new == 0) {
			$yes_new = 0;
		}
		
		if (empty($question_category[0]) || is_super_admin($user_id)) {
			$yes_new = 0;
		}
	}
	
	if (empty($its_question) || (isset($its_question) && $its_question == "question" && $yes_new != 1)) {
		if (empty($its_question) || (isset($its_question) && $its_question == "question" && $custom_permission != "on" || (is_super_admin($user_id)) || (is_user_logged_in() && isset($roles["add_answer"]) && $roles["add_answer"] == 1) || (!is_user_logged_in() && $add_answer == "on"))) {
			$yes_special = 1;
			if (have_comments()) {
				$yes_special = 0;
			}else {
				if (isset($question_category[0]) && $discy_special == "on") {
					if (isset($post->post_author) && $post->post_author > 0 && $post->post_author == $user_id) {
						$yes_special = 1;
					}
				}else if (isset($question_category[0]) && $discy_special == 0) {
					$yes_special = 0;
				}
				
				if (empty($question_category[0]) || is_super_admin($user_id)) {
					$yes_special = 0;
				}
			}
			
			if (isset($its_question) && $its_question == "question" && $yes_special == 1) {
				echo '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry this question is a special, The admin must answer first.","discy").'</p></div>';
			}else {
				if (isset($its_question) && $its_question == "question" && isset($closed_question) && $closed_question == 1) {
					echo '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry this question is closed.","discy").'</p></div>';
				}else {
					$fields =  array(
						'author' => '<div class="form-input"><input type="text" name="author" value="" id="comment_name" aria-required="true" placeholder="'.esc_attr__('Your Name',"discy").'">'.(isset($its_question) && 'question' == $its_question?'<i class="icon-user"></i>':'').'</div>',
						'email'  => '<div class="form-input form-input-last"><input type="email" name="email" value="" id="comment_email" aria-required="true" placeholder="'.esc_attr__('Email',"discy").'">'.(isset($its_question) && 'question' == $its_question?'<i class="icon-mail"></i>':'').'</div>',
						'url'    => '<div class="form-input form-input-full"><input type="url" name="url" value="" id="comment_url" placeholder="'.esc_attr__('URL',"discy").'">'.(isset($its_question) && 'question' == $its_question?'<i class="icon-link"></i>':'').'</div>',
					);
					
					$comment_editor = discy_options((isset($its_question) && 'question' == $its_question?'answer_editor':'comment_editor'));
					if ($comment_editor == "on") {
						$settings = array("textarea_name" => "comment","media_buttons" => true,"textarea_rows" => 10);
						$settings = apply_filters('wpqa_comment_editor_setting',$settings);
						
						ob_start();
						wp_editor("","comment",$settings);
						$comment_contents = ob_get_clean();
					}else {
						$comment_contents = '<textarea id="comment" name="comment" aria-required="true" placeholder="'.apply_filters("discy_filter_textarea_comment".(isset($its_question) && 'question' == $its_question?"_question":""),(isset($its_question) && 'question' == $its_question?esc_html__("Answer","discy"):esc_html__("Comment","discy"))).'"></textarea>'.(isset($its_question) && 'question' == $its_question?'<i class="icon-pencil"></i>':'');
					}
					
					$comments_args = array(
						'must_log_in'          => '<p class="no-login-comment">'.sprintf(wp_kses(__('You must <a href="%s" class="login-panel %s">login</a> or <a href="%s" class="signup-panel %s">register</a> to add a new','discy')." ".(isset($its_question) && 'question' == $its_question?esc_html__("answer","discy"):esc_html__("comment","discy")).".",array('a' => array('href' => array(),'class' => array()))),(class_exists("WPQA")?wpqa_login_permalink():"#"),apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_login',''),(class_exists("WPQA")?wpqa_signup_permalink():"#"),apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_signup','')).'</p>',
						'logged_in_as'         =>  '<p class="comment-login">'.esc_html__('Logged in as',"discy").'<a class="comment-login-login" href="'.esc_url(get_author_posts_url($user_id)).'"><i class="icon-user"></i>'.esc_attr($user_identity).'</a><a class="comment-login-logout" href="'.wp_logout_url(get_permalink()).'" title="'.esc_attr__("Logout of this account","discy").'"><i class="icon-logout"></i>'.esc_html__('Logout',"discy").'</a></p>',
						'title_reply'          => (isset($its_question) && 'question' == $its_question?esc_html__("Leave an answer","discy"):esc_html__("Leave a comment","discy")),
						'title_reply_to'       => (isset($its_question) && 'question' == $its_question?esc_html__("Leave an answer to %s","discy"):esc_html__("Leave a comment to %s","discy")),
						'title_reply_before'   => (isset($its_question) && 'question' == $its_question && !is_user_logged_in() && !get_option('comment_registration')?'<div class="button-default show-answer-form">'.esc_html__("Leave an answer","discy").'</div>':'').'<h3 class="section-title'.(isset($its_question) && 'question' == $its_question && !is_user_logged_in()?' comment-form-hide':'').'">',
						'title_reply_after'    => '</h3>',
						'class_form'           => 'post-section comment-form'.(isset($its_question) && 'question' == $its_question && !is_user_logged_in()?' comment-form-hide':'').(isset($its_question) && 'question' == $its_question?' answers-form':''),
						'comment_notes_after'  => '',
						'comment_notes_before' => '',
						'comment_field'        => '<div class="form-input form-textarea'.($comment_editor == "on"?" form-comment-editor":" form-comment-normal").'">'.$comment_contents.'</div>',
						'fields'               => apply_filters('comment_form_default_fields',$fields),
						'label_submit'         => esc_html__("Submit","discy"),
						'class_submit'         => 'button-default button-hide-click',
						'cancel_reply_before'  => '<div class="cancel-comment-reply">',
						'cancel_reply_after'   => '</div>',
						'format'               => 'html5'
					);
					comment_form(apply_filters("discy_filter_comment_form",$comments_args,$post->post_type));
				}
			}
		}else {
			echo '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry, you do not have a permission to answer to this question.","discy").'</p></div>';
		}
	}else {
		echo '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry, you do not have a permission to answer to this question.","discy").'</p></div>';
	}
}?>