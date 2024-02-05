<?php
ini_set('display_errors', 1);
$reset_password_code = '6384753';
include("mail_conn.php");
//Recipients
$mail->setFrom('info@ahke.net', 'ahke.net');
$mail->addAddress('alaminawd1@gmail.com', '');     //Add a recipient

//Content
$mail->isHTML(true);                                  //Set email format to HTML
$mail->Subject = 'reset password code';
$mail->Body    = "<div style='text-align: right;'> عزيزي المستخدم كود التحقق هو <b>{$reset_password_code}</b> </div>";
$mail->AltBody = "عزيزي المستخدم كود التحقق هو {$reset_password_code}";

$sent = $mail->send();
if(isset($sent)){
   echo $sent;
}
?>