<?php
// Block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

if (isset($_POST['email'])) {
  // Check for CSRF
  if ($security->validate_token($_POST['token'])) {
    echo $_SESSION['csrf_token'];
    // Find email in db
    $email = $security->make_secure($_POST['email']);
    $statement = $dbc->prepare("SELECT email, password, user_salt FROM users WHERE email = ?");
    $statement->execute([$email]);
    $user = $statement->fetch();
    if (!empty($user)) {
      // email exists in db, check password
      echo "User exists in db<br>";
      $password = $security->password_hash($_POST['password'], $user['user_salt']);
      echo $password;
      // Check the password is correct
      $statement = $dbc->prepare("SELECT ID FROM users WHERE email = ? AND password = ?");
      $statement->execute([$email,$password]);
      if (!empty($user)) {
        // Password matches the email in database
        echo "Password matches<br>";
      }
    }
  }
}
?>
<section id="login">
  <h2>Login</h2>
  <form method="POST">
    <input type="email" name="email" placeholder="E-mail"><br>
    <input type="password" name="password" placeholder="Password"><br>
    <?php echo $security->generate_token(); ?>
    <button type="submit">Log-in</button>
  </form>
</section>


<?php


// hash('sha256',$password . $user['user_salt'] . SALT);
