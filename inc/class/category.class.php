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
}