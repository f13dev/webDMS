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
    if (isset($_GET['ID']) && isset($_GET['download'])) {
        $doc = new document($_GET['ID']);
        header('Content-Disposition: attachment; filename="'.$doc->getPsuedoName().'"');
    } else {
        header('Content-Disposition: inline; filename="'.$filename.'"');
    }
    header('Content-Length:'.filesize($file));
    header('Accept-Ranges: bytes');
    @readfile($file);
}