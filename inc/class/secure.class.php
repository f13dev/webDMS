<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}
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
      echo 'There was a session token error.';
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
        header('Location: ' . SITE_URL);
        exit;
      }
    } else {
      $_SESSION['fingerprint'] = sha1($_SERVER['HTTP_USER_AGENT'] . SALT);
    }
  }

  /**
    * Sanitise inputs
    **/
  function sanitise($data) {
    // code to sanitise inputs, remove quotes etc...
    return htmlspecialchars($data);
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

  function generateSessionSalt() {
    return bin2hex(random_bytes(32));
  }

  function generateUserToken() {
    return hash('sha256',$_SESSION['email'] . $_SESSION['salt'] . SALT);
  }

  function checkUserToken() {
    if (hash_equals($this->generateUserToken(), $_SESSION['usertoken'])) {
      return true;
    } else {
      echo '<h1>Error</h1><h2>There has been a session error</h2>';
      session_destroy();
      die();
    }
  }

  /**
    * JS Session timeout
    **/
  function t800() {
    global $uri;
  ?>
    <div id="timer"></div>
    <form action="<?php echo $uri->page('logout'); ?>" method="POST" id="t800" style="display:none;">
      <input type="hidden" name="uri" value="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>" >
    </form>
    <script>
      // Add a countdown to destroy the session if no activity
      var sec         = <?php echo SESSION_TIME; ?>,
          countDiv    = document.getElementById("timer"),
          secpass,
          countDown   = setInterval(function () {
            'use strict';
            secpass();
      }, 1000);

      function secpass() {
        'use strict';
        var min     = Math.floor(sec / 60),
            remSec  = sec % 60;
        if (remSec < 10) {
            remSec = '0' + remSec;
        }
        if (min < 10) {
            min = '0' + min;
        }
        countDiv.innerHTML = "Session ends in: " + min + ":" + remSec;
        if (sec > 0) {
            sec = sec - 1;
        } else {
            clearInterval(countDown);
            document.getElementById("t800").submit();
        }
    }
    </script>
    <?php
  }

}
