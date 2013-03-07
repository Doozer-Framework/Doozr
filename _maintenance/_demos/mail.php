<?php

// include DoozR core
require_once 'Controller/Core/Core.php';

// instanciate DoozR core
$DoozR = DoozR_Core::getInstance('');


// get new instance of ffmpeg class
$mail = $DoozR->getModuleHandle('mailer');

$mail->IsSMTP();  // telling the class to use SMTP
$mail->Host     = "192.168.1.5"; // SMTP server

$mail->From     = "opensource@clickalicious.de";
$mail->AddAddress("technik@upside.de");

$body = '<b>HTML rocks!!!</b>';

$mail->Subject  = "DoozR - Email unittest";
$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
$mail->MsgHTML($body);

$mail->WordWrap = 50;

if(!$mail->Send()) {
  echo 'Message was not sent.';
  echo 'Mailer error: ' . $mail->ErrorInfo;
} else {
  echo 'Message has been sent.';
}

?>