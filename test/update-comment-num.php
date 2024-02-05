<?php
include("../include/config.php");
include("../include/crypt.php");

$get_post_sql = "SELECT * FROM ahke_category.question";
$get_post = $conn->query($get_post_sql);
while($post = $get_post->fetch()){
   $id = $post["id"];
   $get_comment_num_sql = "SELECT COUNT(id) as comment_num FROM ahke_comment.question_comment WHERE item_id = {$id} ";
   $get_comment_num = $conn->query($get_comment_num_sql);
   $get_comment_num = $get_comment_num->fetch();
   $comment_num = $get_comment_num["comment_num"];

   $update_comment_num_sql = "UPDATE ahke_category.question SET  comment_num = {$comment_num} WHERE id = {$id} ";
   $update_comment_num = $conn->exec($update_comment_num_sql);
}
?>