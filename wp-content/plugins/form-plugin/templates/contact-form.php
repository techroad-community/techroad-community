<a href="/wp-admin/edit.php?post_type=testimonial"><?php _e('Goto Result through Admin page','brand-h-form'); ?> </a>
<form id="brandh-form-form" action="#" method="post" data-url="<?php echo admin_url('admin-ajax.php'); ?>">

	<div class="field-container">
		<input type="text" class="field-input" placeholder="Your Name" id="name" name="name" required>
		<small class="field-msg error" data-error="invalidName">Your Name is Required</small>
	</div>

	<div class="field-container">
		<input type="email" class="field-input" placeholder="Your Email" id="email" name="email" required>
		<small class="field-msg error" data-error="invalidEmail">The Email address is not valid</small>
	</div>

	<div class="field-container">
		<input type="text" class="field-input" placeholder="Your Phone" id="phone" name="phone" >
		<small class="field-msg error" data-error="invalidPhone">The Phone is not valid</small>
	</div>

	<div class="field-container">
		<input type="text" class="field-input" placeholder="Your Care Point" id="care" name="care" >
		<small class="field-msg error" data-error="invalidCare">The Care is not valid</small>
	</div>

	<div class="field-container">
		<textarea name="message" id="message" class="field-input" placeholder="Your Message" required></textarea>
		<small class="field-msg error" data-error="invalidMessage">A Message is Required</small>
	</div>
	
	<div class="field-container">
		<div>
            <button type="stubmit" class="btn btn-default btn-lg btn-sunset-form">Submit</button>
        </div>
		<small class="field-msg js-form-submission">Submission in process, please wait&hellip;</small>
		<small class="field-msg success js-form-success">Successfully submitted, <a href="/wp-admin/edit.php?post_type=testimonial"> Goto Result through Admin page</a></small>
		<small class="field-msg error js-form-error">There was a problem with the Contact Form, please try again!</small>
	</div>

	<input type="hidden" name="action" value="submit_testimonial">
	<input type="hidden" name="nonce" value="<?php echo wp_create_nonce("testimonial-nonce") ?>">

</form>