<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

if ($_SESSION['type'] <= 2) {
// Create a file object 
$delFile = new document(['ID' => $_GET['id']]);

if ($delFile->unsetFile()) {
    if ($delFile->unsetEntry()) {
        // Set a header location 
        $uri->redirect($uri->recycleBin());
        //header('Location:' . $uri->recycleBin());
    } else {
        echo 'A problem occured while removing the database entry for: ' . $delFile->getTitle();
    }
} else {
    echo 'A problem occured while deleting the file: ' . $delFile->getFile();
}
} else {
    echo permissionDeny();
}