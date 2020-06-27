<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

if ($_SESSION['type'] <= 1 ) {

if (isset($_GET['delete'])) {
    if ($_SESSION['type'] <= 0 ) {
        // Delete a user
        echo "Delete user";
        
    } else {
        echo permissionDeny();
    }
}
if (isset($_GET['new'])) {
    // Show new user form
    echo "New user form";

} else if (isset($_GET['id'])) {
    // Show user details/update form
    echo "Showing user ID " . $_GET['id'];

} else {
    // Show user table 
    $users = new users();
    echo $users->getAllTable();
}
} else {
    echo permissionDeny();
}