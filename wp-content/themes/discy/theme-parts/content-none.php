<div class="no-results not-found">
	<?php $whats_post_type = (isset($its_question) && 'question' == $its_question?esc_html__('Questions','discy'):esc_html__('Posts','discy'));
	if (class_exists("WPQA")) {
		$templates = array("search.php");
		$wpqa_get_template = wpqa_get_template($templates,(isset($folder) && $folder != ""?$folder."/":""));
	}
	if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
		<h3><?php printf(esc_html__('Sorry, No %1$s Found.','discy'),$whats_post_type)?></h3>
		<p><?php printf( wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'discy' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>
	<?php elseif ( ( class_exists('WPQA') && wpqa_is_search() ) || is_search() ) : ?>
		<h3><?php esc_html_e( 'Sorry, No Results Found.', 'discy' ); ?></h3>
		<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'discy' ); ?></p>
		<?php isset($wpqa_get_template) ? include $wpqa_get_template:"";?>
	<?php elseif ( is_tax() || is_post_type_archive() || is_archive() ) :?>
		<h3><?php printf(esc_html__('Sorry, No %1$s Found.','discy'),$whats_post_type)?></h3>
		<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'discy' ); ?></p>
		<?php isset($wpqa_get_template) ? include $wpqa_get_template:"";
	else : ?>
		<h2><?php esc_html_e( '404', 'discy' ); ?></h2>
		<h3><?php esc_html_e( 'Oops! Page Not Found.', 'discy' ); ?></h3>
		<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'discy' ); ?></p>
		<?php isset($wpqa_get_template) ? include $wpqa_get_template:"";?>
	<?php endif; ?>
</div><!-- no-results -->