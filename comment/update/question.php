<?php
// include("api-setting.php");
// include("include/config.php");
// include("include/crypt.php");
// $data = json_decode(file_get_contents('php://input'),1);
// $category_db_name = 'ahke_comment.question_comment';
// if(isset($data["item_id"])){
//     $item_id = local_filter_input($data["item_id"]);
//     $type = local_filter_input($data["type"]);
//     $more_num = local_filter_input($data["more_num"]);
//     $get_comment_sql = "SELECT * FROM {$category_db_name} WHERE item_id = {$item_id} AND type = {$type} LIMIT {$more_num},10 ";
//     $get_comment = $conn->query($get_comment_sql);
//     $get_comment = $get_comment->fetchAll(PDO::FETCH_ASSOC);
//     echo json_encode($get_comment);
// }
?>