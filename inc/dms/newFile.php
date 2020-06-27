<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

$folder = new folder(['ID'=>$_GET['id']]);
if (!$folder->isSet()) {
  // kill
}

if ($_SESSION['type'] <= 3) {

if (isset($_POST['title'])) {
  $error = false;
  $errormsg = '';
  // Check for CSRF
  if ($security->validate_token($_POST['token'])) {
    // Check the title is valid
    if (!$validate->title($_POST['title'])) {
      $error = true;
      $errormsg .= '<p>The file title must be between 1 and 32 characters in length, containing only letters, numbers, space, underscore and hyphen.</p>';
    }
    if (!$validate->date($_POST['document_date'])) {
      $error = true;
      $errormsg .= '<p>The document date must be set as a valid date.</p>';
    }
    if (!$validate->file($_FILES['file']['name'])) {
      $error = true;
      $valid = 'pdf,';
      foreach (FILE_TYPES as $filetype) {
        foreach ($filetype as $eachtype) {
          $valid .= $eachtype . ',';
        }
      }
      $valid = substr($valid, 0, -1); // remove last comma
      $errormsg .= '<p>Please select a valid file [' . $valid . '].</p>';
    }
    if ($error == false) {
      // If no errors, process the new file
      $document = new document([
        'title'=>$security->sanitise($_POST['title']),
        'folder'=>$folder->getID(),
        'description'=>$security->sanitise($_POST['description']),
        'document_date'=>$security->sanitise($_POST['document_date']),
        'file'=>$_FILES,
      ]);
      if ($document == false) {
        $error = true;
        $errormsg .= '<p>There was a database error.</p>';
      } else {
        // Generate the URL to direct to
        $uri->redirect($uri->document($folder->getID(),$folder->getTitle(),$dbc->lastInsertId(),$security->sanitise($_POST['title'])));
      }
    }
  }
}

?>

<div id="form">
  <form method="POST" enctype="multipart/form-data">
    <h2 class="text-center"><?php echo $folder->getTitle(); ?>: Upload document</h2>
      <label for="title" class="text-info">Title:</label><br>
      <input type="text" name="title" class="form-control"<?php if (isset($_POST['title'])) echo 'value="' . $security->sanitise($_POST['title']) . '"'; ?>><br>
      <label for="description" class="text-info">Description:</label><br>
      <textarea name="description"><?php if (isset($_POST['description'])) echo $security->sanitise($_POST['description']); ?></textarea>
      <label for="document_date" class="text-info">Document date:</label><br>
      <input type="date" name="document_date" value="<?php if (isset($_POST['document_date'])) echo $security->sanitise($_POST['document_date']); else echo date('Y-m-d'); ?>">
      <label for="file" class="text-info">Select a document:</label><br>
      <input type="file" name="file" id="fileupload" data-buttonText="Choose a document"><br>
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
<?php
} else {
  echo permissionDeny();
}