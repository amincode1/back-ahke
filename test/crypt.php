<?php
function encrypt($message, $password) {
  // Convert the message and password to binary strings
  $messageBuffer = pack('H*', bin2hex(mb_convert_encoding($message, 'UTF-32BE', 'UTF-8')));
  $passwordBuffer = pack('H*', bin2hex(mb_convert_encoding($password, 'UTF-32BE', 'UTF-8')));

  // Derive a key from the password using PBKDF2
  $salt = ""; // Use an empty salt
  $iterations = 10000;
  $keyLength = 32; // 256 bits
  $hash = "sha256";
  $keyBuffer = hash_pbkdf2($hash, $passwordBuffer, $salt, $iterations, $keyLength, true);

  // Generate a random IV
  $iv = random_bytes(12);

  // Encrypt the message using AES-GCM
  $encrypted = openssl_encrypt($messageBuffer, 'aes-256-gcm', $keyBuffer, OPENSSL_RAW_DATA, $iv);

  // Concatenate the IV and the encrypted message and encode them in base64
  $result = base64_encode($iv . $encrypted);

  return $result;
}

echo encrypt("amin","123");
?>