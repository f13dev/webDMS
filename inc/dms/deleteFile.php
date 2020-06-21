<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

// Create a file object 
$delFile = new document(['ID' => $_GET['id']]);

if ($delFile->setRecycle(true)) {
  // Send header location
  header("location:" . $uri->folder($delFile->getFolder(), $delFile->getFolderTitle()));
} else {
  // Show an error
  echo 'A problem occured while deleting: ' . $delFile->getTitle();
}