<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

if ($_SESSION['type'] <= 2) {
// Check that there's no folders in the category, check that it's not -1 (uncategorised)

$category = new category();
$error = '';

if ($_GET['id'] != '-1') {
  if ($category->getNumberFolders($_GET['id']) == 0) {
    // Process category delete 
    $statement = $dbc->prepare("DELETE FROM categories WHERE ID = ?");
    if ($statement->execute([$_GET['id']])) {
      // Header location 
      $uri->redirect(SITE_URL);
      //header('location: ' . SITE_URL);
    } else {
      // Show DB error
      $error .= '<p>There was a database error while processing the category deletion.</p>';
    }
  } else {
    // Show error that folders are associated with category
    $error .= '<p>This category cannot be deleted because it has folders associated with it.</p>';
  }
} else {
  // Show error that uncategorised can't be deleted
  $error .= '<p>The "Uncategorised" category cannot be deleted.</p>';
}

echo '<div id="form">
        <h2>Delete category</h2>
        <div class="warning notice">
          ' . $error . '
        </div>
      </div>';

} else {
  echo permissionDeny();
}