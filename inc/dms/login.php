<?php
// Block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}
?>
<section id="login">
  <h2>Login</h2>
</section>


<?php
// temporarily get 1st user
$statement = $dbc->prepare("SELECT * FROM users WHERE ID = 1");
$statement->execute();
$user = $statement->fetch();


// hash('sha256',$password . $user['user_salt'] . SALT);

$data = 'jv@f13dev.com';

$encrypted = $security->make_secure($data);
echo $encrypted . '<br><br>';

echo $security->revert_secure($encrypted);
