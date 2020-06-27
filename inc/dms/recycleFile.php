<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

if ($_SESSION['type'] <= PERM_DOC_DELETE) {

// Create a file object 
$delFile = new document(['ID' => $_GET['d']]);

if ($delFile->setRecycle(true)) {
  // Send header location
  $uri->redirect($uri->folder($delFile->getFolder(), $delFile->getFolderTitle()));
} else {
  // Show an error
  echo 'A problem occured while deleting: ' . $delFile->getTitle();
}
} else {
  echo permissionDeny();
}