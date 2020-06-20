<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}
$document = new document(['ID'=>$_GET['id']]);
print_r($document);
print_r($_GET);
?>
<div id="form">
  <form method="POST">
    <h2 class="text-center">Edit: <?php echo $document->getTitle(); ?></h2>
    <label for="title" class="text-info">Title:</label><br>
    <input type="text" name="title" class="form-control" value="<?php if (isset($_POST['title'])) echo $security->sanitise($_POST['title']); else echo $document->getTitle(); ?>"><br>
    <label for="description" class="text-info">Description:</label><br>
    <textarea name="description"><?php if (isset($_POST['description'])) echo $security->sanitise($_POST['description']); else echo $document->getNotes(); ?></textarea><br>
    <label for="folder" class="text-info">Folder:</label><br>
    <select> 
      <?php 
      $cat = new category();
      if (isset($_POST['folder'])) $folder = $security->sanitise($_GET['folder']); else $folder = $document->getFolder();
      echo $cat->getCategoryFolderOption($folder);
      ?>
    </select><br>
    <label for="document_date" class="text-info">Document date:</label><br>
    <input type="date" name="document_date" value="<?php if (isset($_POST['document_date'])) echo $security->sanitise($_POST['document_date']); else echo $document->getDocDate('Y-m-d'); ?>"><br>
    <?php echo $security->generate_token(); ?>
    <input type="submit" name="submit" class="btn bttn-info btn-md" value="Update">
    </form>
</div>