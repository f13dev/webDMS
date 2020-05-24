<?php
// Block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

var_dump($_POST);
?>
<section id="login">
  <h2>Login</h2>
  <form method="POST">
    <input type="email" name="email" placeholder="E-mail"><br>
    <input type="password" name="password" placeholder="Password"><br>
    <button type="submit">Log-in</button>
  </form>
</section>


<?php


// hash('sha256',$password . $user['user_salt'] . SALT);
