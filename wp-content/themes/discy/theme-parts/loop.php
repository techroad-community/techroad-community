<?php $post_id_main    = (isset($post_id_main)?$post_id_main:"");
$pagination_show       = "yes";
$ask_question_to_users = discy_options("ask_question_to_users");
$first_one             = (isset($first_one) && $first_one != ""?$first_one:"");
$last_one              = (isset($last_one) && $last_one != ""?$last_one:"");
$get_user_var          = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
$user_id               = get_current_user_id();
$question_bump         = discy_options("question_bump");
$active_points         = discy_options("active_points");
$custom_category       = (isset($tab_category) && $tab_category == true && isset($custom_args)?$custom_args:array());

include locate_template("includes/slugs.php");

include locate_template("includes/".(isset($its_question) && "question" == $its_question?"question":"loop")."-setting.php");

if (isset($tab_category) && $tab_category == true && ($first_one == $answers_slug || $first_one == $answers_slug_2)) {
	include locate_template("includes/templates.php");
	$rows_per_page = get_option('posts_per_page');
	$offset        = ($paged -1) * $rows_per_page;
	$paged         = (get_query_var("paged") != ""?(int)get_query_var("paged"):(get_query_var("page") != ""?(int)get_query_var("page"):1));
	$current       = max(1,$paged);
	$categories    = get_terms('question-category',array('child_of' => $category_id,'hide_empty' => false));
	$category_list =  array($category_id);
	foreach ($categories as $term) {
		$category_list[] = (int)$term->term_id;
	}

	$posts = get_objects_in_term($category_list,'question-category');
	$specific_date_where = (isset($specific_date) && $specific_date != ""?" AND ($wpdb->comments.comment_date > '".date("Y-m-d",strtotime(date("Y-m-d")." +".$specific_date." ago"))."')":"");
	$query = $wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID in (".implode(',',$posts).")".$specific_date_where." AND comment_approved = %s ORDER by comment_date DESC LIMIT $rows_per_page OFFSET $offset",1);
	$comments_list = $wpdb->get_results($query);
	$total_query = $wpdb->get_var($wpdb->prepare("SELECT COUNT($wpdb->comments.comment_ID) FROM $wpdb->comments WHERE comment_post_ID in (".implode(',',$posts).")".$specific_date_where." AND comment_approved = %s",1));
	$total_pages = ceil($total_query/$rows_per_page);
	if (count($comments_list) > 0) {
		$date_format = get_option( 'date_format' );
		echo '<div class="page-content commentslist">
			<ol class="commentlist clearfix">';
				$k_ad = -1;
				foreach ($comments_list as $comment) {$k_ad++;
					$yes_private = (class_exists('WPQA')?wpqa_private($comment->comment_post_ID,get_post($comment->comment_post_ID)->post_author,$user_id):1);
					if ($yes_private == 1) {
							$comment_id = esc_attr($comment->comment_ID);
							discy_comment($comment,"","",($post_type == "post"?"comment":"answer"),"",$k_ad,"",
								array(
									"comment_with_title" => true
								)
							);?>
						</li>
					<?php }else {?>
						<li class="comment">
							<div class="comment-body clearfix">
								<?php echo '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry it a private answer.","discy").'</p></div>';?>
							</div>
						</li>
					<?php }
				}
			echo '</ol>
		</div>';
		if ($total_pages > 1) {
			echo '<div class="main-pagination"><div class="pagination">'
				.paginate_links(array(
					'base'      => add_query_arg('page','%#%'),
					'format'    => 'page/%#%/',
					'show_all'  => false,
					'current'   => $current,
					'total'     => $total_pages,
					'prev_text' => '<i class="icon-left-open"></i>',
					'next_text' => '<i class="icon-right-open"></i>',
				)).
			'</div></div><div class="clearfix"></div>';
		}
	}else {
		echo '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("Still no answers yet","discy").'</p></div>';
	}
}else if (isset($wp_page_template) && (($wp_page_template == "template-home.php" && isset($first_one) && ($first_one == $answers_slug || $first_one == $answers_slug_2)) || $wp_page_template == "template-comments.php")) {
	include locate_template("includes/templates.php");
	$paged   = (get_query_var("paged") != ""?(int)get_query_var("paged"):(get_query_var("page") != ""?(int)get_query_var("page"):1));
	$current = max(1,$paged);
	
	if ($orderby_answers == 'votes' && $post_type == 'question') {
		$args = array('order' => (isset($order_post)?$order_post:'DESC'),'orderby' => 'meta_value_num','meta_key' => 'comment_vote');
	}else if ($orderby_answers == 'oldest') {
		$args = array('order' => 'ASC','orderby' => 'comment_date');
	}else {
		$args = array('order' => (isset($order_post) && $orderby_answers == 'date'?$order_post:'DESC'),'orderby' => 'comment_date');
	}

	$comments_all = get_comments(array_merge($specific_date_array,$args,array('status' => 'approve','post_type' => $post_type,'meta_query' => array(array('key' => 'answer_question_user','compare' => 'NOT EXISTS')))));
	$max_num_pages = $total = ceil(sizeof($comments_all)/$post_number);
	if (!empty($comments_all)) {
		if (isset($post_pagination) && ($post_pagination == "pagination" || $post_pagination == "standard")) {
			$pagination_args = array(
				'format'    => 'page/%#%/',
				'total'     => $total,
				'current'   => $current,
				'show_all'  => false,
				'prev_text' => ($post_pagination == 'standard'?'<span>'.esc_html__('New Answers',"discy").'</span><i class="icon-right-thin"></i>':'<i class="icon-left-open"></i>'),
				'next_text' => ($post_pagination == 'standard'?'<i class="icon-left-thin"></i><span>'.esc_html__('Old Answers',"discy").'</span>':'<i class="icon-left-open"></i>'),
			);
		}
		
		$k_ad  = -1;
		$start = ($current - 1) * $post_number;
		$end   = $start + $post_number;?>
		<div class="page-content commentslist">
			<ol class="commentlist clearfix">
				<?php $end = (sizeof($comments_all) < $end) ? sizeof($comments_all) : $end;
				for ($k = $start;$k < $end ;++$k ) {$k_ad++;
					$comment = $comments_all[$k];
					$yes_private = (class_exists('WPQA')?wpqa_private($comment->comment_post_ID,get_post($comment->comment_post_ID)->post_author,$user_id):1);
					if ($yes_private == 1) {
							$comment_id = esc_attr($comment->comment_ID);
							discy_comment($comment,"","",($post_type == "post"?"comment":"answer"),"",$k_ad,"",
								array(
									"answer_question_id" => (isset($post_id_main)?$post_id_main:""),
									"custom_home_answer" => (isset($wp_page_template) && $wp_page_template == "template-home.php" && isset($first_one) && ($first_one == $answers_slug || $first_one == $answers_slug_2)?discy_post_meta("custom_home_answer",$post_id_main):""),
									"custom_answers"     => (isset($wp_page_template) && $wp_page_template == "template-comments.php"?discy_post_meta("custom_answers",$post_id_main):""),
									"comment_with_title" => true
								)
							);?>
						</li>
					<?php }else {?>
						<li class="comment">
							<div class="comment-body clearfix">
								<?php echo '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry it a private answer.","discy").'</p></div>';?>
							</div>
						</li>
					<?php }
				}?>
			</ol>
		</div>
	<?php }else {
		echo '<div class="alert-message warning"><i class="icon-flag"></i><p>'.($post_type == 'question'?esc_html__("Still no answers yet","discy"):esc_html__("Still no comments yet","discy")).'</p></div>';
	}
	
	if (isset($post_pagination) && ($post_pagination == "pagination" || $post_pagination == "standard") && $comments_all && $pagination_args["total"] > 1) {?>
		<div class="main-pagination"><div class='comment-pagination <?php echo ($post_pagination == "standard"?"standard-pagination page-navigation page-navigation-before":"pagination")?>'><?php echo (paginate_links($pagination_args) != ""?paginate_links($pagination_args):"")?></div></div>
	<?php }else if (isset($post_pagination) && ($post_pagination == "infinite_scroll" || $post_pagination == "load_more")) {
		$it_answer_pagination = true;
		if (class_exists("WPQA")) {
			wpqa_pagination_load((isset($post_pagination)?$post_pagination:"pagination"),(isset($max_num_pages)?$max_num_pages:""),(isset($it_answer_pagination)?$it_answer_pagination:false),(isset($its_question)?$its_question:false),(isset($wpqa_query)?$wpqa_query:null),(isset($post_type) && $post_type == "post"?true:false));
		}
	}
}else {
	if ((class_exists('WPQA') && wpqa_is_user_profile()) || (isset($wp_page_template) && $wp_page_template == "template-home.php")) {
		$show_custom_error = true;
	}
	
	$array_data = array();
	
	$question_meta_query = array("key" => "user_id","compare" => "NOT EXISTS");
	$advanced_queries = discy_options("advanced_queries");
	if ($advanced_queries == "on" && !is_super_admin($user_id)) {
		$question_meta_query = array(
			$question_meta_query,array(
				'relation' => 'OR',
				array("key" => "private_question","compare" => "NOT EXISTS"),
				array("key" => "private_question","compare" => "=","value" => 0),
				array(
					'relation' => 'AND',
					array("key" => "private_question","compare" => "EXISTS"),
					array("key" => "private_question_author","compare" => "=","value" => $user_id),
				)
			)
		);
	}
	
	if (isset($blog_h) && $blog_h == "blog_h") {
		$array_data = array("posts_per_page" => $post_number,"post_type" => "post");
	}else if ((isset($tab_category) && $tab_category == true) || (isset($wp_page_template) && ($wp_page_template == "template-question.php" || $wp_page_template == "template-blog.php" || $wp_page_template == "template-home.php"))) {
		include locate_template("includes/templates.php");
		if (((isset($tab_category) && $tab_category == true) || (isset($wp_page_template) && $wp_page_template == "template-question.php")) && isset($orderby_post) && ($orderby_post == "popular" || $orderby_post == "most_visited" || $orderby_post == "most_voted")) {
			$active_sticky = false;
			$array_data    = array_merge($custom_category,$orderby_array,$cats_post,$specific_date_array,array("post_type" => $post_type,"ignore_sticky_posts" => 1,"paged" => $paged,"posts_per_page" => $post_number));
		}else if (isset($wp_page_template) && $wp_page_template == "template-home.php" && isset($first_one) && $first_one == "all") {
			$active_sticky = true;
			$custom_args   = array("post_type" => "question");
			$show_sticky   = true;
			$post_not_true = true;
			$array_data    = array_merge($orderby_array,array("post_type" => $post_type,"ignore_sticky_posts" => 1,"paged" => $paged,"posts_per_page" => $post_number));
		}else if (isset($wp_page_template) && $wp_page_template == "template-home.php" && isset($first_one) && is_string($first_one) && isset($get_tax->term_id) && $get_tax->term_id > 0) {
			$active_sticky = true;
			$custom_args   = array("post_type" => "question","tax_query" => array(array("taxonomy" => "question-category","field" => "id","terms" => $get_tax->term_id)));
			$show_sticky   = true;
			$post_not_true = true;
			$array_data    = array_merge($orderby_array,$cats_post,array("post_type" => $post_type,"ignore_sticky_posts" => 1,"paged" => $paged,"posts_per_page" => $post_number,"tax_query" => array(array("taxonomy" => "question-category","field" => "slug","terms" => $first_one))));
		}else if ((isset($wp_page_template) && $wp_page_template == "template-question.php" && isset($orderby_post) && $orderby_post == "sticky") || (((isset($tab_category) && $tab_category == true) || (isset($wp_page_template) && $wp_page_template == "template-home.php")) && isset($first_one) && ($first_one == $question_sticky_slug || $first_one == $question_sticky_slug_2))) {
			$active_sticky = true;
			$sticky_only   = true;
			$custom_args   = array_merge($custom_category,$specific_date_array,array("post_type" => "question"));
			$show_sticky   = true;
		}else if ((isset($wp_page_template) && $wp_page_template == "template-question.php" && isset($orderby_post) && $orderby_post == "polls") || (((isset($tab_category) && $tab_category == true) || (isset($wp_page_template) && $wp_page_template == "template-home.php")) && isset($first_one) && ($first_one == $question_polls_slug || $first_one == $question_polls_slug_2))) {
			$active_sticky = true;
			$custom_args   = array("post_type" => "question");
			$show_sticky   = true;
			$post_not_true = true;
			$poll_array    = array("ignore_sticky_posts" => 1,"meta_query" => array('relation' => 'AND',$question_meta_query,array("key" => "question_poll","value" => "on","compare" => "LIKE")));
			$array_data    = array_merge($custom_category,$specific_date_array,$poll_array,array("post_type" => "question","paged" => $paged,"posts_per_page" => $post_number));
		}else {
			$post_not_true = true;
			if ($first_one == $recent_questions_slug || $first_one == $recent_questions_slug_2) {
				$active_sticky        = true;
				$display_r            = discy_post_meta("question_display_r");
				$single_category_r    = discy_post_meta("question_single_category_r");
				$categories_r         = discy_post_meta("question_categories_r");
				$exclude_categories_r = discy_post_meta("question_exclude_categories_r");
				$questions_r          = discy_post_meta("question_questions_r");
				if ($display_r == "single_category") {
					$cats_post = array('tax_query' => array(array('taxonomy' => "question-category",'field' => 'id','terms' => $single_category_r,'operator' => 'IN')));
				}else if ($display_r == "categories") {
					$cats_post = array('tax_query' => array(array('taxonomy' => "question-category",'field' => 'id','terms' => $categories_r,'operator' => 'IN')));
				}else if ($display_r == "exclude_categories") {
					$cats_post = array('tax_query' => array(array('taxonomy' => "question-category",'field' => 'id','terms' => $exclude_categories_r,'operator' => 'NOT IN')));
				}else if ($display_r == "custom_posts") {
					$custom_posts = explode(",",$questions_r);
					$cats_post    = array('post__in' => $custom_posts);
				}else {
					$cats_post = array();
				}
				$custom_args   = array_merge($custom_category,$cats_post,$specific_date_array,array("post_type" => "question"));
				$show_sticky   = true;
			}
			$array_data = array_merge($custom_category,$orderby_array,$cats_post,$specific_date_array,array("post_type" => $post_type,"ignore_sticky_posts" => 1,"paged" => $paged,"posts_per_page" => $post_number));
		}
		if ((((isset($tab_category) && $tab_category == true) || (isset($wp_page_template) && $wp_page_template == "template-question.php") || (isset($wp_page_template) && $wp_page_template == "template-home.php"))) && ($orderby_post == "no_answer" || ($question_bump == "on" && $active_points == "on" && $orderby_post == "question_bump"))) {
			function discy_comments_filter ($where) {
				$where .= " AND comment_count = 0 ";
				return $where;
			}
			add_filter("posts_where","discy_comments_filter");
		}
		$pagination_show   = "yes";
		$show_custom_error = true;
	}else if (is_category()) {
		$array_data = array("posts_per_page" => $post_number,"cat" => $category_id,"post_type" => "post","paged" => $paged);
	}else if (is_post_type_archive("question") && is_archive("question")) {
		$poll_array = array("meta_query" => array($question_meta_query));
		if (isset($_GET["type"]) && $_GET["type"] == "poll") {
			$poll_array = array("ignore_sticky_posts" => 1,"meta_query" => array('relation' => 'AND',$question_meta_query,array("key" => "question_poll","value" => "on","compare" => "LIKE")));
		}
		$post_not_true = true;
		$array_data = array_merge($poll_array,array("post_type" => "question","paged" => $paged));
	}else if (is_tax("question-category")) {
		$question_numbers = array();
		if (isset($post_number) && $post_number > 0) {
			$question_numbers = array("posts_per_page" => $post_number);
		}
		$post_not_true = true;
		$array_data = array_merge($question_numbers,array("ignore_sticky_posts" => 1,"post_type" => "question","paged" => $paged,"tax_query" => array(array("taxonomy" => "question-category","field" => "id","terms" => $category_id)),"meta_query" => array($question_meta_query)));
	}else if (is_tax("question_tags")) {
		$post_not_true = true;
		$array_data = array_merge(array("ignore_sticky_posts" => 1,"post_type" => "question","paged" => $paged,"tax_query" => array(array("taxonomy" => "question_tags","field" => "id","terms" => $category_id,"operator" => "IN")),"meta_query" => array($question_meta_query)));
	}else if ($last_one == "questions" || $last_one == "posts") {
		$array_data = array("author" => $get_user_var,"post_type" => ($last_one == "questions"?"question":"post"),"paged" => $paged,"ignore_sticky_posts" => 1,"meta_query" => array($question_meta_query));
	}else if ($last_one == "polls") {
		$array_data = array("author" => $get_user_var,"post_type" => "question","paged" => $paged,"meta_query" => array('relation' => 'AND',$question_meta_query,array("key" => "question_poll","value" => "on","compare" => "=")));
	}else if ($last_one == "favorites") {
		$user_login    = get_userdata($get_user_var);
		$ask_me        = discy_options("ask_me");
		if ($ask_me == "on") {
			$old_favorites = get_user_meta($get_user_var,$user_login->user_login."_favorites",true);
			if (isset($old_favorites) && !empty($old_favorites)) {
				update_user_meta($get_user_var,$get_user_var."_favorites",$old_favorites);
				delete_user_meta($get_user_var,$user_login->user_login."_favorites");
			}
		}
		$_favorites    = get_user_meta($get_user_var,$get_user_var."_favorites",true);
		if (is_array($_favorites) && !empty($_favorites) && count($_favorites) > 0) {
			$array_data = array("post_type" => "question","paged" => $paged,"post__in" => $_favorites,"meta_query" => array($question_meta_query));
		}
	}else if ($ask_question_to_users == "on" && ($last_one == "asked" || $last_one == "asked-questions")) {
		if ($last_one == "asked") {
			$meta_asked = array("key" => "user_is_comment","value" => true,"compare" => "=");
		}else {
			$meta_asked = array("key" => "user_is_comment","compare" => "NOT EXISTS");
		}
		$array_data = array("post_type" => "question","paged" => $paged,"meta_query" => array(array_merge(array($meta_asked),array(array("type" => "numeric","key" => "user_id","value" => (int)$get_user_var,"compare" => "=")))));
	}else if ($ask_question_to_users == "on" && ($last_one == "paid-questions")) {
		$array_data = array("author" => $get_user_var,"post_type" => "question","paged" => $paged,"meta_query" => array('relation' => 'AND',$question_meta_query,array('type' => 'numeric',"key" => "_paid_question","value" => 'paid',"compare" => "=")));
	}else if ($last_one == "followed") {
		$following_questions_user = get_user_meta($get_user_var,"following_questions",true);
		if (is_array($following_questions_user) && !empty($following_questions_user) && count($following_questions_user) > 0) {
			$array_data = array("post_type" => "question","paged" => $paged,"post__in" => $following_questions_user,"meta_query" => array($question_meta_query));
		}
	}else if ($last_one == "followers-questions" || $last_one == "followers-posts") {
		$following_me = get_user_meta($get_user_var,"following_me",true);
		if (is_array($following_me) && count($following_me) > 0) {
			$array_data = array("post_type" => ($last_one == "followers-questions"?"question":"post"),"paged" => $paged,"author__in" => $following_me,"ignore_sticky_posts" => 1,"meta_query" => array($question_meta_query));
		}
	}

	$sticky_questions = get_option('sticky_questions');
	$post__not_in = array();
	if (isset($post_not_true) && $post_not_true == true && isset($sticky_questions) && is_array($sticky_questions) && !empty($sticky_questions)) {
		$post__not_in = array("post__not_in" => $sticky_questions);
		$array_data = array_merge($post__not_in,$array_data);
	}

	$k_ad_p = -1;
	if (isset($array_data) && is_array($array_data) && !empty($array_data)) {
		$wpqa_query = new WP_Query($array_data);
	}?>
	<section<?php echo ((isset($post_style) && $post_style == "style_3") || (isset($question_columns) && $question_columns == "style_2")?" class='section-post-with-columns'":"")?><?php echo ($last_one == "questions" || $last_one == "asked" || $last_one == "asked-questions" || $last_one == "paid-questions" || $last_one == "polls" || $last_one == "followed" || $last_one == "favorites" || $last_one == "followers-questions" || $last_one == "posts" || $last_one == "followers-posts"?" id='section-".wpqa_user_title()."'":"")?>>
		<?php if ($last_one == "favorites" && empty($_favorites)) {
			echo "<div class='alert-message warning'><i class='icon-flag'></i><p>".esc_html__("Still no questions at favorite.","discy")."</p></div>";
		}else if ($last_one == "followed" && empty($following_questions_user)) {
			echo "<div class='alert-message warning'><i class='icon-flag'></i><p>".esc_html__("Still no questions you followed.","discy")."</p></div>";
		}else {
			$page_tamplate   = (isset($page_tamplate)?$page_tamplate:'');
			$post_pagination = (isset($post_pagination)?$post_pagination:'');
			if ($page_tamplate != true) {
				$post_pagination = discy_options("post_pagination");
			}
			if (isset($custom_args) || (isset($wpqa_query) && $wpqa_query->have_posts()) || (have_posts() && empty($array_data))) :
				if (empty($wp_page_template) || (isset($wp_page_template) && $wp_page_template != "template-users.php" && $wp_page_template != "template-contact.php" && $wp_page_template != "template-faqs.php" && $wp_page_template != "template-categories.php" && $wp_page_template != "template-tags.php")) :
					$max_num_pages = (isset($wpqa_query->max_num_pages)?$wpqa_query->max_num_pages:"");
					$more_link = get_next_posts_link("",$max_num_pages);?>
					<h2 class="screen-reader-text"><?php echo esc_html__("Discy Latest ", "discy");printf("%s",(isset($its_question) && "question" == $its_question?esc_html__("Questions","discy"):esc_html__("Articles","discy")))?></h2>
					<div class="post-articles<?php echo (isset($its_question) && "question" == $its_question?" question-articles".(isset($question_columns) && $question_columns == "style_2"?" row":""):"").($post_pagination == "none"?" no-pagination":"").(empty($more_link)?" articles-no-pagination":"").(isset($post_style) && $post_style == "style_3"?" row":"")?>">
						<?php if (isset($show_sticky) && $show_sticky == true) {
							include locate_template("theme-parts/sticky-question.php");
							$active_sticky = false;
						}
				endif;
				if (!isset($sticky_only)) :
					if (isset($wpqa_query)) {
						if ($wpqa_query->have_posts()) :
							$wp_reset_postdata = true;
							while ($wpqa_query->have_posts()) : $wpqa_query->the_post();
								$k_ad_p++;
								include locate_template("theme-parts/content".(isset($its_question) && "question" == $its_question?"-question":"").".php");
							endwhile;
						else :
							include locate_template("theme-parts/content-none.php");
						endif;
					}else {
						if ( have_posts() ) :
							while (have_posts()) : the_post();
								$k_ad_p++;
								include locate_template("theme-parts/content".(isset($its_question) && "question" == $its_question?"-question":"").".php");
							endwhile;
						else :
							include locate_template("theme-parts/content-none.php");
						endif;
					}
				endif;
				if (empty($wp_page_template) || (isset($wp_page_template) && $wp_page_template != "template-users.php" && $wp_page_template != "template-contact.php" && $wp_page_template != "template-faqs.php" && $wp_page_template != "template-categories.php" && $wp_page_template != "template-tags.php")) :?>
					</div><!-- End post-articles -->
					<?php if (class_exists("WPQA")) {
						wpqa_pagination_load((isset($post_pagination)?$post_pagination:"pagination"),(isset($max_num_pages)?$max_num_pages:""),(isset($it_answer_pagination)?$it_answer_pagination:false),(isset($its_question)?$its_question:false),(isset($wpqa_query)?$wpqa_query:null));
					}
				endif;
			else :
				if (isset($show_custom_error) && $show_custom_error == true && (!isset($is_questions_sticky) || (isset($is_questions_sticky) && $is_questions_sticky != true))) {
					echo "<div class='alert-message warning'><i class='icon-flag'></i><p>".(isset($its_question) && $its_question == "question"?esc_html__("Still no questions.","discy"):esc_html__("Still no posts.","discy"))."</p></div>";
				}else {
					if (!is_author() && (!isset($is_questions_sticky) || (isset($is_questions_sticky) && $is_questions_sticky != true))) {
						include locate_template("theme-parts/content-none.php");
					}
				}
			endif;
		}
		
		if ((isset($wp_page_template) && ($wp_page_template == "template-question.php" || $wp_page_template == "template-home.php")) && ($orderby_post == "no_answer" || ($question_bump == "on" && $active_points == "on" && $orderby_post == "question_bump"))) {
			remove_filter("posts_where","discy_comments_filter");
		}
		
		$GLOBALS['wp_query'] = $GLOBALS['wp_the_query'];
		
		if (isset($wp_reset_postdata)) {
			wp_reset_postdata();
		}else {
			wp_reset_query();
		}?>
	</section><!-- End section -->
<?php }?>