<script type="text/javascript" src="<?php echo plugins_url('jscolor.js',__FILE__); ?>"></script>
<style>
.wpcs_wrap input[type=text]{width:345px;}
.navbar ul {
  list-style: none;
  overflow: hidden;
  margin: 0;
  border-bottom: 1px solid;
}
.tabs li {
  float: left;
  overflow: hidden;
  margin-bottom: 0;
}
.tabs li a {
  text-decoration: none;
  font-size: 20px;
  color: #fff;
  background: #949597;
  padding: 13px 35px;
  display: block;
  border-top-left-radius: 20px;
  border-top-right-radius: 20px;
  font-family: 'Gill Sans MT Condensed';
  margin-right: 5px;
  font-weight: bolder;
}
.tabs a:focus, .tabs a:hover, .tabs .current a {
  background: #c7c8ca;
  border-bottom: 0px;
  color: #231f20;
}
.tabs li:first-child a {
  border-left: none;
}
</style>
 <script>
 jQuery(function(){
  jQuery('ul.tabs li:first').addClass('current');
  jQuery('.tab_content').hide();
  jQuery('.tab_content:first').show();
  jQuery('ul.tabs li').on('click',function(){
    jQuery('ul.tabs li').removeClass('current');
    jQuery(this).addClass('current')
    jQuery('.tab_content').hide();
    var activeTab = jQuery(this).find('a').attr('href');
    jQuery(activeTab).show();
    return false;
  });
})
</script>
<h1>Wordpress Custom Search</h1>
<div class="navbar">
	<ul class="tabs">
		<li class="current"><a href="#one">Settings</a></li>
		<li><a href="#two">Widget</a></li>
	</ul>

	<div class="settings_tab_content" id="settings_tab_content" role="main">

		<div id="one" class="tab_content" style="display: block;">
<div class="wpcs_wrap">

<form method="post" action="options.php">
    <?php settings_fields( 'wpcs-settings-group' ); ?>
    <?php do_settings_sections( 'wpcs-settings-group' ); ?>
    <table class="form-table">
	<tr valign="top">
        <td scope="row" colspan="2" style="background-color: #ccc;"><h2>Search Settings</h2></td>
        </tr>

	<tr valign="top">
        <td scope="row" colspan="2" style="background-color: #ccc;">You Can Easily use WP Custom Search Plugin with a simple shortcode. <em style="padding: 2px 10px;background-color: #C7DFFF;">[wpcs_search]</em>, add it anywhere and enjoy searching..</td>
        </tr>

        <tr valign="top">
        <th scope="row">Top Heading :</th>
        <td><input type="text" name="wpcs_top_head" value="<?php echo get_option('wpcs_top_head'); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Sub Heading :</th>
        <td><input type="text" name="wpcs_sub_head" value="<?php echo get_option('wpcs_sub_head'); ?>" /></td>
        </tr>
 
        <tr valign="top">
        <th scope="row">Show WP Category :</th>
        <td><input type="checkbox" name="wpcs_default_cat" value="1" <?php if( get_option('wpcs_default_cat') == 1 ){echo 'checked="checked"';} ?> /></td>
        </tr>

	<tr valign="top">
	<th scope="row">Show Custom Taxonomy :</th>
	<td>
	<?php
	$args = array(
	  'public'   => true,
	  '_builtin' => false
	  
	); 
	$output = 'names'; // or objects
	$operator = 'and'; // 'and' or 'or'
	$taxonomies = get_taxonomies( $args, $output, $operator ); 
	//print_r($taxonomies);
	if ( count($taxonomies) > 0 ) {
	  foreach ( $taxonomies  as $taxonomy ) {
		?>
		
			<input type="checkbox" name="wpcs_custom_taxonomy[]" value="<?php echo $taxonomy; ?>" <?php
			if(get_option('wpcs_custom_taxonomy') != NULL){
			$custom_taxonomy = get_option('wpcs_custom_taxonomy');
			 if( in_array($taxonomy, $custom_taxonomy) ){echo 'checked="checked"';}
			}
			 ?> /> <?php echo $taxonomy; ?><br/>
		<?php
	  }
	}
	?></td>
	</tr>
	
	 <tr valign="top">
        <th scope="row">Show WP Tags :</th>
        <td><input type="checkbox" name="wpcs_wp_tags" value="1" <?php if( get_option('wpcs_wp_tags') == 1 ){echo 'checked="checked"';} ?> /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Hide Empty Category :</th>
        <td><input type="checkbox" name="wpcs_hide_empty" value="1" <?php if( get_option('wpcs_hide_empty') == 1 ){echo 'checked="checked"';} ?> /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Hide Uncategorized :</th>
        <td><input type="checkbox" name="wpcs_hide_uncat" value="1" <?php if( get_option('wpcs_hide_uncat') == 1 ){echo 'checked="checked"';} ?> /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Searh Button :</th>
        <td><input type="text" name="wpcs_search_button" value="<?php echo get_option('wpcs_search_button'); ?>"  /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Searh Field :</th>
        <td><input type="text" name="wpcs_search_field" value="<?php echo get_option('wpcs_search_field'); ?>"  /></td>
        </tr>

	<tr valign="top">
        <td scope="row" colspan="2" style="background-color: #ccc;"><h2>Styling Options</h2></td>
        </tr>
	
        <tr valign="top">
        <th scope="row">Background Color :</th>
        <td><input class="color" name="wpcs_back_color" value="<?php echo get_option('wpcs_back_color'); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Button Color :</th>
        <td><input class="color" name="wpcs_button_color" value="<?php echo get_option('wpcs_button_color'); ?>" /></td>
        </tr>
	
        <tr valign="top">
        <th scope="row">Category Color :</th>
        <td><input class="color" name="wpcs_cat_color" value="<?php echo get_option('wpcs_cat_color'); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Text Color :</th>
        <td><input class="color" name="wpcs_text_color" value="<?php echo get_option('wpcs_text_color'); ?>" /></td>
        </tr>
	
        <tr valign="top">
        <th scope="row">Top Heading Size :</th>
        <td><input type="number" name="wpcs_top_head_px" min="1" style="width: 50px;"  value="<?php echo get_option('wpcs_top_head_px'); ?>" />px</td>
        </tr>
	
        <tr valign="top">
        <th scope="row">Sub Heading Size :</th>
        <td><input type="number" name="wpcs_sub_head_px" min="1" style="width: 50px;"  value="<?php echo get_option('wpcs_sub_head_px'); ?>" />px</td>
        </tr>
	
	<tr valign="top">
        <th scope="row">Custom CSS :<br/><span style="font-weight: normal;font-size: 11px;">Example:<br/> .textclass{ width:200px; height:200px; }</span></th>
        <td>&nbsp;</td>
        </tr>
<tr valign="top">
<td colspan="2" style="padding: 0px 0 0 0;"><textarea name="wpcs_custom_css" placeholder="Custom CSS Starts Here" style="width:500px; height:300px;"><?php echo get_option('wpcs_custom_css'); ?></textarea></td>
</tr>
    </table> 
    <?php submit_button(); ?>

</div>
</div><!-- end tab_content -->

	<div id="two" class="tab_content" style="display: none;">
	<div class="wpcs_wrap">

    <table class="form-table">
	<tr valign="top">
        <td scope="row" colspan="2" style="background-color: #ccc;"><h2>Search Box Widget Options</h2></td>
        </tr>

        <tr valign="top">
        <th scope="row">Top Heading :</th>
        <td><input type="text" name="wpcs_top_head_widget" value="<?php echo get_option('wpcs_top_head_widget'); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Sub Heading :</th>
        <td><input type="text" name="wpcs_sub_head_widget" value="<?php echo get_option('wpcs_sub_head_widget'); ?>" /></td>
        </tr>
 
        <tr valign="top">
        <th scope="row">Show WP Category :</th>
        <td><input type="checkbox" name="wpcs_default_cat_widget" value="1" <?php if( get_option('wpcs_default_cat_widget') == 1 ){echo 'checked="checked"';} ?> /></td>
        </tr>

	<tr valign="top">
	<th scope="row">Show Custom Taxonomy :</th>
	<td>
	<?php
	$args = array(
	  'public'   => true,
	  '_builtin' => false
	  
	); 
	$output = 'names'; // or objects
	$operator = 'and'; // 'and' or 'or'
	$taxonomies = get_taxonomies( $args, $output, $operator ); 
	//print_r($taxonomies);
	if ( count($taxonomies) > 0 ) {
	  foreach ( $taxonomies  as $taxonomy ) {
		?>
		
			<input type="checkbox" name="wpcs_custom_taxonomy_widget[]" value="<?php echo $taxonomy; ?>" <?php
			if(get_option('wpcs_custom_taxonomy_widget') != NULL){
			$custom_taxonomy = get_option('wpcs_custom_taxonomy_widget');
			 if( in_array($taxonomy, $custom_taxonomy) ){echo 'checked="checked"';}
			}
			 ?> /> <?php echo $taxonomy; ?><br/>
		<?php
	  }
	}
	?></td>
	</tr>
	
	 <tr valign="top">
        <th scope="row">Show WP Tags :</th>
        <td><input type="checkbox" name="wpcs_wp_tags_widget" value="1" <?php if( get_option('wpcs_wp_tags_widget') == 1 ){echo 'checked="checked"';} ?> /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Hide Empty Category :</th>
        <td><input type="checkbox" name="wpcs_hide_empty_widget" value="1" <?php if( get_option('wpcs_hide_empty_widget') == 1 ){echo 'checked="checked"';} ?> /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Hide Uncategorized :</th>
        <td><input type="checkbox" name="wpcs_hide_uncat_widget" value="1" <?php if( get_option('wpcs_hide_uncat_widget') == 1 ){echo 'checked="checked"';} ?> /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Searh Button :</th>
        <td><input type="text" name="wpcs_search_button_widget" value="<?php echo get_option('wpcs_search_button_widget'); ?>"  /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Searh Field :</th>
        <td><input type="text" name="wpcs_search_field_widget" value="<?php echo get_option('wpcs_search_field_widget'); ?>"  /></td>
        </tr>

	<tr valign="top">
        <td scope="row" colspan="2" style="background-color: #ccc;"><h2>Most Viewed Widget Options</h2></td>
        </tr>
	<tr valign="top">
        <th scope="row">Display Setting :</th>
        <td><input type="checkbox" name="wpcs_thnumbnail_widget" value="1" <?php if( get_option('wpcs_thnumbnail_widget') == 1 ){echo 'checked="checked"';} ?> /> Post Thumbnail<br/>
	<input type="checkbox" name="wpcs_title_widget" value="1" <?php if( get_option('wpcs_title_widget') == 1 ){echo 'checked="checked"';} ?> /> Post Title<br/>
	<input type="checkbox" name="wpcs_excerpt_widget" value="1" <?php if( get_option('wpcs_excerpt_widget') == 1 ){echo 'checked="checked"';} ?> /> Post Excerpt<br/>
	<input type="checkbox" name="wpcs_author_widget" value="1" <?php if( get_option('wpcs_author_widget') == 1 ){echo 'checked="checked"';} ?> /> Post Author<br/>
	<input type="checkbox" name="wpcs_count_widget" value="1" <?php if( get_option('wpcs_count_widget') == 1 ){echo 'checked="checked"';} ?> /> Post View Count<br/>
	</td>
        </tr>
	<tr valign="top">
        <th scope="row">Excerpt Length :</th>
        <td><input type="number" class="number" min="0" name="wpcs_excerpt_length_widget" value="<?php echo get_option('wpcs_excerpt_length_widget'); ?>" /></td>
        </tr>
	<tr valign="top">
        <th scope="row">Show Posts :</th>
        <td><input type="number" class="number" min="1" name="wpcs_postno_widget" value="<?php echo get_option('wpcs_postno_widget'); ?>" /></td>
        </tr>

	<tr valign="top">
        <td scope="row" colspan="2" style="background-color: #ccc;"><h2>Styling Options</h2></td>
        </tr>
	
        <tr valign="top">
        <th scope="row">Background Color :</th>
        <td><input class="color" name="wpcs_back_color_widget" value="<?php echo get_option('wpcs_back_color_widget'); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Button Color :</th>
        <td><input class="color" name="wpcs_button_color_widget" value="<?php echo get_option('wpcs_button_color_widget'); ?>" /></td>
        </tr>
	
        <tr valign="top">
        <th scope="row">Category Color :</th>
        <td><input class="color" name="wpcs_cat_color_widget" value="<?php echo get_option('wpcs_cat_color_widget'); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Text Color :</th>
        <td><input class="color" name="wpcs_text_color_widget" value="<?php echo get_option('wpcs_text_color_widget'); ?>" /></td>
        </tr>
	
        <tr valign="top">
        <th scope="row">Top Heading Size :</th>
        <td><input type="number" name="wpcs_top_head_px_widget" min="1" style="width: 50px;"  value="<?php echo get_option('wpcs_top_head_px_widget'); ?>" />px</td>
        </tr>
	
        <tr valign="top">
        <th scope="row">Sub Heading Size :</th>
        <td><input type="number" name="wpcs_sub_head_px_widget" min="1" style="width: 50px;"  value="<?php echo get_option('wpcs_sub_head_px_widget'); ?>" />px</td>
        </tr>
	
    </table> 
    <?php submit_button(); ?>
</form>
</div>
	</div><!-- end tab_content -->

</div><!-- end content -->	

</div>

