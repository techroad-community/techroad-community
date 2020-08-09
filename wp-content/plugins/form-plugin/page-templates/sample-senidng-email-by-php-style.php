<?php 
//get_header();
if ($_SERVER["REQUEST_METHOD"] == "POST"){
	if(!empty($_POST['yourname']))
	{
		$name = $_POST['yourname'];
	}
	if(!empty($_POST['youremail']))
	{
		$email = $_POST['youremail'];
	}
	if(!empty($_POST['phone']))
	{
		$phone = $_POST['phone'];
	}
	if(!empty($_POST['point']))
	{
		$point = $_POST['point'];
	}
	if(!empty($_POST['msg']))
	{
		$message = "<p>Hi $name</p><p>Your Phone number is $phone and Your point are $point.</p><p>".$_POST['msg']."</p>";
	}
      
    $to = $email;
    $subject = "Thanks for Using my Website.";
    
    $headers = 'Content-type: text/html; charset=iso-8859-1';
    $headers .= "From: Job Post at admin@gmail.com";
    $mailResult = wp_mail( $to, $subject, $message ,$headers);
    
	//echo $mailResult; die;
	if($mailResult == 1)
	{
		$data = array(
			'post_title' => "",
			'post_type' => "testimonial",
			'post_status' => "publish"
		);
		//wp_insert_post( $data);

		$post_id = wp_insert_post($data);

		$data1 = array(
			'name' => sanitize_text_field($name),
			'email' => sanitize_email($email),
			'phone' => sanitize_text_field($phone),
			'care' => sanitize_text_field($point),
			'approved' => 1,
			'featured' => 1
		);
		update_post_meta( $post_id, '_brandh_form_key', $data1 );
		
		$message_mail =  1;
	}
	else
	{
		$message_mail =  0;
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
	<?php if(isset($message_mail) && !empty($message_mail) && $message_mail == 1){ ?>
		<div class="alert alert-success">
			<strong>Success!</strong> Mail Sent successful.
		</div>
	<?php }else if(isset($message_mail) && !empty($message_mail) && $message_mail == 0){ ?>
		<div class="alert alert-danger">
			Mail Not Send.
		</div>
		
	<?php } ?>
	<form action="<?php the_permalink(); ?>" method="POST" class="">
		<div class="form-group">
			<label for="name">Your Name:</label>
			<input type="text" class="form-control" id="yourname" placeholder="Your Name" name="yourname" required>
		</div>
		<div class="form-group">
			<label for="email">Email:</label>
			<input type="text" class="form-control" id="youremail" placeholder="Your Email" name="youremail" required>
		</div>
		<div class="form-group">
			<label for="phone">Your Phone:</label>
			<input type="text" class="form-control" id="phone" placeholder="Your Phone" name="phone" required>
		</div>
		<div class="form-group">
			<label for="point">Your Care Point:</label>
			<input type="text" class="form-control" id="point" placeholder="Your Care Point" name="point" required>
		</div>
		<div class="form-group">
			<label for="msg">Message:</label>
			<input type="text" class="form-control" id="msg" placeholder="Message" name="msg" required>
		</div>
		<input type="submit" name="submit" value="Submit" class="btn btn-primary" />
	</form>
</div>

</body>
</html>
<?php //get_footer(); ?>