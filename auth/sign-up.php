<?php
include("../api-setting.php");
include("../include/config.php");
include("../include/crypt.php");
include("function.php");
$user_table = "ahke_user.user";
$user_notification_table = "ahke_user.user_notification";
$data = json_decode(file_get_contents('php://input'),1);
if(isset($data["username"]) && isset($_SERVER['HTTP_REFERER'])){
    $name = local_filter_input($data["name"]);
    $username = local_filter_input($data["username"]);
    $email = local_filter_input($data["email"]);
    $password = local_filter_input($data["password"]);
    $re_password = local_filter_input($data["re_password"]);
    $country = local_filter_input($data["country"]);
    $gender = local_filter_input($data["gender"]);
    
    if(em($name)){
        echo json_encode(["mess" => "لم يتم ادخال الاسم"]);
    }else if(em($username)){
        echo json_encode(["mess" => "لم يتم ادخال معرف المستخدم (username)"]);
    }else if(!is_english($username)){
        echo json_encode(["mess" => "الرجاء ادخال معرف المستخدم (username) بالاحرف الاجنبية فقط"]);
    }else if(em($email)){
        echo json_encode(["mess" => "لم يتم ادخال البريد الالكتروني"]);
    }else if(em($password)){
        echo json_encode(["mess" => "لم يتم ادخال كلمة المرور"]);
    }else if(em($re_password)){
        echo json_encode(["mess" => "الرجاء تاكيد كلمة المرور"]);
    }else if($password != $re_password){
        echo json_encode(["mess" => "كلمة المرور غير متطابقة"]);
    }else if($gender == "الجنس"){
        echo json_encode(["mess" => "لم يتم تحديد الجنس"]);
    }else if($country == "الدولة"){
        echo json_encode(["mess" => "لم يتم تحديد الدولة"]);
    }else{

    	// check username there is not in database
    	$username = slug($username);
    	$check_username_sql = "SELECT COUNT(id) AS username_num FROM {$user_table} WHERE username = '{$username}' ";
        $check_username = $conn->query($check_username_sql);
        $check_username = $check_username->fetch();

        // check email there is not in database
        $check_email_sql = "SELECT COUNT(id) AS email_num FROM {$user_table} WHERE email = '{$email}' ";
        $check_email = $conn->query($check_email_sql);
        $check_email = $check_email->fetch();

        if($check_username["username_num"] != 0){
            echo json_encode(["mess" => "معرف المستخدم (username) موجود بالفعل"]);
        }else if($check_email["email_num"] != 0){
            echo json_encode(["mess" => "هذا البريد الالكتروني مسجل بالفعل "]);
        }else{
            $password = local_crypt($password);
	    	$unico_id = get_random_text(40);
            $profile_image = "images/local-profile-image/default-profile.jpg";
	    	$add_user_sql = "INSERT INTO {$user_table} (name,username,email,password,country,gender,profile_image,unico_id)
	    	                 VALUES ('{$name}','{$username}','{$email}','{$password}','{$country}','{$gender}','{$profile_image}','{$unico_id}') ";
	    	$add_user = $conn->query($add_user_sql);
	    	if(isset($add_user)){
	            echo json_encode(["mess" => "تم التسجيل مرحباً بك"]);
                // set hello notification
                $get_user_id_sql = "SELECT id FROM {$user_table} WHERE username = '{$username}' ";
                $get_user_id = $conn->query($get_user_id_sql);
                $get_user_id = $get_user_id->fetch();
                $get_user_id = $get_user_id["id"];
                $insert_notification_sql = "INSERT INTO {$user_notification_table} (from_user_id,to_user_id,notification_type)
                                            VALUES (1,$get_user_id,1) ";
                $insert_notification = $conn->exec($insert_notification_sql);
	    	}else{
	    		echo json_encode(["mess" => "لم يت التسجيل حاول مرة اخرى"]);
	    	}
        }
    }
}
?>