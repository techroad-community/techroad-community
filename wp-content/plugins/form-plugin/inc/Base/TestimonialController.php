<?php 
/**
 * @package  brandhPlugin
 */
namespace Inc\Base;

use Inc\Api\SettingsApi;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\TestimonialCallbacks;

/**
* 
*/
class TestimonialController extends BaseController
{
	public $settings;

	public $callbacks;

	public function register()
	{
		if ( ! $this->activated( 'form_manager' ) ) return;

		$this->settings = new SettingsApi();

		$this->callbacks = new TestimonialCallbacks();

		add_action( 'init', array( $this, 'testimonial_cpt' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );
		add_action( 'manage_testimonial_posts_columns', array( $this, 'set_custom_columns' ) );
		add_action( 'manage_testimonial_posts_custom_column', array( $this, 'set_custom_columns_data' ), 10, 2 );
		add_filter( 'manage_edit-testimonial_sortable_columns', array( $this, 'set_custom_columns_sortable' ) );

		$this->setShortcodePage();

		add_shortcode( 'techroad-form', array( $this, 'testimonial_form' ) );
		add_shortcode( 'testimonial-slideshow', array( $this, 'testimonial_slideshow' ) );
		add_action( 'wp_ajax_submit_testimonial', array( $this, 'submit_testimonial' ) );
		add_action( 'wp_ajax_nopriv_submit_testimonial', array( $this, 'submit_testimonial' ) );
	}

	public function submit_testimonial()
	{
		if (! DOING_AJAX || ! check_ajax_referer('testimonial-nonce', 'nonce') ) {
			return $this->return_json('error');
		}

		$name = sanitize_text_field($_POST['name']);
		$email = sanitize_email($_POST['email']);
		
		$message = sanitize_textarea_field($_POST['message']);
		$phone = sanitize_textarea_field($_POST['phone']);
		$care = sanitize_textarea_field($_POST['care']);

		$data = array(
			'name' => $name,
			'email' => $email,
			'phone' => $phone,
			'care' => $care,
			'approved' => 0,
			'featured' => 0,
		);

		$args = array(
			'post_title' => 'Form from ' . $name,
			'post_content' => $message,
			'post_author' => 1,
			'post_status' => 'publish',
			'post_type' => 'form',
			'meta_input' => array(
				'_brandh_form_key' => $data
			)
		);

		$postID = wp_insert_post($args);

		if ($postID) {
			return $this->return_json('success');
		}

		return $this->return_json('error');
	}

	public function return_json($status)
	{
		$return = array(
			'status' => $status
		);
		wp_send_json($return);

		wp_die();
	}

	public function testimonial_form()
	{
		ob_start();
		echo "<link rel=\"stylesheet\" href=\"$this->plugin_url/assets/form.css\" type=\"text/css\" media=\"all\" />";
		require_once( "$this->plugin_path/templates/contact-form.php" );
		echo "<script src=\"$this->plugin_url/assets/form.js\"></script>";
		return ob_get_clean();
	}

	public function testimonial_slideshow()
	{
		ob_start();
		echo "<link rel=\"stylesheet\" href=\"$this->plugin_url/assets/slider.css\" type=\"text/css\" media=\"all\" />";
		require_once( "$this->plugin_path/templates/slider.php" );
		echo "<script src=\"$this->plugin_url/assets/slider.js\"></script>";
		return ob_get_clean();
	}

	public function setShortcodePage()
	{
		$subpage = array(
			array(
				'parent_slug' => 'edit.php?post_type=testimonial',
				'page_title' => 'Shortcodes',
				'menu_title' => 'Shortcodes',
				'capability' => 'manage_options',
				'menu_slug' => 'brandh_form_shortcode',
				'callback' => array( $this->callbacks, 'shortcodePage' )
			)
		);

		$this->settings->addSubPages( $subpage )->register();
	}

	public function testimonial_cpt ()
	{
		$labels = array(
			'name' => 'Form',
			'singular_name' => 'Testimonial'
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'has_archive' => false,
			'menu_icon' => 'dashicons-testimonial',
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'supports' => array( 'title', 'editor' ),
			'show_in_rest' => true,	
			'menu_position' => 111
		);

		register_post_type ( 'testimonial', $args );
	}

	public function add_meta_boxes()
	{
		add_meta_box(
			'testimonial_author',
			'Testimonial Options',
			array( $this, 'render_features_box' ),
			'testimonial',
			'side',
			'default'
		);
	}

	public function render_features_box($post)
	{
		wp_nonce_field( 'brandh_form', 'brandh_form_nonce' );

		$data = get_post_meta( $post->ID, '_brandh_form_key', true );
		$name = isset($data['name']) ? $data['name'] : '';
		$email = isset($data['email']) ? $data['email'] : '';
        $phone = isset($data['phone']) ? $data['phone'] : '';
        $care = isset($data['care']) ? $data['care'] : '';
		$approved = isset($data['approved']) ? $data['approved'] : false;
		$featured = isset($data['featured']) ? $data['featured'] : false;
		?>
		<p>
			<label class="meta-label" for="brandh_form_author">Author Name</label>
			<input type="text" id="brandh_form_author" name="brandh_form_author" class="widefat" value="<?php echo esc_attr( $name ); ?>">
		</p>
		<p>
			<label class="meta-label" for="brandh_form_email">Author Email</label>
			<input type="email" id="brandh_form_email" name="brandh_form_email" class="widefat" value="<?php echo esc_attr( $email ); ?>">
		</p>
        <p>
            <label class="meta-label" for="brandh_form_phone">Author Phone</label>
            <input type="text" id="brandh_form_phone" name="brandh_form_phone" class="widefat" value="<?php echo esc_attr( $phone ); ?>">
        </p>
        <p>
            <label class="meta-label" for="brandh_form_care">Author Care</label>
            <input type="text" id="brandh_form_care" name="brandh_form_care" class="widefat" value="<?php echo esc_attr( $care ); ?>">
        </p>
		
		<div class="meta-container">
			<label class="meta-label w-50 text-left" for="brandh_form_approved">Is Contaced</label>
			<div class="text-right w-50 inline">
				<div class="ui-toggle inline"><input type="checkbox" id="brandh_form_approved" name="brandh_form_approved" value="1" <?php echo $approved ? 'checked' : ''; ?>>
					<label for="brandh_form_approved"><div></div></label>
				</div>
			</div>
		</div>
		<div class="meta-container">
			<label class="meta-label w-50 text-left" for="brandh_form_featured">Is Happy Call</label>
			<div class="text-right w-50 inline">
				<div class="ui-toggle inline"><input type="checkbox" id="brandh_form_featured" name="brandh_form_featured" value="1" <?php echo $featured ? 'checked' : ''; ?>>
					<label for="brandh_form_featured"><div></div></label>
				</div>
			</div>
		</div>
		<?php
	}

	public function save_meta_box($post_id)
	{
		if (! isset($_POST['brandh_form_nonce'])) {
			return $post_id;
		}

		$nonce = $_POST['brandh_form_nonce'];
		if (! wp_verify_nonce( $nonce, 'brandh_form' )) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if (! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$data = array(
			'name' => sanitize_text_field( $_POST['brandh_form_author'] ),
			'email' => sanitize_email( $_POST['brandh_form_email'] ),
            'phone' => sanitize_text_field( $_POST['brandh_form_phone'] ),
            'care' => sanitize_text_field( $_POST['brandh_form_care'] ),
			'approved' => isset($_POST['brandh_form_approved']) ? 1 : 0,
			'featured' => isset($_POST['brandh_form_featured']) ? 1 : 0,
		);
		update_post_meta( $post_id, '_brandh_form_key', $data );
	}

	public function set_custom_columns($columns)
	{
		$title = $columns['title'];
		$date = $columns['date'];
		unset( $columns['title'], $columns['date'] );

		$columns['title'] = $title;
		$columns['name'] = 'Customer Name';
		$columns['approved'] = 'Is Contacted';
		$columns['featured'] = 'Is Happy Call';
		
		$columns['date'] = $date;

		return $columns;
	}

	public function set_custom_columns_data($column, $post_id)
	{
		$data = get_post_meta( $post_id, '_brandh_form_key', true );
		$name = isset($data['name']) ? $data['name'] : '';
		$email = isset($data['email']) ? $data['email'] : '';
        $phone = isset($data['phone']) ? $data['phone'] : '';
        $care = isset($data['care']) ? $data['care'] : '';
		$approved = isset($data['approved']) && $data['approved'] === 1 ? '<strong>YES</strong>' : 'NO';
		$featured = isset($data['featured']) && $data['featured'] === 1 ? '<strong>YES</strong>' : 'NO';

		switch($column) {
			case 'name':
				echo '<strong>' . $name . '</strong><br/><a href="mailto:' . $email . '">' . $email . '</a>';
				break;

			case 'approved':
				echo $approved;
				break;

			case 'featured':
				echo $featured;
				break;
		}
	}

	public function set_custom_columns_sortable($columns)
	{
		$columns['name'] = 'name';
		$columns['approved'] = 'approved';
		$columns['featured'] = 'featured';

		return $columns;
	}
}