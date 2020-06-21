<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}
?>

<div id="page-middle-left">
  <form method="POST" action="<?php echo SITE_URL; ?>">
    <input type="hidden" name="p" value="search">
    <input type="text" name="searchString" placeholder="Search..." style="display:inline-block; width:250px">
    <input type="submit" value="Go" style="display:inline-block; width: 58px">
  </form>
  <a href="<?php echo $uri->page('newCategory'); ?>">New category +</a><br>
  <a href="<?php echo $uri->page('newFolder'); ?>">New folder +</a>
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
        $eachFolder = new folder(['ID'=>$eachFolder['ID']]);
        echo '<li class="folder"><a href="' . $uri->folder($eachFolder->getID(), $eachFolder->getTitle()) . '">' . $eachFolder->getTitle() . ' (' . $eachFolder->getNumberFiles() .  ')</a></li>';
      }
      echo '</ul>';
    }
    echo '</li>';
  }
  echo '<li class="category">Recycle bin
          <ul>
            <li class="folder"><a href="' . $uri->recycleBin() . '"><i class="fa fa-trash"></i> (' . $recycleBin->getCount() . ')</a></li>
          </ul>
        </li>';
  echo '</ul>';

  ?>
</div>
<div id="page-middle-right">
  <div id="page-middle-right-top">
    <?php

    // file view
    if ($theFolder->isSet()) {
      echo '<h2 style="display:inline-block;">' . $theFolder->getTitle() . '</h2>';
      if ($recycleBin->getCount($theFolder->getID()) > 0) { 
        if ($recycleBin->getCount($theFolder->getID()) == 1) $fileText = 'file'; else $fileText = 'files';
        echo '<p style="display:inline-block; margin-left:0.5em;">(' . $recycleBin->getCount($theFolder->getID()) . ' ' . $fileText . ' in the <a href="' . $uri->recycleBin() . '">recycling bin <i class="fa fa-trash"></i></a>)</p>';
      }
      echo $theFolder->buildFolderHead();
      echo $theFolder->buildDocumentTable($d, $orderBy, $asc);
    } else if (isset($_GET["searchString"]) || (isset($_POST['searchString']))) {
      // Force a reload if post data is present 
      if (isset($_POST['searchString'])) {
        header("location:" . $uri->search($_POST['searchString']));
      }
      $search = new search($searchString);
      echo '<h2>Search: ' . $search->getTerm() . '</h2>';
      echo 'The search term ' . $search->getTerm() . ' returned ' . $search->getResultCount() . ' results';
      echo $search->buildSearchResultsTable($d);
    } else if (isset($_GET['recycleBin'])) {
      echo '<h2>Recycling bin</h2>';
      echo $recycleBin->buildRecycleTable($d);
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
      // Test code to view session data
      foreach ($_SESSION as $key=>$value) {
        echo $key . ' => ' . $value . '<br>';
      }
      print_r($_GET);
      foreach ($_GET as $key=>$value) {
        echo $key . ' => ' . $value . '<br>';
      }
    }
  ?>
  </div>
</div>
