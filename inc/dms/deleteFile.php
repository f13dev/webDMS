<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

$theFolder = new folder(['ID'=>$_GET['id']]);
if ($theFolder->getNumberFiles() == 0) {
  if (!isset($_GET['confirm'])) {
    // Show confirmation message
  } else {
    // Delete the folder
  }
} else {
  // Alert the user that the folder must be empty
}