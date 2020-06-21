<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

if (isset($_POST['title'])) {
  $error = false;
  $errormsg = '';
  // Check for CSRF
  if ($security->validate_token($_POST['token'])) {
    // Check the category is a-zA-Z0-9 space, hypen, underscore
    if (!$validate->title($_POST['title'])) {
      $error = true;
      $errormsg .= '<p>The category title must be between 1 and 32 characters in length, containing only letters, numbers, space, underscore and hyphen.</p>';
    }
    // Check if the title already exists 
    if (!$validate->titleExists($_POST['title'])) {
      $error = true;
      $errormsg .= '<p>The category already exists in the database.</p>';
    }
    if ($error == false) {
      // If no errors, process the new category 
      $statement = $dbc->prepare("INSERT INTO categories (name) VALUES (?)");
      if (!$statement->execute([$security->sanitise($_POST['title'])])) {
        $error = true;
        $errormsg .= '<p>There was a database error.</p>';
      }
    }
    if ($error == false) {
      $uri->redirect(SITE_URL);
    }
  }
}
?>

<div id="form">
  <form method="POST">
    <h2 class="text-center">New category</h2>
      <label for="text" class="text-info">Title:</label><br>
      <input type="text" name="title" class="form-control"><br>
      <?php echo $security->generate_token(); ?>
      <input type="submit" name="submit" class="btn btn-info btn-md" value="Create">
  </form>
  <?php if (isset($error) && $error == true) { ?>
  <div class="notice warning">
    <?php echo $errormsg; ?>
  </div>
  <?php } ?>
</div>