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
$security->checkFingerprint();
echo $_SESSION['fingerprint'];

// Assume no user
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
  require_once('inc/dms/dms.php');
} else {
  require_once('inc/dms/login.php');
}
