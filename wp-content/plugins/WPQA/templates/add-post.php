<?php

/* @author    2codeThemes
*  @package   WPQA/templates
*  @version   1.0
*/

do_action("wpqa_before_add_post");

echo "<div class='wpqa-add-post-template'>".do_shortcode("[wpqa_add_post]")."</div>";

do_action("wpqa_after_add_post");?>