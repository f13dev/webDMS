<?php
// Include the settings
require_once('../cfg.php');

// Include the classes
foreach (glob("../class/*.class.php") as $class) {
  require $class;
}

// Include functions
foreach (glob("../function/*.func.php") as $func) {
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

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    exit();
} else {
    $file = '/var/www/html/webdmsTest/documents/'.$_GET['file'];
    $filename = $_GET['file'];

    header('Content-type: '. mime_content_type($file));
    header('Content-Disposition: inline; filename="'.$filename.'"');
    header('Content-Length:'.filesize($file));
    header('Accept-Ranges: bytes');
    @readfile($file);
}