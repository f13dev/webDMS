<?php
// Get the page variable
if (isset($_GET['p'])) { $page = $_GET['p']; } else { $page = 'main'; }

// Page layout
?>

<div id="page-top">
  <img src="favicon-32x32.png" id="logo">
  <a href="<?php echo SITE_URL; ?>">webDMS</a> -
  <a href="<?php echo page_uri('account'); ?>">Account details</a> -
  <a href="<?php echo page_uri('logout'); ?>">Logout</a>
  (<?php echo $_SESSION['name']; ?>)<br>
  <!-- will become breadcrumb -->
  webDMS >> Bank statements >> July 2018
</div>

<div id="page-middle">
  <div id="page-middle-left">

    <?php
    $folders = [1 => 'Test', 2 => 'Test 2', 3 => 'Another folder'];

    foreach ($folders as $key => $value) {
    //for ($i = 0; $i < 15; $i++) {
      echo '<a href="' . folder_uri($key,$value) . '">'.$value.'</a><br>';
    }
    echo '<a href="' . document_uri(5,'Folder name', 64, 'Document title') . '">Document title</a>';

    ?>


  </div>
  <div id="page-middle-right">
    <div id="page-middle-right-top">
    </div>
    <div id="page-middle-right-bottom">
    </div>
  </div>
</div>

<div id="page-bottom">
  webDMS 0.4 beta &copy; 2020 <a href="https://f13dev.com">James Valentine</a><br>
  1046 documents, using 554.76 MB, remaining 854.31 GB
</div>


<?php
if ($page == 'logout') {
  require_once('inc/dms/logout.php');
}
