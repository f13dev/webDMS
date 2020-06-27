<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

if (isset($_GET['delete'])) {
    if ($_SESSION['type'] <= PERM_USER_DELETE ) {
        // Delete a user
        echo "Delete user";
        
    } else {
        echo permissionDeny();
    }
}
if (isset($_GET['new'])) {
    if ($_SESSION['type'] <= PERM_USER_CREATE) {
        // Show new user form
        echo "New user form";

    } else {
        echo permissionDeny();
    }

} else if (isset($_GET['id'])) {
    if ($_SESSION['type'] <= PERM_USER_EDIT) {
        // Show user details/update form
        echo "Showing user ID " . $_GET['id'];
    
    } else {
        echo permissionDeny();
    }

} else {
    if ($_SESSION['type'] <= PERM_USER_VIEW) {
        // Show user table 
        $users = new users();
        echo $users->getAllTable();
    } else {
        echo permissionDeny();
    }
}
