<?php
/* Questions Categories */
add_action( 'widgets_init', 'widget_questions_categories_widget' );
function widget_questions_categories_widget() {
	register_widget( 'Widget_Questions_Categories' );
}

function wpqa_term_post_count($taxonomy = 'category',$term = '',$args = array()) {
	$cat = get_terms(array('taxonomy' => $taxonomy,'term_taxonomy_id' => $term,'hide_empty' => 1));
    $count = (isset($cat[0]->count)?(int)$cat[0]->count:0);
    $args = array('child_of' => $term,);
    $tax_terms = get_terms($taxonomy,$args);
    foreach ($tax_terms as $tax_term) {
        $count +=$tax_term->count;
    }
	return $count;
}

class Widget_Questions_Categories extends WP_Widget {

	function __construct() {
		global $wpqa;
		$widget_ops = array( 'classname' => 'questions_categories-widget' );
		$control_ops = array( 'id_base' => 'questions_categories-widget' );
		parent::__construct( 'questions_categories-widget',$wpqa->super_plugin_name().' - Questions Categories', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title            = apply_filters('widget_title', $instance['title'] );
		$questions_counts = esc_attr($instance['questions_counts']);
		$show_child       = esc_attr($instance['show_child']);
		echo ($before_widget);
			if ($title) {
				echo ($title == "empty"?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_attr($title)).$after_title).($title == "empty"?"</div>":"");
			}else {
				echo "<h3 class='screen-reader-text'>".esc_html__("Questions Categories","wpqa")."</h3>";
			}?>
			<div class="widget-wrap">
				<div class="widget_questions_categories">
					<?php if ($show_child == "on") {?>
						<div class="widget_child_categories">
							<div class="categories-toggle-accordion">
					<?php }?>
						<ul>
							<?php $args = array(
							'parent'       => ($show_child == "on"?0:""),
							'orderby'      => 'name',
							'order'        => 'ASC',
							'hide_empty'   => false,
							'hierarchical' => 1,
							'taxonomy'     => 'question-category',
							'pad_counts'   => false);
							$options_categories = get_categories($args);
							foreach ($options_categories as $category) {
								$count = wpqa_term_post_count('question-category',$category->cat_ID,array('post_type' => 'question'));
								if ($show_child == "on") {
									$children = get_terms('question-category',array('parent' => $category->cat_ID,'hide_empty' => false));
								}?>
								<li>
									<?php if ($show_child == "on" && isset($children) && is_array($children) && !empty($children)) {?>
										<h4 class="accordion-title">
									<?php }?>
										<a<?php echo ($show_child == "on"?' class="'.(isset($children) && is_array($children) && !empty($children)?"link-child":"link-not-child").'"':'')?> href="<?php echo get_term_link($category->slug,'question-category')?>"><?php echo esc_html($category->name);
											if ($questions_counts == "on") {?>
												<span class="question-category-main"> <span>(</span> <span class="question-category-span"><?php echo esc_html($count)."</span> <span>";
												if ($count == 1) {
													esc_html_e("Question","wpqa");
												}else {
													esc_html_e("Questions","wpqa");
												}?></span> <span>)</span> </span>
											<?php }?>
											<i></i>
										</a>
									<?php if ($show_child == "on" && isset($children) && is_array($children) && !empty($children)) {?>
										</h4>
									<?php }
									if ($show_child == "on" && isset($children) && is_array($children) && !empty($children)) {?>
										<div class="accordion-inner">
											<ul>
												<?php $args = array(
												'child_of'   => $category->cat_ID,
												'orderby'    => 'name',
												'order'      => 'ASC',
												'hide_empty' => false,
												'taxonomy'   => 'question-category',
												'pad_counts' => false);
												$options_childs = get_categories($args);
												$count = wpqa_term_post_count('question-category',$category->cat_ID,array('post_type' => 'question'));
												foreach ($options_childs as $category) {?>
													<li>
														<a href="<?php echo get_term_link($category->slug,'question-category')?>"><?php echo esc_html($category->name);
															if ($questions_counts == "on") {?>
																<span class="question-category-main"> <span>(</span> <span class="question-category-span"><?php echo esc_html($count)."</span> <span>";
																if ($count == 1) {
																	esc_html_e("Question","wpqa");
																}else {
																	esc_html_e("Questions","wpqa");
																}?></span> <span>)</span> </span>
															<?php }?>
														</a>
													</li>
												<?php }?>
											</ul>
										</div>
									<?php }?>	
								</li>
							<?php }?>
						</ul>
					<?php if ($show_child == "on") {?>
							</div>
						</div>
					<?php }?>
				</div>
			</div>
		<?php echo ($after_widget);
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>