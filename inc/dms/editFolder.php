<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

if ($_SESSION['type'] <= 2) {

// Get the ID
$ID = $security->sanitise($_GET['id']);
// Create a new folder object and populate variables
$theFolder = new folder(['ID'=>$ID]);
$title = $theFolder->getTitle();
$description = $theFolder->getDescription();
$category = $theFolder->getCategory();

// If post data exists, update $title,$description,$category 
if (isset($_POST['title'])) {
  // Update variables
  $title = $security->sanitise($_POST['title']);
  $description = $security->sanitise($_POST['description']);
  $category = $security->sanitise($_POST['category']);
  // Create false error
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
      if ($theFolder->setTitle($title) &&
          $theFolder->setDescription($description) &&
          $theFolder->setCategory($category)) {
            $uri->redirect($uri->folder($theFolder->getID(),$title));
      } else {
        $error = true;
        $errormsg .= '<p>There was a database error.</p>';
      }
    }
  }
}

?>
<div id="form">
  <form method="post">
    <h2 class="text-center">Edit: <?php echo $theFolder->getTitle(); ?></h2>
    <label for="title" class="text-info">Title:</label><br>
    <input type="text" name="title" class="form-control" value="<?php echo $title; ?>"><br>
    <label for="description" class="text-info">Description:</label><br>
    <textarea name="description"><?php echo $description; ?></textarea>
    <label for="category" class="text-info">Category:</label><br>
    <select name='category'>
      <?php 
      $categories = new category();
      echo $categories->getCategoryOption($category);
      ?>
    </select>
    <?php //print_r($categories->getCategories()); ?>
    <?php echo $security->generate_token(); ?>
    <input type="submit" name="submit" class="btn btn-info btn-md" value="Update">
  </form>
  <?php if (isset($error) && $error == true) { ?>
  <div class="notice warning">
    <?php echo $errormsg; ?>
  </div>
  <?php } ?>
  </form>
 </div>
<?php 
} else {
  echo permissionDeny();
}