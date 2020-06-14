<?php
// Check the user is logged in 
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    exit();
} else {
    // Requirements
    require ('../cfg.php');
    require ('../class/file.class.php');
    $file = SITE_DOCS . $_GET['file'];
    $filename = $_GET['file'];

    header('Content-type: '. mime_content_type($file));
    header('Content-Length:'.filesize($file));
    @readfile($file);
}