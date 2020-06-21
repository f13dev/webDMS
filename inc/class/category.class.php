<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

Class category {
  public function getCategoryOption($id = false) {
    global $dbc;
    $statement = $dbc->prepare("SELECT ID, name FROM categories ORDER BY name");
    $statement->execute();
    $results = $statement->fetchAll();
    $return = '';
    foreach ($results as $result) {
      $selected = '';
      if ($id != false && $id == $result['ID']) {
        $selected = ' selected ';
      }
      $return .= '<option value="' . $result['ID'] . '"' . $selected . '>' . $result['name'] . '</option>';
    }
    return $return;
  }

  public function getCategoryFolderOption($id = false) {
    global $dbc;
    $statement = $dbc->prepare("SELECT ID, name FROM categories ORDER BY name");
    $statement->execute();
    $results = $statement->fetchAll();
    $return = '';
    foreach ($results as $result) {
      $return .= '<option disabled>' . $result['name'] . '</option>';
      $fstatement = $dbc->prepare("SELECT ID, title FROM folders WHERE category = ? ORDER BY title");
      $fstatement->execute([$result['ID']]);
      $fresults = $fstatement->fetchAll();
      foreach ($fresults as $fresult) {
        if ($id != false && $id == $fresult['ID']) $selected = ' selected '; else $selected = '';
        $return .= '<option value="' . $fresult['ID'] . '"' . $selected . '>&nbsp; &nbsp;' . $fresult['title'] . '</option>';
      }
    }
    return $return;
  }

  public function getNumberFolders($id) {
    global $dbc;
    $statement = $dbc->prepare("SELECT count(*) FROM folders WHERE category = ?");
    $statement->execute([$id]);
    return $statement->fetchColumn();
  }
}