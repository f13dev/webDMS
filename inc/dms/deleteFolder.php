<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

if ($_SESSION['type'] <=2) {
  
// Create the folder object
$delFolder = new folder(['ID'=>$_GET['id']]);

if ($delFolder->getNumberFiles() == 0) {
  // Process the delete
  if ($delFolder->unsetEntry()) {
    // Change header
    $uri->redirect(SITE_URL);
    //header('location: ' . SITE_URL);
  } else {
    echo '<div id="form"><h2>Delete: ' . $delFolder->getTitle() . '</h2><div class="warning notice"><p>There was a database error when attempting to delete this folder.</p></div></div>';
  }
} else {
  // Show an error message 
  echo '<div id="form"><h2>Delete: ' . $delFolder->getTitle() . '</h2><div class="warning notice"><p>This folder cannot be deleted becuase it has documents associated with it.</p></div></div>';
}

} else {
  echo permissionDeny();
}