<?php
/* Buttons */
add_action( 'widgets_init', 'widget_wpqa_widget' );
function widget_wpqa_widget() {
	register_widget( 'Widget_Ask' );
}

class Widget_Ask extends WP_Widget {

	function __construct() {
		global $wpqa;
		$widget_ops = array( 'classname' => 'ask-widget'  );
		$control_ops = array( 'id_base' => 'ask-widget' );
		parent::__construct( 'ask-widget',$wpqa->super_plugin_name().' - Buttons', $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );?>
		<div class="widget widget_ask">
			<?php $button = esc_attr($instance['button']);
			if ($button == "custom") {
				$filter_class = $button_class = "";
				$button_target = esc_attr($instance['button_target']);
				$button_link = esc_attr($instance['button_link']);
				$button_text = esc_attr($instance['button_text']);
			}else if ($button == "post") {
				$filter_class = "post";
				$button_class = "wpqa-post";
				$button_link = wpqa_add_post_permalink();
				$button_text = esc_html__("Add A New Post","wpqa");
			}else if (!is_user_logged_in() && $button == "login") {
				$filter_class = "login";
				$button_class = "login-panel";
				$button_link = wpqa_login_permalink();
				$button_text = esc_html__("Login","wpqa");
			}else if (!is_user_logged_in() && $button == "signup") {
				$filter_class = "signup";
				$button_class = "signup-panel";
				$button_link = wpqa_signup_permalink();
				$button_text = esc_html__("Create A New Account","wpqa");
			}else {
				$filter_class = "question";
				$button_class = "wpqa-question";
				$button_link = wpqa_add_question_permalink();
				$button_text = esc_html__("Ask A Question","wpqa");
			}
			$button_target = ($button == "custom" && isset($button_target) && $button_target == "new_page"?"_blank":"_self");
			echo '<a target="'.esc_attr($button_target).'" href="'.esc_url($button_link).'" class="button-default '.$button_class.apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_'.$filter_class,'').'">'.$button_text.'</a>';?>
		</div>
	<?php }

	public function form( $instance ) {
		/* Save Button */
	}
}?>