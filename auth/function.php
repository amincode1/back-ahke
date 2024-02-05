<?php
function is_english($str){
    if (strlen($str) != strlen(utf8_decode($str))) {
        return false;
    } else {
        return true;
    }
}

function slug($text, string $divider = '_'){
  // replace non letter or digits by divider
  $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, $divider);

  // remove duplicate divider
  $text = preg_replace('~-+~', $divider, $text);

  // lowercase
  $text = strtolower($text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
}

function get_random_text($n) { 
     $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_'; 
     $randomString = ''; 
   
     for ($i = 0; $i < $n; $i++) { 
         $index = rand(0, strlen($characters) - 1); 
         $randomString .= $characters[$index]; 
     } 
   
     return $randomString; 
}
?>