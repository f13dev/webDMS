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
$theFolder = new folder(['ID' => $f]);
$theDocument = new document(['ID'=>$d]);
$recycleBin = new recycle();

// Page layout
?>

<div id="page-top">
  <img src="<?php echo SITE_URL; ?>favicon-32x32.png" id="logo">
  <a href="<?php echo SITE_URL; ?>">Documents</a> -
  <a href="<?php echo $uri->page('expenditure'); ?>">Money</a> - 
  <a href="<?php echo $uri->page('account'); ?>">Account</a> -
  <?php
  if ($_SESSION['type'] <= PERM_USER_VIEW) {
    echo '<a href="' . $uri->users() . '">Users</a> - ';
  }
  ?>
  <a href="<?php echo $uri->page('logout'); ?>">Logout</a>
  (<?php echo $_SESSION['name']; ?> - <?php echo USER_TYPES[$_SESSION['type']]; ?>)<br>
  <!-- Breadcrumb -->
  <?php
  if (isset($page)) { echo $page . ' >> '; }
  if (isset($_GET['recycleBin'])) { echo 'RecycleBin >> '; }
  if ($theFolder->isSet()) { echo $theFolder->getTitle() . ' >> '; }
  if (isset($searchString)) { echo $searchString . ' >> '; }
  if ($theDocument->isSet()) { echo $theDocument->getTitle() . ' >> '; }
  if (isset($_GET['name'])) { echo $security->sanitise($_GET['name']) . ' >> '; }
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
  "newFolder",
  "restore",
  "recycleFile",
  "users",
  "expenditure"
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
