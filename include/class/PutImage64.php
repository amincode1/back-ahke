<?php
class PutImage64{
  public $image_path;

	public function __construct($image_base_64,$input_path){
		// conver image base64 to image and put in path
    $decodedData = base64_decode($image_base_64);
	  // Create a new image resource
	  $image = imagecreatefromstring($decodedData);
		
		// check file in folder
		if($image){
			if(!is_dir($input_path.date("Y"))){
		    mkdir($input_path.date("Y"));
			}
	    if(!is_dir($input_path.date("Y")."/".date("M"))){
	      mkdir($input_path.date("Y")."/".date("M"));
			}
			if(!is_dir($input_path.date("Y")."/".date("M")."/".date("d"))){
	      mkdir($input_path.date("Y")."/".date("M")."/".date("d"));
			}
			// Save the image to a file
		  $image_name = $this->random_name(20);
		  imagejpeg($image, $input_path.date("Y")."/".date("M")."/".date("d")."/{$image_name}.jpg",75);
		  // create small image
		  imagejpeg($image, $input_path.date("Y")."/".date("M")."/".date("d")."/{$image_name}-mini.jpg",30);
      // create very small image
			$this->resize(imagecreatefromjpeg($input_path.date("Y")."/".date("M")."/".date("d")."/{$image_name}.jpg"),$input_path.date("Y")."/".date("M")."/".date("d")."/{$image_name}-load.jpg",imagesx($image),imagesy($image));
			// put image path in public var
			$this->image_path = date("Y")."/".date("M")."/".date("d")."/{$image_name}.jpg";
			// Free up memory
			imagedestroy($image);
		}
	}

  // resize image
	public function resize($image, $new_name, $width, $height){
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
    
  // random name to image
	public function random_name($length) { 
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_'; 
    $randomString = ''; 
    for ($i = 0; $i < $length; $i++) { 
      $index = rand(0, strlen($characters) - 1); 
      $randomString .= $characters[$index]; 
    } 
    return $randomString; 
  }

  public function getPath(){
    return $this->image_path;
  }
}
?>