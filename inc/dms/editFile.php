<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

if ($_SESSION['type'] <= PERM_DOC_EDIT) {

// Get the ID
$ID = $security->sanitise($_GET['id']);
// Create a new document object and populate variables
$document = new document(['ID'=>$ID]);
$title = $document->getTitle();
$date = $document->getDocDate('Y-m-d');
$description = $document->getNotes();
$folder = $document->getFolder();

// If post data exists
if (isset($_POST['title'])) {
  // Update variables
  $title = $security->sanitise($_POST['title']);
  $date = $security->sanitise($_POST['document_date']);
  $description = $security->sanitise($_POST['description']);
  $folder = $security->sanitise($_POST['folder']);
  // Create false error 
  $error = false;
  $errormsg = '';
  // Check CSRF 
  if ($security->validate_token($_POST['token'])) {
    // Validate form data
    if (!$validate->title($title)) {
      $error = true;
      $errormsg .= '<p>The file title must be between 1 and 32 characters in length, containing only letters, numbers, space, underscore and hyphen.</p>';
    }
    if (!$validate->date($date)) {
      $error = true;
      $errormsg = '<p>The document date must be set as a valid date.</p>';
    }
    if ($error == false) {
      // No errors, process the form
      if ($document->setTitle($title) && $document->setDocDate($date) && $document->setNotes($description) && $document->setFolder($folder)) {
        // Rebuild the object 
        $document = new document(['ID' => $ID]);
        // Go to document
        $uri->redirect($uri->document($document->getFolder(),$document->getFolderTitle(),$document->getID(),$document->getTitle()));        
      } else {
        $error = true;
        $errormsg .= '<p>There was a database error.</p>';
      }
    }
  }
}



?>
<div id="form">
  <form method="POST">
    <h2 class="text-center">Edit: <?php echo $document->getTitle(); ?></h2>
    <label for="title" class="text-info">Title:</label><br>
    <input type="text" name="title" class="form-control" value="<?php echo $title; ?>"><br>
    <label for="description" class="text-info">Description:</label><br>
    <textarea name="description"><?php echo $description; ?></textarea><br>
    <label for="folder" class="text-info">Folder:</label><br>
    <select name="folder"> 
      <?php 
      $cat = new category();
      echo $cat->getCategoryFolderOption($folder);
      ?>
    </select><br>
    <label for="document_date" class="text-info">Document date:</label><br>
    <input type="date" name="document_date" value="<?php echo $date; ?>"><br>
    <?php echo $security->generate_token(); ?>
    <input type="submit" name="submit" class="btn bttn-info btn-md" value="Update">
    </form>
    <?php if (isset($error) && $error == true) { ?>
    <div class="notice warning">
      <?php echo $errormsg; ?>
    </div>
    <?php } ?>
</div>

<?php 
} else {
  echo permissionDeny();
}