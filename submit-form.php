<?php

$name=$_POST['name'];
$email=$_POST['email'];
$subject=$_POST['subject'];
$message=$_POST['message'];

if($_POST['phone'] != '') {
  die('Thank you.  Your message has been sent. Boo');
}


//$to='ggolden@sgucandcs.org';
$to='johnathan@missionaldigerati.org';

$headers = 'From: '.$name."\r\n" .
	'Reply-To: '.$email."\r\n" .
	'X-Mailer: PHP/' . phpversion();
$subject = $subject;
$body='A new message from the Annual Fund website:'."\n\n";

$body.='Name: '.$name."\n";
$body.='Email: '.$email."\n";
$body.='Subject: '.$subject."\n";
$body.='Message: '."\n".$message."\n";
	
if(mail($to, $subject, $body, $headers)) {
	 die('Thank you.  Your message has been sent.');
} else {
	die('Sorry, there was an error sending your message.');
}

?>