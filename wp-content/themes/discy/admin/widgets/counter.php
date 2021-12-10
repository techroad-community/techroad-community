<?php
/* Counter */
add_action( 'widgets_init', 'widget_counter_widget' );
function widget_counter_widget() {
	register_widget( 'Widget_Counter' );
}
class Widget_Counter extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget-statistics' );
		$control_ops = array( 'id_base' => 'widget_counter' );
		parent::__construct( 'widget_counter',discy_theme_name.' - Social counter', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title      = apply_filters('widget_title', $instance['title'] );
		$facebook   = esc_attr($instance['facebook']);
		$twitter    = esc_attr($instance['twitter']);
		$pinterest  = esc_attr($instance['pinterest']);
		$vimeo      = esc_attr($instance['vimeo']);
		$instagram  = esc_attr($instance['instagram']);
		$dribbble   = esc_attr($instance['dribbble']);
		$youtube    = esc_attr($instance['youtube']);
		$soundcloud = esc_attr($instance['soundcloud']);
		$behance    = esc_attr($instance['behance']);
		$envato     = esc_attr($instance['envato']);
		$github     = esc_attr($instance['github']);
		
		echo ($before_widget);
			if ($title) {
				echo ($title == "empty"?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_attr($title)).$after_title).($title == "empty"?"</div>":"");
			}else {
				echo "<h3 class='screen-reader-text'>".esc_html__("Social counter","discy")."</h3>";
			}
			
			$s_cs = array(
				"facebook"   => ($facebook != ""?array(discy_counter_facebook($facebook, 'link'),discy_counter_facebook($facebook),esc_html__("Fans","discy")):""),
				"twitter"    => ($twitter != ""?array("https://twitter.com/".$twitter,discy_counter_twitter($twitter),esc_html__("Followers","discy")):""),
				"pinterest"  => ($pinterest != ""?array($pinterest,discy_counter_pinterest($pinterest),esc_html__("Followers","discy")):""),
				"vimeo"      => ($vimeo != ""?array(discy_counter_vimeo($vimeo, 'link'),discy_counter_vimeo($vimeo),esc_html__("Subscribers","discy")):""),
				"instagram"  => ($instagram != ""?array(discy_counter_instagram($instagram, 'link'),discy_counter_instagram($instagram),esc_html__("Followers","discy"),"instagrem"):""),
				"dribbble"   => ($dribbble != ""?array(discy_counter_dribbble($dribbble, 'link'),discy_counter_dribbble($dribbble),esc_html__("Followers","discy")):""),
				"youtube"    => ($youtube != ""?array("https://www.youtube.com/channel/".$youtube,discy_counter_youtube($youtube),esc_html__("Followers","discy"),"play"):""),
				"soundcloud" => ($soundcloud != ""?array(discy_counter_soundcloud($soundcloud, 'link'),discy_counter_soundcloud($soundcloud),esc_html__("Followers","discy")):""),
				"behance"    => ($behance != ""?array(discy_counter_behance($behance, 'link'),discy_counter_behance($behance),esc_html__("Followers","discy")):""),
				"envato"     => ($envato != ""?array(discy_counter_envato($envato, 'link'),discy_counter_envato($envato),esc_html__("Followers","discy"),"leaf"):""),
				"github"     => ($github != ""?array(discy_counter_github($github, 'link'),discy_counter_github($github),esc_html__("Followers","discy")):""),
			);?>
			<div class="widget-wrap">
				<ul class="social-background">
					<?php if (isset($s_cs) && is_array($s_cs)) {
						foreach ($s_cs as $s_k => $s_v) {
							if (is_array($s_v)) {?>
								<li class="social-<?php echo esc_attr($s_k)?>">
									<a href="<?php echo esc_url($s_v[0])?>" target="_blank">
										<i class="<?php echo ($s_k == "envato"?"icon-leaf":"icon-".esc_attr((isset($s_v[3]) && $s_v[3] != ""?$s_v[3]:$s_k)))?>"></i>
										<span class="social-content">
											<span class="social-followers"><?php echo discy_count_number((int)$s_v[1])?></span>
											<span class="social-text"><?php echo ($s_v[2])?></span>
										</span>
									</a>
								</li>
							<?php }
						}
					}?>
				</ul>
				<div class="clearfix"></div>
			</div>
		<?php echo ($after_widget);
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>