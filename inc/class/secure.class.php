<?php
Class secure {
  /**
    * Validate CSRF token
    **/
  function validateCSRF($postToken) {
    global $token;
    $token = isset($_SESSION['csrf']) ? $_SESSION['csrf'] : "";
    if ($token && $postToken === $token) {
      return true;
    } else {
      return false;
    }
  }

  /**
    * Generate a new CSRF token
    **/
  function generateCSRF($regen) {
    if ($regen) {
      unset($_SESSION['csrf']);
    }
    global $token;
    $token = isset($_SESSION['csrf']) ? $_SESSION['csrf'] : "";
    if (!$token) {
      $token = sha1(openssl_random_pseudo_bytes(128));
      $_SESSION['csrf'] = $token;
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

}
