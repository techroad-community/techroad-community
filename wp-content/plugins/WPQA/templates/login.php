<?php

/* @author    2codeThemes
*  @package   WPQA/templates
*  @version   1.0
*/

do_action("wpqa_before_login");

echo "<div class='wpqa-login-template'>".do_shortcode("[wpqa_login]")."</div>";

do_action("wpqa_after_login");?>