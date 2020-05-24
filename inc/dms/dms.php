<?php
// Get the page variable
if (isset($_GET['p'])) { $page = $_GET['p']; } else { $page = 'main'; }

// Page layout
?>

<div id="page-top">
  <img src="favicon-32x32.png" id="logo">
  <a href="<?php echo SITE_URL; ?>">webDMS</a> -
  <a href="?p=account">Account details</a> -
  <a href="?p=logout">Logout</a>
  (<?php echo $_SESSION['name']; ?>)<br>
  <!-- will become breadcrumb -->
  webDMS >> Bank statements >> July 2018
</div>

<div id="page-middle">
  <div id="page-middle-left">
  </div>
  <div id="page-middle-right">
    <div id="page-middle-right-top">
    </div>
    <div id="page-middle-right-bottom">
    </div>
  </div>
</div>

<div id="page-bottom">

</div>


<?php
if ($page == 'logout') {
  require_once('inc/dms/logout.php');
}
