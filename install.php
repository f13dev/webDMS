<?php
// Show all errors for testing, must remove for production
//ini_set('display_errors', 1);
//ini_set('display_startup_error', 1);
//error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="inc/theme/style.css">
</head>
<body>


    <?php
    require('inc/class/secure.class.php');
    require('inc/class/validate.class.php');
    $security = new secure();
    $validate = new Validate();

    // Get the step
    if (isset($_GET['step'])) { $step = $_GET['step']; } else { $step = '0'; }

    // Create basic template
    echo '<div id="form">';
    echo '<h2 class="text-center">webDMS Installation</h2>';
    if ($step == '0') {
        if (isset($_POST['submit'])) {
            header('location: ?step=1');
        }
        echo '<form method="POST">';
            echo '<h2 class="text-center">Pre-install checks</h2>';
            echo '<label for="php_version" class="text-info">PHP Version</label><br>';
            echo '<input type="text" class="form-control" name="php_version" value="7.3" disabled><br>';
            echo '<label for="cfg_writeable" class="text-info">inc/cfg.php writeable</label><br>';
            echo '<input type="text" class="form-control" name="cfg_writeable" value="No" disabled><br>';
            echo '<label for="exec" class="text-info">PHP Exec enabled</label><br>';
            echo '<input type="text" class="form-control" name="exec" value="Yes" disabled><br>';
            echo '<label for="libreoffice" class="text-info">Libreoffice present</label><br>';
            echo '<input type="text" class="form-control" name="libreoffice" value="Yes" disabled><br>';
            echo $security->generate_token();
            echo '<input type="submit" name="submit" class="btn btn-info btn-md" value="Next >>">';
            echo '<input type="submit" name="refresh" class="btn btn-info btn-md" value="Refresh">';
        echo '</form>';
    } else if ($step == '1') {
        // Check for step 1 POST
        if (isset($_POST['submit'])) {
            header('location: ?step=2');
        }
        // Show step 1 form
        echo '<form method="POST">';
            echo '<h2 class="text-center">Database</h2>';
            echo '<label for="db_name" class="text-info">Database name</label><br>';
            echo '<input type="text" class="form-control" name="db_name"><br>';
            echo '<label for="db_user" class="text-info">Database username</label><br>';
            echo '<input type="text" class="form-control" name="db_user"><br>';
            echo '<label for="db_pass" class="text-info">Database password</label><br>';
            echo '<input type="text" class="form-control" name="db_pass"><br>';
            echo '<label for="db_host" class="text-info">Database host</label><br>';
            echo '<input type="text" class="form-control" name="db_host" value="localhost"><br>';
            echo $security->generate_token();
            echo '<input type="submit" name="submit" class="btn btn-info btn-md" value="Next >>">';
        echo '</form>';
    } else if ($step == '2') {
        // Check for step 1 POST
        if (isset($_POST['submit'])) {
            header('location: ?step=3');
        }
        $URL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $URL = explode('install.php', $URL)[0];
        // Show step 1 form
        echo '<form method="POST">';
            echo '<h2 class="text-center">Site structure</h2>';
            echo '<label for="site_url" class="text-info">Site URL</label><br>';
            echo '<input type="text" class="form-control" name="site_url" value="' . $URL . '"><br>';
            echo '<label for="site_root" class="text-info">Site root</label><br>';
            echo '<input type="text" class="form-control" name="site_root" value="' . getcwd() . '"><br>';
            echo '<label for="site_docs" class="text-info">Document root</label><br>';
            echo '<input type="text" class="form-control" name="site_docs" value="' . $URL . 'documents/"><br>';
            echo $security->generate_token();
            echo '<input type="submit" name="submit" class="btn btn-info btn-md" value="Next >>">';
        echo '</form>';
    } else if ($step == '3') {
        // Check for step 1 POST
        if (isset($_POST['submit'])) {
            header('location: ?step=4');
        }
        // Show step 1 form
        echo '<form method="POST">';
            echo '<h2 class="text-center">Email Settings</h2>';
            echo '<label for="mail_from" class="text-info">Send email from</label><br>';
            echo '<input type="text" class="form-control" name="email_from" placeholder="you@domain.com"><br>';
            echo '<label for="mail_server" class="text-info">Use an external mail server</label><br>';
            echo '<select name="mail_server">';
                echo '<option value="0">No</option>';
                echo '<option value="1">Yes</option>';
            echo '</select>';
            echo '<label for="mail_host" class="text-info">Mail server host</label><br>';
            echo '<input type="text" class="form-control" name="mail_host"><br>';
            echo '<label for="mail_port" class="text-info">Mail server port</label><br>';
            echo '<input type="text" class="form-control" name="mail_port"><br>';
            echo '<label for="mail_user" class="text-info">Mail server user</label><br>';
            echo '<input type="text" class="form-control" name="mail_user"><br>';
            echo '<label for="mail_pass" class="text-info">Mail server password</label><br>';
            echo '<input type="text" class="form-control" name="mail_pass"><br>';
            echo $security->generate_token();
            echo '<input type="submit" name="submit" class="btn btn-info btn-md" value="Next >>">';
        echo '</form>';
    } else if ($step == '4') {
        // Check for step 1 POST
        if (isset($_POST['submit'])) {
            header('location: ?step=5');
        }
        // Show step 1 form
        echo '<form method="POST">';
            echo '<h2 class="text-center">Master account</h2>';
            echo '<label for="first_name" class="text-info">First name</label><br>';
            echo '<input type="text" class="form-control" name="first_name"><br>';
            echo '<label for="last_name" class="text-info">Last name</label><br>';
            echo '<input type="text" class="form-control" name="last_name"><br>';
            echo '<label for="email" class="text-info">Email</label><br>';
            echo '<input type="text" class="form-control" name="email"><br>';
            echo $security->generate_token();
            echo '<input type="submit" name="submit" class="btn btn-info btn-md" value="Next >>">';
        echo '</form>';
    } else if ($step == '5') {
        // Check for step 1 POST
        if (isset($_POST['submit'])) {
            require('inc/cfg.php');
            $URL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $URL = explode('install.php', $URL)[0];
            $statement = $dbc->prepare("SELECT email,password,user_salt FROM users WHERE ID=1");
            $statement->execute();
            $user = $statement->fetch();
            $code = $security->generateResetCode($user['email'],$user['password'],$user['user_salt']);
            $URL = $url . 'index.php?reset&code=' . $code;
            header('location: ' . $URL);
        }
        echo '<form method="POST">';
            echo '<h2 class="text-center">Complete</h2>';
            echo '<label for="submit" class="form-control">Click next to delete the install script and set the password on the newly create master account.</label><br>';
            echo '<input type="submit" name="submit" class="btn btn-info btn-md" value="Next >>">';
        echo '</form>';
    }

    echo '</div>';
    ?>
</body>
</html>