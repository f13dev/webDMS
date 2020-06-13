<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

if (isset($_POST['title'])) {
  print_r($_POST);
  $error = false;
  $errormsg = '';
  // Check for CSRF
  if ($security->validate_token($_POST['token'])) {
    // Check the category is a-zA-Z0-9 space, hypen, underscore
    if (!$validate->title($_POST['title'])) {
      $error = true;
      $errormsg .= '<p>The category title must be between 1 and 32 characters in length, containing only letters, numbers, space, underscore and hyphen.</p>';
    }
    if (!$validate->numeric($_POST['category'])) {
      $error = true;
      $errormsg .= '<p>The category is not a valid numeric identifier.</p>';
    }
    if ($error == false) {
      // If no errors, process the new folder 
      $statement = $dbc->prepare("INSERT INTO folders (title, category, description) VALUES (?,?,?)");
      if (!$statement->execute([$_POST['title'], $_POST['category'], $_POST['description']])) {
        $error = true;
        $errormsg .= '<p>There was a database error.</p>';
      }
    }
    if ($error == false) {
      header("location:" . folder_uri($dbc->lastInsertId(), $_POST['title']));
    }
  }
}
?>

<div id="form">
  <form method="POST">
    <h2 class="text-center">New folder</h2>
      <label for="title" class="text-info">Title:</label><br>
      <input type="text" name="title" class="form-control"<?php if (isset($_POST['title'])) echo 'value="' . $security->sanitise($_POST['title']) . '"'; ?>><br>
      <label for="description" class="text-info">Description:</label><br>
      <textarea name="description"><?php if (isset($_POST['description'])) echo $security->sanitise($_POST['description']); ?></textarea>
      <label for="category" class="text-info">Category:</label><br>
      <select name='category'>
        <?php 
        $categories = new category();
        echo $categories->getCategories();
        ?>
      </select>
      <?php //print_r($categories->getCategories()); ?>
      <?php echo $security->generate_token(); ?>
      <input type="submit" name="submit" class="btn btn-info btn-md" value="Create">
  </form>
  <?php if (isset($error) && $error == true) { ?>
  <div class="notice warning">
    <?php echo $errormsg; ?>
  </div>
  <?php } ?>
</div>