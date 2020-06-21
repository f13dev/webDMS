<?php
// Block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

if (isset($_POST['email'])) {
  // Check for CSRF
  if ($security->validate_token($_POST['token'])) {
    // Find email in db
    $email = $security->make_secure($_POST['email']);
    $statement = $dbc->prepare("SELECT email, password, user_salt FROM users WHERE email = ?");
    $statement->execute([$email]);
    $user = $statement->fetch();
    if (!empty($user)) {
      // email exists in db, check password
      $password = $security->password_hash($_POST['password'], $user['user_salt']);
      // Check the password is correct
      $statement = $dbc->prepare("SELECT ID, first_name, last_name FROM users WHERE email = ? AND password = ?");
      $statement->execute([$email,$password]);
      $password = $statement->fetch();
      if (!empty($password)) {
        // Password matches the email in database
        echo 'creating session';
        $_SESSION['loggedin'] = true;
        $_SESSION['ID'] = $password['ID'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['name'] = $password['first_name'] . ' ' . $password['last_name'];
        $_SESSION['salt'] = $security->generateSessionSalt();
        $_SESSION['usertoken'] = $security->generateUserToken();
        $currentURI = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $uri->redirect($currentURI);
      } else {
        $loginerror = true;
      }
    } else {
      $loginerror = true;
    }
  }
}
?>

<div id="form">
  <form method="POST">
    <h2 class="text-center">webDMS login</h2>
      <label for="email" class="text-info">Email address:</label><br>
      <input type="email" name="email" class="form-control"><br>
      <label for="password" class="text-info">Password:</label><br>
      <input type="password" name="password" class="form-control"><br>
      <?php echo $security->generate_token(); ?>
      <input type="submit" name="submit" class="btn btn-info btn-md" value="Login">
  </form>
  <?php if (isset($loginerror) && $loginerror == true) { ?>
  <div class="notice warning">
    The credentials entered did not match those of an account in the database, pease try again.
  </div>
  <?php } ?>
</div>
