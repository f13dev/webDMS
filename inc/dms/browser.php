<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}
?>

<div id="page-middle-left">
  <form>
    <input type="text" placeholder="Search..." style="display:inline-block; width:250px">
    <input type="submit" value="Go" style="display:inline-block; width: 58px">
  </form>
  <a href="<?php echo page_uri('newCategory'); ?>">New category +</a><br>
  <a href="<?php echo page_uri('newFolder'); ?>">New folder +</a>
  <hr>
  <?php
  // Shift this to category.class.php
  // get the categories
  $statement = $dbc->prepare("SELECT ID, name FROM categories ORDER BY name");
  $statement->execute();
  $categories = $statement->fetchall();
  // Look through categories
  echo '<ul class="list">';
  foreach ($categories as $eachCategory) {
    echo '<li class="category">' . $eachCategory['name'];
    $statement = $dbc->prepare("SELECT ID, title FROM folders WHERE category = ? ORDER BY title");
    $statement->execute([$eachCategory['ID']]);
    $folders = $statement->fetchAll();
    if (sizeof($folders) > 0) {
      echo '<ul>';
      foreach ($folders as $eachFolder) {
        echo '<li class="folder"><a href="' . folder_uri($eachFolder['ID'], $eachFolder['title']) . '">' . $eachFolder['title'] . '</a></li>';
      }
      echo '</ul>';
    }
  }
  echo '</ul>';
  ?>
</div>
<div id="page-middle-right">
  <div id="page-middle-right-top">
    <?php

    // file view
    if ($theFolder->isSet()) {
      echo '<h2>' . $theFolder->getTitle() . '</h2>';
      echo $theFolder->buildDocumentTable($d, $orderBy, $asc);
    } else {
      echo '<h2>Please select a folder</h2>';
    }

    ?>
  </div>
  <div id="page-middle-right-bottom">
  <?php
    if ($theDocument->isSet()) {
      echo $theDocument->showFile();
    } else {
      echo '<h2>Please select a file</h2>';
    }
  ?>
  </div>
</div>
