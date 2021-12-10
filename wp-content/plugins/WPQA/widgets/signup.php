<?php
/* Signup */
add_action( 'widgets_init', 'widget_signup_widget' );
function widget_signup_widget() {
	register_widget( 'Widget_Signup' );
}

class Widget_Signup extends WP_Widget {

	function __construct() {
		global $wpqa;
		$widget_ops = array( 'classname' => 'signup-widget' );
		$control_ops = array( 'id_base' => 'signup-widget' );
		parent::__construct( 'signup-widget',$wpqa->super_plugin_name().' - Signup', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title'] );
		if (!is_user_logged_in()) {
			echo ($before_widget);
				if ($title) {
					echo ($title == "empty"?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_attr($title)).$after_title).($title == "empty"?"</div>":"");
				}else {
					echo "<h3 class='screen-reader-text'>".esc_html__("Signup","wpqa")."</h3>";
				}?>
				<div class="widget-wrap">
					<div class="widget_signup">
						<?php echo do_shortcode("[wpqa_signup]")?>
					</div>
				</div>
			<?php echo ($after_widget);
		}
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>