<?php
require ('../cfg.php');
// Check the user is logged in 
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    exit();
} else {
    $file = SITE_DOCS . $_GET['file'];
    $filename = $_GET['file'];

    header('Content-type: '. mime_content_type($file));
    header('Content-Disposition: inline; filename="'.$filename.'"');
    header('Content-Length:'.filesize($file));
    header('Accept-Ranges: bytes');
    @readfile($file);
}