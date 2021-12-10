<?php

/* @author    2codeThemes
*  @package   WPQA/templates
*  @version   1.0
*/

do_action("wpqa_before_signup");

echo "<div class='wpqa-signup-template'>".do_shortcode("[wpqa_signup]")."</div>";

do_action("wpqa_after_signup");?>