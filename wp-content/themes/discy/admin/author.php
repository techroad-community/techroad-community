<?php /* Author options */
function discy_admin_author($user_id = "") {
	
	$user = get_userdata($user_id);
	$get_current_user_id = get_current_user_id();
	
	$options = array();
	$prefix = discy_author."_";
	
	$options[] = array(
		'name' => esc_html__('Author Setting','discy'),
		'type' => 'heading-2'
	);

	$options[] = array(
		'id'    => 'from_admin',
		'std'   => 'yes',
		'type'  => 'hidden',
		'unset' => 'unset',
	);

	$options = apply_filters('discy_options_before_author_setting',$options);
	
	if (current_user_can('upload_files')) {
		$user_meta_avatar = discy_options("user_meta_avatar");
		$user_meta_avatar = apply_filters("wpqa_user_meta_avatar",$user_meta_avatar);
		$user_meta_avatar = ($user_meta_avatar != ""?$user_meta_avatar:"you_avatar");
		$options[] = array(
			'name' => esc_html__('Your avatar','discy'),
			'id'   => $user_meta_avatar,
			'type' => 'upload'
		);

		$cover_image = discy_options("cover_image");
		if ($cover_image == "on") {
			$user_meta_cover = discy_options("user_meta_cover");
			$user_meta_cover = apply_filters("wpqa_user_meta_cover",$user_meta_cover);
			$user_meta_cover = ($user_meta_cover != ""?$user_meta_cover:"you_cover");
			$options[] = array(
				'name' => esc_html__('Your cover','discy'),
				'id'   => $user_meta_cover,
				'type' => 'upload'
			);
		}
	}
	
	$options[] = array(
		'name' => esc_html__('Add profile credential','discy'),
		'id'   => 'profile_credential',
		'type' => 'text',
	);
	
	$options[] = array(
		'name'    => esc_html__('Country','discy'),
		'id'      => 'country',
		'first'   => esc_html__('Select a country&hellip;','discy'),
		'type'    => 'select',
		'options' => apply_filters('wpqa_get_countries',false)
	);
	
	$options[] = array(
		'name' => esc_html__('City','discy'),
		'id'   => 'city',
		'type' => 'text',
	);
	
	$options[] = array(
		'name' => esc_html__('Age','discy'),
		'id'   => 'age',
		'type' => 'date',
		'js'   => array("changeMonth" => true,"changeYear" => true,"yearRange" => "-90:+00","dateFormat" => "yy-mm-dd"),
	);
	
	$options[] = array(
		'name' => esc_html__('Phone','discy'),
		'id'   => 'phone',
		'type' => 'text',
	);
	
	$gender_other = discy_options("gender_other");
	$gender_other = ($gender_other == "on"?array('3' => esc_html__('Other','discy')):array());
	$gender_options = array('1' => esc_html__('Male','discy'),'2' => esc_html__('Female','discy'))+$gender_other;
	
	$options[] = array(
		'name'    => esc_html__('Gender','discy'),
		'id'      => 'gender',
		'type'    => 'radio',
		'options' => $gender_options
	);
	
	$options[] = array(
		'name' => esc_html__('Check if you need this user is verified user.','discy'),
		'id'   => 'verified_user',
		'type' => 'checkbox',
	);
	
	$options[] = array(
		'name' => esc_html__('Show follow up email?','discy'),
		'id'   => 'follow_email',
		'type' => 'checkbox',
	);
	
	$options[] = array(
		'name' => esc_html__('Show your private pages for all the users? (Points, favorite and followed pages).','discy'),
		'id'   => 'show_point_favorite',
		'type' => 'checkbox',
	);
	
	$send_email_question_groups = discy_options("send_email_question_groups");
	if (is_array($send_email_question_groups) && isset($user->roles[0]) && in_array($user->roles[0],$send_email_question_groups)) {
		$options[] = array(
			'name' => esc_html__('Received email when any one add a new question','discy'),
			'id'   => 'received_email',
			'type' => 'checkbox',
		);
	}
	
	$active_message = discy_options("active_message");
	if ($active_message == "on") {
		$options[] = array(
			'name' => esc_html__('Do you need to receive message from another users?','discy'),
			'id'   => 'received_message',
			'type' => 'checkbox',
		);
	}
	
	if (is_super_admin($get_current_user_id) && !is_super_admin($user->ID) && $active_message == "on") {
		$options[] = array(
			'name' => esc_html__('Do you need this user blocked to send messages?','discy'),
			'id'   => 'block_message',
			'type' => 'checkbox',
		);
	}
	
	$active_points = discy_options("active_points");
	if (is_super_admin($get_current_user_id) && $active_points == "on") {
		$options[] = array(
			'name'    => esc_html__('Add or remove points for the user','discy'),
			'id'      => 'add_remove_point',
			'type'    => 'select',
			'unset'   => 'unset',
			'options' => array('add' => esc_html__('Add','discy'),'remove' => esc_html__('Remove','discy'))
		);
		
		$options[] = array(
			'name'  => esc_html__('The points','discy'),
			'id'    => 'the_points',
			'type'  => 'text',
			'unset' => 'unset',
		);
		
		$options[] = array(
			'name'  => esc_html__('The reason','discy'),
			'id'    => 'the_reason',
			'type'  => 'text',
			'unset' => 'unset',
		);
	}
	
	if ($get_current_user_id > 0 && $get_current_user_id != $user_id && is_super_admin($get_current_user_id)) {
		$options[] = array(
			'name'  => esc_html__('Check if you need this user choose or remove the best answer','discy'),
			'id'    => 'user_best_answer',
			'type'  => 'checkbox',
		);
		
		$options[] = array(
			'id'    => 'admin',
			'std'   => 'save',
			'type'  => 'hidden',
			'unset' => 'unset',
		);
	}
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Social Networking','discy'),
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Facebook','discy'),
		'id'   => 'facebook',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Twitter','discy'),
		'id'   => 'twitter',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Linkedin','discy'),
		'id'   => 'linkedin',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Pinterest','discy'),
		'id'   => 'pinterest',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Instagram','discy'),
		'id'   => 'instagram',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Youtube','discy'),
		'id'   => 'youtube',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Vimeo','discy'),
		'id'   => 'vimeo',
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	if (is_super_admin($get_current_user_id)) {
		$options[] = array(
			'name' => esc_html__('Advertising','discy'),
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'type' => 'info',
			'name' => esc_html__('Advertising after header 1','discy')
		);
		
		$options[] = array(
			'name'    => esc_html__('Advertising type','discy'),
			'id'      => $prefix.'header_adv_type_1',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","discy"),"custom_image" => esc_html__("Custom Image","discy"))
		);
		
		$options[] = array(
			'name'      => esc_html__('Image URL','discy'),
			'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','discy'),
			'id'        => $prefix.'header_adv_img_1',
			'condition' => $prefix.'header_adv_type_1:is(custom_image)',
			'type'      => 'upload'
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising url','discy'),
			'id'        => $prefix.'header_adv_href_1',
			'std'       => '#',
			'condition' => $prefix.'header_adv_type_1:is(custom_image)',
			'type'      => 'text'
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','discy'),
			'id'        => $prefix.'header_adv_code_1',
			'condition' => $prefix.'header_adv_type_1:is(display_code)',
			'type'      => 'textarea'
		);
		
		$options[] = array(
			'type' => 'info',
			'name' => esc_html__('Advertising after left menu','discy')
		);
		
		$options[] = array(
			'name'    => esc_html__('Advertising type','discy'),
			'id'      => $prefix.'left_menu_adv_type',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","discy"),"custom_image" => esc_html__("Custom Image","discy"))
		);
		
		$options[] = array(
			'name'      => esc_html__('Image URL','discy'),
			'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','discy'),
			'id'        => $prefix.'left_menu_adv_img',
			'type'      => 'upload',
			'condition' => $prefix.'left_menu_adv_type:is(custom_image)'
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising url','discy'),
			'id'        => $prefix.'left_menu_adv_href',
			'std'       => '#',
			'type'      => 'text',
			'condition' => $prefix.'left_menu_adv_type:is(custom_image)'
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','discy'),
			'id'        => $prefix.'left_menu_adv_code',
			'type'      => 'textarea',
			'condition' => $prefix.'left_menu_adv_type:is(display_code)'
		);
		
		$options[] = array(
			'type' => 'info',
			'name' => esc_html__('Advertising after content','discy')
		);
		
		$options[] = array(
			'name'    => esc_html__('Advertising type','discy'),
			'id'      => $prefix.'content_adv_type',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","discy"),"custom_image" => esc_html__("Custom Image","discy"))
		);
		
		$options[] = array(
			'name'      => esc_html__('Image URL','discy'),
			'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','discy'),
			'id'        => $prefix.'content_adv_img',
			'type'      => 'upload',
			'condition' => $prefix.'content_adv_type:is(custom_image)'
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising url','discy'),
			'id'        => $prefix.'content_adv_href',
			'std'       => '#',
			'type'      => 'text',
			'condition' => $prefix.'content_adv_type:is(custom_image)'
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','discy'),
			'id'        => $prefix.'content_adv_code',
			'type'      => 'textarea',
			'condition' => $prefix.'content_adv_type:is(display_code)'
		);
		
		$options[] = array(
			'name' => esc_html__('Between questions or posts','discy'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name'    => esc_html__('Advertising type','discy'),
			'id'      => $prefix.'between_adv_type',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","discy"),"custom_image" => esc_html__("Custom Image","discy"))
		);
		
		$options[] = array(
			'name'      => esc_html__('Image URL','discy'),
			'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','discy'),
			'id'        => $prefix.'between_adv_img',
			'condition' => $prefix.'between_adv_type:is(custom_image)',
			'type'      => 'upload'
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising url','discy'),
			'id'        => $prefix.'between_adv_href',
			'std'       => '#',
			'condition' => $prefix.'between_adv_type:is(custom_image)',
			'type'      => 'text'
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising Code html (Ex: Google ads)','discy'),
			'id'        => $prefix.'between_adv_code',
			'condition' => $prefix.'between_adv_type:not(custom_image)',
			'type'      => 'textarea'
		);
		
		$options[] = array(
			'name' => esc_html__('Between comments or answers','discy'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name'    => esc_html__('Advertising type','discy'),
			'id'      => $prefix.'between_comments_adv_type',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","discy"),"custom_image" => esc_html__("Custom Image","discy"))
		);
		
		$options[] = array(
			'name'      => esc_html__('Image URL','discy'),
			'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','discy'),
			'id'        => $prefix.'between_comments_adv_img',
			'condition' => $prefix.'between_comments_adv_type:is(custom_image)',
			'type'      => 'upload'
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising url','discy'),
			'id'        => $prefix.'between_comments_adv_href',
			'std'       => '#',
			'condition' => $prefix.'between_comments_adv_type:is(custom_image)',
			'type'      => 'text'
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising Code html (Ex: Google ads)','discy'),
			'id'        => $prefix.'between_comments_adv_code',
			'condition' => $prefix.'between_comments_adv_type:not(custom_image)',
			'type'      => 'textarea'
		);
	}
	
	return $options;
}