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
    if (!$validate->numeric($_POST['category'])) {
      $error = true;
      $errormsg .= '<p>The category is not a valid numeric identifier.</p>';
    }
    if ($error == false) { 
      // If no errors, process the new folder 

      if (new folder([
        'title'=>$security->sanitise($_POST['title']),
        'category'=>$security->sanitise($_POST['category']),
        'description'=>$security->sanitise($_POST['description']),
      ]) == false) {
        $error = true;
        $errormsg .= '<p>There was a database error.</p>';
      } else {
        header('location:'.$uri->folder($dbc->lastInsertId(), $security->sanitise($_POST['title'])));
      }
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
        if (isset($_POST['category'])) {
          $category = $_POST['category'];
        } else {
          $category = false;
        }
        echo $categories->getCategoryOption($category);
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