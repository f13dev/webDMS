<?php
// Get the page variable
if (isset($_GET['p'])) { $page = $_GET['p']; } else { $page = 'main'; }

if ($page == 'logout') {
  require_once('inc/dms/logout.php');
}
