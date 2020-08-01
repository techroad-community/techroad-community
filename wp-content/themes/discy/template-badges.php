<?php /* Template Name: Badges */
get_header();
$page_id        = $post_id_main = $post->ID;
$active_points  = discy_options("active_points");
$badges_details = discy_options("badges_details");
$badges_style   = discy_options("badges_style");
$badges_groups  = discy_options("badges_groups");
if ($badges_style == "by_groups_points") {
	$badges = discy_options("badges_groups_points");
}else {
	$badges = discy_options("badges");
}?>
	<div class="page-sections">
		<?php if ($active_points == "on") {
			$points_details        = discy_options("points_details");
			$point_add_question    = discy_options("point_add_question");
			$point_best_answer     = discy_options("point_best_answer");
			$point_voting_question = discy_options("point_voting_question");
			$point_add_comment     = discy_options("point_add_comment");
			$point_voting_answer   = discy_options("point_voting_answer");
			$point_following_me    = discy_options("point_following_me");
			$point_new_user        = discy_options("point_new_user");
			$point_add_post        = discy_options("point_add_post");
			$points_array          = array("point_add_question" => $point_add_question,"point_best_answer" => $point_best_answer,"point_voting_question" => $point_voting_question,"point_add_comment" => $point_add_comment,"point_voting_answer" => $point_voting_answer,"point_following_me" => $point_following_me,"point_new_user" => $point_new_user,"point_add_post" => $point_add_post);
			arsort($points_array);
			if (isset($points_array) && is_array($points_array)) {?>
				<div class="page-section">
					<div class="page-wrap-content">
						<h2 class="post-title-3"><i class="icon-bucket"></i><?php esc_html_e("Points System","discy")?></h2>
						<?php if (isset($points_details) && $points_details != "") {?>
							<div class="post-content-text"><p><?php echo discy_kses_stip($points_details)?></p></div>
						<?php }?>
						<div class="points-section">
							<ul class="row">
								<?php foreach ($points_array as $key => $value) {
									if ($value > 0) {?>
										<li class="col col4">
											<div class="point-section">
												<div class="point-div">
													<i class="icon-bucket"></i>
													<span><?php echo discy_count_number($value)?></span><?php esc_html_e("Points","discy")?>
												</div>
												<p><?php if ($key == "point_add_question") {
													esc_html_e("Add a new question to the community.","discy");
												}else if ($key == "point_best_answer") {
													esc_html_e("Choosing the best answer for a question.","discy");
												}else if ($key == "point_voting_question") {
													esc_html_e("Voting a question in the community.","discy");
												}else if ($key == "point_add_comment") {
													esc_html_e("Add an answer in the community.","discy");
												}else if ($key == "point_voting_answer") {
													esc_html_e("Voting an answer in the community.","discy");
												}else if ($key == "point_following_me") {
													esc_html_e("Following a user in the community.","discy");
												}else if ($key == "point_new_user") {
													esc_html_e("Points for a new user in the community.","discy");
												}else if ($key == "point_add_post") {
													esc_html_e("Add a new post to the community.","discy");
												}?></p>
											</div>
										</li>
									<?php }
								}?>
							</ul>
						</div>
					</div><!-- End page-wrap-content -->
				</div><!-- End page-section -->
			<?php }
		}
		
		if (($badges_style != "by_groups" && isset($badges) && is_array($badges)) || ($badges_style == "by_groups" && isset($badges_groups) && is_array($badges_groups) && isset($badges_details) && $badges_details != "")) {?>
			<div class="page-section">
				<div class="page-wrap-content">
					<h2 class="post-title-3"><i class="icon-trophy"></i><?php esc_html_e("Badges System","discy")?></h2>
					<?php if (isset($badges_details) && $badges_details != "") {?>
						<div class="post-content-text"><p><?php echo discy_kses_stip($badges_details)?></p></div>
					<?php }
					if ($badges_style != "by_groups") {?>
						<div class="badges-section">
							<ul>
								<?php foreach ($badges as $badges_k => $badges_v) {?>
									<li>
										<div class="badge-section">
											<div class="badge-div">
												<span class="badge-span" style="background-color: <?php echo esc_html($badges_v["badge_color"])?>;"><?php echo strip_tags(stripslashes($badges_v["badge_name"]),"<i>")?></span>
												<div class="point-div">
													<i class="icon-bucket"></i>
													<span><?php echo discy_count_number($badges_v["badge_points"])?></span><?php esc_html_e("Points","discy")?>
												</div>
											</div>
											<p><?php echo discy_kses_stip($badges_v["badge_details"])?></p>
										</div>
									</li>
								<?php }?>
							</ul>
						</div>
					<?php }?>
				</div><!-- End page-wrap-content -->
			</div><!-- End page-section -->
		<?php }?>
	</div><!-- End page-sections -->
<?php get_footer();?>