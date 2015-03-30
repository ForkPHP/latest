 
<?php
require 'PHPMailer/PHPMailerAutoload.php';
/*

This Class is the extension of PhpMailer package.

Example: 

$mail = new PHPMailer;								
 
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.yourhost.in';                       // Specify main and backup server
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'admin@yourhost.in';                   // SMTP username
$mail->Password = '***************';               // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
$mail->Port = 25;                                    //Set the SMTP port number - 587 for authenticated TLS
$mail->setFrom('admin@yourhost.in', 'Admin');     //Set who the message is to be sent from
$mail->addReplyTo('demo@gmail.com', 'First Last');  //Set an alternative reply-to address
$mail->addAddress('eshantsahu@gmail.com', 'Eshant Sahu');  // Add a recipient
$mail->addAddress('ellen@example.com');               // Name is optional
$mail->addCC('cc@example.com');
$mail->addBCC('bcc@example.com');
$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
$mail->addAttachment('/usr/demo/file.doc');         // Add attachments
$mail->addAttachment('/images/image.jpg', 'new.jpg'); // Optional name
$mail->isHTML(true);                                  // Set email format to HTML
 
$mail->Subject = 'Here is the subject';
$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
 
Read an HTML message body from an external file, convert referenced images to embedded,
convert HTML into a basic plain-text alternative body

$mail->msgHTML('contents.html');
 
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
   exit;
}
 
echo 'Message has been sent'; */

class ForkMailer extends PHPMailer{

	function __construct($object=null){

		if(is_array($object)){

			$object = (object)$object;
			// sync properties with ForkMailer Object
			parent::syncForkObject($object);
		}
		
	}

	function loadCredentials($name){
		/*--- load form InI using name ---*/
		$configObj = new appConfig('application');
		$iniData = $configObj->get();

		$iniCredentials =array();
		$iniCredentials['Name'] = $iniData['smtp'][$name.'.name'];
		$iniCredentials['Username'] = $iniData['smtp'][$name.'.email'];
		$iniCredentials['Host'] = $iniData['smtp'][$name.'.host'];
		$iniCredentials['Password'] = $iniData['smtp'][$name.'.password'];
		$iniCredentials['Port'] = $iniData['smtp'][$name.'.port'];
		$iniCredentials = (object)$iniCredentials;
		
		/*--- load form InI ---*/
		 foreach ($iniCredentials as $key => $value) {
            if(isset($this->$key)){
                $this->$key = $value;
            }
        }
        $this->SMTPAuth = true;
        $this->SMTPSecure = 'tls';
        $this->isSMTP();
		parent::setFrom($iniCredentials->Username, $iniCredentials->Name); 
	}
}
/*
* Fork Mailer Example

	$obj = new ForkMailer();
	$obj->loadCredentials("admin");
	$obj->isHTML(true);   
	$obj->addAddress('eshantsahu@gmail.com', 'Eshant Sahu');
	$obj->Body    = 'This is the HTML message body <b>in bold!</b>';
	//$obj->addAttachment('mailer.php', 'mailer.php');

	if(!$obj->send()) {
	   echo 'Message could not be sent.';
	   echo 'Mailer Error: ' . $obj->ErrorInfo;
	   exit;
	}
*/