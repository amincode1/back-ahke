<?php
$base64String = file_get_contents("text/image.text");
// Decode the base64 string
$decodedData = base64_decode($base64String);
// Create a new image resource
$image = imagecreatefromstring($decodedData);
// Save the image to a file
imagejpeg($image, 'image.jpg',100);
imagejpeg($image, 'image-75.jpg',75);
imagejpeg($image, 'image-50.jpg',50);
imagejpeg($image, 'image-25.jpg',25);
// Free up memory
imagedestroy($image);

?>