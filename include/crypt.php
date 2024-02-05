<?php
function local_crypt( $string, $action = 'e' ) {
  $secret_key = 'ahke04167426131';
  $secret_iv = 'ahke0924765228';
  
  $output = false;
  $encrypt_method = "aes-256-ctr";
  $key = hash( 'sha256', $secret_key , true );
  $iv = substr( hash( 'ripemd160', $secret_iv , true ), 0, 16 );
        
  if( $action == 'e' ) {
    $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    $output = str_replace('=', '', $output);
  }
  else if( $action == 'd' ){
    $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
  }
  return $output;
  $output = null;
}


function local_filter_post($var){
    $var = strip_tags($var, "<br>");
    $var=preg_replace("/'/i", ' 0s0 ', $var);
    $var=preg_replace('/"/i', ' 0d0 ', $var);
    $var=preg_replace("/;/i", '', $var);
    $var=preg_replace("/select/i", '', $var);
    $var=preg_replace("/select/i", '', $var);
    $var=preg_replace("/select/i", '', $var);
    $var=preg_replace("/update/i", '', $var);
    $var=preg_replace("/update/i", '', $var);
    $var=preg_replace("/update/i", '', $var);
    $var=preg_replace("/delete/i", '', $var);
    $var=preg_replace("/delete/i", '', $var);
    $var=preg_replace("/delete/i", '', $var);
    $var=preg_replace("/union/i", '', $var);
    $var=preg_replace("/union/i", '', $var);
    $var=preg_replace("/union/i", '', $var);
    return $var;
    $var = null;
}
function local_filter_input($var){
    $var = strip_tags($var, "");
    $var=preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $var);
    $var=preg_replace("/'/i", ' 0s0 ', $var);
    $var=preg_replace('/"/i', ' 0d0 ', $var);
    $var=preg_replace("/;/i", '', $var);
    $var=preg_replace("/%/i", '', $var);
    $var=preg_replace("/select/i", '', $var);
    $var=preg_replace("/select/i", '', $var);
    $var=preg_replace("/select/i", '', $var);
    $var=preg_replace("/update/i", '', $var);
    $var=preg_replace("/update/i", '', $var);
    $var=preg_replace("/delete/i", '', $var);
    $var=preg_replace("/delete/i", '', $var);
    $var=preg_replace("/delete/i", '', $var);
    $var=preg_replace("/union/i", '', $var);
    $var=preg_replace("/union/i", '', $var);
    $var=preg_replace("/union/i", '', $var);
    $var=preg_replace("/</i", '&lt;', $var);
    $var=preg_replace("/=/i", '', $var);
    $var=preg_replace("/>/i", '&gt;', $var);
    return $var;
    $var = null;
}

function em($text){
  $text = preg_replace("/&nbsp/i", '', $text);
  $text = trim($text,"<br>");
  $text = explode(' ',$text);
  $arry_check = [];
  foreach($text as $st){
    if($st != ""){
        array_push($arry_check,1);
    }else{
        array_push($arry_check,0);
    }
  }
  if(in_array(1,$arry_check)){
    return false;
  }else{
    return true;
  }
}




?>