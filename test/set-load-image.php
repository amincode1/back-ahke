<?php
include("../include/config.php");
function resize($image, $new_name, $width, $height)
{
$width = ($width * 10) / 100;
$height = ($height * 10) / 100;
$tmp_image = imagecreatetruecolor($width, $height);
imagefill($tmp_image, 0, 0, 0xFFFFFF);
$radio = imagesx($image) > imagesy($image) ? imagesx($image) /
$width : imagesy($image) / $height ;
if(imagesx($image) > imagesy($image))
{
$height = imagesy($image) / $radio;
}
elseif(imagesy($image) > imagesx($image))
{
$width = imagesx($image) / $radio;
}
imagecopyresampled($tmp_image, $image, 0, 0, 0, 0, $width,
$height, $width * $radio, $height * $radio);
imagejpeg($tmp_image, $new_name);
imagedestroy($image);
}


$category_table = "ahke_category.images";
$get_category_post_sql = "SELECT * FROM $category_table WHERE image_path != null || image_path != ''";
$get_category_post = $conn->query($get_category_post_sql);
while ($post = $get_category_post->fetch()) {
	$image_path = $post["image_path"];
	$image_path_arr = explode("/", $image_path);
	// $file_name = explode('/', $image_path);
    $file_name = explode('.',$image_path_arr[3]);
	$parent_path = "../media/images/category/";
    
    $image_file = $parent_path.$image_path_arr[0]."/".$image_path_arr[1]."/".trim($image_path_arr[2])."/".trim($file_name[0]).".".$file_name[1];
    $image_file_load = $parent_path.$image_path_arr[0]."/".$image_path_arr[1]."/".trim($image_path_arr[2])."/".trim($file_name[0])."-load.".$file_name[1];
    if($file_name[1] == "jpg" || $file_name[1] == "jpeg"){
        $image = imagecreatefromjpeg($image_file);
    }else{
    	$image = imagecreatefrompng($image_file);
    }
 
    resize($image,$image_file_load,imagesx($image),imagesy($image));
}
?>