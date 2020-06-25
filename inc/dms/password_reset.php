<?php
// Block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

if (isset($_GET['code'])) {
    $error = false;
    $errormsg = '';
    // process step 2
    $email = explode('-',$_GET['code'])[0];
    $statement = $dbc->prepare("SELECT email, password,user_salt FROM users WHERE email = ?");
    $statement->execute([$email]);
    $user = $statement->fetch();
    if (isset($user['email'])) {
        if ($_GET['code'] == $security->generateResetCode($user['email'],$user['password'],$user['user_salt'])) {
            // Check for post data 
            if (isset($_POST['email'])) {
                // Check for CSRF
                if ($security->validate_token($_POST['token'])) {
                    // Check the email address matches the code
                    if ($security->make_secure($_POST['email']) != $email) {
                        $error = true;
                        $errormsg .= '<p>There was an error with the email address provided.</p>';
                    }
                    // Check the passwords match 
                    if ($_POST['pass1'] != $_POST['pass2']) {
                        $error = true;
                        $errormsg .= '<p>The password and password confirmation do not match.</p>';
                    }
                    // Check the password is a valid format 
                    if (!$validate->password($_POST['pass1'])) {
                        $error = true;
                        $errormsg .= '<p>Please enter a password of 8 or more charachters with uppercase, lowercase, numbers and specail characters.</p>';
                    }
                    // If no errors submit the new password
                    if (!$error) {
                        // Get the user_salt 
                        $statement = $dbc->prepare("SELECT user_salt FROM users WHERE email = ?");
                        $statement->execute([$email]);
                        $user = $statement->fetch();
                        $newPassHash = $security->password_hash($_POST['pass1'],$user['user_salt']);
                        $statement = $dbc->prepare("UPDATE users SET password = ? WHERE email = ?");
                        if ($statement->execute([$newPassHash,$email])) {
                            // redirect to the home page
                            $uri->redirect($uri->resetComplete());
                        } else {
                            $error = true;
                            $errormsg .= '<p>There was a database error.</p>';
                        }
                    }
                }
            }
            // Show the password reset form
            ?>
            <div id="form">
                <form method="POST">
                    <h2 class="text-center">Password reset</h2>
                    <label for="email" class="text-info">Confirm email address</label><br>
                    <input type="email" name="email" class="form-control"><br>
                    <label for="pass1" class="text-info">Password</label><br>
                    <input type="password" name="pass1" class="form-control"><br>
                    <label for="pass2" class="text-info">Confirm password</label><br>
                    <input type="password" name="pass2" class="form-control"><br>
                    <?php echo $security->generate_token(); ?>
                    <input type="submit" name="submit" class="btn btn-info btn-md" value="Submit">
                </form>
                <?php if (isset($error) && $error == true) { ?>
                    <div class="notice warning">
                        <?php echo $errormsg; ?>
                    </div>
                <?php } ?>
            </div>
            <?php 
        } else {
            $code_error = true;
        }
    } else {
        $code_error = true;
    } 

    if (isset($code_error) && $code_error == true) {
        ?>
        <div id="form">
            <h2 class="text-center">Error</h2>
            <p>There was an error with the password reset code, it may have expired or already been used.</p>
            <p>Please fill in the <a href="<?php echo $uri->reset(); ?>">password reset form</a> again.</p>
        </div>
        <?php
    }

} else {
    // process step 1
    if (isset($_POST['email'])) {
        // Check for CSRF
        if ($security->validate_token($_POST['token'])) {
            $submit = true;
            $email_address = $security->make_secure($_POST['email']);
            // Get the database row 
            $statement = $dbc->prepare("SELECT email, password, user_salt FROM users WHERE email = ?");
            $statement->execute([$email_address]);
            $user = $statement->fetch();
            if (isset($user['email'])) {
                // A user was found, send the code
                $code = $security->generateResetCode($user['email'],$user['password'],$user['user_salt']);
                $url = $uri->resetCode($code);
                $email = new email();
                $email->setTo($security->revert_secure($user['email']));
                $email->setSubject('webDMS password reset');
                $message = "A password reset has been requested \r\n";
                $message .= "To reset your password, visit: " . $url . "\r\n";
                $email->setbody($message);
                $email->send();
            }
        }
    }
    ?>
    <div id="form">
        <form method="POST">
            <h2 class="text-center">Password reset</h2>
            <label for="email" class="text-info">Email address</label><br>
            <input type="email" name="email" class="form-control"><br>
            <?php echo $security->generate_token(); ?>
            <input type="submit" name="submit" class="btn btn-info btn-md" value="Submit">
        </form>
        <?php if (isset($submit) && $submit == true) { ?>
            <div class="notice warning">
                <p>Your request has been received, if the email address is found in our database a code will be emailed to you.</p>
            </div>
        <?php } ?>
    <?php 
}