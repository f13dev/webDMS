<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

// Get the default values
$statement = $dbc->prepare("SELECT first_name,last_name,email,password,user_salt FROM users WHERE ID = ?");
$statement->execute([$_SESSION['ID']]);
$dbuser = $statement->fetch();
$first_name = $dbuser['first_name'];
$last_name =$dbuser['last_name'];
$email = $security->revert_secure($dbuser['email']);

// Process forms
if (isset($_POST['updateDetails'])) {
  $derror = false;
  $derrormsg = '';
  // Update values 
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $email = $_POST['email'];
  // validate first_name
  if (!$validate->length($first_name,1)) {
    $derror = true;
    $derrormsg .= '<p>Please enter a first name or initial.</p>';
  }
  // validate last_name 
  if (!$validate->length($last_name,2)) {
    $derror = true;
    $derrormsg .= '<p>Please enter a last name (2 or more charachters).</p>';
  }
  // validate email 
  if (!$validate->email($email)) {
    $derror = true;
    $derrormsg .= '<p>Please enter a valid email address.</p>';
  }
  // No errors, process form
  if (!$derror) {
    // Check password is correct 
    if ($dbuser['password'] == $security->password_hash($_POST['password'],$dbuser['user_salt'])) {
      // Password is correct
      $updateEmail = $security->make_secure($_POST['email']);
      $statement = $dbc->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE ID = ?");
      if ($statement->execute([$_POST['first_name'],$_POST['last_name'],$updateEmail,$_SESSION['ID']])) {
        // Update session 
        $_SESSION['name'] = $_POST['first_name'] . ' ' . $_POST['last_name'];
        $notice = '<p>Your details have been been updated.</p>';
      } else {
        $derror = true;
        $derrormsg .= '<p>There was a database error.</p>';
      }
    } else {
      $derror = true;
      $derrormsg .= '<p>The password entered did not match that stored in the database.</p>';
    }
  }
  // submit form
}

if (isset($_POST['updatePassword'])) {
  // Process password form 
  $perror = false;
  $perrormsg = '';
  // Check pass1 and pass2 match 
  if ($_POST['new1'] == $_POST['new2']) {
    // Check pass1 is valid 
    if ($validate->password($_POST['new1'])) {
      // Check old pass is valid
      $statement = $dbc->prepare("SELECT password,user_salt FROM users WHERE ID = ?");
      $statement->execute([$_SESSION['ID']]);
      $old = $statement->fetch();
      // Check if old password matches
      if ($old['password'] == $security->password_hash($_POST['current'], $old['user_salt'])) {
        // Process the change
        $newPassHash = $security->password_hash($_POST['new1'], $old['user_salt']);
        $statement = $dbc->prepare('UPDATE users SET password = ? WHERE ID = ?');
        if ($statement->execute([$newPassHash, $_SESSION['ID']])) {
          $notice = '<p>Your password has been updated.</p>';
        } else {
          $perror = true;
          $perrormsg .= '<p>There was a database error.</p>';
        }
      } else {
        // Confirm password doesn't match
        $perror = true;
        $perrormsg .= '<p>The old password did not match the one held in the database.</p>';
      }
    } else {
      // Pass1 not a valid password
      $perror = true;
      $perrormsg .= '</p>Please enter a new password 8 or more charachters long, with at least 1: upper case, lower case and special charachter.</p>';
    }
  } else {
    // Pass1 and Pass2 don't match
    $perror = true;
    $perrormsg .= '</p>The new password and confirmation did not match.</p>';
  }

}

// Use the same token for both forms
$token = $security->generate_token();

if (isset($notice)) {
  echo '<div class="notice notification">';
    echo $notice;
  echo '</div>';
}

?>

<div id="form">
  <form method="POST">
    <h2 class="text-center">Account details</h2>
    <label for="firstname" class="text-info">First name:</label><br>
    <input type="text" name="first_name" class="form-control" value="<?php echo $first_name; ?>"><br>
    <label for="lastname" class="text-info">Last name:</label><br>
    <input type="text" name="last_name" class="form-control" value="<?php echo $last_name; ?>"><br>
    <label for="email" class="text-info">Email:</label><br>
    <input type="email" name="email" class="form-control" value="<?php echo $email; ?>"><br>
    <label for="password" class="text-info">Password:</label><br>
    <input type="password" name="password" class="form-control"><br>
    <?php echo $token; ?>
    <input type="submit" name="updateDetails" class="btn btn-info btn-md" value="Update">
  </form>
  <?php if (isset($derror) && $derror == true) { ?>
  <div class="notice warning">
    <?php echo $derrormsg; ?>
  </div>
  <?php } ?>
</div>
<div id="form" style="margin-bottom: 2em;">
  <form method="POST">
    <h2 class="text-center">Update password</h2>
    <label for="current" class="text-info">Current password:</label><br>
    <input type="password" name="current" class="form-control"><br>
    <label for="new1" class="text-info">New password:</label><br>
    <input type="password" name="new1" class="form-control"><br>
    <label for="new2" class="text-info">Confirm new password:</label><br>
    <input type="password" name="new2" class="form-control"><br>
    <?php echo $token; ?>
    <input type="submit" name="updatePassword" class="btn btn-info btn-md" value="Update">
  </form>
  <?php if (isset($perror) && $perror == true) { ?>
  <div class="notice warning">
    <?php echo $perrormsg; ?>
  </div>
  <?php } ?>
</div>