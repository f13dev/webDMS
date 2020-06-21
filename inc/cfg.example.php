<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../");
}

/**
  * webDMS configuration
  * This file will be auto generated at the time of installation,
  * this template file can be used to manually create the cfg.
  **/

// Database details
define('DB_NAME', 'testing');
define('DB_USER', 'testing');
define('DB_PASS', 'testing');
define('DB_HOST', 'localhost');

// Create a connection
try {
  $dbc = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS);
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
  die;
}

// Structure
define('SITE_URL', 'http://nightfury/webdmsTest/');
define('SITE_ROOT', '/var/www/html/webdmsTest/');
define('SITE_DOCS', '/var/www/docs/');
define('EMAIL_FROM', 'you@domain.com');

// Is LibreOffice installed on the server?
// This is used for converting doc/xls to pdf
define('OFFICE_APP', 'true');

// Security settings
define('SESSION_UNIQUE_ID', 'Random generated ID');
define('SALT', 'Random generated salt');
define('KEY', 'Random generated key');
define('IV', 'Random generated IV');
define('SESSION_TIME', 900);

// Formatting
define('DATE_FORMAT', 'd-M-Y');

// Rewrite URI's
define('REWRITE', true);

// Supported filetypes
define('FILE_TYPES',[
  'pdf' => 'pdf',
  'office' => [
    'doc', 'docx', 'odf','xls','xlsx','ods'
  ],
  'image' => [
    'jpg','jpeg','png','gif'
  ],
  'audio' => [
    'mp3','wav','ogg'
  ]
]
);