<?php

/* @author    2codeThemes
*  @package   WPQA/templates
*  @version   1.0
*/

$search_type     = wpqa_search_type();
$search_value    = wpqa_search();
$paged           = (get_query_var("paged") != ""?(int)get_query_var("paged"):(get_query_var("page") != ""?(int)get_query_var("page"):1));
$post_pagination = 'pagination';
$search_attrs    = wpqa_options("search_attrs");?>
<div class="section-all-search">
	<?php $show_search_form_filter = apply_filters("wpqa_show_search_form_filter",true);
	if ($show_search_form_filter == true) {
		include wpqa_get_template(array('search-form.php'));
	}

	if ($search_value != "" && ($search_type == "answers" && isset($search_attrs["answers"]["value"]) && $search_attrs["answers"]["value"] == "answers") || ($search_type == "comments" && isset($search_attrs["comments"]["value"]) && $search_attrs["comments"]["value"] == "comments")) {
		$args = array('search' => $search_value,"meta_query" => array(array("key" => "answer_question_private","compare" => "NOT EXISTS")),'post_type' => ($search_type == "answers"?"question":"post")
		);
		
		$comments_query = new WP_Comment_Query;
		$comments_all = $comments_query->query($args);
		
		$current = max(1,$paged);
		$post_number = apply_filters('wpqa_search_per_page',get_option("posts_per_page"));
		
		if (!empty($comments_all)) {
			$k_ad = -1;
			$pagination_args = array(
				'base'      => esc_url(add_query_arg('page','%#%')),
				'total'     => ceil(sizeof($comments_all)/$post_number),
				'current'   => $current,
				'show_all'  => false,
				'prev_text' => '<i class="icon-left-open"></i>',
				'next_text' => '<i class="icon-right-open"></i>',
			);
			
			$start = ($current - 1) * $post_number;
			$end = $start + $post_number;
			?>
			<div class="page-content commentslist">
				<ol class="commentlist clearfix">
					<?php $end = (sizeof($comments_all) < $end) ? sizeof($comments_all) : $end;
					for ($k = $start;$k < $end ;++$k ) {$k_ad++;
						$comment = $comments_all[$k];
						$yes_private = wpqa_private($comment->comment_post_ID,get_post($comment->comment_post_ID)->post_author,get_current_user_id());
						if ($yes_private == 1) {
								$comment_id = esc_attr($comment->comment_ID);
								wpqa_comment($comment,"","",(get_post_type($comment->comment_post_ID) == "question"?"answer":"comment"),"",$k_ad);?>
							</li>
						<?php }else {?>
							<li class="comment">
								<div class="comment-body clearfix">
									<?php echo '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry it a private answer.","wpqa").'</p></div>';?>
								</div>
							</li>
						<?php }
					}?>
				</ol>
			</div>
		<?php }else {
			include locate_template("theme-parts/search-none.php");
		}
		if ($comments_all && $pagination_args["total"] > 1) {?>
			<div class="main-pagination"><div class='pagination'><?php echo (paginate_links($pagination_args) != ""?paginate_links($pagination_args):"")?></div></div>
		<?php }
	}else if ($search_value != "" && $search_type == "users" && isset($search_attrs["users"]["value"]) && $search_attrs["users"]["value"] == "users") {
		include locate_template("theme-parts/users.php");
	}else if ($search_value != "" && ($search_type == "question-category" && isset($search_attrs["question-category"]["value"]) && $search_attrs["question-category"]["value"] == "question-category") || ($search_type == "category" && isset($search_attrs["category"]["value"]) && $search_attrs["category"]["value"] == "category")) {
		include locate_template("theme-parts/categories.php");
	}else if ($search_value != "" && ($search_type == "question_tags" && isset($search_attrs["question_tags"]["value"]) && $search_attrs["question_tags"]["value"] == "question_tags") || ($search_type == "post_tag" && isset($search_attrs["post_tag"]["value"]) && $search_attrs["post_tag"]["value"] == "post_tag")) {
		include locate_template("theme-parts/tags.php");
	}else {
		$search_value = apply_filters("wpqa_filter_search_value",$search_value);
		if ($search_value != "") {
			$meta_query = array();
			if ($search_type == "posts" && isset($search_attrs["posts"]["value"]) && $search_attrs["posts"]["value"] == "posts") {
				$post_type_array = array('post');
			}else {
				$its_question = "question";
				$meta_query = array("meta_query" => array(array("key" => "question_private","compare" => "NOT EXISTS")));
				$post_type_array = array('question');
			}
			
			$array_data = array_merge(array('s' => $search_value,'paged' => $paged,'post_type' => $post_type_array),$meta_query);
			$array_data = apply_filters("wpqa_array_data_filter",$array_data);
			$wpqa_query = new WP_Query($array_data);
			$pagination_show = "yes";?>
			<section>
				<?php if ( isset($wpqa_query) && $wpqa_query->have_posts() ) :
					$k_ad_p = 0;
					global $post;
					$more_link = get_next_posts_link("");?>
					<h2 class="screen-reader-text"><?php echo esc_html__("Search results", "wpqa")?></h2>
					<div class="post-articles<?php echo (isset($its_question) && $its_question == "question"?" question-articles".(isset($question_columns) && $question_columns == "style_2"?" row":""):"").($post_pagination == "none"?" no-pagination":"").(empty($more_link)?" articles-no-pagination":"")?>">
						<?php while ($wpqa_query->have_posts()) : $wpqa_query->the_post();
							include locate_template("includes/".($post->post_type == "question"?"question":"loop")."-setting.php");
							include locate_template("theme-parts/content".($post->post_type == "question"?"-question":"").".php");
							$k_ad_p++;
						endwhile;?>
					</div><!-- End post-articles -->
					<?php 
					wp_reset_postdata();
					wpqa_pagination_load((isset($post_pagination)?$post_pagination:"pagination"),(isset($max_num_pages)?$max_num_pages:""),(isset($it_answer_pagination)?$it_answer_pagination:false),(isset($its_question)?$its_question:false),(isset($wpqa_query)?$wpqa_query:null));
				else :
					include locate_template("theme-parts/search-none.php");
				endif;?>
			</section><!-- End section -->
		<?php }
	}?>
</div>