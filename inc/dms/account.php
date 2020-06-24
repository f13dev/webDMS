<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

// Get the default values

// Process forms
if (isset($_POST['updateDetails'])) {
  echo 'Processing details form';
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
    <input type="text" name="firstname" class="form-control" value=""><br>
    <label for="lastname" class="text-info">Last name:</label><br>
    <input type="text" name="lastname" class="form-control" value=""><br>
    <label for="email" class="text-info">Email:</label><br>
    <input type="email" name="email" class="form-control" value=""><br>
    <label for="password" class="text-info">Password:</label><br>
    <input type="password" name="password" class="form-control"><br>
    <?php echo $token; ?>
    <input type="submit" name="updateDetails" class="btn btn-info btn-md" value="Update">
  </form>
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