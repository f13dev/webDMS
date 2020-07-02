<?php 
// Show all errors for testing, must remove for production
ini_set('display_errors', 1);
ini_set('display_startup_error', 1);
error_reporting(E_ALL);

$email = 'Enter your email here';

require('../inc/class/secure.class.php');
require('../inc/cfg.php');
$security = new secure();

echo $security->make_secure($email);