<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}
// Get the page variable
if (isset($_GET['p'])) { $page = $_GET['p']; } else { $page = 'main'; }
if (isset($_GET['f'])) { $f = $_GET['f']; } else { $f = false; }
if (isset($_GET['d'])) { $d = $_GET['d']; } else { $d = false; }
if (isset($_GET['orderBy'])) { $orderBy = $_GET['orderBy']; } else ($orderBy = 'document_date');
if (isset($_GET['asc'])) { $asc = $_GET['asc']; } else { $asc = 'false'; }
if (isset($_GET['title'])) {$title = $_GET['title'];} else {$title = 'false';}
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
  if ($theDocument->isSet()) { echo $theDocument->getTitle() . ' >> '; }
  ?>
</div>

<div id="page-middle">
  <div id="page-middle-left">
    <a href="<?php echo page_uri('newCategory'); ?>">New category +</a><br>
    <a href="<?php echo page_uri('newFolder'); ?>">New folder +</a><hr>
    <?php
    // get the categories
    $statement = $dbc->prepare("SELECT ID, name FROM categories ORDER BY name");
    $statement->execute();
    $categories = $statement->fetchall();
    // Look through categories
    echo '<ul class="list">';
    foreach ($categories as $eachCategory) {
      echo '<li class="category">' . $eachCategory['name'];
      $statement = $dbc->prepare("SELECT ID, title FROM folders WHERE category = ? ORDER BY title");
      $statement->execute([$eachCategory['ID']]);
      $folders = $statement->fetchAll();
      if (sizeof($folders) > 0) {
        echo '<ul>';
        foreach ($folders as $eachFolder) {
          echo '<li class="folder"><a href="' . folder_uri($eachFolder['ID'], $eachFolder['title']) . '">' . $eachFolder['title'] . '</a></li>';
        }
        echo '</ul>';
      }
    }
    echo '</ul>';
    ?>
  </div>
  <div id="page-middle-right">
    <div id="page-middle-right-top">
      <?php

      // file view
      if ($theFolder->isSet()) {
        echo '<h2>' . $theFolder->getTitle() . '</h2>';
        echo $theFolder->buildDocumentTable($orderBy, $asc);
      } else {
        echo '<h2>Please select a folder</h2>';
      }

      ?>
    </div>
    <div id="page-middle-right-bottom">
    <?php
      print_r($_GET);
      if ($theDocument->isSet()) {
        echo '<h2>' . $theDocument->getTitle() . '</h2>';
      }
    ?>
    </div>
  </div>
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
