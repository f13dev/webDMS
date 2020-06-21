<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    header("Location: ../../");
  }

// Create the file object
$restoreDoc = new document(['ID' => $d]);

if ($restoreDoc->setRecycle(false)) {
    // Send header location 
    $uri->redirect($uri->recycleBin());
} else {
    // Show an error 
    echo 'A problem occured while restoring: ' . $restoreDoc->getTitle();
}
