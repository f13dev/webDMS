<?php
require ('inc/cfg.php');
// Start the session 
session_start();

// Session security
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id();
    $_SESSION['initiated'] = true;
}

// Check the fingerprint and user token are valid
require('inc/class/secure.class.php');
$security = new Secure();
$security->checkFingerprint();
$security->checkUserToken();


// Check the user is logged in 
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    echo '<h1>Error</h1><h2>You do not have the required permissions to view this file</h2>';
    exit();
} else {
    
    // Additional requirement
    require ('inc/class/file.class.php');
    $file = SITE_DOCS . $_GET['file'];
    $filename = $_GET['file'];

    header('Content-type: '. mime_content_type($file));
    header('Content-Length:'.filesize($file));
    @readfile($file);
    
}