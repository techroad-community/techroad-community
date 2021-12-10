<?php /* Admin options */
function discy_admin_options() {
	// Background Defaults
	$background_defaults = array(
		'color'      => '',
		'image'      => '',
		'repeat'     => 'repeat',
		'position'   => 'top center',
		'attachment' =>'scroll' 
	);

	// Pull all the pages into an array
	$options_pages = array();
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	$options_pages[''] = 'Select a page:';
	foreach ($options_pages_obj as $page) {
		$options_pages[$page->ID] = $page->post_title;
	}
	
	// Pull all the sidebars into an array
	$new_sidebars = array('default'=> 'Default');
	foreach ($GLOBALS['wp_registered_sidebars'] as $sidebar) {
		$new_sidebars[$sidebar['id']] = $sidebar['name'];
	}
	
	// Menus
	$menus = array();
	$all_menus = get_terms('nav_menu',array('hide_empty' => true));
	foreach ($all_menus as $menu) {
	    $menus[$menu->term_id] = $menu->name;
	}
	
	// Pull all the roles into an array
	global $wp_roles;
	$new_roles = array();
	foreach ($wp_roles->roles as $key => $value) {
		$new_roles[$key] = $value['name'];
	}
	
	// Share
	$share_array = array(
		"share_facebook" => array("sort" => "Facebook","value" => "share_facebook"),
		"share_twitter"  => array("sort" => "Twitter","value" => "share_twitter"),
		"share_linkedin" => array("sort" => "LinkedIn","value" => "share_linkedin"),
		"share_whatsapp" => array("sort" => "WhatsApp","value" => "share_whatsapp"),
	);
	
	// Export
	$export = array(discy_options);
	$current_options = array();
	foreach ($export as $option) {
		$get_option_ = get_option($option);
		if ($get_option_) {
			$current_options[$option] = $get_option_;
		}else {
			$current_options[$option] = array();
		}
	}
	$current_options_e = json_encode($current_options);
	
	// If using image radio buttons, define a directory path
	$imagepath =  get_template_directory_uri().'/admin/images/';
	$imagepath_theme =  get_template_directory_uri().'/images/';

	$options = array();
	
	$options[] = array(
		'name' => esc_html__('General settings','discy'),
		'id'   => 'general',
		'icon' => 'admin-site',
		'type' => 'heading'
	);
	
	$options[] = array(
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Active the lightbox at the site','discy'),
		'desc' => esc_html__('Select ON if you want to active the lightbox at the site.','discy'),
		'id'   => 'active_lightbox',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Active the go up button at the site','discy'),
		'desc' => esc_html__('Select ON if you want to active the go up button at the site.','discy'),
		'id'   => 'go_up_button',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Active the ask question button at the site','discy'),
		'desc' => esc_html__('Select ON if you want to active the ask question button at the site beside go to up button.','discy'),
		'id'   => 'ask_button',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Active the font awesome at the site','discy'),
		'desc' => esc_html__('Select ON if you want to active the font awesome at the site.','discy'),
		'id'   => 'active_awesome',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Enable loader','discy'),
		'desc' => esc_html__('Select ON to enable loader.','discy'),
		'id'   => 'loader',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => sprintf(esc_html__('Type the date format %s see this link %s.','discy'),'<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">','</a>'),
		'desc' => esc_html__('Type here your date format.','discy'),
		'id'   => 'date_format',
		'std'  => 'F j, Y',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => sprintf(esc_html__('Type the time format %s see this link %s.','discy'),'<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">','</a>'),
		'desc' => esc_html__('Type here your time format.','discy'),
		'id'   => 'time_format',
		'std'  => 'g:i a',
		'type' => 'text'
	);
	
	$options[] = array(
		'name'    => esc_html__('Excerpt type ','discy'),
		'desc'    => esc_html__('Choose form here the excerpt type.','discy'),
		'id'      => 'excerpt_type',
		'std'     => 'words',
		'type'    => "select",
		'options' => array(
			'words'      => esc_html__('Words','discy'),
			'characters' => esc_html__('Characters','discy')
		)
	);
	
	$options[] = array(
		'name' => esc_html__('Hide the top bar for WordPress','discy'),
		'desc' => esc_html__('Select ON if you want to hide the top bar for WordPress.','discy'),
		'id'   => 'top_bar_wordpress',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$groups_no_admin = $new_roles;
	unset($groups_no_admin["administrator"]);
	
	$options[] = array(
		'name'      => esc_html__("Choose the groups you need not allowed for they to see the wp admin top bar.",'discy'),
		'id'        => 'top_bar_groups',
		'type'      => 'multicheck',
		'options'   => $groups_no_admin,
		'condition' => 'top_bar_wordpress:not(0)',
		'std'       => array('activation' => 'activation','subscriber' => 'subscriber','author' => 'author'),
	);
	
	$options[] = array(
		'name' => esc_html__('Do you need to redirect unlogged from wp admin?','discy'),
		'desc' => esc_html__('Select ON if you want to redirect the unlogged from the wp admin to the theme login page.','discy'),
		'id'   => 'redirect_wp_admin_unlogged',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Do you need to redirect user from wp admin?','discy'),
		'desc' => esc_html__('Select ON if you want to redirect the user from the wp admin.','discy'),
		'id'   => 'redirect_wp_admin',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__("Choose the groups you need not allowed for they to see the wp admin.",'discy'),
		'id'        => 'redirect_groups',
		'type'      => 'multicheck',
		'options'   => $groups_no_admin,
		'condition' => 'redirect_wp_admin:not(0)',
		'std'       => array('activation' => 'activation','subscriber' => 'subscriber','author' => 'author'),
	);
	
	$options[] = array(
		'name' => esc_html__('Enable SEO options','discy'),
		'desc' => esc_html__('Select ON to enable SEO options.','discy'),
		'id'   => 'seo_active',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Facebook share image','discy'),
		'desc' => esc_html__('This is the Facebook share image','discy'),
		'id'   => 'fb_share_image',
		'type' => 'upload'
	);
	
	$options[] = array(
		'name' => esc_html__('Head code','discy'),
		'desc' => esc_html__('Past your Google analytics code in the box','discy'),
		'id'   => 'head_code',
		'type' => 'textarea'
	);

	$options[] = array(
		'name' => esc_html__('Footer code','discy'),
		'desc' => esc_html__('Paste footer code in the box','discy'),
		'id'   => 'footer_code',
		'type' => 'textarea'
	);
	
	$options[] = array(
		'name' => esc_html__('SEO keywords','discy'),
		'desc' => esc_html__('Paste your keywords in the box','discy'),
		'id'   => 'the_keywords',
		'type' => 'textarea'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'redirect_wp_admin:not(on),redirect_wp_admin_unlogged:not(on)',
		'operator'  => 'or',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('WordPress login logo','discy'),
		'desc' => esc_html__('This is the logo that appears on the default WordPress login page','discy'),
		'id'   => 'login_logo',
		'std'  => $imagepath_theme."logo-footer.png",
		'type' => 'upload'
	);
	
	$options[] = array(
		'name' => esc_html__('WordPress login logo height','discy'),
		"id"   => "login_logo_height",
		"type" => "sliderui",
		'std'  => '45',
		"step" => "1",
		"min"  => "0",
		"max"  => "300"
	);
	
	$options[] = array(
		'name' => esc_html__('WordPress login logo width','discy'),
		"id"   => "login_logo_width",
		"type" => "sliderui",
		'std'  => '166',
		"step" => "1",
		"min"  => "0",
		"max"  => "300"
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	if (!function_exists('wp_site_icon') || !has_site_icon()) {
		$options[] = array(
			'name' => esc_html__('Custom favicon','discy'),
			'desc' => esc_html__('Upload the site\'s favicon here , You can create new favicon here favicon.cc','discy'),
			'id'   => 'favicon',
			'std'  => $imagepath_theme."favicon.png",
			'type' => 'upload'
		);
		
		$options[] = array(
			'name' => esc_html__('Custom favicon for iPhone','discy'),
			'desc' => esc_html__('Upload your custom iPhone favicon','discy'),
			'id'   => 'iphone_icon',
			'type' => 'upload'
		);
		
		$options[] = array(
			'name' => esc_html__('Custom iPhone retina favicon','discy'),
			'desc' => esc_html__('Upload your custom iPhone retina favicon','discy'),
			'id'   => 'iphone_icon_retina',
			'type' => 'upload'
		);
		
		$options[] = array(
			'name' => esc_html__('Custom favicon for iPad','discy'),
			'desc' => esc_html__('Upload your custom iPad favicon','discy'),
			'id'   => 'ipad_icon',
			'type' => 'upload'
		);
		
		$options[] = array(
			'name' => esc_html__('Custom iPad retina favicon','discy'),
			'desc' => esc_html__('Upload your custom iPad retina favicon','discy'),
			'id'   => 'ipad_icon_retina',
			'type' => 'upload'
		);
	}
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$header_settings = array(
		"header_s"     => esc_html__('Header setting','discy'),
		"call_action"  => esc_html__('Call to action','discy'),
		"breadcrumb_s" => esc_html__('Breadcrumbs','discy'),
		"posts_header" => esc_html__('Posts at header or footer','discy'),
		"slider"      => esc_html__('Slider','discy'),
	);
	
	$options[] = array(
		'name'    => esc_html__('Header settings','discy'),
		'id'      => 'header',
		'type'    => 'heading',
		'icon'    => 'menu',
		'std'     => 'header_s',
		'options' => apply_filters("discy_header_settings",$header_settings)
	);
	
	$options[] = array(
		'name' => esc_html__('Header setting','discy'),
		'id'   => 'header_s',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Header height','discy'),
		'desc'    => esc_html__('Choose the header height.','discy'),
		'id'      => 'header_height',
		'std'     => 'style_1',
		'type'    => 'radio',
		'options' => array("style_1" => esc_html__("Large","discy"),"style_2" => esc_html__("Small","discy"))
	);
	
	$options[] = array(
		'name'    => esc_html__('Header style','discy'),
		'desc'    => esc_html__('Choose the header style.','discy'),
		'id'      => 'header_style',
		'std'     => 'normal',
		'type'    => 'radio',
		'options' => array("normal" => esc_html__("Normal","discy"),"simple" => esc_html__("Simple","discy"))
	);
	
	$options[] = array(
		'name'    => esc_html__('Header skin','discy'),
		'desc'    => esc_html__('Choose the header skin.','discy'),
		'id'      => 'header_skin',
		'std'     => 'dark',
		'type'    => 'radio',
		'options' => array("dark" => esc_html__("Dark","discy"),"light" => esc_html__("Light","discy"),"colored" => esc_html__("Colored","discy"))
	);
	
	$options[] = array(
		'name'    => esc_html__('Logo display','discy'),
		'desc'    => esc_html__('Choose the logo display.','discy'),
		'id'      => 'logo_display',
		'std'     => 'custom_image',
		'type'    => 'radio',
		'options' => array("display_title" => esc_html__("Display site title","discy"),"custom_image" => esc_html__("Custom Image","discy"))
	);
	
	$options[] = array(
		'name'      => esc_html__('Logo upload','discy'),
		'desc'      => esc_html__('Upload your custom logo.','discy'),
		'id'        => 'logo_img',
		'std'       => $imagepath_theme."logo.png",
		'type'      => 'upload',
		'condition' => 'logo_display:is(custom_image)',
		'options'   => array("height" => "logo_height","width" => "logo_width"),
	);
	
	$options[] = array(
		'name'      => esc_html__('Logo retina upload','discy'),
		'desc'      => esc_html__('Upload your custom logo retina.','discy'),
		'id'        => 'retina_logo',
		'std'       => $imagepath_theme."logo-2x.png",
		'type'      => 'upload',
		'condition' => 'logo_display:is(custom_image)'
	);
	
	$options[] = array(
		'name'      => esc_html__('Logo height','discy'),
		"id"        => "logo_height",
		"type"      => "sliderui",
		'std'       => '45',
		"step"      => "1",
		"min"       => "0",
		"max"       => "80",
		'condition' => 'logo_display:is(custom_image)'
	);
	
	$options[] = array(
		'name'      => esc_html__('Logo width','discy'),
		"id"        => "logo_width",
		"type"      => "sliderui",
		'std'       => '137',
		"step"      => "1",
		"min"       => "0",
		"max"       => "170",
		'condition' => 'logo_display:is(custom_image)'
	);
	
	$options[] = array(
		'name' => esc_html__('Header search option','discy'),
		'desc' => esc_html__('Select ON to enable header search.','discy'),
		'id'   => 'header_search',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Activate the search bigger?','discy'),
		'desc'      => esc_html__('Select ON to enable header search more bigger.','discy'),
		'id'        => 'big_search',
		'condition' => 'header_search:not(0)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Header user login','discy'),
		'desc' => esc_html__('Select ON to enable header user login.','discy'),
		'id'   => 'header_user_login',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'header_user_login:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Header user login style','discy'),
		'desc'    => esc_html__('choose Header user login style.','discy'),
		'id'      => 'user_login_style',
		'std'     => 'style_1',
		'type'    => 'radio',
		'options' => array("style_1" => "Style 1","style_2" => "Style 2")
	);
	
	$user_login_links = array(
		"user-profile"        => array("sort" => esc_html__('User Profile','discy'),"value" => "user-profile"),
		"edit-profile"        => array("sort" => esc_html__('Edit Profile','discy'),"value" => "edit-profile"),
		"messages"            => array("sort" => esc_html__('Messages','discy'),"value" => "messages"),
		"questions"           => array("sort" => esc_html__('Questions','discy'),"value" => ""),
		"polls"               => array("sort" => esc_html__('Polls','discy'),"value" => ""),
		"questions"           => array("sort" => esc_html__('Questions','discy'),"value" => ""),
		"answers"             => array("sort" => esc_html__('Answers','discy'),"value" => ""),
		"followed"            => array("sort" => esc_html__('Followed','discy'),"value" => ""),
		"favorites"           => array("sort" => esc_html__('Favorites','discy'),"value" => ""),
		"posts"               => array("sort" => esc_html__('Posts','discy'),"value" => ""),
		"comments"            => array("sort" => esc_html__('Comments','discy'),"value" => ""),
		"followers-questions" => array("sort" => esc_html__('Followers Questions','discy'),"value" => ""),
		"followers-answers"   => array("sort" => esc_html__('Followers Answers','discy'),"value" => ""),
		"followers-posts"     => array("sort" => esc_html__('Followers Posts','discy'),"value" => ""),
		"followers-comments"  => array("sort" => esc_html__('Followers Comments','discy'),"value" => ""),
		"paid-questions"      => array("sort" => esc_html__('Paid Questions','discy'),"value" => ""),
		"asked-questions"     => array("sort" => esc_html__('Asked Questions','discy'),"value" => "asked-questions"),
		"best-answers"        => array("sort" => esc_html__('Best Answers','discy'),"value" => "best-answers"),
		"points"              => array("sort" => esc_html__('Points','discy'),"value" => "points"),
		"following"           => array("sort" => esc_html__('Following','discy'),"value" => ""),
		"followers"           => array("sort" => esc_html__('Followers','discy'),"value" => ""),
		"activities"          => array("sort" => esc_html__('activity Log','discy'),"value" => "activities"),
		"notifications"       => array("sort" => esc_html__('Notifications','discy'),"value" => ""),
		"log-out"             => array("sort" => esc_html__('Logout','discy'),"value" => "log-out"),
	);
	
	$options[] = array(
		'name'         => esc_html__('Select the pages show at the login area','discy'),
		'id'           => 'user_login_links',
		'type'         => 'multicheck',
		'sort'         => 'yes',
		'limit-height' => 'yes',
		'std'          => $user_login_links,
		'options'      => $user_login_links
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'div'       => 'div',
		'type'      => 'heading-2',
		'condition' => 'active_message:not(0),header_style:is(simple)'
	);
	
	$options[] = array(
		'name' => esc_html__('Header messages','discy'),
		'desc' => esc_html__('Select ON to enable header messages.','discy'),
		'id'   => 'header_messages',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'type'      => 'heading-2',
		'condition' => 'active_message:not(0),header_style:is(simple),header_messages:not(0)'
	);
	
	$options[] = array(
		'name'    => esc_html__('Header messages style','discy'),
		'desc'    => esc_html__('choose Header messages style.','discy'),
		'id'      => 'messages_style',
		'std'     => 'style_1',
		'type'    => 'radio',
		'options' => array("style_1" => "Style 1","style_2" => "Style 2")
	);
	
	$options[] = array(
		'name' => esc_html__('Header messages number','discy'),
		'desc' => esc_html__('Put the header messages number.','discy'),
		'id'   => 'messages_number',
		'std'  => 5,
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Header notifications','discy'),
		'desc' => esc_html__('Select ON to enable header notifications.','discy'),
		'id'   => 'header_notifications',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'type'      => 'heading-2',
		'condition' => 'header_notifications:not(0)'
	);
	
	$options[] = array(
		'name'    => esc_html__('Header notifications style','discy'),
		'desc'    => esc_html__('choose Header notifications style.','discy'),
		'id'      => 'notifications_style',
		'std'     => 'style_1',
		'type'    => 'radio',
		'options' => array("style_1" => "Style 1","style_2" => "Style 2")
	);
	
	$options[] = array(
		'name' => esc_html__('Header notifications number','discy'),
		'desc' => esc_html__('Put the header notifications number.','discy'),
		'id'   => 'notifications_number',
		'std'  => 5,
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Fixed header option','discy'),
		'desc' => esc_html__('Select ON to enable fixed header.','discy'),
		'id'   => 'header_fixed',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Call to action','discy'),
		'id'   => 'call_action',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Active the call to action','discy'),
		'desc' => esc_html__('Select ON to enable the call to action for the unlogged users.','discy'),
		'id'   => 'call_action',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'type'      => 'heading-2',
		'condition' => 'call_action:not(0)'
	);
	
	$options[] = array(
		'name'    => esc_html__('The call to action works at all the pages, custom pages, or home page only?','discy'),
		'id'      => 'action_home_pages',
		'options' => array(
			'home_page'    => esc_html__('Home page','discy'),
			'all_pages'    => esc_html__('All pages','discy'),
			'custom_pages' => esc_html__('Custom pages','discy'),
		),
		'std'     => 'home_page',
		'type'    => 'radio'
	);

	$options[] = array(
		'name'      => esc_html__('Page ids','discy'),
		'desc'      => esc_html__('Type from here the page ids','discy'),
		'id'        => 'action_pages',
		'type'      => 'text',
		'condition' => 'action_home_pages:is(custom_pages)'
	);
	
	$options[] = array(
		'name'    => esc_html__('Action skin','discy'),
		'desc'    => esc_html__('Choose the action skin.','discy'),
		'id'      => 'action_skin',
		'std'     => 'dark',
		'type'    => 'radio',
		'options' => array("light" => esc_html__("Light","discy"),"dark" => esc_html__("Dark","discy"),"colored" => esc_html__("Colored","discy"))
	);
	
	$options[] = array(
		'name'    => esc_html__('Action style','discy'),
		'desc'    => esc_html__('Choose action style from here.','discy'),
		'id'      => 'action_style',
		'options' => array(
			'style_1'  => 'Style 1',
			'style_2'  => 'Style 2',
		),
		'std'     => 'style_1',
		'type'    => 'radio'
	);
	
	$options[] = array(
		'name'    => esc_html__('Upload the background','discy'),
		'id'      => 'action_background',
		'type'    => 'background',
		'options' => array('color' => '','image' => ''),
		'std'     => array(
			'image' => $imagepath_theme."action.png"
		)
	);
	
	$options[] = array(
		"name" => esc_html__('Choose the background opacity','discy'),
		"desc" => esc_html__('Choose from here the background opacity','discy'),
		"id"   => "action_opacity",
		"type" => "sliderui",
		'std'  => 50,
		"step" => "5",
		"min"  => "0",
		"max"  => "100"
	);
	
	$options[] = array(
		'name' => esc_html__('The headline','discy'),
		'desc' => esc_html__('Type from here the headline','discy'),
		'id'   => 'action_headline',
		'type' => 'text',
		'std'  => 'Share & grow the world\'s knowledge!'
	);
	
	$options[] = array(
		'name' => esc_html__('The paragraph','discy'),
		'desc' => esc_html__('Type from here the paragraph','discy'),
		'id'   => 'action_paragraph',
		'type' => 'textarea',
		'std'  => 'We want to connect the people who have knowledge to the people who need it, to bring together people with different perspectives so they can understand each other better, and to empower everyone to share their knowledge.'
	);
	
	$options[] = array(
		'name'    => esc_html__('Action button','discy'),
		'desc'    => esc_html__('Choose action button style from here.','discy'),
		'id'      => 'action_button',
		'options' => array(
			'signup'   => esc_html__('Create A New Account','discy'),
			'login'    => esc_html__('Login','discy'),
			'question' => esc_html__('Ask A Question','discy'),
			'post'     => esc_html__('Add A Post','discy'),
			'custom'   => esc_html__('Custom link','discy'),
		),
		'std'     => 'signup',
		'type'    => 'radio'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'action_button:is(custom)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Open the page in same page or a new page?','discy'),
		'id'      => 'action_button_target',
		'std'     => "new_page",
		'type'    => 'select',
		'options' => array("same_page" => esc_html__("Same page","discy"),"new_page" => esc_html__("New page","discy"))
	);
	
	$options[] = array(
		'name' => esc_html__('Type the button link','discy'),
		'id'   => 'action_button_link',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Type the button text','discy'),
		'id'   => 'action_button_text',
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name'    => esc_html__('The call to action works for unlogged, logged users or both','discy'),
		'desc'    => esc_html__('Choose the call to action works for for unlogged, logged users or both.','discy'),
		'id'      => 'action_logged',
		'options' => array(
			'unlogged' => esc_html__('Unlogged users','discy'),
			'logged'   => esc_html__('Logged users','discy'),
			'both'     => esc_html__('Both','discy'),
		),
		'std'     => 'unlogged',
		'type'    => 'radio',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Breadcrumbs','discy'),
		'id'   => 'breadcrumb_s',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Breadcrumbs','discy'),
		'desc' => esc_html__('Select ON to enable the breadcrumbs.','discy'),
		'id'   => 'breadcrumbs',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Breadcrumbs separator','discy'),
		'desc'      => esc_html__('Add your breadcrumbs separator.','discy'),
		'id'        => 'breadcrumbs_separator',
		'std'       => '/',
		'type'      => 'text',
		'condition' => 'breadcrumbs:not(0)'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Posts at header or footer','discy'),
		'id'   => 'posts_header',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Active the posts area or not','discy'),
		'desc' => esc_html__('Select ON to enable the posts area.','discy'),
		'id'   => 'blog_h',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'blog_h:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('The posts area works after header or before footer?','discy'),
		'id'      => 'blog_h_where',
		'options' => array(
			'header' => esc_html__('After header','discy'),
			'footer' => esc_html__('Before footer','discy'),
		),
		'std'     => 'footer',
		'type'    => 'radio'
	);
	
	$options[] = array(
		'name'    => esc_html__('The posts area works at all the pages, custom pages, or home page only?','discy'),
		'id'      => 'blog_h_home_pages',
		'options' => array(
			'home_page'    => esc_html__('Home page','discy'),
			'all_pages'    => esc_html__('All pages','discy'),
			'custom_pages' => esc_html__('Custom pages','discy'),
		),
		'std'     => 'home_page',
		'type'    => 'radio'
	);

	$options[] = array(
		'name'      => esc_html__('Page ids','discy'),
		'desc'      => esc_html__('Type from here the page ids','discy'),
		'id'        => 'blog_h_pages',
		'type'      => 'text',
		'condition' => 'blog_h_home_pages:is(custom_pages)'
	);
	
	$options[] = array(
		'name' => esc_html__('The title','discy'),
		'desc' => esc_html__('Type from here the title','discy'),
		'id'   => 'blog_h_title',
		'type' => 'text',
		'std'  => 'Latest News & Updates'
	);
	
	$options[] = array(
		'name' => esc_html__('Active the more post button','discy'),
		'desc' => esc_html__('Select ON to enable the button.','discy'),
		'id'   => 'blog_h_button',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'blog_h_button:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('The text for the button','discy'),
		'desc' => esc_html__('Type from here the text for the button','discy'),
		'id'   => 'blog_h_button_text',
		'type' => 'text',
		'std'  => 'Explore Our Blog'
	);
	
	$options[] = array(
		'name'    => esc_html__('Blog page','discy'),
		'desc'    => esc_html__('Select the blog page','discy'),
		'id'      => 'blog_h_page',
		'type'    => 'select',
		'options' => $options_pages
	);
	
	$options[] = array(
		'name' => esc_html__('Type the blog link if you don\'t like a page','discy'),
		'id'   => 'blog_h_link',
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Posts number','discy'),
		'id'   => 'blog_h_post_number',
		'std'  => 3,
		'type' => 'text'
	);
	
	$options[] = array(
		'name'    => esc_html__('Post style','discy'),
		'desc'    => esc_html__('Choose post style from here.','discy'),
		'id'      => 'blog_h_post_style',
		'options' => array(
			'style_1' => esc_html__('1 column','discy'),
			'style_2' => esc_html__('List style','discy'),
			'style_3' => esc_html__('Columns','discy'),
		),
		'std'   => 'style_3',
		'type'  => 'radio',
	);
	
	$options[] = array(
		'name' => esc_html__('Choose a custom setting for the posts','discy'),
		'id'   => 'blog_h_custom_home_blog',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'blog_h_custom_home_blog:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Hide the featured image in the loop','discy'),
		'desc' => esc_html__('Select ON to hide the featured image in the loop.','discy'),
		'id'   => 'blog_h_featured_image',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'id'        => "blog_h_sort_meta_title_image",
		'condition' => 'blog_h_post_style:is(style_3)',
		'std'       => array(
						array("value" => "image",'name' => esc_html__('Image','discy'),"default" => "yes"),
						array("value" => "meta_title",'name' => esc_html__('Meta and title','discy'),"default" => "yes"),
					),
		'type'      => "sort",
		'options'   => array(
						array("value" => "image",'name' => esc_html__('Image','discy'),"default" => "yes"),
						array("value" => "meta_title",'name' => esc_html__('Meta and title','discy'),"default" => "yes"),
					)
	);
	
	$options[] = array(
		'name' => esc_html__('Read more enable or disable','discy'),
		'id'   => 'blog_h_read_more',
		'std'  => "on",
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Excerpt post','discy'),
		'desc' => esc_html__('Put here the excerpt post.','discy'),
		'id'   => 'blog_h_post_excerpt',
		'std'  => 40,
		'type' => 'text'
	);
	
	$options[] = array(
		'name'    => esc_html__('Select the meta options','discy'),
		'id'      => 'blog_h_post_meta',
		'type'    => 'multicheck',
		'std'     => array(
			"category_post" => "category_post",
			"title_post"    => "title_post",
			"author_by"     => "author_by",
			"post_date"     => "post_date",
			"post_comment"  => "post_comment",
			"post_views"    => "post_views",
		),
		'options' => array(
			"category_post" => esc_html__('Category post - Work at 1 column only','discy'),
			"title_post"    => esc_html__('Title post','discy'),
			"author_by"     => esc_html__('Author by - Work at 1 column only','discy'),
			"post_date"     => esc_html__('Date meta','discy'),
			"post_comment"  => esc_html__('Comment meta','discy'),
			"post_views"    => esc_html__("Views stats",'discy'),
		)
	);
	
	$options[] = array(
		'name'      => esc_html__('Select the share options','discy'),
		'id'        => 'blog_h_post_share',
		'condition' => 'blog_h_post_style:not(style_3)',
		'type'      => 'multicheck',
		'sort'      => 'yes',
		'std'       => $share_array,
		'options'   => $share_array
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Slider','discy'),
		'id'   => 'slider',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Active the slider or not','discy'),
		'desc' => esc_html__('Select ON to enable the posts area.','discy'),
		'id'   => 'slider_h',
		'type' => 'checkbox',
		'std'  => 'on'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'slider_h:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Slider works at all the pages, custom pages, or home page only?','discy'),
		'id'      => 'slider_h_home_pages',
		'options' => array(
			'home_page'    => esc_html__('Home page','discy'),
			'all_pages'    => esc_html__('All pages','discy'),
			'custom_pages' => esc_html__('Custom pages','discy'),
		),
		'std'     => 'home_page',
		'type'    => 'radio'
	);

	$options[] = array(
		'name'      => esc_html__('Page ids','discy'),
		'desc'      => esc_html__('Type from here the page ids','discy'),
		'id'        => 'slider_h_pages',
		'type'      => 'text',
		'condition' => 'slider_h_home_pages:is(custom_pages)'
	);

	$options[] = array(
		'name'    => esc_html__('Slider works for unlogged, logged users or both','discy'),
		'id'      => 'slider_h_logged',
		'options' => array(
			'unlogged' => esc_html__('Unlogged users','discy'),
			'logged'   => esc_html__('Logged users','discy'),
			'both'     => esc_html__('Both','discy'),
		),
		'std'     => 'both',
		'type'    => 'radio',
	);

	$options[] = array(
		'name'    => esc_html__('Choose the slider works with theme or add your custom slide by put the code or shortcodes','discy'),
		'id'      => 'custom_slider',
		'options' => array(
			'slider' => esc_html__('Theme slider','discy'),
			'custom' => esc_html__('Custom slider','discy'),
		),
		'std'     => 'slider',
		'type'    => 'radio',
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'custom_slider:is(slider)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Slider height','discy'),
		"id"   => "slider_height",
		"type" => "sliderui",
		"step" => "50",
		"min"  => "400",
		"max"  => "1000",
		"std"  => "500"
	);

	$slide_elements = array(
		array(
			"type" => "color",
			"id"   => "color",
			"name" => esc_html__('Color','discy')
		),
		array(
			"type" => "upload",
			"id"   => "image",
			"name" => esc_html__('Image','discy')
		),
		array(
			"type"  => "slider",
			"name"  => esc_html__('Choose the background opacity','discy'),
			"id"    => "opacity",
			"std"   => "0",
			"step"  => "1",
			"min"   => "0",
			"max"   => "100",
			"value" => "0"
		),
		array(
			"type"    => "radio",
			"id"      => "align",
			"name"    => esc_html__('Align','discy'),
			'options' => array(
				'left'   => esc_html__('Left','discy'),
				'center' => esc_html__('Center','discy'),
				'right'  => esc_html__('Right','discy'),
			),
			'std'     => 'left',
		),
		array(
			"type"      => "radio",
			"id"        => "login",
			"name"      => esc_html__('Login or Signup','discy'),
			'options'   => array(
				'none'   => esc_html__('None','discy'),
				'login'  => esc_html__('Login','discy'),
				'signup' => esc_html__('Signup','discy'),
			),
			'condition' => '[%id%]align:not(center),[%id%]button_block:not(block)',
			'std'       => 'login',
		),
		array(
			"type" => "text",
			"id"   => "title",
			"name" => esc_html__('Title','discy')
		),
		array(
			"type" => "text",
			"id"   => "title_2",
			"name" => esc_html__('Second title','discy')
		),
		array(
			"type" => "textarea",
			"id"   => "paragraph",
			"name" => esc_html__('Paragraph','discy')
		),
		array(
			"type"    => "radio",
			"id"      => "button_block",
			"name"    => esc_html__('Button or Block','discy'),
			'options' => array(
				'none'   => esc_html__('None','discy'),
				'button' => esc_html__('button','discy'),
				'block'  => esc_html__('Block','discy'),
			),
			'std'     => 'none',
		),
		array(
			"type"      => "radio",
			"id"        => "block",
			"name"      => esc_html__('Block','discy'),
			'options'   => array(
				'search'   => esc_html__('Search','discy'),
				'question' => esc_html__('Ask A Question','discy'),
			),
			'condition' => '[%id%]button_block:is(block)',
			'std'       => 'search',
		),
		array(
			"type"      => "radio",
			"id"        => "button",
			"name"      => esc_html__('Button','discy'),
			'options'   => array(
				'signup'   => esc_html__('Create A New Account','discy'),
				'login'    => esc_html__('Login','discy'),
				'question' => esc_html__('Ask A Question','discy'),
				'post'     => esc_html__('Add A Post','discy'),
				'custom'   => esc_html__('Custom link','discy'),
			),
			'condition' => '[%id%]button_block:is(button)',
			'std'       => 'signup',
		),
		array(
			"type"      => "radio",
			"id"        => "button_style",
			"name"      => esc_html__('Button style','discy'),
			'options'   => array(
				'style_1' => esc_html__('Style 1','discy'),
				'style_2' => esc_html__('Style 2','discy'),
				'style_3' => esc_html__('Style 3','discy'),
			),
			'condition' => '[%id%]button_block:is(button)',
			'std'       => 'style_1',
		),
		array(
			'div'       => 'div',
			'condition' => '[%id%]button:is(custom),[%id%]button_block:is(button)',
			'type'      => 'heading-2'
		),
		array(
			'name'    => esc_html__('Open the page in same page or a new page?','discy'),
			'id'      => 'button_target',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","discy"),"new_page" => esc_html__("New page","discy"))
		),
		array(
			'name' => esc_html__('Type the button link','discy'),
			'id'   => 'button_link',
			'type' => 'text'
		),
		array(
			'name' => esc_html__('Type the button text','discy'),
			'id'   => 'button_text',
			'type' => 'text'
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
	);
	
	$options[] = array(
		'id'      => "add_slides",
		'type'    => "elements",
		'button'  => esc_html__('Add new slide','discy'),
		'hide'    => "yes",
		'options' => $slide_elements,
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'id'        => "custom_slides",
		'type'      => "textarea",
		'name'      => esc_html__('Add your custom slide or shortcode','discy'),
		'condition' => 'custom_slider:is(custom)',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options = apply_filters('discy_options_after_slider_setting',$options);
	
	$options[] = array(
		'name' => esc_html__('Mobile setting','discy'),
		'id'   => 'mobile',
		'icon' => 'smartphone',
		'type' => 'heading'
	);
	
	$options[] = array(
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name'      => esc_html__('Stop fixed header in mobile','discy'),
		'desc'      => esc_html__('Select ON to stop fixed header in mobile.','discy'),
		'id'        => 'header_fixed_responsive',
		'condition' => 'header_fixed:not(0)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name'    => esc_html__('Button at mobile for the unlogged case','discy'),
		'desc'    => esc_html__('Choose button type at the mobile display for the unlogged case from here.','discy'),
		'id'      => 'mobile_sign',
		'options' => array(
			'login'  => esc_html__('Login','discy'),
			'signup' => esc_html__('Signup','discy'),
		),
		'std'     => 'login',
		'type'    => 'radio'
	);
	
	$options[] = array(
		'name' => esc_html__('Choose the mobile menu skin','discy'),
		'id'   => "mobile_menu",
		'std'  => "dark",
		'type' => "images",
		'options' => array(
			'dark'  => $imagepath.'menu_dark.jpg',
			'gray'  => $imagepath.'sidebar_no.jpg',
			'light' => $imagepath.'menu_light.jpg',
		)
	);
	
	$options[] = array(
		'name' => esc_html__('Mobile bar enable or disable?','discy'),
		'desc' => esc_html__('Select ON to enable the mobile bar.','discy'),
		'id'   => 'mobile_bar',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Active a custom mobile menu or not?','discy'),
		'desc' => esc_html__('Select ON to enable the custom mobile menu.','discy'),
		'id'   => 'active_mobile_menu',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Choose from here what\'s menu will show at mobile menu for unlogged users.','discy'),
		'id'        => 'mobile_menu',
		'type'      => 'select',
		'condition' => 'active_mobile_menu:not(0)',
		'options'   => $menus
	);
	
	$options[] = array(
		'name'      => esc_html__('Choose from here what\'s menu will show at mobile menu for logged users.','discy'),
		'id'        => 'mobile_menu_logged',
		'type'      => 'select',
		'condition' => 'active_mobile_menu:not(0)',
		'options'   => $menus
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name'    => esc_html__('Posts settings','discy'),
		'id'      => 'posts',
		'icon'    => 'admin-page',
		'type'    => 'heading',
		'std'     => 'post_loop',
		'options' => array(
			"post_loop"            => esc_html__('Posts & Loop setting','discy'),
			"add_edit_delete_post" => esc_html__('Add - Edit - Delete','discy'),
			"post_meta"            => esc_html__('Post meta setting','discy'),
			"inner_pages"          => esc_html__('Inner pages setting','discy'),
			"share_setting"        => esc_html__('Share setting','discy'),
			"related_setting"      => esc_html__('Related setting','discy'),
			"posts_layouts"        => esc_html__('Posts layouts','discy')
		)
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'post_loop',
		'name' => esc_html__('Posts & Loop setting','discy')
	);
	
	$options[] = array(
		'name'    => esc_html__('Post style','discy'),
		'desc'    => esc_html__('Choose post style from here.','discy'),
		'id'      => 'post_style',
		'options' => array(
			'style_1' => esc_html__('1 column','discy'),
			'style_2' => esc_html__('List style','discy'),
			'style_3' => esc_html__('Columns','discy'),
		),
		'std'     => 'style_1',
		'type'    => 'radio'
	);
	
	$options[] = array(
		'name' => esc_html__('Category description enable or disable','discy'),
		'desc' => esc_html__('Select ON to enable the category description in the category page.','discy'),
		'id'   => 'category_description',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Category rss enable or disable','discy'),
		'desc'      => esc_html__('Select ON to enable the category rss in the category page.','discy'),
		'id'        => 'category_rss',
		'std'       => 'on',
		'condition' => 'category_description:not(0)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Tag description enable or disable','discy'),
		'desc' => esc_html__('Select ON to enable the tag description in the tag page.','discy'),
		'id'   => 'tag_description',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Tag rss enable or disable','discy'),
		'desc'      => esc_html__('Select ON to enable the tag rss in the tag page.','discy'),
		'id'        => 'tag_rss',
		'std'       => 'on',
		'condition' => 'tag_description:not(0)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Hide the featured image in the loop','discy'),
		'desc' => esc_html__('Select ON to hide the featured image in the loop.','discy'),
		'id'   => 'featured_image_loop_post',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'id'        => "sort_meta_title_image",
		'condition' => 'post_style:is(style_3)',
		'std'       => array(
						array("value" => "image",'name' => esc_html__('Image','discy'),"default" => "yes"),
						array("value" => "meta_title",'name' => esc_html__('Meta and title','discy'),"default" => "yes"),
					),
		'type'      => "sort",
		'options'   => array(
						array("value" => "image",'name' => esc_html__('Image','discy'),"default" => "yes"),
						array("value" => "meta_title",'name' => esc_html__('Meta and title','discy'),"default" => "yes"),
					)
	);
	
	$options[] = array(
		'name' => esc_html__('Read more enable or disable','discy'),
		'id'   => 'read_more',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Excerpt post','discy'),
		'desc' => esc_html__('Put here the excerpt post.','discy'),
		'id'   => 'post_excerpt',
		'std'  => 40,
		'type' => 'text'
	);
	
	$options[] = array(
		'name'    => esc_html__('Pagination style','discy'),
		'desc'    => esc_html__('Choose pagination style from here.','discy'),
		'id'      => 'post_pagination',
		'options' => array(
			'standard'        => esc_html__('Standard','discy'),
			'pagination'      => esc_html__('Pagination','discy'),
			'load_more'       => esc_html__('Load more','discy'),
			'infinite_scroll' => esc_html__('Infinite scroll','discy'),
			'none'            => esc_html__('None','discy'),
		),
		'std'     => 'pagination',
		'type'    => 'radio'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'add_edit_delete_post',
		'name' => esc_html__('Add, edit and delete post','discy')
	);
	
	$options[] = array(
		'name' => esc_html__('Add posts','discy'),
		'type' => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__('Add post slug','discy'),
		'desc' => esc_html__('Put the add post slug.','discy'),
		'id'   => 'add_posts_slug',
		'std'  => 'add-post',
		'type' => 'text'
	);
	
	if (class_exists("WPQA")) {
		$html_content = '<a href="'.wpqa_add_post_permalink().'" target="_blank">'.esc_html__('The Link For The Add Post Page.','discy').'</a>';
		
		$options[] = array(
			'name' => $html_content,
			'type' => 'info'
		);
	}
	
	$options[] = array(
		'name' => esc_html__('Active the add post with popup','discy'),
		'desc' => esc_html__('Add post with popup enable or disable.','discy'),
		'id'   => 'active_post_popup',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Any one can add post without register','discy'),
		'desc' => esc_html__('Any one can add post without register enable or disable.','discy'),
		'id'   => 'add_post_no_register',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'    => esc_html__('Choose post status for users only','discy'),
		'desc'    => esc_html__('Choose post status after user publish the post.','discy'),
		'id'      => 'post_publish',
		'options' => array("publish" => esc_html__("Publish","discy"),"draft" => esc_html__("Draft","discy")),
		'std'     => 'draft',
		'type'    => 'select'
	);
	
	$options[] = array(
		'name'      => esc_html__('Choose post status for unlogged user only','discy'),
		'desc'      => esc_html__('Choose post status after unlogged user publish the post.','discy'),
		'id'        => 'post_publish_unlogged',
		'options'   => array("publish" => esc_html__("Publish","discy"),"draft" => esc_html__("Draft","discy")),
		'std'       => 'draft',
		'type'      => 'select',
		'condition' => 'add_post_no_register:not(0)',
	);
	
	$options[] = array(
		'name'      => esc_html__('Send email when the post need a review','discy'),
		'desc'      => esc_html__('Email for posts review enable or disable.','discy'),
		'id'        => 'send_email_draft_posts',
		'std'       => 'on',
		'operator'  => 'or',
		'condition' => 'post_publish:not(publish),post_publish_unlogged:not(publish)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Approved auto for the users have a previously approved posts.','discy'),
		'id'        => 'approved_posts',
		'condition' => 'post_publish:not(publish)',
		'type'      => 'checkbox'
	);

	$add_post_items = array(
		"tags_post"      => array("sort" => esc_html__('Post Tags','discy'),"value" => "tags_post"),
		"featured_image" => array("sort" => esc_html__('Post featured image','discy'),"value" => "featured_image"),
		"content_post"   => array("sort" => esc_html__('Post content','discy'),"value" => "content_post"),
	);
	
	$options[] = array(
		'name'    => esc_html__('Select what\'s show at ask post form','discy'),
		'id'      => 'add_post_items',
		'type'    => 'multicheck',
		'sort'    => 'yes',
		'std'     => $add_post_items,
		'options' => $add_post_items
	);
	
	$options[] = array(
		'name'      => esc_html__('Editor enable or disable for details in add post form','discy'),
		'id'        => 'editor_post_details',
		'condition' => 'add_post_items:has(content_post)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Edit posts','discy'),
		'type' => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__('The users can edit the posts?','discy'),
		'id'   => 'can_edit_post',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'can_edit_post:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Edit post slug','discy'),
		'desc' => esc_html__('Put the edit post slug.','discy'),
		'id'   => 'edit_posts_slug',
		'std'  => 'edit-post',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('After edit post approved auto or need to approved again?','discy'),
		'desc' => esc_html__('Press ON to approved auto','discy'),
		'id'   => 'post_approved',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('After edit post change the URL like the title?','discy'),
		'desc' => esc_html__('Press ON to edit the URL','discy'),
		'id'   => 'change_post_url',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Delete posts','discy'),
		'type' => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__('Active user can delete the posts','discy'),
		'desc' => esc_html__('Select ON if you want the user can delete the posts.','discy'),
		'id'   => 'post_delete',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('When the users delete the post went to the trash or delete it ever?','discy'),
		'id'        => 'delete_post',
		'options'   => array(
			'delete' => esc_html__('Delete','discy'),
			'trash'  => esc_html__('Trash','discy'),
		),
		'std'       => 'delete',
		'condition' => 'post_delete:not(0)',
		'type'      => 'radio'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Post meta setting','discy'),
		'id'   => 'post_meta',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Select the meta options','discy'),
		'id'      => 'post_meta',
		'type'    => 'multicheck',
		'std'     => array(
			"category_post" => "category_post",
			"title_post"    => "title_post",
			"author_by"     => "author_by",
			"post_date"     => "post_date",
			"post_comment"  => "post_comment",
			"post_views"    => "post_views",
		),
		'options' => array(
			"category_post" => esc_html__('Category post - Work at 1 column only','discy'),
			"title_post"    => esc_html__('Title post','discy'),
			"author_by"     => esc_html__('Author by - Work at 1 column only','discy'),
			"post_date"     => esc_html__('Date meta','discy'),
			"post_comment"  => esc_html__('Comment meta','discy'),
			"post_views"    => esc_html__("Views stats",'discy'),
		)
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'inner_pages',
		'name' => esc_html__('Inner pages setting','discy')
	);
	
	$order_sections = array(
		"author"        => array("sort" => esc_html__('About the author','discy'),"value" => "author"),
		"next_previous" => array("sort" => esc_html__('Next and Previous articles','discy'),"value" => "next_previous"),
		"advertising"   => array("sort" => esc_html__('Advertising','discy'),"value" => "advertising"),
		"related"       => array("sort" => esc_html__('Related articles','discy'),"value" => "related"),
		"comments"      => array("sort" => esc_html__('Comments','discy'),"value" => "comments"),
	);
	
	$options[] = array(
		'name'    => esc_html__('Sort your sections','discy'),
		'id'      => 'order_sections',
		'type'    => 'multicheck',
		'sort'    => 'yes',
		'std'     => $order_sections,
		'options' => $order_sections
	);
	
	$options[] = array(
		'name' => esc_html__('Hide the featured image in the single post','discy'),
		'desc' => esc_html__('Select ON to hide the featured image in the single post.','discy'),
		'id'   => 'featured_image',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'    => esc_html__('Featured image style','discy'),
		'desc'    => esc_html__('Featured image style from here.','discy'),
		'id'      => 'featured_image_style',
		'std'     => 'default',
		'options' => array(
			'default' => 'Default',
			'style_270'   => '270x180',
			'style_140'   => '140x140',
			'custom_size' => esc_html__('Custom size','discy'),
		),
		'type'    => 'radio'
	);
	
	$options[] = array(
		'type'      => 'heading-2',
		'condition' => 'featured_image_style:is(custom_size)',
		'div'       => 'div'
	);
		
	$options[] = array(
		'name' => esc_html__('Featured image width','discy'),
		"id"   => "featured_image_width",
		"type" => "sliderui",
		"step" => "1",
		"min"  => "140",
		"max"  => "500"
	);
	
	$options[] = array(
		'name' => esc_html__('Featured image height','discy'),
		"id"   => "featured_image_height",
		"type" => "sliderui",
		"step" => "1",
		"min"  => "140",
		"max"  => "500"
	);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
	);
	
	$options[] = array(
		'name' => esc_html__('Tags enable or disable','discy'),
		'id'   => 'post_tags',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Navigation post for the same category only?','discy'),
		'desc'      => esc_html__('Navigation post (next and previous posts) for the same category only?','discy'),
		'id'        => 'post_nav_category',
		'condition' => 'order_sections:has(next_previous)',
		'std'       => 'on',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Share setting','discy'),
		'id'   => 'share_setting',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name'      => esc_html__('Select the share options','discy'),
		'id'        => 'post_share',
		'type'      => 'multicheck',
		'condition' => 'post_style:not(style_3)',
		'sort'      => 'yes',
		'std'       => $share_array,
		'options'   => $share_array
	);
	
	$options[] = array(
		'name'      => esc_html__('The share setting works at the 1 columns and list style only.','discy'),
		'condition' => 'post_style:is(style_3)',
		'type'      => 'info'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Related setting','discy'),
		'id'   => 'related_setting',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name'      => esc_html__('Active it first from Inner pages setting.','discy'),
		'condition' => 'order_sections:has_not(related)',
		'type'      => 'info'
	);
	
	$options[] = array(
		'type'      => 'heading-2',
		'condition' => 'order_sections:has(related)',
		'div'       => 'div'
	);
	
	$options[] = array(
		'name'    => esc_html__('Related style','discy'),
		'desc'    => esc_html__('Type related style from here.','discy'),
		'id'      => 'related_style',
		'std'     => 'style_1',
		'options' => array(
			'style_1' => 'Style 1',
			'links'   => 'Style 2',
		),
		'type'    => 'radio'
	);
	
	$options[] = array(
		'name' => esc_html__('Related posts number','discy'),
		'desc' => esc_html__('Type related posts number from here.','discy'),
		'id'   => 'related_number',
		'std'  => 2,
		'type' => 'text'
	);
	
	$options[] = array(
		'name'      => esc_html__('Related posts number at sidebar','discy'),
		'desc'      => esc_html__('Type related posts number at sidebar from here.','discy'),
		'id'        => 'related_number_sidebar',
		'std'       => 3,
		'condition' => 'related_style:not(links)',
		'type'      => 'text'
	);
	
	$options[] = array(
		'name'      => esc_html__('Related posts number at full width','discy'),
		'desc'      => esc_html__('Type related posts number at full width from here.','discy'),
		'id'        => 'related_number_full',
		'std'       => 4,
		'condition' => 'related_style:not(links)',
		'type'      => 'text'
	);
	
	$options[] = array(
		'name'    => esc_html__('Query type','discy'),
		'desc'    => esc_html__('Select what the related posts will show.','discy'),
		'id'      => 'query_related',
		'std'     => 'categories',
		'options' => array(
			'categories' => esc_html__('Posts in the same categories','discy'),
			'tags'       => esc_html__('Posts in the same tags (If not find any tags will show by the same categories)','discy'),
			'author'     => esc_html__('Posts by the same author','discy'),
		),
		'type'    => 'radio'
	);
	
	$options[] = array(
		'name' => esc_html__('Excerpt title in related posts','discy'),
		'desc' => esc_html__('Type excerpt title in related posts from here.','discy'),
		'id'   => 'excerpt_related_title',
		'std'  => '10',
		'type' => 'text'
	);
	
	$options[] = array(
		'name'      => esc_html__('Comment in related enable or disable','discy'),
		'id'        => 'comment_in_related',
		'std'       => 'on',
		'condition' => 'related_style:not(links)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Date in related enable or disable','discy'),
		'id'        => 'date_in_related',
		'std'       => 'on',
		'condition' => 'related_style:not(links)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Archive, categories, tags and inner post','discy'),
		'id'   => 'posts_layouts',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Post sidebar layout','discy'),
		'id'   => "post_sidebar_layout",
		'std'  => "default",
		'type' => "images",
		'options' => array(
			'default'      => $imagepath.'sidebar_default.jpg',
			'menu_sidebar' => $imagepath.'menu_sidebar.jpg',
			'right'        => $imagepath.'sidebar_right.jpg',
			'full'         => $imagepath.'sidebar_no.jpg',
			'left'         => $imagepath.'sidebar_left.jpg',
			'centered'     => $imagepath.'centered.jpg',
			'menu_left'    => $imagepath.'menu_left.jpg',
		)
	);
	
	$options[] = array(
		'name'      => esc_html__('Post Page sidebar','discy'),
		'id'        => "post_sidebar",
		'options'   => $new_sidebars,
		'type'      => 'select',
		'condition' => 'post_sidebar_layout:not(full),post_sidebar_layout:not(centered),post_sidebar_layout:not(menu_left)'
	);
	
	$options[] = array(
		'name'      => esc_html__('Post Page sidebar 2','discy'),
		'id'        => "post_sidebar_2",
		'options'   => $new_sidebars,
		'type'      => 'select',
		'operator'  => 'or',
		'condition' => 'post_sidebar_layout:is(menu_sidebar),post_sidebar_layout:is(menu_left)'
	);
	
	$options[] = array(
		'name'    => esc_html__('Choose Your Skin','discy'),
		'class'   => "site_skin",
		'id'      => "post_skin",
		'std'     => "default",
		'type'    => "images",
		'options' => array(
			'default'    => $imagepath.'default_color.jpg',
			'skin'       => $imagepath.'default.jpg',
			'violet'     => $imagepath.'violet.jpg',
			'bright_red' => $imagepath.'bright_red.jpg',
			'green'      => $imagepath.'green.jpg',
			'red'        => $imagepath.'red.jpg',
			'cyan'       => $imagepath.'cyan.jpg',
			'blue'       => $imagepath.'blue.jpg',
		)
	);
	
	$options[] = array(
		'name' => esc_html__('Primary Color','discy'),
		'id'   => 'post_primary_color',
		'type' => 'color' 
	);
	
	$options[] = array(
		'name'    => esc_html__('Background Type','discy'),
		'id'      => 'post_background_type',
		'std'     => 'default',
		'type'    => 'radio',
		'options' => 
			array(
				"default"           => esc_html__("Default","discy"),
				"none"              => esc_html__("None","discy"),
				"patterns"          => esc_html__("Patterns","discy"),
				"custom_background" => esc_html__("Custom Background","discy")
			)
	);

	$options[] = array(
		'name'      => esc_html__('Background Color','discy'),
		'id'        => 'post_background_color',
		'type'      => 'color',
		'condition' => 'post_background_type:is(patterns)'
	);
		
	$options[] = array(
		'name'      => esc_html__('Choose Pattern','discy'),
		'id'        => "post_background_pattern",
		'std'       => "bg13",
		'type'      => "images",
		'condition' => 'post_background_type:is(patterns)',
		'class'     => "pattern_images",
		'options'   => array(
			'bg1'  => $imagepath.'bg1.jpg',
			'bg2'  => $imagepath.'bg2.jpg',
			'bg3'  => $imagepath.'bg3.jpg',
			'bg4'  => $imagepath.'bg4.jpg',
			'bg5'  => $imagepath.'bg5.jpg',
			'bg6'  => $imagepath.'bg6.jpg',
			'bg7'  => $imagepath.'bg7.jpg',
			'bg8'  => $imagepath.'bg8.jpg',
			'bg9'  => $imagepath_theme.'patterns/bg9.png',
			'bg10' => $imagepath_theme.'patterns/bg10.png',
			'bg11' => $imagepath_theme.'patterns/bg11.png',
			'bg12' => $imagepath_theme.'patterns/bg12.png',
			'bg13' => $imagepath.'bg13.jpg',
			'bg14' => $imagepath.'bg14.jpg',
			'bg15' => $imagepath_theme.'patterns/bg15.png',
			'bg16' => $imagepath_theme.'patterns/bg16.png',
			'bg17' => $imagepath.'bg17.jpg',
			'bg18' => $imagepath.'bg18.jpg',
			'bg19' => $imagepath.'bg19.jpg',
			'bg20' => $imagepath.'bg20.jpg',
			'bg21' => $imagepath_theme.'patterns/bg21.png',
			'bg22' => $imagepath.'bg22.jpg',
			'bg23' => $imagepath_theme.'patterns/bg23.png',
			'bg24' => $imagepath_theme.'patterns/bg24.png',
		)
	);

	$options[] = array(
		'name'      => esc_html__('Custom Background','discy'),
		'id'        => 'post_custom_background',
		'std'       => $background_defaults,
		'type'      => 'background',
		'options'   => $background_defaults,
		'condition' => 'post_background_type:is(custom_background)'
	);
		
	$options[] = array(
		'name'      => esc_html__('Full Screen Background','discy'),
		'desc'      => esc_html__('Select ON to Full Screen Background','discy'),
		'id'        => 'post_full_screen_background',
		'type'      => 'checkbox',
		'condition' => 'post_background_type:is(custom_background)'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	if (class_exists("WPQA")) {
		$questions_settings = array(
			"general_setting"   => esc_html__('General settings','discy'),
			"question_slug"     => esc_html__('Question slugs','discy'),
			"add_edit_delete"   => esc_html__('Add - Edit - Delete','discy'),
			"question_meta"     => esc_html__('Question meta setting','discy'),
			"question_category" => esc_html__('Questions categories setting','discy'),
			"questions_loop"    => esc_html__('Questions & Loop settings','discy'),
			"inner_question"    => esc_html__('Inner question','discy'),
			"share_setting_q"   => esc_html__('Share setting','discy'),
			"questions_layout"  => esc_html__('Questions layout','discy')
		);

		$options[] = array(
			'name'    => esc_html__('Questions settings','discy'),
			'id'      => 'question',
			'icon'    => 'editor-help',
			'type'    => 'heading',
			'std'     => 'general_setting',
			'options' => apply_filters("discy_questions_settings",$questions_settings)
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'general_setting',
			'name' => esc_html__('General settings','discy')
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON if you need to choose the question at simple layout','discy'),
			'id'   => 'question_simple',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'    => esc_html__('Ajax file load from admin or theme','discy'),
			'desc'    => esc_html__('choose ajax file load from admin or theme.','discy'),
			'id'      => 'ajax_file',
			'std'     => 'admin',
			'type'    => 'select',
			'options' => array("admin" => esc_html__("Admin","discy"),"theme" => esc_html__("Theme","discy"))
		);
		
		$options[] = array(
			'name' => esc_html__('Show filter at categories and archive pages','discy'),
			'desc' => esc_html__('Select ON to enable the filter at categories and archive pages.','discy'),
			'id'   => 'category_filter',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Tag description enable or disable','discy'),
			'desc' => esc_html__('Select ON to enable the tag description in the tag page.','discy'),
			'id'   => 'question_tag_description',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Tag rss enable or disable','discy'),
			'desc'      => esc_html__('Select ON to enable the tag rss in the tag page.','discy'),
			'id'        => 'question_tag_rss',
			'std'       => 'on',
			'condition' => 'question_tag_description:not(0)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Active the best answer for the normal users in site?','discy'),
			'desc' => esc_html__('The best answer enable or disable.','discy'),
			'id'   => 'active_best_answer',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Active the points system in site?','discy'),
			'desc' => esc_html__('The points system enable or disable.','discy'),
			'id'   => 'active_points',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_points:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Active the question bump','discy'),
			'desc' => esc_html__('Select ON if you want the question bump.','discy'),
			'id'   => 'question_bump',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('When delete the question or answer have a best answer remove it from the stats and user point?','discy'),
			'desc' => esc_html__('Select ON if you want to remove the best answer from the user point.','discy'),
			'id'   => 'remove_best_answer_stats',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Active the reports in site?','discy'),
			'desc' => esc_html__('Active the reports enable or disable.','discy'),
			'id'   => 'active_reports',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_reports:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Active the reports in site for the logged users only?','discy'),
			'desc' => esc_html__('Active the reports in site for the logged users only enable or disable.','discy'),
			'id'   => 'active_logged_reports',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_points:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Active the users have such points can move the question or answer to trash or draft by reporting.','discy'),
			'id'   => 'active_trash_reports',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_trash_reports:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Move the question or answer to trash or draft when report it.','discy'),
			'id'      => 'trash_draft_reports',
			'options' => array("trash" => esc_html__("Trash","discy"),"draft" => esc_html__("Draft","discy")),
			'type'    => 'select'
		);
		
		$options[] = array(
			'name' => esc_html__('Add the points to allowe the users have them to move the question or answer to trash or draft when report it.','discy'),
			'id'   => 'trash_reports_points',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Add minimum of the points if anyone have them, their questions or answers will not move to trash or draft.','discy'),
			'id'   => 'reports_min_points',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Whitelist questions.','discy'),
			'desc' => esc_html__('Add here the whitelist question, Any questions here will not move to trash or draft.','discy'),
			'id'   => 'whitelist_questions',
			'type' => 'textarea'
		);
		
		$options[] = array(
			'name' => esc_html__('Whitelist answers.','discy'),
			'desc' => esc_html__('Add here the whitelist answers, Any answers here will not move to trash or draft.','discy'),
			'id'   => 'whitelist_answers',
			'type' => 'textarea'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Active the vote in the site?','discy'),
			'desc' => esc_html__('The vote for questions and answers in the site is enable or disable.','discy'),
			'id'   => 'active_vote',
			'std'  => "on",
			'type' => 'checkbox'
		);

		$options[] = array(
			'name'      => esc_html__('Active the vote in the site for the unlogged users?','discy'),
			'desc'      => esc_html__('The vote for questions and answers in the site for the unlogged users is enable or disable.','discy'),
			'id'        => 'active_vote_unlogged',
			'std'       => "on",
			'type'      => 'checkbox',
			'condition' => 'active_vote:not(0)'
		);
		
		$options[] = array(
			'name' => esc_html__('Active the pop up at the author image in site?','discy'),
			'desc' => esc_html__('Pop up at the author image in site enable or disable.','discy'),
			'id'   => 'author_image_pop',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Active the separator for the numbers at the site?','discy'),
			'id'   => 'active_separator',
			'type' => 'checkbox'
		);
	
		$options[] = array(
			'name'      => esc_html__('Number separator','discy'),
			'desc'      => esc_html__('Add your number separator.','discy'),
			'id'        => 'number_separator',
			'std'       => ',',
			'type'      => 'text',
			'condition' => 'active_separator:not(0)'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Question slugs','discy'),
			'id'   => 'question_slug',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Questions archive slug','discy'),
			'desc' => esc_html__('Add your questions archive slug.','discy'),
			'id'   => 'archive_question_slug',
			'std'  => 'questions',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Click ON, if you need to remove the question slug and choose "Post name" from WordPress Settings/Permalinks.','discy'),
			'id'   => 'remove_question_slug',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Question slug','discy'),
			'desc'      => esc_html__('Add your question slug.','discy'),
			'id'        => 'question_slug',
			'std'       => 'question',
			'condition' => 'remove_question_slug:not(on)',
			'type'      => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Question category slug','discy'),
			'desc' => esc_html__('Add your question category slug.','discy'),
			'id'   => 'category_question_slug',
			'std'  => 'question-category',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Question tag slug','discy'),
			'desc' => esc_html__('Add your question tag slug.','discy'),
			'id'   => 'tag_question_slug',
			'std'  => 'question-tag',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'add_edit_delete',
			'name' => esc_html__('Add, edit and delete question','discy')
		);
		
		$options[] = array(
			'name' => esc_html__('Any one can ask question without register','discy'),
			'desc' => esc_html__('Any one can ask question without register enable or disable.','discy'),
			'id'   => 'ask_question_no_register',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Charge points for questions settings','discy'),
			'desc' => esc_html__('Select ON if you want to charge points from users for asking questions.','discy'),
			'id'   => 'question_points_active',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'question_points_active:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Charge points for questions','discy'),
			'desc' => esc_html__('How many points should be taken from the user\'s account for asking questions.','discy'),
			'id'   => 'question_points',
			'std'  => '5',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Point back to the user when he select the best answer','discy'),
			'desc' => esc_html__('Point back to the user when he select the best answer.','discy'),
			'id'   => 'point_back',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Or type here the point want back','discy'),
			'desc'      => esc_html__('Or type here the point want back, type 0 to back all the point.','discy'),
			'id'        => 'point_back_number',
			'condition' => 'point_back:not(0)',
			'std'       => '0',
			'type'      => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name'    => esc_html__('Choose question status for users only','discy'),
			'desc'    => esc_html__('Choose question status after user publish the question.','discy'),
			'id'      => 'question_publish',
			'options' => array("publish" => esc_html__("Publish","discy"),"draft" => esc_html__("Draft","discy")),
			'std'     => 'publish',
			'type'    => 'select'
		);
		
		$options[] = array(
			'name'      => esc_html__('Choose question status for unlogged user only','discy'),
			'desc'      => esc_html__('Choose question status after unlogged user publish the question.','discy'),
			'id'        => 'question_publish_unlogged',
			'options'   => array("publish" => esc_html__("Publish","discy"),"draft" => esc_html__("Draft","discy")),
			'std'       => 'draft',
			'type'      => 'select',
			'condition' => 'ask_question_no_register:not(0)',
		);
		
		$options[] = array(
			'name'      => esc_html__('Send email when the question need a review','discy'),
			'desc'      => esc_html__('Email for questions review enable or disable.','discy'),
			'id'        => 'send_email_draft_questions',
			'std'       => 'on',
			'operator'  => 'or',
			'condition' => 'question_publish:not(publish),question_publish_unlogged:not(publish)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Approved auto for the users have a previously approved questions.','discy'),
			'id'        => 'approved_questions',
			'condition' => 'question_publish:not(publish)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Send email for the user to notification a new question','discy'),
			'desc' => esc_html__('Send email enable or disable.','discy'),
			'id'   => 'send_email_new_question',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Send email for custom groups to notification a new question','discy'),
			'id'        => 'send_email_question_groups',
			'type'      => 'multicheck',
			'condition' => 'send_email_new_question:not(0)',
			'std'       => array("editor" => "editor","administrator" => "administrator","author" => "author","contributor" => "contributor","subscriber" => "subscriber"),
			'options'   => discy_options_groups()
		);
		
		$options[] = array(
			'name' => esc_html__('Ask questions','discy'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Ask question slug','discy'),
			'desc' => esc_html__('Put the ask question slug.','discy'),
			'id'   => 'add_questions_slug',
			'std'  => 'add-question',
			'type' => 'text'
		
		);
		
		if (class_exists("WPQA")) {
			$html_content = '<a href="'.wpqa_add_question_permalink().'" target="_blank">'.esc_html__('The Link For The Ask Question Page.','discy').'</a>';
			
			$options[] = array(
				'name' => $html_content,
				'type' => 'info'
			);
		}
		
		$ask_question_items = array(
			"categories_question"  => array("sort" => esc_html__('Question Categories','discy'),"value" => "categories_question"),
			"tags_question"        => array("sort" => esc_html__('Question Tags','discy'),"value" => "tags_question"),
			"poll_question"        => array("sort" => esc_html__('Question Poll','discy'),"value" => "poll_question"),
			"attachment_question"  => array("sort" => esc_html__('Question Attachment','discy'),"value" => "attachment_question"),
			"featured_image"       => array("sort" => esc_html__('Featured image','discy'),"value" => "featured_image"),
			"comment_question"     => array("sort" => esc_html__('Question content','discy'),"value" => "comment_question"),
			"anonymously_question" => array("sort" => esc_html__('Ask Anonymously','discy'),"value" => "anonymously_question"),
			"video_desc_active"    => array("sort" => esc_html__('Video Description','discy'),"value" => "video_desc_active"),
			"private_question"     => array("sort" => esc_html__('Private Question','discy'),"value" => "private_question"),
			"remember_answer"      => array("sort" => esc_html__('Remember Answer','discy'),"value" => "remember_answer"),
			"terms_active"         => array("sort" => esc_html__('Terms Active','discy'),"value" => "terms_active"),
		);
		
		$ask_question_items_std = $ask_question_items;
		unset($ask_question_items_std["attachment_question"]);
		
		$options[] = array(
			'name'    => esc_html__('Select what\'s show at ask question form','discy'),
			'id'      => 'ask_question_items',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $ask_question_items_std,
			'options' => $ask_question_items
		);
		
		$options[] = array(
			'name'      => esc_html__("Category at ask question form single, multi or ajax",'discy'),
			'desc'      => esc_html__("Choose category is show at ask question form single, multi or ajax",'discy'),
			'id'        => 'category_single_multi',
			'std'       => 'single',
			'type'      => 'radio',
			'condition' => 'ask_question_items:has(categories_question)',
			'options'   => 
				array(
					"single" => "Single",
					"multi"  => "Multi",
					"ajax"   => "Ajax"
			)
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_question_items:has(poll_question)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Active image in the poll','discy'),
			'id'   => 'poll_image',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'poll_image:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Active the title in the poll images','discy'),
			'id'   => 'poll_image_title',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Make the title in the poll images required','discy'),
			'id'        => 'poll_image_title_required',
			'condition' => 'poll_image_title:not(0)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_question_items:has(comment_question)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Details in ask question form is required','discy'),
			'id'   => 'comment_question',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Editor enable or disable for details in ask question form','discy'),
			'id'   => 'editor_question_details',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_question_items:has(terms_active)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','discy'),
			'id'      => 'terms_active_target',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","discy"),"new_page" => esc_html__("New page","discy"))
		);
		
		$options[] = array(
			'name'    => esc_html__('Terms page','discy'),
			'desc'    => esc_html__('Select the terms page','discy'),
			'id'      => 'terms_page',
			'type'    => 'select',
			'options' => $options_pages
		);
		
		$options[] = array(
			'name' => esc_html__('Type the terms link if you don\'t like a page','discy'),
			'id'   => 'terms_link',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		
		);
		
		$options[] = array(
			'name' => esc_html__('Edit questions','discy'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Active user can edit the questions','discy'),
			'desc' => esc_html__('Select ON if you want the user can edit the questions.','discy'),
			'id'   => 'question_edit',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'question_edit:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Edit question slug','discy'),
			'desc' => esc_html__('Put the edit question slug.','discy'),
			'id'   => 'edit_questions_slug',
			'std'  => 'edit-question',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('After edit question approved auto or need to approved again?','discy'),
			'desc' => esc_html__('Press ON to approved auto','discy'),
			'id'   => 'question_approved',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('After edit question change the URL like the title?','discy'),
			'desc' => esc_html__('Press ON to edit the URL','discy'),
			'id'   => 'change_question_url',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		
		);
		
		$options[] = array(
			'name' => esc_html__('Delete questions','discy'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Active user can delete the questions','discy'),
			'desc' => esc_html__('Select ON if you want the user can delete the questions.','discy'),
			'id'   => 'question_delete',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('When the users delete the question went to the trash or delete it ever?','discy'),
			'id'        => 'delete_question',
			'options'   => array(
				'delete' => esc_html__('Delete','discy'),
				'trash'  => esc_html__('Trash','discy'),
			),
			'std'       => 'delete',
			'condition' => 'question_delete:not(0)',
			'type'      => 'radio'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Question meta setting','discy'),
			'id'   => 'question_meta',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON if you want to activate the vote with meta.','discy'),
			'id'   => 'question_meta_vote',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON if you want icons only at the question meta.','discy'),
			'id'   => 'question_meta_icon',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'    => esc_html__('Select the meta options','discy'),
			'id'      => 'question_meta',
			'type'    => 'multicheck',
			'std'     => array(
				"author_by"         => "author_by",
				"question_date"     => "question_date",
				"asked_to"          => "asked_to",
				"category_question" => "category_question",
				"question_answer"   => "question_answer",
				"question_views"    => "question_views",
				"bump_meta"         => "bump_meta",
			),
			'options' => array(
				"author_by"         => esc_html__('Author by','discy'),
				"question_date"     => esc_html__('Date meta','discy'),
				"asked_to"          => esc_html__('Asked to meta','discy'),
				"category_question" => esc_html__('Category question','discy'),
				"question_answer"   => esc_html__('Answer meta','discy'),
				"question_views"    => esc_html__('Views stats','discy'),
				"bump_meta"         => esc_html__('Bump question meta','discy'),
			)
		);
		
		$options[] = array(
			'name' => esc_html__('Active user can add the questions at favorite','discy'),
			'desc' => esc_html__('Select ON if you want the user can add the questions at favorite.','discy'),
			'id'   => 'question_favorite',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Active user can follow the questions','discy'),
			'desc' => esc_html__('Select ON if you want the user can follow the questions.','discy'),
			'id'   => 'question_follow',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Active the follow button at questions loop','discy'),
			'desc' => esc_html__('Select ON if you want to activate the follow button at questions loop.','discy'),
			'id'   => 'question_follow_loop',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Questions categories setting','discy'),
			'id'   => 'question_category',
			'type' => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Category description enable or disable','discy'),
			'desc' => esc_html__('Select ON to enable the category description in the category page.','discy'),
			'id'   => 'question_category_description',
			'std'  => 'on',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name'      => esc_html__('Category rss enable or disable','discy'),
			'desc'      => esc_html__('Select ON to enable the category rss in the category page.','discy'),
			'id'        => 'question_category_rss',
			'std'       => 'on',
			'condition' => 'question_category_description:not(0)',
			'type'      => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Active the points by category?','discy'),
			'desc' => esc_html__('The points for categories enable or disable.','discy'),
			'id'   => 'active_points_category',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Active the tabs for questions categorires?','discy'),
			'desc' => esc_html__('The tabs for questions categories enable or disable.','discy'),
			'id'   => 'tabs_category',
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'tabs_category:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Put here the exclude categories ids','discy'),
			'id'   => 'exclude_categories',
			'type' => 'text'
		);

		$category_tabs = array(
			"recent-questions"   => array("sort" => esc_html__('Recent Questions','discy'),"value" => "recent-questions"),
			"most-answers"       => array("sort" => esc_html__('Most Answered','discy'),"value" => "most-answers"),
			"answers"            => array("sort" => esc_html__('Answers','discy'),"value" => "answers"),
			"no-answers"         => array("sort" => esc_html__('No Answers','discy'),"value" => "no-answers"),
			"most-visit"         => array("sort" => esc_html__('Most Visited','discy'),"value" => "most-visit"),
			"most-vote"          => array("sort" => esc_html__('Most Voted','discy'),"value" => "most-vote"),
			"random"             => array("sort" => esc_html__('Random Questions','discy'),"value" => "random"),
			"question-bump"      => array("sort" => esc_html__('Question Bump','discy'),"value" => ""),
			"new-questions"      => array("sort" => esc_html__('New Questions','discy'),"value" => ""),
			"sticky-questions"   => array("sort" => esc_html__('Sticky Questions','discy'),"value" => ""),
			"polls"              => array("sort" => esc_html__('Poll Questions','discy'),"value" => ""),
			
			"recent-questions-2" => array("sort" => esc_html__('Recent Questions With Time','discy'),"value" => ""),
			"most-answers-2"     => array("sort" => esc_html__('Most Answered With Time','discy'),"value" => ""),
			"answers-2"          => array("sort" => esc_html__('Answers With Time','discy'),"value" => ""),
			"no-answers-2"       => array("sort" => esc_html__('No Answers With Time','discy'),"value" => ""),
			"most-visit-2"       => array("sort" => esc_html__('Most Visited With Time','discy'),"value" => ""),
			"most-vote-2"        => array("sort" => esc_html__('Most Voted With Time','discy'),"value" => ""),
			"random-2"           => array("sort" => esc_html__('Random Questions With Time','discy'),"value" => ""),
			"question-bump-2"    => array("sort" => esc_html__('Question Bump With Time','discy'),"value" => ""),
			"new-questions-2"    => array("sort" => esc_html__('New Questions With Time','discy'),"value" => ""),
			"sticky-questions-2" => array("sort" => esc_html__('Sticky Questions With Time','discy'),"value" => ""),
			"polls-2"            => array("sort" => esc_html__('Poll Questions With Time','discy'),"value" => ""),
		);

		$options[] = array(
			'name'    => esc_html__('Select the tabs you want to show','discy'),
			'id'      => 'category_tabs',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $category_tabs,
			'options' => $category_tabs
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'category_tabs:has(recent-questions-2),category_tabs:has(most-answers-2),category_tabs:has(question-bump-2),category_tabs:has(new-questions-2),category_tabs:has(sticky-questions-2),category_tabs:has(polls-2),category_tabs:has(answers-2),category_tabs:has(most-visit-2),category_tabs:has(most-vote-2),category_tabs:has(random-2),category_tabs:has(no-answers-2)',
			'operator'  => 'or',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'type' => 'info',
			'name' => esc_html__('Time frame for the tabs','discy')
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for recent questions tab.','discy'),
			'desc'      => esc_html__('Select the specific date for recent questions tab.','discy'),
			'id'        => "date_recent_questions",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(recent-questions-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','discy'),
				'24'    => esc_html__('Last 24 Hours','discy'),
				'48'    => esc_html__('Last 2 Days','discy'),
				'72'    => esc_html__('Last 3 Days','discy'),
				'96'    => esc_html__('Last 4 Days','discy'),
				'120'   => esc_html__('Last 5 Days','discy'),
				'144'   => esc_html__('Last 6 Days','discy'),
				'week'  => esc_html__('Last Week','discy'),
				'month' => esc_html__('Last Month','discy'),
				'year'  => esc_html__('Last Year','discy'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for most answered tab.','discy'),
			'desc'      => esc_html__('Select the specific date for most answered tab.','discy'),
			'id'        => "date_most_answered",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(most-answers-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','discy'),
				'24'    => esc_html__('Last 24 Hours','discy'),
				'48'    => esc_html__('Last 2 Days','discy'),
				'72'    => esc_html__('Last 3 Days','discy'),
				'96'    => esc_html__('Last 4 Days','discy'),
				'120'   => esc_html__('Last 5 Days','discy'),
				'144'   => esc_html__('Last 6 Days','discy'),
				'week'  => esc_html__('Last Week','discy'),
				'month' => esc_html__('Last Month','discy'),
				'year'  => esc_html__('Last Year','discy'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for question bump tab.','discy'),
			'desc'      => esc_html__('Select the specific date for question bump tab.','discy'),
			'id'        => "date_question_bump",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(question-bump-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','discy'),
				'24'    => esc_html__('Last 24 Hours','discy'),
				'48'    => esc_html__('Last 2 Days','discy'),
				'72'    => esc_html__('Last 3 Days','discy'),
				'96'    => esc_html__('Last 4 Days','discy'),
				'120'   => esc_html__('Last 5 Days','discy'),
				'144'   => esc_html__('Last 6 Days','discy'),
				'week'  => esc_html__('Last Week','discy'),
				'month' => esc_html__('Last Month','discy'),
				'year'  => esc_html__('Last Year','discy'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for answers tab.','discy'),
			'desc'      => esc_html__('Select the specific date for answers tab.','discy'),
			'id'        => "date_answers",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(answers-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','discy'),
				'24'    => esc_html__('Last 24 Hours','discy'),
				'48'    => esc_html__('Last 2 Days','discy'),
				'72'    => esc_html__('Last 3 Days','discy'),
				'96'    => esc_html__('Last 4 Days','discy'),
				'120'   => esc_html__('Last 5 Days','discy'),
				'144'   => esc_html__('Last 6 Days','discy'),
				'week'  => esc_html__('Last Week','discy'),
				'month' => esc_html__('Last Month','discy'),
				'year'  => esc_html__('Last Year','discy'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for most visited tab.','discy'),
			'desc'      => esc_html__('Select the specific date for most visited tab.','discy'),
			'id'        => "date_most_visited",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(most-visit-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','discy'),
				'24'    => esc_html__('Last 24 Hours','discy'),
				'48'    => esc_html__('Last 2 Days','discy'),
				'72'    => esc_html__('Last 3 Days','discy'),
				'96'    => esc_html__('Last 4 Days','discy'),
				'120'   => esc_html__('Last 5 Days','discy'),
				'144'   => esc_html__('Last 6 Days','discy'),
				'week'  => esc_html__('Last Week','discy'),
				'month' => esc_html__('Last Month','discy'),
				'year'  => esc_html__('Last Year','discy'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for most voted tab.','discy'),
			'desc'      => esc_html__('Select the specific date for most voted tab.','discy'),
			'id'        => "date_most_voted",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(most-vote-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','discy'),
				'24'    => esc_html__('Last 24 Hours','discy'),
				'48'    => esc_html__('Last 2 Days','discy'),
				'72'    => esc_html__('Last 3 Days','discy'),
				'96'    => esc_html__('Last 4 Days','discy'),
				'120'   => esc_html__('Last 5 Days','discy'),
				'144'   => esc_html__('Last 6 Days','discy'),
				'week'  => esc_html__('Last Week','discy'),
				'month' => esc_html__('Last Month','discy'),
				'year'  => esc_html__('Last Year','discy'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for no answers tab.','discy'),
			'desc'      => esc_html__('Select the specific date for no answers tab.','discy'),
			'id'        => "date_no_answers",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(no-answers-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','discy'),
				'24'    => esc_html__('Last 24 Hours','discy'),
				'48'    => esc_html__('Last 2 Days','discy'),
				'72'    => esc_html__('Last 3 Days','discy'),
				'96'    => esc_html__('Last 4 Days','discy'),
				'120'   => esc_html__('Last 5 Days','discy'),
				'144'   => esc_html__('Last 6 Days','discy'),
				'week'  => esc_html__('Last Week','discy'),
				'month' => esc_html__('Last Month','discy'),
				'year'  => esc_html__('Last Year','discy'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for random questions tab.','discy'),
			'desc'      => esc_html__('Select the specific date for random questions tab.','discy'),
			'id'        => "date_random_questions",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(random-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','discy'),
				'24'    => esc_html__('Last 24 Hours','discy'),
				'48'    => esc_html__('Last 2 Days','discy'),
				'72'    => esc_html__('Last 3 Days','discy'),
				'96'    => esc_html__('Last 4 Days','discy'),
				'120'   => esc_html__('Last 5 Days','discy'),
				'144'   => esc_html__('Last 6 Days','discy'),
				'week'  => esc_html__('Last Week','discy'),
				'month' => esc_html__('Last Month','discy'),
				'year'  => esc_html__('Last Year','discy'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for new questions tab.','discy'),
			'desc'      => esc_html__('Select the specific date for new questions tab.','discy'),
			'id'        => "date_new_questions",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(new-questions-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','discy'),
				'24'    => esc_html__('Last 24 Hours','discy'),
				'48'    => esc_html__('Last 2 Days','discy'),
				'72'    => esc_html__('Last 3 Days','discy'),
				'96'    => esc_html__('Last 4 Days','discy'),
				'120'   => esc_html__('Last 5 Days','discy'),
				'144'   => esc_html__('Last 6 Days','discy'),
				'week'  => esc_html__('Last Week','discy'),
				'month' => esc_html__('Last Month','discy'),
				'year'  => esc_html__('Last Year','discy'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for sticky questions tab.','discy'),
			'desc'      => esc_html__('Select the specific date for sticky questions tab.','discy'),
			'id'        => "date_sticky_questions",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(sticky-questions-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','discy'),
				'24'    => esc_html__('Last 24 Hours','discy'),
				'48'    => esc_html__('Last 2 Days','discy'),
				'72'    => esc_html__('Last 3 Days','discy'),
				'96'    => esc_html__('Last 4 Days','discy'),
				'120'   => esc_html__('Last 5 Days','discy'),
				'144'   => esc_html__('Last 6 Days','discy'),
				'week'  => esc_html__('Last Week','discy'),
				'month' => esc_html__('Last Month','discy'),
				'year'  => esc_html__('Last Year','discy'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for poll questions tab.','discy'),
			'desc'      => esc_html__('Select the specific date for poll questions tab.','discy'),
			'id'        => "date_poll_questions",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(polls-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','discy'),
				'24'    => esc_html__('Last 24 Hours','discy'),
				'48'    => esc_html__('Last 2 Days','discy'),
				'72'    => esc_html__('Last 3 Days','discy'),
				'96'    => esc_html__('Last 4 Days','discy'),
				'120'   => esc_html__('Last 5 Days','discy'),
				'144'   => esc_html__('Last 6 Days','discy'),
				'week'  => esc_html__('Last Week','discy'),
				'month' => esc_html__('Last Month','discy'),
				'year'  => esc_html__('Last Year','discy'),
			)
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'category_tabs:has(recent-questions),category_tabs:has(most-answers),category_tabs:has(question-bump),category_tabs:has(new-questions),category_tabs:has(sticky-questions),category_tabs:has(polls),category_tabs:has(answers),category_tabs:has(most-visit),category_tabs:has(most-vote),category_tabs:has(random),category_tabs:has(no-answers),category_tabs:has(recent-questions-2),category_tabs:has(most-answers-2),category_tabs:has(question-bump-2),category_tabs:has(new-questions-2),category_tabs:has(sticky-questions-2),category_tabs:has(polls-2),category_tabs:has(answers-2),category_tabs:has(most-visit-2),category_tabs:has(most-vote-2),category_tabs:has(random-2),category_tabs:has(no-answers-2)',
			'operator'  => 'or',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'type' => 'info',
			'name' => esc_html__('Custom setting for the slugs','discy')
		);

		$options[] = array(
			'name'      => esc_html__('Recent questions slug','discy'),
			'id'        => 'recent_questions_slug',
			'std'       => 'recent-questions',
			'condition' => 'category_tabs:has(recent-questions)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Most answered slug','discy'),
			'id'        => 'most_answers_slug',
			'std'       => 'most-answered',
			'condition' => 'category_tabs:has(most-answers)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question bump slug','discy'),
			'id'        => 'question_bump_slug',
			'std'       => 'question-bump',
			'condition' => 'category_tabs:has(question-bump)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('New questions slug','discy'),
			'id'        => 'question_new_slug',
			'std'       => 'new',
			'condition' => 'category_tabs:has(new-questions)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question sticky slug','discy'),
			'id'        => 'question_sticky_slug',
			'std'       => 'sticky',
			'condition' => 'category_tabs:has(sticky-questions)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question polls slug','discy'),
			'id'        => 'question_polls_slug',
			'std'       => 'polls',
			'condition' => 'category_tabs:has(polls)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Answers slug','discy'),
			'id'        => 'answers_slug',
			'std'       => 'answers',
			'condition' => 'category_tabs:has(answers)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Most visited slug','discy'),
			'id'        => 'most_visit_slug',
			'std'       => 'most-visited',
			'condition' => 'category_tabs:has(most-visit)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Most voted slug','discy'),
			'id'        => 'most_vote_slug',
			'std'       => 'most-voted',
			'condition' => 'category_tabs:has(most-vote)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Random slug','discy'),
			'id'        => 'random_slug',
			'std'       => 'random',
			'condition' => 'category_tabs:has(random)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('No answers slug','discy'),
			'id'        => 'no_answers_slug',
			'std'       => 'no-answers',
			'condition' => 'category_tabs:has(no-answers)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Recent questions with time slug','discy'),
			'id'        => 'recent_questions_slug_2',
			'std'       => 'recent-questions-time',
			'condition' => 'category_tabs:has(recent-questions-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Most answered with time slug','discy'),
			'id'        => 'most_answers_slug_2',
			'std'       => 'most-answered-time',
			'condition' => 'category_tabs:has(most-answers-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question bump with time slug','discy'),
			'id'        => 'question_bump_slug_2',
			'std'       => 'question-bump-time',
			'condition' => 'category_tabs:has(question-bump-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('New questions with time slug','discy'),
			'id'        => 'question_new_slug_2',
			'std'       => 'new-time',
			'condition' => 'category_tabs:has(new-questions-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question sticky with time slug','discy'),
			'id'        => 'question_sticky_slug_2',
			'std'       => 'sticky-time',
			'condition' => 'category_tabs:has(sticky-questions-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question polls with time slug','discy'),
			'id'        => 'question_polls_slug_2',
			'std'       => 'polls-time',
			'condition' => 'category_tabs:has(polls-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Answers with time slug','discy'),
			'id'        => 'answers_slug_2',
			'std'       => 'answers-time',
			'condition' => 'category_tabs:has(answers-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Most visited with time slug','discy'),
			'id'        => 'most_visit_slug_2',
			'std'       => 'most-visited-time',
			'condition' => 'category_tabs:has(most-visit-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Most voted with time slug','discy'),
			'id'        => 'most_vote_slug_2',
			'std'       => 'most-voted-time',
			'condition' => 'category_tabs:has(most-vote-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Random with time slug','discy'),
			'id'        => 'random_slug_2',
			'std'       => 'random-time',
			'condition' => 'category_tabs:has(random-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('No answers with time slug','discy'),
			'id'        => 'no_answers_slug_2',
			'std'       => 'no-answers-time',
			'condition' => 'category_tabs:has(no-answers-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'questions_loop',
			'name' => esc_html__('Questions & Loop settings','discy')
		);
		
		$options[] = array(
			'name'      => esc_html__('Columns in the archive, taxonomy and tags pages','discy'),
			'id'		=> "question_columns",
			'type'		=> 'radio',
			'options'	=> array(
				'style_1' => esc_html__('1 column','discy'),
				'style_2' => esc_html__('2 columns','discy')." - ".esc_html__('Works with sidebar, full width, and left menu only.','discy'),
			),
			'std'		=> '1_column'
		);
		
		$options[] = array(
			'name' => esc_html__('Active the author image in questions loop?','discy'),
			'desc' => esc_html__('Author image in questions loop enable or disable.','discy'),
			'id'   => 'author_image',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Active the vote in loop?','discy'),
			'desc' => esc_html__('Vote in loop enable or disable.','discy'),
			'id'   => 'vote_question_loop',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Select ON to hide the dislike at questions loop','discy'),
			'desc'      => esc_html__('If you put it ON the dislike will not show.','discy'),
			'id'        => 'question_loop_dislike',
			'condition' => 'vote_question_loop:not(0)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to show the poll in questions loop','discy'),
			'id'   => 'question_poll_loop',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to hide the excerpt in questions','discy'),
			'id'   => 'excerpt_questions',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'excerpt_questions:is(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Excerpt question','discy'),
			'desc' => esc_html__('Put here the excerpt question.','discy'),
			'id'   => 'question_excerpt',
			'std'  => 40,
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to active the read more button in questions','discy'),
			'id'   => 'read_more_question',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Video description settings at the question loop','discy'),
			'desc' => esc_html__('Select ON if you want to let users to add video with their question.','discy'),
			'id'   => 'video_desc_active_loop',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'video_desc_active_loop:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Video description position at the question loop','discy'),
			'desc'    => esc_html__('Choose the video description position.','discy'),
			'id'      => 'video_desc_loop',
			'options' => array("before" => "Before content","after" => "After content"),
			'std'     => 'after',
			'type'    => 'select'
		);
		
		$options[] = array(
			'name' => esc_html__('Set the video description with 100%?','discy'),
			'desc' => esc_html__('Select ON if you want to set the video description 100%.','discy'),
			'id'   => 'video_desc_100_loop',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			"name"      => esc_html__("Set the width for the video description for the questions",'discy'),
			"id"        => "video_description_width",
			'condition' => 'video_desc_100_loop:not(on)',
			"type"      => "sliderui",
			'std'       => 260,
			"step"      => "1",
			"min"       => "50",
			"max"       => "600"
		);
		
		$options[] = array(
			"name" => esc_html__("Set the height for the video description for the questions",'discy'),
			"id"   => "video_description_height",
			"type" => "sliderui",
			'std'  => 500,
			"step" => "1",
			"min"  => "50",
			"max"  => "600"
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('Click ON to show featured image in the questions','discy'),
			'id'   => 'featured_image_loop',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'featured_image_loop:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to enable the lightbox for featured image','discy'),
			'id'   => 'featured_image_question_lightbox',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			"name" => esc_html__("Set the width for the featured image for the questions",'discy'),
			"id"   => "featured_image_question_width",
			"type" => "sliderui",
			'std'  => 260,
			"step" => "1",
			"min"  => "50",
			"max"  => "600"
		);
		
		$options[] = array(
			"name" => esc_html__("Set the height for the featured image for the questions",'discy'),
			"id"   => "featured_image_question_height",
			"type" => "sliderui",
			'std'  => 185,
			"step" => "1",
			"min"  => "50",
			"max"  => "600"
		);
		
		$options[] = array(
			'name'    => esc_html__('Featured image position','discy'),
			'desc'    => esc_html__('Choose the featured image position.','discy'),
			'id'      => 'featured_position',
			'options' => array("before" => "Before content","after" => "After content"),
			'std'     => 'before',
			'type'    => 'select'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('Tags at loop enable or disable','discy'),
			'id'   => 'question_tags_loop',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Active the answer at loop by best answer, most voted, last answer or first answer','discy'),
			'id'   => 'question_answer_loop',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Answer type','discy'),
			'desc'      => esc_html__('Choose what\'s the answer you need to show from here.','discy'),
			'id'        => 'question_answer_show',
			'condition' => 'question_answer_loop:not(0)',
			'options'   => array(
				'best'   => esc_html__('Best answer','discy'),
				'vote'   => esc_html__('Most voted','discy'),
				'last'   => esc_html__('Last answer','discy'),
				'oldest' => esc_html__('First answer','discy'),
			),
			'std'       => 'best',
			'type'      => 'radio'
		);
		
		$options[] = array(
			'name'    => esc_html__('Pagination style','discy'),
			'desc'    => esc_html__('Choose pagination style from here.','discy'),
			'id'      => 'question_pagination',
			'options' => array(
				'standard'        => esc_html__('Standard','discy'),
				'pagination'      => esc_html__('Pagination','discy'),
				'load_more'       => esc_html__('Load more','discy'),
				'infinite_scroll' => esc_html__('Infinite scroll','discy'),
				'none'            => esc_html__('None','discy'),
			),
			'std'     => 'pagination',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Inner question','discy'),
			'id'   => 'inner_question',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Active poll for user only?','discy'),
			'desc' => esc_html__('Select ON if you want the poll allow to users only.','discy'),
			'id'   => 'poll_user_only',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_question_items:has(video_desc_active)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('video description position','discy'),
			'desc'    => esc_html__('Choose the video description position.','discy'),
			'id'      => 'video_desc',
			'options' => array("before" => esc_html__("Before content","discy"),"after" => esc_html__("After content","discy")),
			'std'     => 'after',
			'type'    => 'select'
		);
		
		$options[] = array(
			"name" => esc_html__("Set the height for the video description for the questions",'discy'),
			"id"   => "video_desc_height",
			"type" => "sliderui",
			'std'  => 500,
			"step" => "1",
			"min"  => "50",
			"max"  => "600"
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Click ON to show featured image in the single question','discy'),
			'id'   => 'featured_image_single',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Active the author image in single?','discy'),
			'desc' => esc_html__('Author image in single enable or disable.','discy'),
			'id'   => 'author_image_single',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Active the vote in single?','discy'),
			'desc' => esc_html__('Vote in single enable or disable.','discy'),
			'id'   => 'vote_question_single',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Select ON to hide the dislike at questions single','discy'),
			'desc'      => esc_html__('If you put it ON the dislike will not show.','discy'),
			'id'        => 'question_single_dislike',
			'condition' => 'vote_question_single:not(0)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Active close and open questions','discy'),
			'desc' => esc_html__('Select ON if you want active close and open questions.','discy'),
			'id'   => 'question_close',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Active close and open questions for the admin only','discy'),
			'desc' => esc_html__('Select ON if you want active close and open questions for the admin only.','discy'),
			'id'   => 'question_close_admin',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Share style at the inner question page.','discy'),
			'id'        => 'share_style',
			'std'       => 'style_1',
			'type'      => 'radio',
			'condition' => 'question_simple:not(on)',
			'options'   => 
				array(
					"style_1" => esc_html__("Style 1","discy"),
					"style_2" => esc_html__("Style 2","discy"),
				)
		);
		
		$options[] = array(
			'name' => esc_html__('Tags at single question enable or disable','discy'),
			'desc' => esc_html__('Select ON if you want active tags at single question.','discy'),
			'id'   => 'question_tags',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Navigation question enable or disable','discy'),
			'desc' => esc_html__('Navigation question (next and previous questions) enable or disable.','discy'),
			'id'   => 'question_navigation',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Navigation question for the same category only?','discy'),
			'desc'      => esc_html__('Navigation question (next and previous questions) for the same category only?','discy'),
			'id'        => 'question_nav_category',
			'condition' => 'question_navigation:not(0)',
			'std'       => 'on',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Answers enable or disable','discy'),
			'desc' => esc_html__('Select ON if you want active the answers.','discy'),
			'id'   => 'question_answers',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Share setting','discy'),
			'id'   => 'share_setting_q',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Select the share options','discy'),
			'id'      => 'question_share',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $share_array,
			'options' => $share_array
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Questions layout','discy'),
			'id'   => 'questions_layout',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Question sidebar layout','discy'),
			'id'   => "question_sidebar_layout",
			'std'  => "default",
			'type' => "images",
			'options' => array(
				'default'      => $imagepath.'sidebar_default.jpg',
				'menu_sidebar' => $imagepath.'menu_sidebar.jpg',
				'right'        => $imagepath.'sidebar_right.jpg',
				'full'         => $imagepath.'sidebar_no.jpg',
				'left'         => $imagepath.'sidebar_left.jpg',
				'centered'     => $imagepath.'centered.jpg',
				'menu_left'    => $imagepath.'menu_left.jpg',
			)
		);
		
		$options[] = array(
			'name'      => esc_html__('Question Page sidebar','discy'),
			'id'        => "question_sidebar",
			'std'       => '',
			'options'   => $new_sidebars,
			'type'      => 'select',
			'condition' => 'question_sidebar_layout:not(full),question_sidebar_layout:not(centered),question_sidebar_layout:not(menu_left)'
		);
		
		$options[] = array(
			'name'      => esc_html__('Question Page sidebar 2','discy'),
			'id'        => "question_sidebar_2",
			'std'       => '',
			'options'   => $new_sidebars,
			'type'      => 'select',
			'operator'  => 'or',
			'condition' => 'question_sidebar_layout:is(menu_sidebar),question_sidebar_layout:is(menu_left)'
		);
		
		$options[] = array(
			'name'    => esc_html__('Choose Your Skin','discy'),
			'class'   => "site_skin",
			'id'      => "question_skin",
			'std'     => "default",
			'type'    => "images",
			'options' => array(
				'default'    => $imagepath.'default_color.jpg',
				'skin'       => $imagepath.'default.jpg',
				'violet'     => $imagepath.'violet.jpg',
				'bright_red' => $imagepath.'bright_red.jpg',
				'green'      => $imagepath.'green.jpg',
				'red'        => $imagepath.'red.jpg',
				'cyan'       => $imagepath.'cyan.jpg',
				'blue'       => $imagepath.'blue.jpg',
			)
		);
		
		$options[] = array(
			'name' => esc_html__('Primary Color','discy'),
			'id'   => 'question_primary_color',
			'type' => 'color' 
		);
		
		$options[] = array(
			'name'    => esc_html__('Background Type','discy'),
			'id'      => 'question_background_type',
			'std'     => 'default',
			'type'    => 'radio',
			'options' => 
				array(
					"default"           => esc_html__("Default","discy"),
					"none"              => esc_html__("None","discy"),
					"patterns"          => esc_html__("Patterns","discy"),
					"custom_background" => esc_html__("Custom Background","discy")
				)
		);
	
		$options[] = array(
			'name'      => esc_html__('Background Color','discy'),
			'id'        => 'question_background_color',
			'type'      => 'color',
			'condition' => 'question_background_type:is(patterns)'
		);
			
		$options[] = array(
			'name'      => esc_html__('Choose Pattern','discy'),
			'id'        => "question_background_pattern",
			'std'       => "bg13",
			'type'      => "images",
			'condition' => 'question_background_type:is(patterns)',
			'class'     => "pattern_images",
			'options'   => array(
				'bg1'  => $imagepath.'bg1.jpg',
				'bg2'  => $imagepath.'bg2.jpg',
				'bg3'  => $imagepath.'bg3.jpg',
				'bg4'  => $imagepath.'bg4.jpg',
				'bg5'  => $imagepath.'bg5.jpg',
				'bg6'  => $imagepath.'bg6.jpg',
				'bg7'  => $imagepath.'bg7.jpg',
				'bg8'  => $imagepath.'bg8.jpg',
				'bg9'  => $imagepath_theme.'patterns/bg9.png',
				'bg10' => $imagepath_theme.'patterns/bg10.png',
				'bg11' => $imagepath_theme.'patterns/bg11.png',
				'bg12' => $imagepath_theme.'patterns/bg12.png',
				'bg13' => $imagepath.'bg13.jpg',
				'bg14' => $imagepath.'bg14.jpg',
				'bg15' => $imagepath_theme.'patterns/bg15.png',
				'bg16' => $imagepath_theme.'patterns/bg16.png',
				'bg17' => $imagepath.'bg17.jpg',
				'bg18' => $imagepath.'bg18.jpg',
				'bg19' => $imagepath.'bg19.jpg',
				'bg20' => $imagepath.'bg20.jpg',
				'bg21' => $imagepath_theme.'patterns/bg21.png',
				'bg22' => $imagepath.'bg22.jpg',
				'bg23' => $imagepath_theme.'patterns/bg23.png',
				'bg24' => $imagepath_theme.'patterns/bg24.png',
			)
		);
	
		$options[] = array(
			'name'      => esc_html__('Custom Background','discy'),
			'id'        => 'question_custom_background',
			'std'       => $background_defaults,
			'type'      => 'background',
			'options'   => $background_defaults,
			'condition' => 'question_background_type:is(custom_background)'
		);
			
		$options[] = array(
			'name'      => esc_html__('Full Screen Background','discy'),
			'desc'      => esc_html__('Select ON to Full Screen Background','discy'),
			'id'        => 'question_full_screen_background',
			'type'      => 'checkbox',
			'condition' => 'question_background_type:is(custom_background)'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options = apply_filters('discy_options_after_questions_layout',$options);

		$paymeny_setting = array(
			"payments_settings" => esc_html__('Payment setting','discy'),
			"buy_points"        => esc_html__('Buy points','discy'),
			"pay_to_ask"        => esc_html__('Pay to ask','discy'),
			"pay_to_sticky"     => esc_html__('Pay to sticky question','discy'),
			"coupons_setting"   => esc_html__('Coupons setting','discy'),
		);

		$options[] = array(
			'name'    => esc_html__('Payment setting','discy'),
			'id'      => 'payment_setting',
			'icon'    => 'tickets-alt',
			'type'    => 'heading',
			'std'     => 'payments_settings',
			'options' => apply_filters("discy_paymeny_setting",$paymeny_setting)
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'payments_settings',
			'name' => esc_html__('Payment setting','discy')
		);
		
		$options[] = array(
			'name' => esc_html__('Enable PayPal sandbox','discy'),
			'desc' => esc_html__('PayPal sandbox can be used to test payments.','discy'),
			'id'   => 'paypal_sandbox',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'    => esc_html__('Currency code','discy'),
			'desc'    => esc_html__('Choose form here the currency code.','discy'),
			'id'      => 'currency_code',
			'std'     => 'USD',
			'type'    => "select",
			'options' => array(
				'USD' => 'USD',
				'EUR' => 'EUR',
				'GBP' => 'GBP',
				'JPY' => 'JPY',
				'CAD' => 'CAD'
			)
		);
		
		$options[] = array(
			'name' => esc_html__("PayPal email",'discy'),
			'desc' => esc_html__("put your PayPal email",'discy'),
			'id'   => 'paypal_email',
			'std'  => get_bloginfo("admin_email"),
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__("PayPal Identity Token",'discy'),
			'desc' => esc_html__("Add your PayPal Identity Token",'discy'),
			'id'   => 'identity_token',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Buy points','discy'),
			'id'   => 'buy_points',
			'type' => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Buy points','discy'),
			'desc' => esc_html__('Select ON to active buy points.','discy'),
			'id'   => 'buy_points_payment',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'buy_points_payment:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Buy points slug','discy'),
			'desc' => esc_html__('Put the buy points slug.','discy'),
			'id'   => 'buy_points_slug',
			'std'  => 'buy-points',
			'type' => 'text'
		);

		if (class_exists("WPQA") && function_exists("wpqa_buy_points_permalink")) {
			$html_content = '<a href="'.wpqa_buy_points_permalink().'" target="_blank">'.esc_html__('The Link For The Buy Points Page.','discy').'</a>';
			
			$options[] = array(
				'name' => $html_content,
				'type' => 'info'
			);
		}

		$buy_points_elements = array(
			array(
				"type" => "text",
				"id"   => "package_name",
				"name" => esc_html__('Package name','discy')
			),
			array(
				"type" => "text",
				"id"   => "package_points",
				"name" => esc_html__('Points','discy')
			),
			array(
				"type" => "text",
				"id"   => "package_price",
				"name" => esc_html__('Price','discy')
			),
			array(
				"type" => "text",
				"id"   => "package_description",
				"name" => esc_html__('Package description','discy')
			)
		);
		
		$options[] = array(
			'id'      => "buy_points",
			'type'    => "elements",
			'sort'    => "no",
			'hide'    => "yes",
			'button'  => esc_html__('Add new package','discy'),
			'options' => $buy_points_elements,
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Pay to ask','discy'),
			'id'   => 'pay_to_ask',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Pay to ask question','discy'),
			'desc' => esc_html__('Select ON to active the pay to ask question.','discy'),
			'id'   => 'pay_ask',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'pay_ask:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name'    => esc_html__('Payment type','discy'),
			'desc'    => esc_html__('Choose the payment type for the ask question','discy'),
			'id'      => 'payment_type_ask',
			'std'     => 'paypal',
			'type'    => 'radio',
			'options' => 
				array(
					"paypal" => esc_html__('By PayPal','discy'),
					"points" => esc_html__('By points','discy')
			)
		);
		
		$options[] = array(
			'name'    => esc_html__("Choose the groups add a question without pay.",'discy'),
			'id'      => 'payment_group',
			'type'    => 'multicheck',
			'options' => $new_roles
		);
		
		$options[] = array(
			"name"      => esc_html__("What's price to ask a new question?",'discy'),
			"desc"      => esc_html__("Type here price of the payment to ask a new question",'discy'),
			"id"        => "pay_ask_payment",
			"type"      => "text",
			'condition' => 'payment_type_ask:not(points)',
			'std'       => 10
		);
		
		$options[] = array(
			"name"      => esc_html__("What's points to ask a new question?",'discy'),
			"desc"      => esc_html__("Type here points of the payment to ask a new question",'discy'),
			"id"        => "ask_payment_points",
			"type"      => "text",
			'condition' => 'payment_type_ask:is(points)',
			'std'       => 20
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Pay to sticky question','discy'),
			'id'   => 'pay_to_sticky',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Pay to sticky question at the top','discy'),
			'desc' => esc_html__('Select ON to active the pay to sticky question.','discy'),
			'id'   => 'pay_to_sticky',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'pay_to_sticky:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name'    => esc_html__('Payment type','discy'),
			'desc'    => esc_html__('Choose the payment type sticky the question','discy'),
			'id'      => 'payment_type_sticky',
			'std'     => 'paypal',
			'type'    => 'radio',
			'options' => 
				array(
					"paypal" => esc_html__('By PayPal','discy'),
					"points" => esc_html__('By points','discy')
			)
		);
		
		$options[] = array(
			"name"      => esc_html__("What's price to sticky the question?",'discy'),
			"desc"      => esc_html__("Type here price of the payment to sticky the question.",'discy'),
			"id"        => "pay_sticky_payment",
			"type"      => "text",
			'condition' => 'payment_type_sticky:not(points)',
			'std'       => 5
		);
		
		$options[] = array(
			"name"      => esc_html__("What's points to sticky the question?",'discy'),
			"desc"      => esc_html__("Type here points of the payment to sticky the question",'discy'),
			"id"        => "sticky_payment_points",
			"type"      => "text",
			'condition' => 'payment_type_sticky:is(points)',
			'std'       => 10
		);
		
		$options[] = array(
			"name" => esc_html__("What's days to sticky the question?",'discy'),
			"desc" => esc_html__("Type here days of the payment to sticky the question.",'discy'),
			"id"   => "days_sticky",
			"type" => "sliderui",
			'std'  => 7,
			"step" => "1",
			"min"  => "0",
			"max"  => "365"
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Coupons setting','discy'),
			'id'   => 'coupons_setting',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Active the Coupons','discy'),
			'desc' => esc_html__('Select ON to active the coupons.','discy'),
			'id'   => 'active_coupons',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_coupons:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Show the free coupons when make any payment?','discy'),
			'desc' => esc_html__('Select ON to show the free coupons.','discy'),
			'id'   => 'free_coupons',
			'type' => 'checkbox'
		);
		
		$coupon_elements = array(
			array(
				"type" => "text",
				"id"   => "coupon_name",
				"name" => esc_html__('Coupons name','discy')
			),
			array(
				"type"    => "select",
				"id"      => "coupon_type",
				"name"    => esc_html__('Discount type','discy'),
				"options" => array("discount" => esc_html__("Discount",'discy'),"percent" => esc_html__("% Percent",'discy'))
			),
			array(
				"type" => "text",
				"id"   => "coupon_amount",
				"name" => esc_html__('Amount','discy')
			),
			array(
				"type" => "date",
				"id"   => "coupon_date",
				"name" => esc_html__('Expiry date','discy')
			)
		);
		
		$options[] = array(
			'id'      => "coupons",
			'type'    => "elements",
			'sort'    => "no",
			'hide'    => "yes",
			'button'  => esc_html__('Add new coupon','discy'),
			'options' => $coupon_elements,
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options = apply_filters('discy_options_after_coupons_setting',$options);
	}
	
	$options[] = array(
		'name' => esc_html__('Captcha setting','discy'),
		'id'   => 'captcha',
		'icon' => 'admin-network',
		'type' => 'heading'
	);
	
	$options[] = array(
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Captcha enable or disable (in ask question form)','discy'),
		'id'   => 'the_captcha',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Captcha enable or disable (in add post form)','discy'),
		'id'   => 'the_captcha_post',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Captcha enable or disable (in register form)','discy'),
		'id'   => 'the_captcha_register',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Captcha enable or disable (in login form)','discy'),
		'id'   => 'the_captcha_login',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Captcha enable or disable (in answer form)','discy'),
		'id'   => 'the_captcha_answer',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Captcha enable or disable (in comment form)','discy'),
		'id'   => 'the_captcha_comment',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Captcha enable or disable (in send message form)','discy'),
		'id'   => 'the_captcha_message',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Captcha works for unlogged users or unlogged and logged users','discy'),
		'id'        => 'captcha_users',
		'std'       => 'unlogged',
		'operator'  => 'or',
		'condition' => 'the_captcha:not(0),the_captcha_post:not(0),the_captcha_answer:not(0),the_captcha_comment:not(0),the_captcha_message:not(0)',
		'type'      => 'radio',
		'options'   => 
			array(
				"unlogged" => esc_html__('Unlogged users','discy'),
				"both"     => esc_html__('Unlogged and logged users','discy')
		)
	);
	
	$options[] = array(
		'div'       => 'div',
		'operator'  => 'or',
		'condition' => 'the_captcha:not(0),the_captcha_post:not(0),the_captcha_register:not(0),the_captcha_login:not(0),the_captcha_answer:not(0),the_captcha_comment:not(0),the_captcha_message:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Captcha style','discy'),
		'desc'    => esc_html__('Choose the captcha style','discy'),
		'id'      => 'captcha_style',
		'std'     => 'question_answer',
		'type'    => 'radio',
		'options' => 
			array(
				"question_answer" => esc_html__('Question and answer','discy'),
				"normal_captcha"  => esc_html__('Normal captcha','discy')
		)
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'captcha_style:not(normal_captcha)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Captcha answer enable or disable in forms','discy'),
		'id'   => 'show_captcha_answer',
		'std'  => "on",
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Captcha question','discy'),
		'desc' => esc_html__('put the Captcha question','discy'),
		'id'   => 'captcha_question',
		'type' => 'text',
		'std'  => "What is the capital of Egypt?"
	);
	
	$options[] = array(
		'name' => esc_html__('Captcha answer','discy'),
		'desc' => esc_html__('put the Captcha answer','discy'),
		'id'   => 'captcha_answer',
		'type' => 'text',
		'std'  => "Cairo"
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('User setting','discy'),
		'id'   => 'user',
		'icon' => 'admin-users',
		'type' => 'heading',
		'std'     => 'setting_profile',
		'options' => array(
			"setting_profile"  => esc_html__('General Setting','discy'),
			"register_setting" => esc_html__('Register Setting','discy'),
			"edit_profile"     => esc_html__('Edit Profile','discy'),
			"ask_users"        => esc_html__('Ask Users','discy'),
			"user_slugs"       => esc_html__('User Slugs','discy'),
			"permissions"      => esc_html__('Permissions','discy'),
			"author_setting"   => esc_html__('Author Setting','discy')
		)
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'setting_profile',
		'name' => esc_html__('General Setting','discy')
	);
	
	$options[] = array(
		'name' => esc_html__('Author info box enable or disable.','discy'),
		'id'   => 'author_box',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Cover image enable or disable.','discy'),
		'id'   => 'cover_image',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Cover full width or fixed','discy'),
		'desc'      => esc_html__('Choose the cover to make it work with full width or fixed.','discy'),
		'id'        => 'cover_fixed',
		'options'   => array(
			'normal' => esc_html__('Full width','discy'),
			'fixed'  => esc_html__('Fixed','discy'),
		),
		'std'       => 'normal',
		'condition' => 'cover_image:is(on)',
		'type'      => 'radio'
	);
	
	$options[] = array(
		'name' => esc_html__('Profile by login or nicename','discy'),
		'desc' => esc_html__('Choose the user profile page work by login or nicename.','discy'),
		'id'   => 'profile_type',
		'options' => array(
			'nicename' => esc_html__('Nicename','discy'),
			'login'    => esc_html__('Login name','discy'),
		),
		'std'     => 'nicename',
		'type'    => 'radio'
	);
	
	$options[] = array(
		'name' => esc_html__('Active the notifications system in site?','discy'),
		'desc' => esc_html__('Active the notifications system enable or disable.','discy'),
		'id'   => 'active_notifications',
		'std'  => "on",
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Active the activity log site?','discy'),
		'desc' => esc_html__('Active the activity log enable or disable.','discy'),
		'id'   => 'active_activity_log',
		'std'  => "on",
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'    => esc_html__('Select the user stats','discy'),
		'id'      => 'user_stats',
		'type'    => 'multicheck',
		'std'     => array(
			"questions"    => "questions",
			"answers"      => "answers",
			"best_answers" => "best_answers",
			"points"       => "points",
		),
		'options' => array(
			"questions"    => esc_html__('Questions','discy'),
			"answers"      => esc_html__('Answers','discy'),
			"best_answers" => esc_html__('Best Answers','discy'),
			"points"       => esc_html__('Points','discy'),
		)
	);
	
	$options[] = array(
		'name'    => "",
		"margin"  => "-15px 0 0",
		'id'      => 'user_stats_2',
		'type'    => 'multicheck',
		'std'     => array(
			"i_follow"     => "i_follow",
			"followers"    => "followers",
		),
		'options' => array(
			"i_follow"     => esc_html__('Authors I Follow','discy'),
			"followers"    => esc_html__('Followers','discy'),
		)
	);
	
	$user_profile_pages = array(
		"questions"           => array("sort" => esc_html__('Questions','discy'),"value" => "questions"),
		"polls"               => array("sort" => esc_html__('Polls','discy'),"value" => "polls"),
		"answers"             => array("sort" => esc_html__('Answers','discy'),"value" => "answers"),
		"best-answers"        => array("sort" => esc_html__('Best Answers','discy'),"value" => "best-answers"),
		"asked"               => array("sort" => esc_html__('Asked Questions','discy'),"value" => "asked"),
		"asked-questions"     => array("sort" => esc_html__('Waiting Questions','discy'),"value" => "asked-questions"),
		"paid-questions"      => array("sort" => esc_html__('Paid Questions','discy'),"value" => "paid-questions"),
		"followed"            => array("sort" => esc_html__('Followed Questions','discy'),"value" => "followed"),
		"favorites"           => array("sort" => esc_html__('Favorite Questions','discy'),"value" => "favorites"),
		"posts"               => array("sort" => esc_html__('Posts','discy'),"value" => "posts"),
		"comments"            => array("sort" => esc_html__('Comments','discy'),"value" => "comments"),
		"followers-questions" => array("sort" => esc_html__('Followers Questions','discy'),"value" => "followers-questions"),
		"followers-answers"   => array("sort" => esc_html__('Followers Answers','discy'),"value" => "followers-answers"),
		"followers-posts"     => array("sort" => esc_html__('Followers Posts','discy'),"value" => "followers-posts"),
		"followers-comments"  => array("sort" => esc_html__('Followers Comments','discy'),"value" => "followers-comments"),
	);
	
	$options[] = array(
		'name'         => esc_html__('Select the pages show at the user profile page','discy'),
		'id'           => 'user_profile_pages',
		'type'         => 'multicheck',
		'sort'         => 'yes',
		'limit-height' => 'yes',
		'std'          => $user_profile_pages,
		'options'      => $user_profile_pages
	);
	
	$options[] = array(
		'name'    => esc_html__('Select the columns in the user admin','discy'),
		'id'      => 'user_meta_admin',
		'type'    => 'multicheck',
		'options' => array(
			"phone"   => esc_html__('Phone','discy'),
			"country" => esc_html__('Country','discy'),
			"age"     => esc_html__('Age','discy'),
		)
	);
	
	$options[] = array(
		'name'    => esc_html__('Users style at followed and search pages','discy'),
		'desc'    => esc_html__('Choose the users style at followed and search pages.','discy'),
		'id'      => 'user_style_pages',
		'options' => array(
			'columns'       => esc_html__('Columns','discy'),
			'simple_follow' => esc_html__('Simple with follow','discy'),
			'small'         => esc_html__('Small','discy'),
			'grid'          => esc_html__('Grid','discy'),
			'normal'        => esc_html__('Normal','discy'),
		),
		'std'     => 'columns',
		'type'    => 'radio'
	);
	
	$options[] = array(
		'name' => esc_html__('Users per page at followed and search pages','discy'),
		'desc' => esc_html__('Put the users per page at followed and search pages.','discy'),
		'id'   => 'users_per_page',
		'std'  => '10',
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'register_setting',
		'name' => esc_html__('Register Setting','discy')
	);
	
	$options[] = array(
		'name'    => esc_html__('Register in default group','discy'),
		'desc'    => esc_html__('Select the default group when users registered.','discy'),
		'id'      => 'default_group',
		'std'     => 'subscriber',
		'type'    => 'select',
		'options' => discy_options_groups()
	);
	
	$options[] = array(
		'name'    => esc_html__('After register go to?','discy'),
		'id'      => 'after_register',
		'std'     => "same_page",
		'type'    => 'select',
		'options' => array("same_page" => esc_html__("Same page","discy"),"home" => esc_html__("Home","discy"),"profile" => esc_html__("Profile","discy"),"custom_link" => esc_html__("Custom link","discy"))
	);
	
	$options[] = array(
		'name'      => esc_html__('Type the link if you don\'t like above','discy'),
		'id'        => 'after_register_link',
		'condition' => 'after_register:is(custom_link)',
		'type'      => 'text'
	);
	
	$options[] = array(
		'name'    => esc_html__('After login go to?','discy'),
		'id'      => 'after_login',
		'std'     => "same_page",
		'type'    => 'select',
		'options' => array("same_page" => esc_html__("Same page","discy"),"home" => esc_html__("Home","discy"),"profile" => esc_html__("Profile","discy"),"custom_link" => esc_html__("Custom link","discy"))
	);
	
	$options[] = array(
		'name'      => esc_html__('Type the link if you don\'t like above','discy'),
		'id'        => 'after_login_link',
		'condition' => 'after_login:is(custom_link)',
		'type'      => 'text'
	);
	
	$options[] = array(
		'name'    => esc_html__('After logout go to?','discy'),
		'id'      => 'after_logout',
		'std'     => "same_page",
		'type'    => 'select',
		'options' => array("same_page" => esc_html__("Same page","discy"),"home" => esc_html__("Home","discy"),"custom_link" => esc_html__("Custom link","discy"))
	);
	
	$options[] = array(
		'name'      => esc_html__('Type the link if you don\'t like above','discy'),
		'id'        => 'after_logout_link',
		'condition' => 'after_logout:is(custom_link)',
		'type'      => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Confirm with email enable or disable (in register form)','discy'),
		'id'   => 'confirm_email',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Active the page terms?','discy'),
		'desc' => esc_html__('Select ON if you want active the page terms.','discy'),
		'id'   => 'terms_active_register',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'terms_active_register:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Open the page in same page or a new page?','discy'),
		'id'      => 'terms_active_target_register',
		'std'     => "new_page",
		'type'    => 'select',
		'options' => array("same_page" => esc_html__("Same page","discy"),"new_page" => esc_html__("New page","discy"))
	);
	
	$options[] = array(
		'name'    => esc_html__('Terms page','discy'),
		'desc'    => esc_html__('Select the terms page','discy'),
		'id'      => 'terms_page_register',
		'type'    => 'select',
		'options' => $options_pages
	);
	
	$options[] = array(
		'name' => esc_html__('Type the terms link if you don\'t like a page','discy'),
		'id'   => 'terms_link_register',
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Select ON to allow for the users register with space','discy'),
		'id'   => 'allow_spaces',
		'std'  => 'on',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'    => esc_html__('Select the default options when registered','discy'),
		'id'      => 'register_default_options',
		'type'    => 'multicheck',
		'std'     => array(
			"follow_email"        => "follow_email",
			"show_point_favorite" => "show_point_favorite",
			"received_email"      => "received_email",
			"received_message"    => "received_message",
		),
		'options' => array(
			"follow_email"        => esc_html__('Follow Up Email','discy'),
			"show_point_favorite" => esc_html__('Show the private pages','discy'),
			"received_email"      => esc_html__('Received mail when user add a new question','discy'),
			"received_message"    => esc_html__("Received message from another users",'discy'),
		)
	);
	
	$options[] = array(
		'name' => esc_html__('All the site for the register users only?','discy'),
		'desc' => esc_html__('Select ON to active the site for the register users only.','discy'),
		'id'   => 'site_users_only',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'site_users_only:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'type' => 'info',
		'name' => esc_html__('Un register page setting','discy')
	);
	
	$options[] = array(
		'name'    => esc_html__('Page style','discy'),
		'desc'    => esc_html__('Choose page style from here.','discy'),
		'id'      => 'register_style',
		'options' => array(
			'style_1'  => 'Style 1',
			'style_2'  => 'Style 2',
		),
		'std'     => 'style_1',
		'type'    => 'radio'
	);
	
	$options[] = array(
		'name'    => esc_html__('Upload the background','discy'),
		'desc'    => esc_html__('Upload the background for the un register page','discy'),
		'id'      => 'register_background',
		'type'    => 'background',
		'options' => array('color' => '','image' => ''),
		'std'     => array(
			'color' => '#272930',
			'image' => $imagepath_theme."register.png"
		)
	);
	
	$options[] = array(
		"name" => esc_html__('Choose the background opacity','discy'),
		"desc" => esc_html__('Choose from here the background opacity','discy'),
		"id"   => "register_opacity",
		"type" => "sliderui",
		'std'  => 30,
		"step" => "5",
		"min"  => "0",
		"max"  => "100"
	);
	
	$options[] = array(
		'name'    => esc_html__('Choose from here what\'s menu will show for the un register users.','discy'),
		'id'      => 'register_menu',
		'type'    => 'select',
		'options' => $menus
	);
	
	$options[] = array(
		'name' => esc_html__('The headline','discy'),
		'desc' => esc_html__('Type from here the headline','discy'),
		'id'   => 'register_headline',
		'type' => 'text',
		'std'  => 'Join the world\'s  biggest Q & A network!'
	);
	
	$options[] = array(
		'name' => esc_html__('The paragraph','discy'),
		'desc' => esc_html__('Type from here the paragraph','discy'),
		'id'   => 'register_paragraph',
		'type' => 'textarea',
		'std'  => 'Login to our social questions & Answers Engine to ask questions answer people’s questions & connect with other people.'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Login setting','discy'),
		'type' => 'info'
	);
	
	$options[] = array(
		'name'    => esc_html__('Login popup style','discy'),
		'desc'    => esc_html__('Choose login popup style from here.','discy'),
		'id'      => 'login_style',
		'options' => array(
			'style_1' => esc_html__('Style 1','discy'),
			'style_2' => esc_html__('Style 2','discy'),
		),
		'std'     => 'style_1',
		'type'    => 'radio'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'login_style:not(style_2)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Logo for the login popup','discy'),
		'id'      => 'logo_login',
		'type'    => 'upload',
		'options' => array("height" => "logo_login_height","width" => "logo_login_width"),
	);

	$options[] = array(
		'name' => esc_html__('Logo retina for the login popup','discy'),
		'id'   => 'logo_login_retina',
		'type' => 'upload'
	);
	
	$options[] = array(
		'name' => esc_html__('Logo height','discy'),
		"id"   => "logo_login_height",
		"type" => "sliderui",
		'std'  => '45',
		"step" => "1",
		"min"  => "0",
		"max"  => "80"
	);
	
	$options[] = array(
		'name' => esc_html__('Logo width','discy'),
		"id"   => "logo_login_width",
		"type" => "sliderui",
		'std'  => '137',
		"step" => "1",
		"min"  => "0",
		"max"  => "170"
	);

	$options[] = array(
		'name' => esc_html__('Text for the login popup after the logo or the normal text','discy'),
		'id'   => 'text_login',
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'login_style:is(style_2)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Login image','discy'),
		'id'   => 'login_image',
		'type' => 'upload'
	);
	
	$options[] = array(
		"type" => "textarea",
		"id"   => "login_details",
		"std"  => "Login to our social questions & Answers Engine to ask questions answer people’s questions & connect with other people.",
		"name" => esc_html__('Details for login popup','discy')
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Signup setting','discy'),
		'type' => 'info'
	);
	
	$options[] = array(
		'name'    => esc_html__('Signup popup style','discy'),
		'desc'    => esc_html__('Choose signup popup style from here.','discy'),
		'id'      => 'signup_style',
		'options' => array(
			'style_1' => esc_html__('Style 1','discy'),
			'style_2' => esc_html__('Style 2','discy'),
		),
		'std'     => 'style_1',
		'type'    => 'radio'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'signup_style:not(style_2)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Logo for the signup popup','discy'),
		'id'      => 'logo_signup',
		'type'    => 'upload',
		'options' => array("height" => "logo_signup_height","width" => "logo_signup_width"),
	);

	$options[] = array(
		'name' => esc_html__('Logo retina for the signup popup','discy'),
		'id'   => 'logo_signup_retina',
		'type' => 'upload'
	);
	
	$options[] = array(
		'name' => esc_html__('Logo height','discy'),
		"id"   => "logo_signup_height",
		"type" => "sliderui",
		'std'  => '45',
		"step" => "1",
		"min"  => "0",
		"max"  => "80"
	);
	
	$options[] = array(
		'name' => esc_html__('Logo width','discy'),
		"id"   => "logo_signup_width",
		"type" => "sliderui",
		'std'  => '137',
		"step" => "1",
		"min"  => "0",
		"max"  => "170"
	);

	$options[] = array(
		'name' => esc_html__('Text for the signup popup after the logo or the normal text','discy'),
		'id'   => 'text_signup',
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'signup_style:is(style_2)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Signup image','discy'),
		'id'   => 'signup_image',
		'type' => 'upload'
	);
	
	$options[] = array(
		"type" => "textarea",
		"id"   => "signup_details",
		"std"  => "Sign Up to our social questions and Answers Engine to ask questions, answer people’s questions, and connect with other people.",
		"name" => esc_html__('Details for signup popup','discy')
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Forget password setting','discy'),
		'type' => 'info'
	);
	
	$options[] = array(
		'name'    => esc_html__('Forget password popup style','discy'),
		'desc'    => esc_html__('Choose forget password popup style from here.','discy'),
		'id'      => 'pass_style',
		'options' => array(
			'style_1' => esc_html__('Style 1','discy'),
			'style_2' => esc_html__('Style 2','discy'),
		),
		'std'     => 'style_1',
		'type'    => 'radio'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'pass_style:not(style_2)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Logo for the forget password popup','discy'),
		'id'      => 'logo_pass',
		'type'    => 'upload',
		'options' => array("height" => "logo_pass_height","width" => "logo_pass_width"),
	);

	$options[] = array(
		'name' => esc_html__('Logo retina for the forget password popup','discy'),
		'id'   => 'logo_pass_retina',
		'type' => 'upload'
	);
	
	$options[] = array(
		'name' => esc_html__('Logo height','discy'),
		"id"   => "logo_pass_height",
		"type" => "sliderui",
		'std'  => '45',
		"step" => "1",
		"min"  => "0",
		"max"  => "80"
	);
	
	$options[] = array(
		'name' => esc_html__('Logo width','discy'),
		"id"   => "logo_pass_width",
		"type" => "sliderui",
		'std'  => '137',
		"step" => "1",
		"min"  => "0",
		"max"  => "170"
	);

	$options[] = array(
		'name' => esc_html__('Text for the forget password popup after the logo or the normal text','discy'),
		'id'   => 'text_pass',
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'pass_style:is(style_2)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Forget password image','discy'),
		'id'   => 'pass_image',
		'type' => 'upload'
	);
	
	$options[] = array(
		"type" => "textarea",
		"id"   => "pass_details",
		"std"  => "Lost your password? Please enter your email address. You will receive a link and will create a new password via email.",
		"name" => esc_html__('Details for forget password popup','discy')
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'edit_profile',
		'name' => esc_html__('Edit Profile','discy')
	);
	
	$edit_profile_sections = array(
		array('name' => esc_html__('Basic Information','discy'),"value" => "basic","default" => "yes"),
		array('name' => esc_html__('Social Profiles','discy'),"value" => "social","default" => "yes"),
		array('name' => esc_html__('About Me','discy'),"value" => "about","default" => "yes"),
	);
	
	$options[] = array(
		'name'    => esc_html__('Select what\'s show at edit profile','discy'),
		'id'      => 'edit_profile_sections',
		'type'    => 'sort',
		'std'     => $edit_profile_sections,
		'options' => $edit_profile_sections,
	);
	
	$edit_profile_items_1 = array(
		"names"         => array("sort" => esc_html__('Names','discy'),"value" => "names"),
		"image_profile" => array("sort" => esc_html__('Image Profile','discy'),"value" => "image_profile"),
		"cover"         => array("sort" => esc_html__('Cover','discy'),"value" => "cover"),
		"country"       => array("sort" => esc_html__('Country','discy'),"value" => "country"),
		"city"          => array("sort" => esc_html__('City','discy'),"value" => "city"),
		"phone"         => array("sort" => esc_html__('Phone','discy'),"value" => "phone"),
		"gender"        => array("sort" => esc_html__('Gender','discy'),"value" => "gender"),
		"age"           => array("sort" => esc_html__('Age','discy'),"value" => "age"),
	);
	
	$options[] = array(
		'name'    => esc_html__('Select what\'s show at edit profile at the Basic Information section','discy'),
		'id'      => 'edit_profile_items_1',
		'type'    => 'multicheck',
		'sort'    => 'yes',
		'std'     => $edit_profile_items_1,
		'options' => $edit_profile_items_1
	);
	
	$options[] = array(
		'name'      => esc_html__('You need to activate the cover option from the User setting/General Setting','discy'),
		'condition' => 'cover_image:not(on),edit_profile_items_1:has(cover)',
		'type'      => 'info'
	);
	
	$options[] = array(
		'name'      => esc_html__('Names in edit profile is required.','discy'),
		'id'        => 'names_required',
		'condition' => 'edit_profile_items_1:has(names)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Profile picture setting','discy'),
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Default image profile enable or disable.','discy'),
		'desc' => esc_html__('Check ON to upload your default image for the user didn\'t upload the image profile.','discy'),
		'id'   => 'default_image_active',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Upload default image profile for the user.','discy'),
		'id'        => 'default_image',
		'std'       => $imagepath_theme."default-image.png",
		'condition' => 'default_image_active:not(0)',
		'type'      => 'upload'
	);
	
	$options[] = array(
		'name'      => esc_html__('Upload default image profile for the user females.','discy'),
		'id'        => 'default_image_females',
		'std'       => $imagepath_theme."default-image-females.png",
		'condition' => 'default_image_active:not(0)',
		'type'      => 'upload'
	);
	
	$options[] = array(
		'name'      => esc_html__('Profile picture in edit profile is required','discy'),
		'id'        => 'profile_picture_required',
		'condition' => 'edit_profile_items_1:has(image_profile)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Add the maximum size for the profile picture, Add it with KB, for 1 MB add it 1024.','discy'),
		'desc'      => esc_html__('Add the maximum size for the profile picture, Leave it empty if you need it unlimited size.','discy'),
		'id'        => 'profile_picture_size',
		'condition' => 'edit_profile_items_1:has(image_profile)',
		'type'      => 'text'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'edit_profile_items_1:has(cover),cover_image:is(on)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Cover picture setting','discy'),
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Default cover enable or disable.','discy'),
		'desc' => esc_html__('Check ON to upload your default cover for the user didn\'t upload the cover profile.','discy'),
		'id'   => 'default_cover_active',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Upload default cover for the user.','discy'),
		'id'        => 'default_cover',
		'condition' => 'default_cover_active:not(0)',
		'type'      => 'upload'
	);
	
	$options[] = array(
		'name'      => esc_html__('Upload default cover for the user females.','discy'),
		'id'        => 'default_cover_females',
		'condition' => 'default_cover_active:not(0)',
		'type'      => 'upload'
	);
	
	$options[] = array(
		'name' => esc_html__('Profile cover in edit profile is required','discy'),
		'id'   => 'profile_cover_required',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Add the maximum size for the profile picture, Add it with KB, for 1 MB add it 1024.','discy'),
		'desc' => esc_html__('Add the maximum size for the profile picture, Leave it empty if you need it unlimited size.','discy'),
		'id'   => 'profile_cover_size',
		'type' => 'text'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'edit_profile_items_1:has(gender)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Gender setting','discy'),
		'type' => 'info',
	);
	
	$options[] = array(
		'name' => esc_html__('Gender in edit profile is required.','discy'),
		'id'   => 'gender_required',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Active other at the gender in edit profile.','discy'),
		'id'   => 'gender_other',
		'type' => 'checkbox'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name'      => esc_html__('Other settings','discy'),
		'type'      => 'info',
		'operator'  => 'or',
		'condition' => 'edit_profile_items_1:has(country),edit_profile_items_1:has(city),edit_profile_items_1:has(phone),edit_profile_items_1:has(age)',
	);
	
	$options[] = array(
		'name'      => esc_html__('Country in edit profile is required.','discy'),
		'id'        => 'country_required',
		'condition' => 'edit_profile_items_1:has(country)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('City in edit profile is required.','discy'),
		'id'        => 'city_required',
		'condition' => 'edit_profile_items_1:has(city)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Phone in edit profile is required.','discy'),
		'id'        => 'phone_required',
		'condition' => 'edit_profile_items_1:has(phone)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Age in edit profile is required.','discy'),
		'id'        => 'age_required',
		'condition' => 'edit_profile_items_1:has(age)',
		'type'      => 'checkbox'
	);
	
	$edit_profile_items_2 = array(
		"facebook"  => array("sort" => esc_html__('Facebook','discy'),"value" => "facebook"),
		"twitter"   => array("sort" => esc_html__('Twitter','discy'),"value" => "twitter"),
		"youtube"   => array("sort" => esc_html__('Youtube','discy'),"value" => "youtube"),
		"vimeo"     => array("sort" => esc_html__('Vimeo','discy'),"value" => "vimeo"),
		"linkedin"  => array("sort" => esc_html__('Linkedin','discy'),"value" => "linkedin"),
		"instagram" => array("sort" => esc_html__('Instagram','discy'),"value" => "instagram"),
		"pinterest" => array("sort" => esc_html__('Pinterest','discy'),"value" => "pinterest"),
	);
	
	$options[] = array(
		'name'    => esc_html__('Select what\'s show at edit profile at the Social Profiles section','discy'),
		'id'      => 'edit_profile_items_2',
		'type'    => 'multicheck',
		'sort'    => 'yes',
		'std'     => $edit_profile_items_2,
		'options' => $edit_profile_items_2
	);
	
	$edit_profile_items_3 = array(
		"website"            => array("sort" => esc_html__('Website','discy'),"value" => "website"),
		"bio"                => array("sort" => esc_html__('Professional Bio','discy'),"value" => "bio"),
		"profile_credential" => array("sort" => esc_html__('Profile credential','discy'),"value" => "profile_credential"),
		"follow_email"       => array("sort" => esc_html__('Follow Up Email','discy'),"value" => "follow_email"),
		"private_pages"      => array("sort" => esc_html__('Private Pages','discy'),"value" => "private_pages"),
		"send_emails"        => array("sort" => esc_html__('Send Email For Question','discy'),"value" => "send_emails"),
		"received_message"   => array("sort" => esc_html__('Received message from the users','discy'),"value" => "received_message"),
	);
	
	$options[] = array(
		'name'    => esc_html__('Select what\'s show at edit profile at the About Me section','discy'),
		'id'      => 'edit_profile_items_3',
		'type'    => 'multicheck',
		'sort'    => 'yes',
		'std'     => $edit_profile_items_3,
		'options' => $edit_profile_items_3
	);
	
	$options[] = array(
		'name'      => esc_html__('Editor enable or disable for professional bio','discy'),
		'id'        => 'bio_editor',
		'condition' => 'edit_profile_items_3:has(bio)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Profile credential in edit profile is required.','discy'),
		'id'        => 'profile_credential_required',
		'condition' => 'edit_profile_items_3:has(profile_credential)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'ask_users',
		'name' => esc_html__('Ask Users','discy')
	);
	
	$options[] = array(
		'name' => esc_html__('Ask question to the users','discy'),
		'desc' => esc_html__('Any one can ask question to the users enable or disable.','discy'),
		'id'   => 'ask_question_to_users',
		'std'  => "on",
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'ask_question_to_users:not(0)',
		'type'      => 'heading-2'
	);
	
	$ask_user_items = array(
		"comment_question"     => array("sort" => esc_html__('Question content','discy'),"value" => "comment_question"),
		"anonymously_question" => array("sort" => esc_html__('Ask Anonymously','discy'),"value" => "anonymously_question"),
		"private_question"     => array("sort" => esc_html__('Private Question','discy'),"value" => "private_question"),
		"remember_answer"      => array("sort" => esc_html__('Remember Answer','discy'),"value" => "remember_answer"),
		"terms_active"         => array("sort" => esc_html__('Terms Active','discy'),"value" => "terms_active"),
	);
	
	$options[] = array(
		'name'    => esc_html__('Select what\'s show at ask user question form','discy'),
		'id'      => 'ask_user_items',
		'type'    => 'multicheck',
		'sort'    => 'yes',
		'std'     => $ask_user_items,
		'options' => $ask_user_items
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'ask_user_items:has(comment_question)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Details in ask question form is required','discy'),
		'id'   => 'content_ask_user',
		'std'  => "on",
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Editor enable or disable for details in ask question form','discy'),
		'id'   => 'editor_ask_user',
		'std'  => "on",
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'ask_user_items:has(terms_active)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Open the page in same page or a new page?','discy'),
		'id'      => 'terms_active_user_target',
		'std'     => "new_page",
		'type'    => 'select',
		'options' => array("same_page" => esc_html__("Same page","discy"),"new_page" => esc_html__("New page","discy"))
	);
	
	$options[] = array(
		'name'    => esc_html__('Terms page','discy'),
		'desc'    => esc_html__('Select the terms page','discy'),
		'id'      => 'terms_page_user',
		'type'    => 'select',
		'options' => $options_pages
	);
	
	$options[] = array(
		'name' => esc_html__('Type the terms link if you don\'t like a page','discy'),
		'id'   => 'terms_link_user',
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'user_slugs',
		'name' => esc_html__('User Slugs','discy')
	);

	$options = apply_filters('discy_options_user_slugs',$options);
	
	$options[] = array(
		'name' => esc_html__('Login slug','discy'),
		'desc' => esc_html__('Put the login slug.','discy'),
		'id'   => 'login_slug',
		'std'  => 'log-in',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Signup slug','discy'),
		'desc' => esc_html__('Put the signup slug.','discy'),
		'id'   => 'signup_slug',
		'std'  => 'sign-up',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Lost password slug','discy'),
		'desc' => esc_html__('Put the lost password slug.','discy'),
		'id'   => 'lost_password_slug',
		'std'  => 'lost-password',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User profile slug','discy'),
		'desc' => esc_html__('Put the user profile slug.','discy'),
		'id'   => 'profile_slug',
		'std'  => 'profile',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Edit profile slug','discy'),
		'desc' => esc_html__('Put the edit profile slug.','discy'),
		'id'   => 'edit_slug',
		'std'  => 'edit',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User followers slug','discy'),
		'desc' => esc_html__('Put the user followers slug.','discy'),
		'id'   => 'followers_slug',
		'std'  => 'followers',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User following slug','discy'),
		'desc' => esc_html__('Put the user following slug.','discy'),
		'id'   => 'following_slug',
		'std'  => 'following',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User notifications slug','discy'),
		'desc' => esc_html__('Put the user notifications slug.','discy'),
		'id'   => 'notifications_slug',
		'std'  => 'notifications',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User activities slug','discy'),
		'desc' => esc_html__('Put the user activities slug.','discy'),
		'id'   => 'activities_slug',
		'std'  => 'activities',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User questions slug','discy'),
		'desc' => esc_html__('Put the user questions slug.','discy'),
		'id'   => 'questions_slug',
		'std'  => 'questions',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User answers slug','discy'),
		'desc' => esc_html__('Put the user answers slug.','discy'),
		'id'   => 'answers_slug',
		'std'  => 'answers',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User best answers slug','discy'),
		'desc' => esc_html__('Put the user best answers slug.','discy'),
		'id'   => 'best_answers_slug',
		'std'  => 'best-answers',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User points slug','discy'),
		'desc' => esc_html__('Put the user points slug.','discy'),
		'id'   => 'points_slug',
		'std'  => 'points',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User polls slug','discy'),
		'desc' => esc_html__('Put the user polls slug.','discy'),
		'id'   => 'polls_slug',
		'std'  => 'polls',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User asked slug','discy'),
		'desc' => esc_html__('Put the user asked slug.','discy'),
		'id'   => 'asked_slug',
		'std'  => 'asked',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User asked questions slug','discy'),
		'desc' => esc_html__('Put the user asked questions slug.','discy'),
		'id'   => 'asked_questions_slug',
		'std'  => 'asked-questions',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User paid questions slug','discy'),
		'desc' => esc_html__('Put the user paid questions slug.','discy'),
		'id'   => 'paid_questions_slug',
		'std'  => 'paid-questions',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User followed slug','discy'),
		'desc' => esc_html__('Put the user followed slug.','discy'),
		'id'   => 'followed_slug',
		'std'  => 'followed',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User favorites slug','discy'),
		'desc' => esc_html__('Put the user favorites slug.','discy'),
		'id'   => 'favorites_slug',
		'std'  => 'favorites',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User posts slug','discy'),
		'desc' => esc_html__('Put the user posts slug.','discy'),
		'id'   => 'posts_slug',
		'std'  => 'posts',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User comments slug','discy'),
		'desc' => esc_html__('Put the user comments slug.','discy'),
		'id'   => 'comments_slug',
		'std'  => 'comments',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User followers questions slug','discy'),
		'desc' => esc_html__('Put the user followers questions slug.','discy'),
		'id'   => 'followers_questions_slug',
		'std'  => 'followers-questions',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User followers answers slug','discy'),
		'desc' => esc_html__('Put the user followers answers slug.','discy'),
		'id'   => 'followers_answers_slug',
		'std'  => 'followers-answers',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User followers posts slug','discy'),
		'desc' => esc_html__('Put the user followers posts slug.','discy'),
		'id'   => 'followers_posts_slug',
		'std'  => 'followers-posts',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('User followers comments slug','discy'),
		'desc' => esc_html__('Put the user followers comments slug.','discy'),
		'id'   => 'followers_comments_slug',
		'std'  => 'followers-comments',
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'permissions',
		'name' => esc_html__('Permissions','discy')
	);
	
	$options[] = array(
		'name' => esc_html__('Select ON to can add a custom permission.','discy'),
		'id'   => 'custom_permission',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'custom_permission:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Unlogged users','discy'),
		'type' => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__('Select ON to can add a question.','discy'),
		'id'   => 'ask_question',
		'std'  => "on",
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Select ON to can show other questions.','discy'),
		'id'   => 'show_question',
		'std'  => "on",
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Select ON to can add a answer.','discy'),
		'id'   => 'add_answer',
		'std'  => "on",
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Select ON to can show other answers.','discy'),
		'id'   => 'show_answer',
		'std'  => "on",
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Select ON to can add a post.','discy'),
		'id'   => 'add_post',
		'std'  => "on",
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Select ON to can send message.','discy'),
		'id'   => 'send_message',
		'std'  => "on",
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Setting for groups & Add new groups','discy'),
		'type' => 'info'
	);
	
	$options[] = array(
		'id'   => "roles",
		'type' => 'roles'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'author_setting',
		'name' => esc_html__('Author Setting','discy')
	);
	
	$options[] = array(
		'name'    => esc_html__('Post style','discy'),
		'desc'    => esc_html__('Choose post style from here.','discy'),
		'id'      => 'author_post_style',
		'options' => array(
			'default'  => esc_html__('Default','discy'),
			'style_1'  => esc_html__('1 column','discy'),
			'style_2'  => esc_html__('List style','discy'),
			'style_3'  => esc_html__('Columns','discy'),
		),
		'std'     => 'default',
		'type'    => 'radio'
	);
	
	$options[] = array(
		'id'        => "author_sort_meta_title_image",
		'condition' => 'author_post_style:is(style_3)',
		'std'       => array(
						array("value" => "image",'name' => esc_html__('Image','discy'),"default" => "yes"),
						array("value" => "meta_title",'name' => esc_html__('Meta and title','discy'),"default" => "yes"),
					),
		'type'      => "sort",
		'options'   => array(
						array("value" => "image",'name' => esc_html__('Image','discy'),"default" => "yes"),
						array("value" => "meta_title",'name' => esc_html__('Meta and title','discy'),"default" => "yes"),
					)
	);
	
	$options[] = array(
		'name'    => esc_html__('Author sidebar layout','discy'),
		'id'      => "author_sidebar_layout",
		'std'     => "default",
		'type'    => "images",
		'options' => array(
			'default'      => $imagepath.'sidebar_default.jpg',
			'menu_sidebar' => $imagepath.'menu_sidebar.jpg',
			'right'        => $imagepath.'sidebar_right.jpg',
			'full'         => $imagepath.'sidebar_no.jpg',
			'left'         => $imagepath.'sidebar_left.jpg',
			'centered'     => $imagepath.'centered.jpg',
			'menu_left'    => $imagepath.'menu_left.jpg',
		)
	);
	
	$options[] = array(
		'name'      => esc_html__('Author Page sidebar','discy'),
		'id'        => "author_sidebar",
		'options'   => $new_sidebars,
		'type'      => 'select',
		'condition' => 'author_sidebar_layout:not(full),author_sidebar_layout:not(centered),author_sidebar_layout:not(menu_left)'
	);
	
	$options[] = array(
		'name'      => esc_html__('Author Page sidebar 2','discy'),
		'id'        => "author_sidebar_2",
		'options'   => $new_sidebars,
		'type'      => 'select',
		'operator'  => 'or',
		'condition' => 'author_sidebar_layout:is(menu_sidebar),author_sidebar_layout:is(menu_left)'
	);
	
	$options[] = array(
		'name'    => esc_html__('Choose Your Skin','discy'),
		'class'   => "site_skin",
		'id'      => "author_skin",
		'std'     => "default",
		'type'    => "images",
		'options' => array(
			'default'    => $imagepath.'default_color.jpg',
			'skin'       => $imagepath.'default.jpg',
			'violet'     => $imagepath.'violet.jpg',
			'bright_red' => $imagepath.'bright_red.jpg',
			'green'      => $imagepath.'green.jpg',
			'red'        => $imagepath.'red.jpg',
			'cyan'       => $imagepath.'cyan.jpg',
			'blue'       => $imagepath.'blue.jpg',
		)
	);
	
	$options[] = array(
		'name' => esc_html__('Primary Color','discy'),
		'id'   => 'author_primary_color',
		'type' => 'color' 
	);
	
	$options[] = array(
		'name'    => esc_html__('Background Type','discy'),
		'id'      => 'author_background_type',
		'std'     => 'default',
		'type'    => 'radio',
		'options' => 
			array(
				"default"           => esc_html__("Default","discy"),
				"none"              => esc_html__("None","discy"),
				"patterns"          => esc_html__("Patterns","discy"),
				"custom_background" => esc_html__("Custom Background","discy")
			)
	);

	$options[] = array(
		'name'      => esc_html__('Background Color','discy'),
		'id'        => 'author_background_color',
		'type'      => 'color',
		'condition' => 'author_background_type:is(patterns)'
	);
		
	$options[] = array(
		'name'      => esc_html__('Choose Pattern','discy'),
		'id'        => "author_background_pattern",
		'std'       => "bg13",
		'type'      => "images",
		'condition' => 'author_background_type:is(patterns)',
		'class'     => "pattern_images",
		'options'   => array(
			'bg1'  => $imagepath.'bg1.jpg',
			'bg2'  => $imagepath.'bg2.jpg',
			'bg3'  => $imagepath.'bg3.jpg',
			'bg4'  => $imagepath.'bg4.jpg',
			'bg5'  => $imagepath.'bg5.jpg',
			'bg6'  => $imagepath.'bg6.jpg',
			'bg7'  => $imagepath.'bg7.jpg',
			'bg8'  => $imagepath.'bg8.jpg',
			'bg9'  => $imagepath_theme.'patterns/bg9.png',
			'bg10' => $imagepath_theme.'patterns/bg10.png',
			'bg11' => $imagepath_theme.'patterns/bg11.png',
			'bg12' => $imagepath_theme.'patterns/bg12.png',
			'bg13' => $imagepath.'bg13.jpg',
			'bg14' => $imagepath.'bg14.jpg',
			'bg15' => $imagepath_theme.'patterns/bg15.png',
			'bg16' => $imagepath_theme.'patterns/bg16.png',
			'bg17' => $imagepath.'bg17.jpg',
			'bg18' => $imagepath.'bg18.jpg',
			'bg19' => $imagepath.'bg19.jpg',
			'bg20' => $imagepath.'bg20.jpg',
			'bg21' => $imagepath_theme.'patterns/bg21.png',
			'bg22' => $imagepath.'bg22.jpg',
			'bg23' => $imagepath_theme.'patterns/bg23.png',
			'bg24' => $imagepath_theme.'patterns/bg24.png',
		)
	);

	$options[] = array(
		'name'      => esc_html__('Custom Background','discy'),
		'id'        => 'author_custom_background',
		'std'       => $background_defaults,
		'type'      => 'background',
		'options'   => $background_defaults,
		'condition' => 'author_background_type:is(custom_background)'
	);
		
	$options[] = array(
		'name'      => esc_html__('Full Screen Background','discy'),
		'desc'      => esc_html__('Select ON to Full Screen Background','discy'),
		'id'        => 'author_full_screen_background',
		'type'      => 'checkbox',
		'condition' => 'author_background_type:is(custom_background)'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Message setting','discy'),
		'icon' => 'email-alt',
		'type' => 'heading'
	);
	
	$options[] = array(
		'type' => 'heading-2',
	);
	
	$options[] = array(
		'name' => esc_html__('Active messages to the users','discy'),
		'desc' => esc_html__('Any one can send message to the users enable or disable.','discy'),
		'id'   => 'active_message',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'active_message:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__("Messages slug",'discy'),
		'desc' => esc_html__("Select the messages slug",'discy'),
		'id'   => 'messages_slug',
		'type' => 'text',
		'std'  => 'messages'
	);
	
	$options[] = array(
		'name'    => esc_html__('Choose message status','discy'),
		'desc'    => esc_html__('Choose message status after user publish the question.','discy'),
		'id'      => 'message_publish',
		'options' => array("publish" => "Publish","draft" => "Draft"),
		'std'     => 'draft',
		'type'    => 'select'
	);
	
	$options[] = array(
		'name' => esc_html__('Any one can send message without register','discy'),
		'desc' => esc_html__('Any one can send message without register enable or disable.','discy'),
		'id'   => 'send_message_no_register',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Details in send message form is required','discy'),
		'id'   => 'comment_message',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Editor enable or disable for details in send message form','discy'),
		'id'   => 'editor_message_details',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Send email after send a message?','discy'),
		'id'   => 'send_email_message',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Active user can delete the messages','discy'),
		'desc' => esc_html__('Select ON if you want the user can delete the messages.','discy'),
		'id'   => 'message_delete',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Active user can seen the message by send notification','discy'),
		'desc' => esc_html__('Select ON if you want the user know if any one seen the message by send notification.','discy'),
		'id'   => 'seen_message',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$badges_setting = array(
		"badges" => esc_html__('Badges setting','discy'),
		"points" => esc_html__('Points setting','discy')
	);
	
	$options[] = array(
		'name'    => esc_html__('Badges & Points setting','discy'),
		'id'      => 'badges',
		'icon'    => 'star-filled',
		'type'    => 'heading',
		'std'     => 'badges',
		'options' => apply_filters("discy_badges_setting",$badges_setting)
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'badges',
		'name' => esc_html__('Badges setting','discy')
	);
	
	$options[] = array(
		"type" => "textarea",
		"id"   => "badges_details",
		"name" => esc_html__('Details for badges','discy')
	);
	
	$options[] = array(
		'name'    => esc_html__('Choose the badges style','discy'),
		'desc'    => esc_html__('Choose from here the badges style.','discy'),
		'id'      => 'badges_style',
		'options' => array("by_points" => esc_html__("By points","discy"),"by_groups" => esc_html__("By groups","discy"),"by_groups_points" => esc_html__("By groups and points","discy")),
		'std'     => 'by_points',
		'type'    => 'select'
	);
	
	$badge_elements = array(
		array(
			"type" => "text",
			"id"   => "badge_name",
			"name" => esc_html__('Badge name','discy')
		),
		array(
			"type" => "text",
			"id"   => "badge_points",
			"name" => esc_html__('Points','discy')
		),
		array(
			"type" => "color",
			"id"   => "badge_color",
			"name" => esc_html__('Color','discy')
		),
		array(
			"type" => "textarea",
			"id"   => "badge_details",
			"name" => esc_html__('Details','discy')
		)
	);
	
	$options[] = array(
		'id'        => "badges",
		'type'      => "elements",
		'sort'      => "no",
		'hide'      => "yes",
		'button'    => esc_html__('Add new badge','discy'),
		'options'   => $badge_elements,
		'condition' => 'badges_style:is(by_points)',
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'badges_style:is(by_groups)',
		'type'      => 'heading-2'
	);
	
	$badges_groups = $new_roles;
	unset($badges_groups["activation"]);
	
	$badge_elements = array(
		array(
			"type"    => "select",
			"id"      => "badge_name",
			"options" => $badges_groups,
			"name"    => esc_html__('Badge name','discy')
		),
		array(
			"type" => "color",
			"id"   => "badge_color",
			"name" => esc_html__('Color','discy')
		),
	);
	
	$options[] = array(
		'id'      => "badges_groups",
		'type'    => "elements",
		'sort'    => "no",
		'hide'    => "yes",
		'button'  => esc_html__('Add new badge','discy'),
		'options' => $badge_elements,
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'badges_style:is(by_groups_points)',
		'type'      => 'heading-2'
	);
	
	$badge_elements = array(
		array(
			"type"    => "text",
			"id"      => "badge_name",
			"name"    => esc_html__('Badge name','discy')
		),
		array(
			"type"    => "select",
			"id"      => "badge_group",
			"options" => $badges_groups,
			"name"    => esc_html__('Badge group','discy')
		),
		array(
			"type" => "text",
			"id"   => "badge_points",
			"name" => esc_html__('Points','discy')
		),
		array(
			"type" => "color",
			"id"   => "badge_color",
			"name" => esc_html__('Color','discy')
		),
		array(
			"type" => "textarea",
			"id"   => "badge_details",
			"name" => esc_html__('Details','discy')
		)
	);
	
	$options[] = array(
		'id'      => "badges_groups_points",
		'type'    => "elements",
		'sort'    => "no",
		'hide'    => "yes",
		'button'  => esc_html__('Add new badge','discy'),
		'options' => $badge_elements,
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'points',
		'name' => esc_html__('Points setting','discy')
	);
	
	$options[] = array(
		'name'      => esc_html__('You must activate the points at your site to see the options from "Questions settings/General settings".','discy'),
		'type'      => 'info',
		'condition' => 'active_points:not(on)'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'active_points:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		"type" => "textarea",
		"id"   => "points_details",
		"name" => esc_html__('Details for points','discy')
	);
	
	$options[] = array(
		'name' => esc_html__('Points for add a new question (put it 0 for off the option)','discy'),
		'desc' => esc_html__('put the Points choose for add a new question','discy'),
		'id'   => 'point_add_question',
		'type' => 'text',
		'std'  => 0
	);
	
	$options[] = array(
		'name' => esc_html__('Points for add a new post (put it 0 for off the option)','discy'),
		'desc' => esc_html__('put the Points choose for add a new post','discy'),
		'id'   => 'point_add_post',
		'type' => 'text',
		'std'  => 0
	);
	
	$options[] = array(
		'name' => esc_html__('Points for choosing the best answer','discy'),
		'desc' => esc_html__('put the Points for choosing the best answer','discy'),
		'id'   => 'point_best_answer',
		'type' => 'text',
		'std'  => 5
	);
	
	$options[] = array(
		'name' => esc_html__('Points voting question','discy'),
		'desc' => esc_html__('put the Points voting question','discy'),
		'id'   => 'point_voting_question',
		'type' => 'text',
		'std'  => 1
	);
	
	$options[] = array(
		'name' => esc_html__('Points add answer','discy'),
		'desc' => esc_html__('put the Points add answer','discy'),
		'id'   => 'point_add_comment',
		'type' => 'text',
		'std'  => 2
	);
	
	$options[] = array(
		'name' => esc_html__('Points voting answer','discy'),
		'desc' => esc_html__('put the Points voting answer','discy'),
		'id'   => 'point_voting_answer',
		'type' => 'text',
		'std'  => 1
	);
	
	$options[] = array(
		'name' => esc_html__('Points following user','discy'),
		'desc' => esc_html__('put the Points following user','discy'),
		'id'   => 'point_following_me',
		'type' => 'text',
		'std'  => 1
	);
	
	$options[] = array(
		'name' => esc_html__('Points for a new user','discy'),
		'desc' => esc_html__('put the Points for a new user','discy'),
		'id'   => 'point_new_user',
		'type' => 'text',
		'std'  => 20
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options = apply_filters('discy_options_after_points_setting',$options);
		
	$options[] = array(
		'name'    => esc_html__('Comments & Answers','discy'),
		'id'      => 'comment_answer',
		'icon'    => 'admin-comments',
		'type'    => 'heading',
		'std'     => 'comments_setting',
		'options' => array(
			"comments_setting" => esc_html__('Comments','discy'),
			"answers_setting"  => esc_html__('Answers','discy')
		)
	);
	
	$options[] = array(
		'name' => esc_html__('Comments setting','discy'),
		'id'   => 'comments_setting',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Select the share options to show at the comments/answers','discy'),
		'id'      => 'comment_share',
		'type'    => 'multicheck',
		'sort'    => 'yes',
		'std'     => $share_array,
		'options' => $share_array
	);
	
	$options[] = array(
		'name' => esc_html__('Enable or disable the editor in the comment','discy'),
		'id'   => 'comment_editor',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Add your number of letters for the answer minimum limit, like 15, 20, if you leave it empty will make it not important','discy'),
		'id'   => 'comment_min_limit',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Add your number of letters for the comment limit, like 140, 200, if you leave it empty will make it unlimited','discy'),
		'id'   => 'comment_limit',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Active the author image in comments/answers?','discy'),
		'desc' => esc_html__('Author image in comments/answers enable or disable.','discy'),
		'id'   => 'answer_image',
		'std'  => "on",
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Note: if you need all the comments/answers manually approved, From here Settings >> Discussion >> Comment must be manually approved.','discy'),
		'type' => 'info'
	);
	
	$options[] = array(
		'name'    => esc_html__('Choose comments/answers status for unlogged user only','discy'),
		'desc'    => esc_html__('Choose comments/answers status after unlogged user publish the comments/answers.','discy'),
		'id'      => 'comment_unlogged',
		'options' => array("publish" => esc_html__("Publish","discy"),"draft" => esc_html__("Draft","discy")),
		'std'     => 'draft',
		'type'    => 'select'
	);
	
	$options[] = array(
		'name' => esc_html__('Edit comments/answers','discy'),
		'type' => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__('User can edit the comments/answers?','discy'),
		'id'   => 'can_edit_comment',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'can_edit_comment:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		"name" => esc_html__('User can edit the comments/answers after x hours','discy'),
		"desc" => esc_html__('If you want the user edit it all the time leave it 0','discy'),
		"id"   => "can_edit_comment_after",
		"type" => "sliderui",
		'std'  => 1,
		"step" => "1",
		"min"  => "0",
		"max"  => "24"
	);
	
	$options[] = array(
		'name' => esc_html__('Edit comments/answers slug','discy'),
		'desc' => esc_html__('Put the edit comments/answers slug.','discy'),
		'id'   => 'edit_comments_slug',
		'std'  => 'edit-comment',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('After edit comments/answers approved auto or need to approved again?','discy'),
		'desc' => esc_html__('Press ON to approved auto','discy'),
		'id'   => 'comment_approved',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Delete comments/answers','discy'),
		'type' => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__('User can delete the comments/answers?','discy'),
		'id'   => 'can_delete_comment',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('When the users delete the comments/answers went to the trash or delete it ever?','discy'),
		'id'        => 'delete_comment',
		'options'   => array(
			'delete' => esc_html__('Delete','discy'),
			'trash'  => esc_html__('Trash','discy'),
		),
		'std'       => 'delete',
		'condition' => 'can_delete_comment:not(0)',
		'type'      => 'radio'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Answers Setting','discy'),
		'id'   => 'answers_setting',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Enable or disable the editor in the answer','discy'),
		'id'   => 'answer_editor',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'    => esc_html__('Answer with question title style at the answers page.','discy'),
		'desc'    => esc_html__('Choose the answers with question title style at the answers page.','discy'),
		'id'      => 'answer_question_style',
		'options' => array('style_1' => 'Style 1','style_2' => 'Style 2','style_3' => 'Style 3'),
		'std'     => 'style_1',
		'type'    => 'radio'
	);
	
	$options[] = array(
		'name' => esc_html__('Add your number of letters for the answer minimum limit, like 15, 20, if you leave it empty will make it not important','discy'),
		'id'   => 'answer_min_limit',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Add your number of letters for the answer limit, like 140, 200, if you leave it empty will make it unlimited','discy'),
		'id'   => 'answer_limit',
		'type' => 'text'
	);
	
	$answers_tabs = array(
		"votes"  => array("sort" => esc_html__('Voted','discy'),"value" => "votes"),
		"oldest" => array("sort" => esc_html__('Oldest','discy'),"value" => "oldest"),
		"recent" => array("sort" => esc_html__('Recent','discy'),"value" => "recent"),
	);
	
	$options[] = array(
		'name'    => esc_html__('Tabs at the answers','discy'),
		'desc'    => esc_html__('Select the tabs at the answers on the question page.','discy'),
		'id'      => 'answers_tabs',
		'type'    => 'multicheck',
		'sort'    => 'yes',
		'std'     => $answers_tabs,
		'options' => $answers_tabs
	);
	
	$options[] = array(
		'name' => esc_html__('Select ON to active the vote at answers','discy'),
		'desc' => esc_html__('Select ON to enable the vote at the answers.','discy'),
		'id'   => 'active_vote_answer',
		'std'  => "on",
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Select ON to hide the dislike at answers','discy'),
		'desc'      => esc_html__('If you put it ON the dislike will not show.','discy'),
		'id'        => 'show_dislike_answers',
		'condition' => 'active_vote_answer:not(0)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Video','discy'),
		'type' => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__('Video in the answer form','discy'),
		'desc' => esc_html__('Select ON to enable the video in the answer form.','discy'),
		'id'   => 'answer_video',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'answer_video:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Video description position at answer','discy'),
		'desc'    => esc_html__('Choose the video description position.','discy'),
		'id'      => 'video_answer_position',
		'options' => array("before" => "Before content","after" => "After content"),
		'std'     => 'after',
		'type'    => 'select'
	);
	
	$options[] = array(
		'name' => esc_html__('Set the video description with 100%?','discy'),
		'desc' => esc_html__('Select ON if you want to set the video description 100%.','discy'),
		'id'   => 'video_answer_100',
		'std'  => "on",
		'type' => 'checkbox'
	);
	
	$options[] = array(
		"name"      => esc_html__("Set the width for the video description for the answer",'discy'),
		"id"        => "video_answer_width",
		'condition' => 'video_answer_100:not(on)',
		"type"      => "sliderui",
		'std'       => 260,
		"step"      => "1",
		"min"       => "50",
		"max"       => "600"
	);
	
	$options[] = array(
		"name" => esc_html__("Set the height for the video description for the answer",'discy'),
		"id"   => "video_answer_height",
		"type" => "sliderui",
		'std'  => 500,
		"step" => "1",
		"min"  => "50",
		"max"  => "600"
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name' => esc_html__('Attachment','discy'),
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Attachment in the answer form','discy'),
		'desc' => esc_html__('Select ON to enable the attachment in the answer form.','discy'),
		'id'   => 'attachment_answer',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Featured image','discy'),
		'type' => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__('Featured image in the answer form','discy'),
		'desc' => esc_html__('Select ON to enable the featured image in the answer form.','discy'),
		'id'   => 'featured_image_answer',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'featured_image_answer:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Click ON to show featured image in the question answers','discy'),
		'desc' => esc_html__('Select ON to enable the featured image in the question answers.','discy'),
		'id'   => 'featured_image_question_answers',
		'std'  => "on",
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Click ON to show featured image in the answers tab, answers template, answers at profile or answers is search','discy'),
		'desc' => esc_html__('Select ON to enable the featured image in the answers.','discy'),
		'id'   => 'featured_image_in_answers',
		'std'  => "on",
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Click ON to enable the lightbox for featured image','discy'),
		'desc' => esc_html__('Select ON to enable the lightbox for featured image.','discy'),
		'id'   => 'featured_image_answers_lightbox',
		'std'  => "on",
		'type' => 'checkbox'
	);
	
	$options[] = array(
		"name" => esc_html__("Set the width for the featured image for the answers",'discy'),
		"id"   => "featured_image_answer_width",
		"type" => "sliderui",
		'std'  => 260,
		"step" => "1",
		"min"  => "50",
		"max"  => "600"
	);
	
	$options[] = array(
		"name" => esc_html__("Set the height for the featured image for the answers",'discy'),
		"id"   => "featured_image_answer_height",
		"type" => "sliderui",
		'std'  => 185,
		"step" => "1",
		"min"  => "50",
		"max"  => "600"
	);
	
	$options[] = array(
		'name'    => esc_html__('Featured image position','discy'),
		'desc'    => esc_html__('Choose the featured image position.','discy'),
		'id'      => 'featured_answer_position',
		'options' => array("before" => "Before content","after" => "After content"),
		'std'     => 'before',
		'type'    => 'select'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Search Setting','discy'),
		'id'   => 'search_setting',
		'icon' => 'search',
		'type' => 'heading',
	);
	
	$options[] = array(
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Search slug','discy'),
		'desc' => esc_html__('Put the search slug.','discy'),
		'id'   => 'search_slug',
		'std'  => 'search',
		'type' => 'text'
	);
	
	if (class_exists("WPQA")) {
		$html_content = '<a href="'.wpqa_get_search_permalink().'" target="_blank">'.esc_html__('The Link For The Search Page.','discy').'</a>';
		
		$options[] = array(
			'name' => $html_content,
			'type' => 'info'
		);
	}
	
	$search_attrs = array(
		"questions"         => array("sort" => esc_html__('Questions','discy'),"value" => "questions"),
		"answers"           => array("sort" => esc_html__('Answers','discy'),"value" => "answers"),
		"question-category" => array("sort" => esc_html__('Question categories','discy'),"value" => "question-category"),
		"question_tags"     => array("sort" => esc_html__('Question tags','discy'),"value" => "question_tags"),
		"posts"             => array("sort" => esc_html__('Posts','discy'),"value" => "posts"),
		"comments"          => array("sort" => esc_html__('Comments','discy'),"value" => "comments"),
		"category"          => array("sort" => esc_html__('Post categories','discy'),"value" => "category"),
		"post_tag"          => array("sort" => esc_html__('Post tags','discy'),"value" => "post_tag"),
		"users"             => array("sort" => esc_html__('Users','discy'),"value" => "users"),
	);
	
	$options[] = array(
		'name'    => esc_html__('Select the search options','discy'),
		'desc'    => esc_html__('Select the search options on the search page.','discy'),
		'id'      => 'search_attrs',
		'type'    => 'multicheck',
		'sort'    => 'yes',
		'std'     => $search_attrs,
		'options' => $search_attrs
	);
	
	$options[] = array(
		'name'    => esc_html__('Default search','discy'),
		'desc'    => esc_html__('Choose what\'s the default search','discy'),
		'id'      => 'default_search',
		'type'    => 'select',
		'stc'     => 'questions',
		'options' => array(
			"questions"         => esc_html__("Questions","discy"),
			"answers"           => esc_html__("Answers","discy"),
			"question-category" => esc_html__("Question categories","discy"),
			"question_tags"     => esc_html__("Question tags","discy"),
			"posts"             => esc_html__("Posts","discy"),
			"comments"          => esc_html__("Comments","discy"),
			"category"          => esc_html__("Post categories","discy"),
			"post_tag"          => esc_html__("Post tags","discy"),
			"users"             => esc_html__("Users","discy"),
		)
	);

	$options[] = array(
		'name' => esc_html__("Choose the live search enable or disable",'discy'),
		'id'   => "live_search",
		'type' => 'checkbox',
		'std'  => "on",
	);

	$options[] = array(
		'name'      => esc_html__('Search result number','discy'),
		'desc'      => esc_html__('Type the search result number from here.','discy'),
		'id'        => 'search_result_number',
		'condition' => 'live_search:not(0)',
		'std'       => '5',
		'type'      => 'text'
	);
	
	$options[] = array(
		'name'    => esc_html__('Tags style at search page','discy'),
		'desc'    => esc_html__('Choose the tags style.','discy'),
		'id'      => 'tag_style_pages',
		'options' => array(
			'advanced' => esc_html__('Advanced','discy'),
			'simple'   => esc_html__('Simple','discy'),
		),
		'std'     => 'advanced',
		'type'    => 'radio'
	);
	
	$options[] = array(
		'name' => esc_html__('Show the user filter at search page.','discy'),
		'id'   => 'user_filter',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Sidebar','discy'),
		'id'   => 'sidebar',
		'icon' => 'align-none',
		'type' => 'heading'
	);
	
	$options[] = array(
		'type' => 'heading-2'
	);
	
	$sidebar_elements = array(
		array(
			"type" => "text",
			"id"   => "name",
			"name" => esc_html__('Sidebar name','discy')
		),
	);
	
	$options[] = array(
		'id'      => "sidebars",
		'type'    => "elements",
		'sort'    => "no",
		'button'  => esc_html__('Add new sidebar','discy'),
		'options' => $sidebar_elements,
	);
	
	$options[] = array(
		'name'    => esc_html__('Sidebar layout','discy'),
		'id'      => "sidebar_layout",
		'std'     => "menu_sidebar",
		'type'    => "images",
		'options' => array(
			'menu_sidebar' => $imagepath.'menu_sidebar.jpg',
			'right'        => $imagepath.'sidebar_right.jpg',
			'full'         => $imagepath.'sidebar_no.jpg',
			'left'         => $imagepath.'sidebar_left.jpg',
			'centered'     => $imagepath.'centered.jpg',
			'menu_left'    => $imagepath.'menu_left.jpg',
		)
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'sidebar_layout:not(full),sidebar_layout:not(centered),sidebar_layout:not(menu_left)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Sidebar','discy'),
		'id'      => "sidebar_home",
		'options' => $new_sidebars,
		'type'    => 'select'
	);
	
	$options[] = array(
		'name'    => esc_html__('Sticky sidebar','discy'),
		'id'      => 'sticky_sidebar',
		'std'     => 'side_menu_bar',
		'type'    => 'select',
		'options' => array(
			'sidebar'       => esc_html__('Sidebar','discy'),
			'nav_menu'      => esc_html__('Sidemenu (If active it)','discy'),
			'side_menu_bar' => esc_html__('Sidebar & Sidemenu (If active them)','discy'),
			'no_sidebar'    => esc_html__('Not active','discy'),
		)
	);
	
	$options[] = array(
		'name' => esc_html__('Widget icons enable or disable','discy'),
		'id'   => 'widget_icons',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'div'       => 'div',
		'operator'  => 'or',
		'condition' => 'sidebar_layout:is(menu_sidebar),sidebar_layout:is(menu_left)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Sidemenu style','discy'),
		'id'      => 'left_area',
		'std'     => 'menu',
		'type'    => 'radio',
		'options' => 
			array(
				"menu"    => esc_html__("Menu","discy"),
				"sidebar" => esc_html__("Sidebar","discy")
			)
	);
	
	$options[] = array(
		'name'      => esc_html__('Choose the left menu style','discy'),
		'id'        => "left_menu_style",
		'options'   => array('style_1' => 'Style 1','style_2' => 'Style 2','style_3' => 'Style 3'),
		'type'      => 'select',
		'condition' => 'left_area:not(sidebar)',
	);
	
	$options[] = array(
		'name'      => esc_html__('Sidebar 2','discy'),
		'id'        => "sidebar_home_2",
		'options'   => $new_sidebars,
		'type'      => 'select',
		'condition' => 'left_area:is(sidebar)',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name'    => esc_html__('Styling & Typography','discy'),
		'id'      => 'styling',
		'icon'    => 'art',
		'type'    => 'heading',
		'std'     => 'styling',
		'options' => array(
			"styling"    => esc_html__('Styling','discy'),
			"typography" => esc_html__('Typography','discy')
		)
	);
	
	$options[] = array(
		'name' => esc_html__('Styling','discy'),
		'id'   => 'styling',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Choose the site width','discy'),
		"id"   => "site_width",
		"type" => "sliderui",
		"std"  => "1170",
		"step" => "10",
		"min"  => "1170",
		"max"  => "1300"
	);
	
	$options[] = array(
		'name'    => esc_html__('Site style','discy'),
		'id'      => 'site_style',
		'std'     => 'none',
		'type'    => 'radio',
		'options' => 
			array(
				"none"    => esc_html__("Normal style","discy"),
				"style_1" => esc_html__("Boxed style 1","discy"),
				"style_2" => esc_html__("Boxed style 2","discy"),
				"style_3" => esc_html__("Boxed style 3 - without left menu","discy"),
				"style_4" => esc_html__("Boxed style 4 - without left menu","discy"),
			)
	);
	
	$options[] = array(
		'name'    => esc_html__('Choose Your Skin','discy'),
		'class'   => "site_skin",
		'id'      => "site_skin",
		'std'     => "default",
		'type'    => "images",
		'options' => array(
			'default'    => $imagepath.'default.jpg',
			'violet'     => $imagepath.'violet.jpg',
			'bright_red' => $imagepath.'bright_red.jpg',
			'green'      => $imagepath.'green.jpg',
			'red'        => $imagepath.'red.jpg',
			'cyan'       => $imagepath.'cyan.jpg',
			'blue'       => $imagepath.'blue.jpg',
		)
	);
	
	$options[] = array(
		'name' => esc_html__('Primary Color','discy'),
		'id'   => 'primary_color',
		'type' => 'color' 
	);
	
	$options[] = array(
		'name'    => esc_html__('Background Type','discy'),
		'id'      => 'background_type',
		'std'     => 'none',
		'type'    => 'radio',
		'options' => 
			array(
				"none"              => esc_html__("None","discy"),
				"patterns"          => esc_html__("Patterns","discy"),
				"custom_background" => esc_html__("Custom Background","discy")
			)
	);
	
	$options[] = array(
		'name'      => esc_html__('Background Color','discy'),
		'id'        => 'background_color',
		'type'      => 'color',
		'condition' => 'background_type:is(patterns)'
	);
		
	$options[] = array(
		'name'      => esc_html__('Choose Pattern','discy'),
		'id'        => "background_pattern",
		'std'       => "bg13",
		'type'      => "images",
		'condition' => 'background_type:is(patterns)',
		'class'     => "pattern_images",
		'options'   => array(
			'bg1'  => $imagepath.'bg1.jpg',
			'bg2'  => $imagepath.'bg2.jpg',
			'bg3'  => $imagepath.'bg3.jpg',
			'bg4'  => $imagepath.'bg4.jpg',
			'bg5'  => $imagepath.'bg5.jpg',
			'bg6'  => $imagepath.'bg6.jpg',
			'bg7'  => $imagepath.'bg7.jpg',
			'bg8'  => $imagepath.'bg8.jpg',
			'bg9'  => $imagepath_theme.'patterns/bg9.png',
			'bg10' => $imagepath_theme.'patterns/bg10.png',
			'bg11' => $imagepath_theme.'patterns/bg11.png',
			'bg12' => $imagepath_theme.'patterns/bg12.png',
			'bg13' => $imagepath.'bg13.jpg',
			'bg14' => $imagepath.'bg14.jpg',
			'bg15' => $imagepath_theme.'patterns/bg15.png',
			'bg16' => $imagepath_theme.'patterns/bg16.png',
			'bg17' => $imagepath.'bg17.jpg',
			'bg18' => $imagepath.'bg18.jpg',
			'bg19' => $imagepath.'bg19.jpg',
			'bg20' => $imagepath.'bg20.jpg',
			'bg21' => $imagepath_theme.'patterns/bg21.png',
			'bg22' => $imagepath.'bg22.jpg',
			'bg23' => $imagepath_theme.'patterns/bg23.png',
			'bg24' => $imagepath_theme.'patterns/bg24.png',
		)
	);
	
	$options[] = array(
		'name'      => esc_html__('Custom Background','discy'),
		'id'        => 'custom_background',
		'std'       => $background_defaults,
		'type'      => 'background',
		'options'   => $background_defaults,
		'condition' => 'background_type:is(custom_background)'
	);
	
	$options[] = array(
		'name'      => esc_html__('Full Screen Background','discy'),
		'desc'      => esc_html__('Select ON to Full Screen Background','discy'),
		'id'        => 'full_screen_background',
		'type'      => 'checkbox',
		'condition' => 'background_type:is(custom_background)'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Typography','discy'),
		'id'   => 'typography',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		"name"    => esc_html__('Main font','discy'),
		"id"      => "main_font",
		"type"    => "typography",
		'std'     => array("face" => "Default font","color" => "","style" => "","size" => 9),
		'options' => array("color" => false,"styles" => false,"sizes" => false)
	);
	
	$options[] = array(
		"name"    => esc_html__('Second font','discy'),
		"id"      => "second_font",
		"type"    => "typography",
		'std'     => array("face" => "Default font","color" => "","style" => "","size" => 9),
		'options' => array("color" => false,"styles" => false,"sizes" => false)
	);
	
	$options[] = array(
		"name"    => esc_html__('General Typography','discy'),
		"id"      => "general_typography",
		"type"    => "typography",
		'options' => array('faces' => false)
	);
	
	$options[] = array(
		'name' => esc_html__('General link color','discy'),
		"id"   => "general_link_color",
		"type" => "color"
	);
	
	$options[] = array(
		"name"    => esc_html__('H1','discy'),
		"id"      => "h1",
		"type"    => "typography",
		'options' => array('faces' => false,"color" => false)
	);
	
	$options[] = array(
		"name"    => esc_html__('H2','discy'),
		"id"      => "h2",
		"type"    => "typography",
		'options' => array('faces' => false,"color" => false)
	);
	
	$options[] = array(
		"name"    => esc_html__('H3','discy'),
		"id"      => "h3",
		"type"    => "typography",
		'options' => array('faces' => false,"color" => false)
	);
	
	$options[] = array(
		"name"    => esc_html__('H4','discy'),
		"id"      => "h4",
		"type"    => "typography",
		'options' => array('faces' => false,"color" => false)
	);
	
	$options[] = array(
		"name"    => esc_html__('H5','discy'),
		"id"      => "h5",
		"type"    => "typography",
		'options' => array('faces' => false,"color" => false)
	);
	
	$options[] = array(
		"name"    => esc_html__('H6','discy'),
		"id"      => "h6",
		"type"    => "typography",
		'options' => array('faces' => false,"color" => false)
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name'    => esc_html__('Social Setting','discy'),
		'id'      => 'social',
		'icon'    => 'share',
		'type'    => 'heading',
		'std'     => 'social',
		'options' => array(
			"social"          => esc_html__('Social Setting','discy'),
			"add_sort_social" => esc_html__('Add & sort social','discy'),
			"social_api"      => esc_html__('Social media API','discy')
		)
	);
	
	$options[] = array(
		'name' => esc_html__('Social Setting','discy'),
		'id'   => 'social',
		'type' => 'heading-2'
	);
	
	$social = array(
		array('name' => esc_html__('Facebook','discy'),"value" => "facebook","icon" => "facebook","default" => "yes"),
		array('name' => esc_html__('Twitter','discy'),"value" => "twitter","icon" => "twitter","default" => "yes"),
		array('name' => esc_html__('Linkedin','discy'),"value" => "linkedin","icon" => "linkedin","default" => "yes"),
		array('name' => esc_html__('Dribbble','discy'),"value" => "dribbble","icon" => "dribbble","default" => "yes"),
		array('name' => esc_html__('Youtube','discy'),"value" => "youtube","icon" => "play","default" => "yes"),
		array('name' => esc_html__('Vimeo','discy'),"value" => "vimeo","icon" => "vimeo","default" => "yes"),
		array('name' => esc_html__('Skype','discy'),"value" => "skype","icon" => "skype","default" => "yes"),
		array('name' => esc_html__('Flickr','discy'),"value" => "flickr","icon" => "flickr","default" => "yes"),
		array('name' => esc_html__('Soundcloud','discy'),"value" => "soundcloud","icon" => "soundcloud","default" => "yes"),
		array('name' => esc_html__('Instagram','discy'),"value" => "instagram","icon" => "instagrem","default" => "yes"),
		array('name' => esc_html__('Pinterest','discy'),"value" => "pinterest","icon" => "pinterest","default" => "yes"),
		array('name' => esc_html__('Rss','discy'),"value" => "rss","icon" => "rss","default" => "yes")
	);
	
	foreach ($social as $key => $value) {
		if ($value["value"] != "rss") {
			$options[] = array(
				'name' => sprintf(esc_html__('%s URL','discy'),esc_attr($value["name"])),
				'desc' => sprintf('Type the %s URL from here.',esc_attr($value["name"])),
				'id'   => $value["value"].'_icon_h',
				'std'  => '#',
				'type' => 'text'
			);
		}else {
			$options[] = array(
				'name' => esc_html__('Rss enable or disable','discy'),
				'id'   => 'rss_icon_h',
				'std'  => 'on',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name'      => esc_html__('RSS URL if you want change the default URL','discy'),
				'desc'      => esc_html__('Type the RSS URL if you want change the default URL or leave it empty for enable the default URL.','discy'),
				'id'        => 'rss_icon_h_other',
				'condition' => 'rss_icon_h:not(0)',
				'type'      => 'text'
			);
		}
	}
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Add a new social item','discy'),
		'id'   => 'add_sort_social',
		'type' => 'heading-2'
	);
	
	$elements = array(
		array(
			"type" => "text",
			"id"   => "name",
			"name" => esc_html__('Name','discy')
		),
		array(
			"type" => "text",
			"id"   => "url",
			"name" => esc_html__('URL','discy')
		),
		array(
			"type" => "text",
			"id"   => "icon",
			"name" => sprintf(esc_html__('Icon (use %s entypo %s like : facebook)','discy'),'<a target="_blank" href="https://2code.info/demo/themes/Discy/entypo/">','</a>')
		)
	);
	
	$options[] = array(
		'id'      => "add_social",
		'type'    => "elements",
		'button'  => esc_html__('Add Custom Social','discy'),
		'options' => $elements,
		'title'   => "name",
		'addto'   => "sort_social"
	);
	
	$options[] = array(
		'id'      => "sort_social",
		'std'     => $social,
		'type'    => "sort",
		'options' => $social,
		'delete'  => "yes",
		'getthe'  => $elements
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Social media API','discy'),
		'id'   => 'social_api',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Facebook app id.','discy'),
		'id'   => 'facebook_app_id',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Facebook access token.','discy'),
		'id'   => 'facebook_access_token',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Soundcloud client id.','discy'),
		'desc' => esc_html__('Type here the Soundcloud client id.','discy'),
		'id'   => 'soundcloud_client_id',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Behance access token.','discy'),
		'desc' => esc_html__('Type here the Behance access token.','discy'),
		'id'   => 'behance_api_key',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Google API.','discy'),
		'desc' => esc_html__('Type here the Google API.','discy'),
		'id'   => 'google_api',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Instagram session.','discy'),
		'desc' => esc_html__('Type here the Instagram session.','discy'),
		'id'   => 'instagram_sessionid',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => sprintf(wp_kses(__('Dribbble app data (Make app from here : https://dribbble.com/account/applications/new), At Callback URL add <a href="%s">this link</a>','discy'),array('a' => array('href' => array(),'target' => array()))),admin_url('admin.php?page=options&api=dribbble')),
		'type' => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__('Dribbble Client ID.','discy'),
		'id'   => 'dribbble_client_id',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Dribbble Client Secret.','discy'),
		'id'   => 'dribbble_client_secret',
		'type' => 'text'
	);
	
	$dribbble_client_id = discy_options('dribbble_client_id');
	$html_content = '<a href="https://dribbble.com/oauth/authorize?client_id='.$dribbble_client_id.'" target="_blank">'.esc_html__('Get the access token from here.','discy').'</a>';
	
	$options[] = array(
		'name' => $html_content,
		'type' => 'info'
	);
	
	$options[] = array(
		'id'   => 'dribbble_access_token',
		'type' => 'hidden'
	);
	
	$options[] = array(
		'name' => esc_html__('Twitter app data.','discy'),
		'type' => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__('Twitter consumer key','discy'),
		'id'   => 'twitter_consumer_key',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Twitter consumer secret','discy'),
		'id'   => 'twitter_consumer_secret',
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Advertising','discy'),
		'id'   => 'advertising',
		'icon' => 'admin-post',
		'type' => 'heading'
	);
	
	$options[] = array(
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Advertising at 404 pages enable or disable','discy'),
		'id'   => 'adv_404',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Advertising after header','discy'),
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Advertising type','discy'),
		'id'      => 'header_adv_type_1',
		'std'     => 'custom_image',
		'type'    => 'radio',
		'options' => array("display_code" => esc_html__("Display code","discy"),"custom_image" => esc_html__("Custom Image","discy"))
	);
	
	$options[] = array(
		'name'      => esc_html__('Image URL','discy'),
		'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','discy'),
		'id'        => 'header_adv_img_1',
		'condition' => 'header_adv_type_1:is(custom_image)',
		'type'      => 'upload'
	);
	
	$options[] = array(
		'name'      => esc_html__('Advertising url','discy'),
		'id'        => 'header_adv_href_1',
		'std'       => '#',
		'condition' => 'header_adv_type_1:is(custom_image)',
		'type'      => 'text'
	);
	
	$options[] = array(
		'name'      => esc_html__('Advertising Code html (Ex: Google ads)','discy'),
		'id'        => 'header_adv_code_1',
		'condition' => 'header_adv_type_1:not(custom_image)',
		'type'      => 'textarea'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Advertising in post or question','discy'),
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Advertising type','discy'),
		'id'      => 'share_adv_type',
		'std'     => 'custom_image',
		'type'    => 'radio',
		'options' => array("display_code" => esc_html__("Display code","discy"),"custom_image" => esc_html__("Custom Image","discy"))
	);
	
	$options[] = array(
		'name'      => esc_html__('Image URL','discy'),
		'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','discy'),
		'id'        => 'share_adv_img',
		'condition' => 'share_adv_type:is(custom_image)',
		'type'      => 'upload'
	);
	
	$options[] = array(
		'name'      => esc_html__('Advertising url','discy'),
		'id'        => 'share_adv_href',
		'std'       => '#',
		'condition' => 'share_adv_type:is(custom_image)',
		'type'      => 'text'
	);
	
	$options[] = array(
		'name'      => esc_html__('Advertising Code html (Ex: Google ads)','discy'),
		'id'        => 'share_adv_code',
		'condition' => 'share_adv_type:not(custom_image)',
		'type'      => 'textarea'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Advertising after left menu','discy'),
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Advertising type','discy'),
		'id'      => 'left_menu_adv_type',
		'std'     => 'custom_image',
		'type'    => 'radio',
		'options' => array("display_code" => esc_html__("Display code","discy"),"custom_image" => esc_html__("Custom Image","discy"))
	);
	
	$options[] = array(
		'name'      => esc_html__('Image URL','discy'),
		'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','discy'),
		'id'        => 'left_menu_adv_img',
		'condition' => 'left_menu_adv_type:is(custom_image)',
		'type'      => 'upload'
	);
	
	$options[] = array(
		'name'      => esc_html__('Advertising url','discy'),
		'id'        => 'left_menu_adv_href',
		'std'       => '#',
		'condition' => 'left_menu_adv_type:is(custom_image)',
		'type'      => 'text'
	);
	
	$options[] = array(
		'name'      => esc_html__('Advertising Code html (Ex: Google ads)','discy'),
		'id'        => 'left_menu_adv_code',
		'condition' => 'left_menu_adv_type:not(custom_image)',
		'type'      => 'textarea'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Advertising after content','discy'),
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Advertising type','discy'),
		'id'      => 'content_adv_type',
		'std'     => 'custom_image',
		'type'    => 'radio',
		'options' => array("display_code" => esc_html__("Display code","discy"),"custom_image" => esc_html__("Custom Image","discy"))
	);
	
	$options[] = array(
		'name'      => esc_html__('Image URL','discy'),
		'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','discy'),
		'id'        => 'content_adv_img',
		'condition' => 'content_adv_type:is(custom_image)',
		'type'      => 'upload'
	);
	
	$options[] = array(
		'name'      => esc_html__('Advertising url','discy'),
		'id'        => 'content_adv_href',
		'std'       => '#',
		'condition' => 'content_adv_type:is(custom_image)',
		'type'      => 'text'
	);
	
	$options[] = array(
		'name'      => esc_html__('Advertising Code html (Ex: Google ads)','discy'),
		'id'        => 'content_adv_code',
		'condition' => 'content_adv_type:not(custom_image)',
		'type'      => 'textarea'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Between questions or posts','discy'),
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Display after x posts or questions','discy'),
		'id'   => 'between_questions_position',
		'std'  => '2',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Repeat adv?','discy'),
		'desc' => esc_html__('Select ON to enable repeat advertising.','discy'),
		'id'   => 'between_adv_type_repeat',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'    => esc_html__('Advertising type','discy'),
		'id'      => 'between_adv_type',
		'std'     => 'custom_image',
		'type'    => 'radio',
		'options' => array("display_code" => esc_html__("Display code","discy"),"custom_image" => esc_html__("Custom Image","discy"))
	);
	
	$options[] = array(
		'name'      => esc_html__('Image URL','discy'),
		'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','discy'),
		'id'        => 'between_adv_img',
		'condition' => 'between_adv_type:is(custom_image)',
		'type'      => 'upload'
	);
	
	$options[] = array(
		'name'      => esc_html__('Advertising url','discy'),
		'id'        => 'between_adv_href',
		'std'       => '#',
		'condition' => 'between_adv_type:is(custom_image)',
		'type'      => 'text'
	);
	
	$options[] = array(
		'name'      => esc_html__('Advertising Code html (Ex: Google ads)','discy'),
		'id'        => 'between_adv_code',
		'condition' => 'between_adv_type:not(custom_image)',
		'type'      => 'textarea'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Between comments or answers','discy'),
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Display after x comments or answers','discy'),
		'id'   => 'between_comments_position',
		'std'  => '2',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Repeat adv?','discy'),
		'desc' => esc_html__('Select ON to enable repeat advertising.','discy'),
		'id'   => 'between_comments_adv_type_repeat',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'    => esc_html__('Advertising type','discy'),
		'id'      => 'between_comments_adv_type',
		'std'     => 'custom_image',
		'type'    => 'radio',
		'options' => array("display_code" => esc_html__("Display code","discy"),"custom_image" => esc_html__("Custom Image","discy"))
	);
	
	$options[] = array(
		'name'      => esc_html__('Image URL','discy'),
		'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','discy'),
		'id'        => 'between_comments_adv_img',
		'condition' => 'between_comments_adv_type:is(custom_image)',
		'type'      => 'upload'
	);
	
	$options[] = array(
		'name'      => esc_html__('Advertising url','discy'),
		'id'        => 'between_comments_adv_href',
		'std'       => '#',
		'condition' => 'between_comments_adv_type:is(custom_image)',
		'type'      => 'text'
	);
	
	$options[] = array(
		'name'      => esc_html__('Advertising Code html (Ex: Google ads)','discy'),
		'id'        => 'between_comments_adv_code',
		'condition' => 'between_comments_adv_type:not(custom_image)',
		'type'      => 'textarea'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Emails settings','discy'),
		'icon' => 'email',
		'type' => 'heading'
	);
		
	$options[] = array(
		'type' => 'heading-2',
	);
	
	$options[] = array(
		'name' => esc_html__("Custom logo for email template",'discy'),
		'desc' => esc_html__("Upload your custom logo for email template",'discy'),
		'id'   => 'logo_email_template',
		'std'  => $imagepath_theme."logo.png",
		'type' => 'upload'
	);
	
	$options[] = array(
		'name' => esc_html__('Background Color for the email template','discy'),
		'id'   => 'background_email',
		'type' => 'color',
		'std'  => '#272930'
	);

	$options[] = array(
		'name' => esc_html__('SMTP mail enable or disable','discy'),
		'id'   => 'mail_smtp',
		'type' => 'checkbox'
	);

	$options[] = array(
		'type'      => 'heading-2',
		'condition' => 'mail_smtp:not(0)',
		'div'       => 'div'
	);

	$options[] = array(
		'name' => esc_html__('SMTP mail host','discy'),
		'id'   => 'mail_host',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('SMTP mail port','discy'),
		'id'   => 'mail_port',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('SMTP mail username','discy'),
		'id'   => 'mail_username',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('SMTP mail password','discy'),
		'id'   => 'mail_password',
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('SMTP mail secure','discy'),
		'id'      => 'mail_secure',
		'std'     => 'ssl',
		'type'    => 'radio',
		'options' => array("ssl" => "SSL","tls" => "TLS")
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'type'      => 'heading-2',
		'condition' => 'mail_smtp:not(on)',
		'div'       => 'div'
	);

	$parse = parse_url(get_site_url());
	
	$options[] = array(
		'name' => esc_html__("Add your email for email template",'discy'),
		'desc' => esc_html__("Add it professional email, like no_reply@2code.info",'discy'),
		'id'   => 'email_template',
		'std'  => "no_reply@".$parse['host'],
		'type' => 'text'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type'    => 'content',
		'content' => '<h4>Variables work at the all templates</h4>
		<p>[%blogname%] - The site title.</p>
		<p>[%site_url%] - The site URL.</p>
		<p>[%messages_url%] - The messages URL page.</p>',
	);
	
	$options[] = array(
		'type'    => 'content',
		//'content' => '<h4>Variables work at Reset password, Confirm email and Approve user</h4>
		'content' => '<h4>Variables work at Reset password and Confirm email</h4>
		<p>[%user_login%] - The user login name.</p>
		<p>[%user_name%] - The user name.</p>
		<p>[%user_nicename%] - The user nice name.</p>
		<p>[%display_name%] - The user display name.</p>
		<p>[%user_email%] - The user email.</p>
		<p>[%user_profile%] - The user profile URL.</p>',
	);
	
	$wp_editor_settings = array(
		'wpautop'       => false,
		'textarea_rows' => 10,
		'quicktags' 	=> false,
		'media_buttons' => false,
		'tabindex' 		=> 5,
		'tinymce' 		=> array(
			'toolbar1'              => 'bold,|,italic,|,underline,|,link,unlink,|,bullist,numlist,qaimage,qacode',
			'toolbar2'              => '',
			'toolbar3'              => '',
			'autoresize_min_height' => 300,
			'force_p_newlines'      => false,
			'statusbar'             => true,
			'force_br_newlines'     => false
		)
	);
	
	$options[] = array(
		'type'    => 'content',
		'content' => '<h4>Variable work at Reset password and Confirm email</h4>
		<p>[%confirm_link_email%] - Confirm email for the user to reset the password at reset password template and at the confirm email template is confirm email to active the user.</p>',
	);
	
	$options[] = array(
		'name' => esc_html__('Reset password title','discy'),
		'id'   => 'title_new_password',
		'std'  => "Reset your password",
		'type' => 'text'
	);
	
	$options[] = array(
		'name'     => esc_html__('Reset password template','discy'),
		'id'       => 'email_new_password',
		'std'      => "<p>Someone requested that the password be reset for the following account:</p><p>Username: '[%display_name%]' ([%user_login%]).</p><p>If this was a mistake, just ignore this email and nothing will happen.</p><p>To reset your password, visit the following address:</p><p><a href='[%confirm_link_email%]'>Click here to reset your password</a></p><p>If the link above does not work, Please use your browser to go to:</p>[%confirm_link_email%]",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);
	
	$options[] = array(
		'type'    => 'content',
		'content' => '<h4>Variable work at this template only</h4>
		<p>[%reset_password%] - The user password.</p>',
	);
	
	$options[] = array(
		'name' => esc_html__('Reset password 2 title','discy'),
		'id'   => 'title_new_password_2',
		'std'  => "Reset your password",
		'type' => 'text'
	);
	
	$options[] = array(
		'name'     => esc_html__('Reset password 2 template','discy'),
		'id'       => 'email_new_password_2',
		'std'      => "<p>You are : [%display_name%] ([%user_login%])</p><p>The New Password : [%reset_password%]</p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);
	
	$options[] = array(
		'name' => esc_html__('Confirm email title','discy'),
		'id'   => 'title_confirm_link',
		'std'  => "Confirm account",
		'type' => 'text'
	);
	
	$options[] = array(
		'name'     => esc_html__('Confirm email template','discy'),
		'id'       => 'email_confirm_link',
		'std'      => "<p>Hi there</p><p>Your registration has been successful! To confirm your account, kindly click on 'Activate' below.</p><p><a href='[%confirm_link_email%]'>Activate</a></p><p>If the link above does not work, Please use your browser to go to:</p>[%confirm_link_email%]</p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);
	
	$options[] = array(
		'name' => esc_html__('Confirm email 2 title','discy'),
		'id'   => 'title_confirm_link_2',
		'std'  => "Confirm account",
		'type' => 'text'
	);
	
	$options[] = array(
		'name'     => esc_html__('Confirm email 2 template','discy'),
		'id'       => 'email_confirm_link_2',
		'std'      => "<p>Hi there</p><p>This is the link to activate your membership</p><p><a href='[%confirm_link_email%]'>Activate</a></p><p>If the link above does not work, Please use your browser to go to:</p>[%confirm_link_email%]</p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);
	/*
	$options[] = array(
		'name' => esc_html__('Approve user title','discy'),
		'id'   => 'title_approve_user',
		'std'  => "Confirm account",
		'type' => 'text'
	);
	
	$options[] = array(
		'name'     => esc_html__('Approve user template','discy'),
		'id'       => 'email_approve_user',
		'std'      => "<p>Hi there</p><p>We just approved your member.</p><p><a href='[%site_url%]'>[%blogname%]</a></p><p>[%site_url%]</p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);
	*/
	$options[] = array(
		'type'    => 'content',
		'content' => '<h4>Variable work at this template only</h4>
		<p>[%messages_title%] - Show the message title.</p>',
	);
	
	$options[] = array(
		'name' => esc_html__('Send message title','discy'),
		'id'   => 'title_new_message',
		'std'  => "New message",
		'type' => 'text'
	);
	
	$options[] = array(
		'name'     => esc_html__('Send message template','discy'),
		'id'       => 'email_new_message',
		'std'      => "<p>Hi there</p><p>There are a new message</p><p><a href='[%messages_url%]'>[%messages_title%]</a></p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);
	
	$options[] = array(
		'type'    => 'content',
		'content' => '<h4>Variables work at this template only</h4>
		<p>[%item_price%] - Show the item price.</p>
		<p>[%item_currency%] - Show the item currency.</p>
		<p>[%payer_email%] - Show the payer email.</p>
		<p>[%first_name%] - Show the payer first name.</p>
		<p>[%last_name%] - Show the payer last name.</p>
		<p>[%item_transaction%] - Show the transaction id.</p>
		<p>[%date%] - Show the payment date.</p>
		<p>[%time%] - Show the payment time.</p>',
	);
	
	$options[] = array(
		'name' => esc_html__('New payment title','discy'),
		'id'   => 'title_new_payment',
		'std'  => "Instant Payment Notification - Received Payment",
		'type' => 'text'
	);
	
	$options[] = array(
		'name'     => esc_html__('New payment template','discy'),
		'id'       => 'email_new_payment',
		'std'      => "<p>An instant payment notification was successfully recieved</p><p>With [%item_price%] [%item_currency%]</p><p>From [%payer_email%] [%first_name%] - [%last_name%] on [%date%] at [%time%]</p><p>The item transaction id [%item_transaction%]</p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);
	
	$options[] = array(
		'type'    => 'content',
		'content' => '<h4>Variables work at all next 7 templates</h4>
		<p>[%post_title%] - Show the post title.</p>
		<p>[%post_link%] - Show the post link.</p>
		<p>[%the_author_post%] - Show the post author.</p>',
	);
	
	$options[] = array(
		'type'    => 'content',
		'content' => '<h4>Variables work at Report answer, Notified answer and Follow question</h4>
		<p>[%answer_link%] - Show the answer link.</p>
		<p>[%the_name%] - Show the answer author.</p>',
	);
	
	$options[] = array(
		'name' => esc_html__('Report question title','discy'),
		'id'   => 'title_report_question',
		'std'  => "Question report",
		'type' => 'text'
	);
	
	$options[] = array(
		'name'     => esc_html__('Report question template','discy'),
		'id'       => 'email_report_question',
		'std'      => "<p>Hi there</p><p>Abuse have been reported on the use of the following question</p><p><a href='[%post_link%]'>[%post_title%]</a></p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);
	
	$options[] = array(
		'name' => esc_html__('Report answer title','discy'),
		'id'   => 'title_report_answer',
		'std'  => "Answer report",
		'type' => 'text'
	);
	
	$options[] = array(
		'name'     => esc_html__('Report answer template','discy'),
		'id'       => 'email_report_answer',
		'std'      => "<p>Hi there</p><p>Abuse have been reported on the use of the following comment</p><p><a href='[%answer_link%]'>[%post_title%]</a></p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);
	
	$options[] = array(
		'name' => esc_html__('Notified answer title','discy'),
		'id'   => 'title_notified_answer',
		'std'  => "Answer to your question",
		'type' => 'text'
	);
	
	$options[] = array(
		'name'     => esc_html__('Notified answer template','discy'),
		'id'       => 'email_notified_answer',
		'std'      => "<p>Hi there</p><p>We would tell you [%the_author_post%] That the new post was added on a common theme by [%the_name%] Entitled [%the_name%] [%post_title%]</p><p>Click on the link below to go to the topic</p><p><a href='[%answer_link%]'>[%post_title%]</a></p><p>There may be more of Posts and we hope the answer to encourage members and get them to help.</p><p>Accept from us Sincerely</p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);
	
	$options[] = array(
		'name' => esc_html__('Follow question title','discy'),
		'id'   => 'title_follow_question',
		'std'  => "New answer on your following question",
		'type' => 'text'
	);
	
	$options[] = array(
		'name'     => esc_html__('Follow question template','discy'),
		'id'       => 'email_follow_question',
		'std'      => "<p>Hi there</p><p>There are a new answer on your following question</p><p><a href='[%answer_link%]'>[%post_title%]</a></p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);
	
	$options[] = array(
		'name' => esc_html__('New questions title','discy'),
		'id'   => 'title_new_questions',
		'std'  => "New question",
		'type' => 'text'
	);
	
	$options[] = array(
		'name'     => esc_html__('New questions template','discy'),
		'id'       => 'email_new_questions',
		'std'      => "<p>Hi there</p><p>There are a new question</p><p><a href='[%post_link%]'>[%post_title%]</a></p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);
	
	$options[] = array(
		'name' => esc_html__('New questions for review title','discy'),
		'id'   => 'title_new_draft_questions',
		'std'  => "New question for review",
		'type' => 'text'
	);
	
	$options[] = array(
		'name'     => esc_html__('New questions for review template','discy'),
		'id'       => 'email_draft_questions',
		'std'      => "<p>Hi there</p><p>There are a new question for the review</p><p><a href='[%post_link%]'>[%post_title%]</a></p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);
	
	$options[] = array(
		'name' => esc_html__('New posts for review title','discy'),
		'id'   => 'title_new_draft_posts',
		'std'  => "New post for review",
		'type' => 'text'
	);
	
	$options[] = array(
		'name'     => esc_html__('New posts for review template','discy'),
		'id'       => 'email_draft_posts',
		'std'      => "<p>Hi there</p><p>There are a new post for the review</p><p><a href='[%post_link%]'>[%post_title%]</a></p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name'    => esc_html__('Footer settings','discy'),
		'id'      => 'footer',
		'icon'    => 'tagcloud',
		'type'    => 'heading',
		'std'     => 'footer_general',
		'options' => array(
			"footer_general"  => esc_html__('General setting','discy'),
			"footer_main"     => esc_html__('Main Footer setting','discy'),
			"footer_bottom"   => esc_html__('Bottom footer setting','discy'),
			"footer_sort"     => esc_html__('Sort footer elements','discy')
		)
	);
	
	$options[] = array(
		'name' => esc_html__('General setting','discy'),
		'id'   => 'footer_general',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Footer style','discy'),
		'desc'    => esc_html__('Choose the footer style.','discy'),
		'id'      => 'footer_style',
		'std'     => 'footer',
		'type'    => 'radio',
		'options' => array("footer" => esc_html__("Normal footer","discy"),"sidebar" => esc_html__("After sidebar","discy"))
	);
	
	$options[] = array(
		'name'      => esc_html__('Footer skin','discy'),
		'desc'      => esc_html__('Choose the footer skin.','discy'),
		'id'        => 'footer_skin',
		'std'       => 'dark',
		'type'      => 'radio',
		'condition' => 'footer_style:not(sidebar)',
		'options'   => array("dark" => esc_html__("Dark","discy"),"light" => esc_html__("Light","discy"))
	);
	
	$options[] = array(
		'name'      => esc_html__('Footer menu enable or disable','discy'),
		'id'        => 'active_footer_menu',
		'std'       => 'on',
		'condition' => 'footer_style:not(footer)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Choose from here what\'s menu will show after sidebar.','discy'),
		'id'        => 'footer_menu',
		'type'      => 'select',
		'condition' => 'footer_style:not(footer),active_footer_menu:not(0)',
		'options'   => $menus
	);
	
	$options[] = array(
		'name'      => esc_html__('Copyrights','discy'),
		'desc'      => esc_html__('Put the copyrights of footer.','discy'),
		'id'        => 'footer_copyrights',
		'std'       => '&copy; 2019 Discy. All Rights Reserved<br>With Love by <a href=https://2code.info/ target="_blank">2code</a>.',
		'operator'  => 'or',
		'condition' => 'footer_style:not(footer),bottom_footer:not(0)',
		'type'      => 'textarea'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Main Footer setting','discy'),
		'id'   => 'footer_main',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name'      => esc_html__('The main footer work when you choose the footer style as normal footer.','discy'),
		'condition' => 'footer_style:not(footer)',
		'type'      => 'info'
	);
	
	$options[] = array(
		'type'      => 'heading-2',
		'condition' => 'footer_style:not(sidebar)',
		'div'       => 'div'
	);
	
	$options[] = array(
		'name' => esc_html__('Top footer enable or disable','discy'),
		'id'   => 'top_footer',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'top_footer:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Footer widget icons enable or disable','discy'),
		'id'   => 'footer_widget_icons',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Top footer padding top','discy'),
		"id"   => "top_footer_padding_top",
		"type" => "sliderui",
		"std"  => "0",
		"step" => "1",
		"min"  => "0",
		"max"  => "100"
	);
	
	$options[] = array(
		'name' => esc_html__('Top footer padding bottom','discy'),
		"id"   => "top_footer_padding_bottom",
		"type" => "sliderui",
		"std"  => "0",
		"step" => "1",
		"min"  => "0",
		"max"  => "100"
	);
	
	$options[] = array(
		'name'    => esc_html__('Footer Layout','discy'),
		'desc'    => esc_html__('Footer columns Layout.','discy'),
		'id'      => "footer_layout",
		'std'     => "footer_5c",
		'type'    => "images",
		'options' => array(
			'footer_1c' => $imagepath.'footer_1c.jpg',
			'footer_2c' => $imagepath.'footer_2c.jpg',
			'footer_3c' => $imagepath.'footer_3c.jpg',
			'footer_4c' => $imagepath.'footer_4c.jpg',
			'footer_5c' => $imagepath.'footer_5c.jpg')
	);
	
	$footer_elements = array(
		array(
			"type" => "color",
			"id"   => "background_color",
			"name" => esc_html__('Background color','discy')
		),
		array(
			"type"  => "slider",
			"id"    => "padding_top",
			"name"  => esc_html__('Padding top','discy'),
			"std"   => "0",
			"step"  => "1",
			"min"   => "0",
			"max"   => "100",
			"value" => "0"
		),
		array(
			"type"  => "slider",
			"id"    => "padding_bottom",
			"name"  => esc_html__('Padding bottom','discy'),
			"std"   => "0",
			"step"  => "1",
			"min"   => "0",
			"max"   => "100",
			"value" => "0"
		),
		array(
			"type"    => "images",
			"id"      => "layout",
			"name"    => esc_html__('Layout','discy'),
			'std'     => "footer_5c",
			'options' => array(
				'footer_1c' => $imagepath.'footer_1c.jpg',
				'footer_2c' => $imagepath.'footer_2c.jpg',
				'footer_3c' => $imagepath.'footer_3c.jpg',
				'footer_4c' => $imagepath.'footer_4c.jpg',
				'footer_5c' => $imagepath.'footer_5c.jpg')
		),
		array(
			"type"      => "select",
			"id"        => "first_column",
			"name"      => esc_html__('Select first column','discy'),
			'condition' => '[%id%]layout:is(footer_1c),[%id%]layout:is(footer_2c),[%id%]layout:is(footer_3c),[%id%]layout:is(footer_4c),[%id%]layout:is(footer_5c)',
			'operator'  => 'or',
			'options'   => $new_sidebars
		),
		array(
			"type"      => "select",
			"id"        => "second_column",
			"name"      => esc_html__('Select second column','discy'),
			'condition' => '[%id%]layout:is(footer_2c),[%id%]layout:is(footer_3c),[%id%]layout:is(footer_4c),[%id%]layout:is(footer_5c)',
			'operator'  => 'or',
			'options'   => $new_sidebars
		),
		array(
			"type"      => "select",
			"id"        => "third_column",
			"name"      => esc_html__('Select third column','discy'),
			'condition' => '[%id%]layout:is(footer_3c),[%id%]layout:is(footer_4c),[%id%]layout:is(footer_5c)',
			'operator'  => 'or',
			'options'   => $new_sidebars
		),
		array(
			"type"      => "select",
			"id"        => "fourth_column",
			"name"      => esc_html__('Select fourth column','discy'),
			'condition' => '[%id%]layout:is(footer_4c),[%id%]layout:is(footer_5c)',
			'operator'  => 'or',
			'options'   => $new_sidebars
		),
		array(
			"type"      => "select",
			"id"        => "fifth_column",
			"name"      => esc_html__('Select fifth column','discy'),
			'condition' => '[%id%]layout:is(footer_5c)',
			'operator'  => 'or',
			'options'   => $new_sidebars
		),
	);
	
	$options[] = array(
		'id'      => "add_footer",
		'type'    => "elements",
		'button'  => esc_html__('Add new footer level','discy'),
		'hide'    => "yes",
		'options' => $footer_elements,
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Bottom footer setting','discy'),
		'id'   => 'footer_bottom',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name'      => esc_html__('The bottom footer work when you choose the footer style as normal footer.','discy'),
		'condition' => 'footer_style:not(footer)',
		'type'      => 'info'
	);
	
	$options[] = array(
		'type'      => 'heading-2',
		'condition' => 'footer_style:not(sidebar)',
		'div'       => 'div'
	);
	
	$options[] = array(
		'name' => esc_html__('Bottom footer enable or disable','discy'),
		'id'   => 'bottom_footer',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'bottom_footer:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Bottom footer padding top','discy'),
		"id"   => "footer_padding_top",
		"type" => "sliderui",
		"std"  => "0",
		"step" => "1",
		"min"  => "0",
		"max"  => "100"
	);
	
	$options[] = array(
		'name' => esc_html__('Bottom footer padding bottom','discy'),
		"id"   => "footer_padding_bottom",
		"type" => "sliderui",
		"std"  => "0",
		"step" => "1",
		"min"  => "0",
		"max"  => "100"
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Sort the footer elements','discy'),
		'id'   => 'footer_sort',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name'      => esc_html__('The sort footer elements work when you choose the footer style as normal footer.','discy'),
		'condition' => 'footer_style:not(footer)',
		'type'      => 'info'
	);
	
	$options[] = array(
		'id'        => "sort_footer_elements",
		'condition' => 'footer_style:not(sidebar)',
		'std'       => array(
						array("value" => "top_footer",'name' => esc_html__('Top footer','discy'),"default" => "yes"),
						array("value" => "bottom_footer",'name' => esc_html__('Bottom footer','discy'),"default" => "yes")
					),
		'type'      => "sort",
		'options'   => array(
						array("value" => "top_footer",'name' => esc_html__('Top footer','discy'),"default" => "yes"),
						array("value" => "bottom_footer",'name' => esc_html__('Bottom footer','discy'),"default" => "yes")
					)
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Advanced setting','discy'),
		'id'   => "advanced_setting",
		'icon' => 'upload',
		'type' => 'heading',
	);
	
	$options[] = array(
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'id'   => 'uniqid_cookie',
		'std'  => $rand = rand(1,100000),
		'type' => 'hidden'
	);
	
	$options[] = array(
		'name' => esc_html__('Active the advanced queries at the site','discy'),
		'desc' => esc_html__('Select ON if you want to active the advanced queries at the site.','discy'),
		'id'   => 'advanced_queries',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Active this option ON only if you have used Ask Me theme before.','discy'),
		'id'   => 'ask_me',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Do you need to activate the views at your site?','discy'),
		'id'   => 'active_post_stats',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'active_post_stats:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Post meta stats field.','discy'),
		'desc' => esc_html__('Change this if you have used a post views plugin before.','discy'),
		'id'   => 'post_meta_stats',
		'std'  => 'post_stats',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Active the visits at the site work by cookie','discy'),
		'desc' => esc_html__('Select ON if you want to active the cookie for the visits at the site.','discy'),
		'id'   => 'visit_cookie',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name'      => esc_html__('User meta avatar field.','discy'),
		'desc'      => esc_html__('Change this if you have used a user avatar or social plugin before.','discy'),
		'id'        => 'user_meta_avatar',
		'std'       => 'you_avatar',
		'type'      => 'text'
	);
	
	$options[] = array(
		'name'      => esc_html__('User meta cover field.','discy'),
		'desc'      => esc_html__('Change this if you have used a user cover or social plugin before.','discy'),
		'id'        => 'user_meta_cover',
		'std'       => 'your_cover',
		'type'      => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('If you wont to export setting please refresh the page before that','discy'),
		'type' => 'info'
	);

	$options[] = array(
		'name'   => esc_html__('Export Setting','discy'),
		'desc'   => esc_html__('Copy this to saved file','discy'),
		'id'     => 'export_setting',
		'export' => $current_options_e,
		'type'   => 'export'
	);

	$options[] = array(
		'name' => esc_html__('Import Setting','discy'),
		'desc' => esc_html__('Put here the import setting','discy'),
		'id'   => 'import_setting',
		'type' => 'import'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	
	return $options;
}?>