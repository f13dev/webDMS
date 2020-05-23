<?php
// Show all errors for testing, must remove for production
ini_set('display_errors', 1);
ini_set('display_startup_error', 1);
error_reporting(E_ALL);

// Include the settings
require_once('inc/cfg.php');
require_once('inc/uri.list.php');

// Include the classes
foreach (glob("inc/class/*.class.php") as $class) {
  require $class;
}

// Include functions
foreach (glob("inc/function/*.func.php") as $func) {
  require $func;
}

// Initiate secure
$security = new Secure();

// Session data
session_start();
$security->generateCSRF(false);
// Session security
if (!isset($_SESSION['initiated'])) {
  session_regenerate_id();
  $_SESSION['initiated'] = true;
}
if (isset($_SESSION['fingerprint'])) {
  if ($_SESSION['fingerprrint'] != sha1($_SERVER['HTTP_USER_AGENT'] . SALT)) {
    // A session error has occured
    session_destroy();
    header('Location: ' . URI_LOGIN);
    exit;
  }
} else {
  $_SESSION['fingerprint'] = sha1($_SERVER['HTTP_USER_AGENT'] . SALT);
}
echo $_SESSION['fingerprint'];
