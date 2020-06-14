<?php
require('../cfg.php');
$file = '/var/www/html/webdmsTest/documents/'.$_GET['file'];
    $filename = $_GET['file'];

    header('Content-type: '. mime_content_type($file));
    header('Content-Disposition: inline; filename="'.$filename.'"');
    header('Content-Length:'.filesize($file));
    header('Accept-Ranges: bytes');

    @readfile($file);