<?php
include("../api-setting.php");
include("../include/config.php");
include("../include/crypt.php");
include("../PHPMailer/mail_conn.php");
function get_random_text($n) { 
  $characters = '0123456789'; 
  $randomString = ''; 
  for ($i = 0; $i < $n; $i++) { 
      $index = rand(0, strlen($characters) - 1); 
      $randomString .= $characters[$index]; 
  } 
  return $randomString; 
} 
$user_table = "ahke_user.user";
$data = json_decode(file_get_contents('php://input'),1);
if(isset($data["check_email"]) && isset($_SERVER['HTTP_REFERER'])){
   $result = [];
   $email = local_filter_input($data["email"]);
   $get_user_sql = "SELECT * FROM {$user_table} WHERE email = '{$email}' ";
   $get_user = $conn->query($get_user_sql);
   if($get_user->rowCount()){
	   	$reset_password_code = get_random_text(7);
	    $sent_password_code_sql = "UPDATE {$user_table} SET reset_password_code = {$reset_password_code} WHERE email = '{$email}' ";
	    $sent_password_code = $conn->exec($sent_password_code_sql);
	    // sent email
	    $mail->setFrom('info@ahke.net', 'ahke.net');
		$mail->addAddress($email, ''); //Add a recipient

		//Content
		$mail->isHTML(true); //Set email format to HTML
		$mail->Subject = 'أستعادة كلمة المرور';
		$mail->Body    = "<div style='text-align: right;'> عزيزي المستخدم كود التحقق هو <b>{$reset_password_code}</b> </div>";
		$mail->AltBody = "عزيزي المستخدم كود التحقق هو {$reset_password_code}";

		$sent = $mail->send();
		if(isset($sent)){
		   array_push($result,["mess" => 1]);
		}else{
		   array_push($result,["mess" => "حدث خطأ اثناء اريال الكود الي بريدك الالكتروني الرجاء المحاولة مرة اخرى"]);
		}

   }else{
      array_push($result,["mess" => "لم يتم العثور علي البريد الالكتروني"]);
   }
   echo json_encode($result);
}

if(isset($data["check_password_code"]) && isset($_SERVER['HTTP_REFERER'])){
  $result = [];
  $email = local_filter_input($data["email"]);
  $password_code = local_filter_input($data["password_code"]);
  $check_possword_code_sql = "SELECT * FROM {$user_table} WHERE reset_password_code = '{$password_code}' ";
  $check_possword_code = $conn->query($check_possword_code_sql);
  if($check_possword_code->rowCount()){
     array_push($result,["mess" => 1]);
  }else{
  	 array_push($result,["mess" => 'كود التحقق غير صحيح']);
  }
  echo json_encode($result);
}

if(isset($data["reset_password"]) && isset($_SERVER['HTTP_REFERER'])){
  $result = [];
  $email = local_filter_input($data["email"]);
  $password_code = local_filter_input($data["password_code"]);
  $password = local_filter_input($data["password"]);
  $re_password = local_filter_input($data["re_password"]);
  
  if($password == $re_password){
    $password = local_crypt($password);
	$reset_password_sql = "UPDATE {$user_table} SET password = '{$password}' WHERE email = '{$email}' AND reset_password_code = '{$password_code}' ";
	$reset_password = $conn->exec($reset_password_sql);
	if($reset_password){
	  array_push($result,["mess" => 1]);
	}else{
	  array_push($result,["mess" => 'كود التحقق غير صحيح الرجاء المحاولة مرة اخرى']);
	}  
  }else{
  	array_push($result,["mess" => 'كلمة المرور غير متطابقة']);
  }
  echo json_encode($result);
}
?>