<?php
if (isset($_POST['uri'])) { $referrer = $_POST['uri']; } else { $referrer = SITE_URL; }
session_destroy();
echo $referrer;
$uri->redirect($referrer);
