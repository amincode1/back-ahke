<?php
include("../../api-setting.php");
include("../../include/config.php");
include("../../include/crypt.php");
include("../../auth/function.php");
$user_table = "ahke_user.user";
$data = json_decode(file_get_contents('php://input'),1);
if(isset($data['unico_id']) && $HTTP_REFERER){
  $unico_id = local_filter_input($data["unico_id"]);
  $username = local_filter_input($data["username"]);
  $name = local_filter_input($data["name"]);
  $email = local_filter_input($data["email"]);
  $gender = local_filter_input($data["gender"]);
  $country = local_filter_input($data["country"]);
  // get user id from unico id
  $get_user_id_sql = "SELECT id FROM {$user_table} WHERE unico_id = '{$unico_id}' ";
  $get_user_id = $conn->query($get_user_id_sql);
  $get_user_id = $get_user_id->fetch();
  $user_id = $get_user_id["id"];

  // check username there is not in database
  $username = slug($username);
  $check_username_sql = "SELECT COUNT(id) AS username_num 
                         FROM {$user_table} 
                         WHERE username = '{$username}' AND 
                               unico_id != '{$unico_id}'  ";
  $check_username = $conn->query($check_username_sql);
  $check_username = $check_username->fetch();

  // check email there is not in database
  $check_email_sql = "SELECT COUNT(id) AS email_num 
                      FROM {$user_table} 
                      WHERE email = '{$email}' AND 
                            unico_id != '{$unico_id}'";
  $check_email = $conn->query($check_email_sql);
  $check_email = $check_email->fetch();

  if($check_username["username_num"] != 0){
    echo json_encode(["status_request" => 0,"mess" => "معرف المستخدم (username) موجود بالفعل"]);
  }elseif($check_email["email_num"] != 0){
    echo json_encode(["status_request" => 0,"mess" => "هذا البريد الالكتروني مسجل بالفعل "]);
  }else{
    $update_user_sql = "UPDATE {$user_table} SET username = '{$username}',name = '{$name}',email = '{$email}',gender = '{$gender}', country = '{$country}' WHERE id = {$user_id} ";
    $update_user = $conn->exec($update_user_sql);
    if(isset($update_user)){
       echo json_encode(["status_request" => 1,"mess" => "تم التعديل"]);
    }
  }
}
?>