<?php
Class secure {

  /**
    * Generate form token
    **/
  function generate_token() {
    $token = bin2hex(openssl_random_pseudo_bytes(24));
    $tokenHash = hash('sha256', $token . SALT);
    $_SESSION['csrf_token'] = $token;
    return '<input type="hidden" name="token" value="' . $tokenHash . '">';
  }

  /**
    * Validate form token
    **/
  function validate_token($token) {
    $tokenHash = hash('sha256', $_SESSION['csrf_token'] . SALT);
    if (hash_equals($token, $tokenHash)) {
      return true;
    } else {
      // Hash doesn't match, kill everything!!
      session_destroy();
      die;
    }
  }

  /**
    * Check session fingerprint
    **/
  function checkFingerprint() {
    if (isset($_SESSION['fingerprint'])) {
      if ($_SESSION['fingerprint'] != sha1($_SERVER['HTTP_USER_AGENT'] . SALT)) {
        // A session error has occured
        session_destroy();
        header('Location: ' . URI_LOGIN);
        exit;
      }
    } else {
      $_SESSION['fingerprint'] = sha1($_SERVER['HTTP_USER_AGENT'] . SALT);
    }
  }

  /**
    * Encrypt data
    **/
  function make_secure($data) {
    $theKey = hash('sha256', KEY);
    $theIV = substr(hash('sha256', IV), 0, 16);
    $encrypt_method = 'AES-256-CBC';
    return base64_encode(openssl_encrypt($data, $encrypt_method, $theKey, 0, $theIV));
  }


  /**
    * Decrypt data
    **/
  function revert_secure($data) {
    $theKey = hash('sha256', KEY);
    $theIV = substr(hash('sha256', IV), 0, 16);
    $encrypt_method = 'AES-256-CBC';
    return openssl_decrypt(base64_decode($data), $encrypt_method, $theKey, 0, $theIV);
  }

  /**
    * Hash a password
    **/
  function password_hash($password, $salt) {
    return hash('sha256',$password . $salt . SALT);
  }

}
