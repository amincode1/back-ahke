<?php
include("../../../api-setting.php");
include("../../../include/config.php");
include("../../../include/crypt.php");

require '../../../pusher/autoload.php';
include("../../../PHPMailer/mail_conn.php");

$data = json_decode(file_get_contents("php://input"),1);
$user_table = "ahke_user.user";
$user_setting = "ahke_user.user_setting";

if(isset($data["user_id"])){
   $user_id = local_filter_input($data["user_id"]);
   $advise_text = local_filter_input($data["advise_text"]);
   // get user id from unico id
   $get_user_email_sql = "SELECT email FROM {$user_table} WHERE id = '{$user_id}' ";
   $get_user_email = $conn->query($get_user_email_sql);
   $get_user_email = $get_user_email->fetch();
   $user_email = $get_user_email["email"];
   
   // real time 
   $options = array(
      'cluster' => 'eu',
      'useTLS' => true
   );
   $pusher = new Pusher\Pusher(
      'e56518da4ce474a2a70a',
      '949d27292752ae2363bd',
      '1563466',
      $options
   );
   $data['mess'] = $advise_text;
   $pusher->trigger('advise-me', "$user_id", $data);

    //sent email
    $mail->setFrom('info@ahke.net', 'ahke.net');
    $mail->addAddress($user_email, '');
    $mail->isHTML(true); //Set email format to HTML
    $mail->Subject = 'تم إضافة نصيحة جديدة الي صندوق نصائحك';
    $mail->Body    = "<div style='text-align: right;'> {$advise_text} </div>";
    $mail->AltBody = "{$advise_text}";
    $sent = $mail->send();
}
?>