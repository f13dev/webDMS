<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}
// Get the page variable
if (isset($_GET['p'])) { $page = $security->sanitise($_GET['p']); } else { $page = 'main'; }
if (isset($_GET['f'])) { $f = $security->sanitise($_GET['f']); } else { $f = false; }
if (isset($_GET['d'])) { $d = $security->sanitise($_GET['d']); } else { $d = false; }
if (isset($_GET['orderBy'])) { $orderBy = $security->sanitise($_GET['orderBy']); } else ($orderBy = 'document_date');
if (isset($_GET['asc'])) { $asc = $security->sanitise($_GET['asc']); } else { $asc = 'false'; }
if (isset($_GET['title'])) {$title = $security->sanitise($_GET['title']);} else {$title = 'false';}
if (isset($_GET['searchString'])) { $searchString = $security->sanitise($_GET['searchString']); }
// Create new folder
$theFolder = new folder($f);
$theDocument = new document($d);

// Page layout
?>

<div id="page-top">
  <img src="<?php echo SITE_URL; ?>favicon-32x32.png" id="logo">
  <a href="<?php echo SITE_URL; ?>">webDMS</a> -
  <a href="<?php echo page_uri('account'); ?>">Account details</a> -
  <a href="<?php echo page_uri('logout'); ?>">Logout</a>
  (<?php echo $_SESSION['name']; ?>)<br>
  <!-- Breadcrumb -->
  <?php
  if (isset($page)) { echo $page . ' >> '; }
  if ($theFolder->isSet()) { echo $theFolder->getTitle() . ' >> '; }
  if (isset($searchString)) { echo $searchString . ' >> '; }
  if ($theDocument->isSet()) { echo $theDocument->getTitle() . ' >> '; }
  ?>
</div>

<div id="page-middle">
<?php

$pages = array(
  "account", 
  "deleteCategory", 
  "deleteFile", 
  "deleteFolder", 
  "downloadFile", 
  "editFile", 
  "editFolder", 
  "getFile", 
  "newCategory", 
  "newFile", 
  "newFolder"
);
if (in_array($page, $pages)) {
    require('inc/dms/' . $page . '.php');
} else {
  require('inc/dms/browser.php');
}
?>
</div>

<div id="page-bottom">
  webDMS 0.4 beta &copy; 2020 <a href="https://f13dev.com">James Valentine</a><br>
  <?php echo  getDocumentCount(); ?> documents, using <?php echo getDocumentTotalSize(); ?>, remaining <?php echo humanSize(diskfreespace(".")); ?>
</div>


<?php
if ($page == 'logout') {
  require_once('inc/dms/logout.php');
}

// Incude the T800 Terminator
$security->t800();
?>
