<?php
// Get the page variable
if (isset($_GET['p'])) { $page = $_GET['p']; } else { $page = 'main'; }
if (isset($_GET['f'])) { $f = $_GET['f']; } else { $f = -1; }
// Create new folder
$theFolder = new folder($f);

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
        $documentList = $theFolder->getDocuments();
        foreach ($documentList as $eachDocument) {
          $document[$eachDocument['ID']] = new document($eachDocument['ID']);
          // Change this into tabular view
          echo $document[$eachDocument['ID']]->getTitle() . ' - ' .
               $document[$eachDocument['ID']]->getDocDate() . '<br>';
        }
      } else {
        echo '<h2>Please select a folder</h2>';
      }

      ?>
    </div>
    <div id="page-middle-right-bottom">
    </div>
  </div>
</div>

<div id="page-bottom">
  webDMS 0.4 beta &copy; 2020 <a href="https://f13dev.com">James Valentine</a><br>
  <?php echo getDocumentCount(); ?> documents, using <?php echo getDocumentTotalSize(); ?>, remaining <?php echo humanSize(diskfreespace(".")); ?>
</div>


<?php
if ($page == 'logout') {
  require_once('inc/dms/logout.php');
}
