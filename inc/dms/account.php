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
  echo 'Processing password form';
}

print_r($_POST);
// Use the same token for both forms
$token = $security->generate_token();
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
<div id="form">
  <form method="POST">
    <h2 class="text-center">Update password</h2>
    <label for="current" class="text-info">Current password:</label><br>
    <input type="password" name="current" class="form-control"><br>
    <label for="new1" class="text-info">New password:</label><br>
    <input type="password" name="new1" class="form-control"><br>
    <label for="new2" class="text-info">Repeat new password:</label><br>
    <input type="password" name="new2" class="form-control"><br>
    <?php echo $token; ?>
    <input type="submit" name="updatePassword" class="btn btn-info btn-md" value="Update">
  </form>
</div>