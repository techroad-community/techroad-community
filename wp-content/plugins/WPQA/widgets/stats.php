<?php
/* Stats */
add_action( 'widgets_init', 'widget_stats_widget' );
function widget_stats_widget() {
	register_widget( 'Widget_Stats' );
}

class Widget_Stats extends WP_Widget {

	function __construct() {
		global $wpqa;
		$widget_ops = array( 'classname' => 'stats-widget' );
		$control_ops = array( 'id_base' => 'stats-widget' );
		parent::__construct( 'stats-widget',$wpqa->super_plugin_name().' - Stats', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		$stats_array = array("questions" => esc_html__("Questions","wpqa"),"answers" => esc_html__("Answers","wpqa"),"users" => esc_html__("Users","wpqa"),"posts" => esc_html__("Posts","wpqa"),"comments" => esc_html__("Comments","wpqa"),"best_answers" => esc_html__("Best Answers","wpqa"));
		extract( $args );
		$title   = apply_filters('widget_title', $instance['title'] );
		$stats   = $instance['stats'];
		$style   = $instance['style'];
		$divider = $instance['divider'];
		
		if (empty($divider) || $divider != "on") {
			$before_widget = str_replace('class="','class="widget-no-divider ',$before_widget);
		}
		  
		echo ($before_widget);
			if ($title) {
				echo ($title == "empty"?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_attr($title)).$after_title).($title == "empty"?"</div>":"");
			}else {
				echo "<h3 class='screen-reader-text'>".esc_html__("Stats","wpqa")."</h3>";
			}?>
			<div class="widget-wrap">
				<ul class="<?php echo ($style == "style_2"?"stats-inner-2":"stats-inner")?>">
					<?php if (isset($stats) && is_array($stats) && !empty($stats)) {
						foreach ($stats as $key => $value) {
							if (isset($value["value"]) && $value["value"] == $key) {?>
								<li class="stats-<?php echo ($value["value"])?>">
									<div>
										<?php if ($style == "style_2") {
											if ($value["value"] == "questions") {
												echo '<i class="icon-book-open"></i>';
											}else if ($value["value"] == "posts") {
												echo '<i class="icon-user"></i>';
											}else if ($value["value"] == "answers") {
												echo '<i class="icon-comment"></i>';
											}else if ($value["value"] == "comments") {
												echo '<i class="icon-chat"></i>';
											}else if ($value["value"] == "best_answers") {
												echo '<i class="icon-graduation-cap"></i>';
											}else if ($value["value"] == "users") {
												echo '<i class="icon-users"></i>';
											}
										}?>
										<span class="<?php echo ($style == "style_2"?"stats-text-2":"stats-text")?>"><?php echo ($stats_array[$value["value"]]).($style == "style_2"?" : ":"")?></span>
										<span class="<?php echo ($style == "style_2"?"stats-value-2":"stats-value")?>">
											<?php if ($value["value"] == "questions") {
												$question_count = wp_count_posts("question");
												echo wpqa_count_number((isset($question_count->publish)?$question_count->publish:0));
											}else if ($value["value"] == "posts") {
												echo wpqa_count_number(wp_count_posts("post")->publish);
											}else if ($value["value"] == "answers") {
												echo wpqa_count_number(wpqa_all_comments_of_post_type("question"));
											}else if ($value["value"] == "comments") {
												echo wpqa_count_number(wpqa_all_comments_of_post_type("post"));
											}else if ($value["value"] == "best_answers") {
												$best_answer_option = count(get_comments(array("status" => "approve",'post_type' => 'question',"meta_query" => array('relation' => 'AND',array("key" => "best_answer_comment","compare" => "=","value" => "best_answer_comment"),array("key" => "answer_question_user","compare" => "NOT EXISTS")))));
												echo (isset($best_answer_option) && $best_answer_option != "" && $best_answer_option > 0?wpqa_count_number($best_answer_option):0);
											}else if ($value["value"] == "users") {
												$count_users = count_users();
												echo wpqa_count_number($count_users["total_users"]);
											}?>
										</span>
									</div>
								</li>
							<?php }
						}
					}?>
				</ul>
			</div>
		<?php echo ($after_widget);
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>