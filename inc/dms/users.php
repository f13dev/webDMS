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
        // Check for post data
        if (isset($_POST['submit'])) {
            // Validate form token
            if ($security->validate_token($_POST['token'])) {
                $first_name = $security->sanitise($_POST['first_name']);
                $last_name = $security->sanitise($_POST['last_name']);
                $emailaddress = $security->sanitise($_POST['email']);
                $user_type = $security->sanitise($_POST['type']);
                // Set empty error
                $error = false;
                $errormsg = '';
                // Validate fields 
                if (!$validate->length($first_name,1)) {
                    $error = true;
                    $errormsg .= '<p>Please enter a valid first name or initial.</p>';
                }
                if (!$validate->length($last_name,2)) {
                    $error = true;
                    $errormsg .= '<p>Please enter a last name (2 or more charachters).</p>';
                }
                if (!$validate->email($emailaddress)) {
                    $error = true;
                    $errormsg .= '<p>Please enter a valid email address.</p>';
                }
                if (!$validate->emailUnique($emailaddress)) {
                    $error = true;
                    $errormsg .= '<p>This email address already already exists in the database.</p>';
                }
                if (!$validate->userType($user_type)) {
                    $error = true;
                    $errormsg .= '<p>Please select a valid user type.</p>';
                }
                if ($error == false) {
                    // If no errors, process the new user 
                    $password = $security->generateSessionSalt();
                    $user_salt = $security->generateSessionSalt();
                    $statement = $dbc->prepare("INSERT INTO users (first_name,last_name,email,password,user_salt,type) values(?,?,?,?,?,?)");
                    if (!$statement->execute([
                        $first_name,
                        $last_name,
                        $security->make_secure($emailaddress),
                        $password,
                        $user_salt,
                        $user_type,                    
                    ])) {
                        $error = true;
                        $errormsg .= '<p>There was a database error.</p>';
                    } else {
                        // Send an activation email 
                        $code = $security->generateResetCode($security->make_secure($emailaddress),$password,$user_salt);
                        $url = $uri->resetCode($code);
                        $email = new email();
                        $email->setTo($emailaddress);
                        $email->setSubject('New account created on webDMS');
                        $message  = 'An account has been created for you on webDMS, at ' . SITE_URL . "\r\n\r\n";
                        $message .= "In order to activate your account and set a password, visit: " . $url . "\r\n\r\n";
                        $message .= "This code is valid until midnight on the day it was sent, if you wish to activate your account after this time you will need to use the ";
                        $message .= "Password Reset Form: " . $uri->reset() . ".";
                        $email->setBody($message);
                        $email->send();
                        // Unset the variables and show a notice
                        echo '<div class="notice notification"><p>The user account was successfully created. An activation email has been sent.</p></div>';
                        unset($first_name);
                        unset($last_name);
                        unset($emailaddress);
                        unset($user_type);
                    }
                }
            }
        }
        // Show new user form
        ?>

        <div id="form">
            <form method="POST">
                <h2 class="text-center">New user</h2>
                <label for="first_name" class="text-info">First name</label><br>
                <input type="text" name="first_name" <?php if (isset($first_name)) echo 'value="' . $first_name . '"'?>><br>
                <label for="last_name" class="text-info">Last name</label><br>
                <input type="text" name="last_name" <?php if (isset($first_name)) echo 'value="' . $last_name . '"'?>><br>
                <label for="email" class="text-info">Email</label><br>
                <input type="email" name="email" <?php if (isset($emailaddress)) echo 'value="' . $emailaddress . '"'?>><br>
                <label for="type" class="text-info">User level</label><br>
                <select name="type">
                    <?php 
                    foreach (USER_TYPES as $id => $type) {
                        if (isset($user_type)) {
                            if ($user_type == $id) {
                                $selected = 'selected';
                            } else {
                                $selected = '';
                            }
                        }
                        echo '<option value="' . $id . '" ' . $selected . '>' . $type . '</option>';
                    }
                    ?>
                </select>
                <?php echo $security->generate_token(); ?>
                <input type="submit" name="submit" class="btn btn-info btn-md" value="Create">
                <?php if (isset($error) && $error == true) {
                    echo '<div class="notice warning">' . $errormsg . '</div>';
                }
                ?>
            </form>
        </div> 
        <?php
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
        echo '<div class="userTable">';
        if ($_SESSION['type'] <= PERM_USER_CREATE) {
            echo '<a href="' . $uri->userCreate() . '" class="href-button">New user</a>';
        }
        echo $users->getAllTable();
        echo '</div>';
    } else {
        echo permissionDeny();
    }
}
