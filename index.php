<?php
// Start output buffer 
ob_start();
// Show all errors for testing, must remove for production
ini_set('display_errors', 1);
ini_set('display_startup_error', 1);
error_reporting(E_ALL);

// Include the settings
require_once('inc/cfg.php');

// Initiate the Uri class before loading other classes
require('inc/class/uri.class.first.php');
$uri = new Uri();

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
// Initiate validate
$validate = new Validate();

// Session data
session_start();

//$security->session_token();
// Session security
if (!isset($_SESSION['initiated'])) {
  session_regenerate_id();
  $_SESSION['initiated'] = true;
}

$security->checkFingerprint();

// Load the page head
require_once('inc/theme/head.php');
// Assume no user
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
  $security->checkUserToken();
  require_once('inc/dms/dms.php');
} else if (isset($_GET['reset'])) {
  require_once('inc/dms/password_reset.php');
} else {
  require_once('inc/dms/login.php');
}
// Load teh page foot
require_once('inc/theme/foot.php');

// Flush the output buffer 
ob_end_flush();
