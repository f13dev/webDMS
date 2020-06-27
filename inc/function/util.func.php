<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}
function humanSize($bytes) {
  $si_prefix = array('B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB');
  $base = 1024;
  $class = min((int)log($bytes, $base), count($si_prefix) -1);
  return sprintf('%1.2f', $bytes / pow($base,$class)) . ' ' . $si_prefix[$class];
}

function getDocumentTotalSize($path = SITE_DOCS) {
  $bytestotal = 0;
  $path = realpath($path);
  if ($path!==false && $path!='' && file_exists($path)) {
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object) {
      $bytestotal += $object->getSize();
    }
  }
  return humanSize($bytestotal);
}

function getDocumentCount() {
  global $dbc;
  $statement = $dbc->prepare("SELECT count(ID) FROM documents");
  $statement->execute();
  return $statement->fetch()['count(ID)'];
}

function permissionDeny() {
  $return =  '<div id="form">';
  $return .= '<h2>Permission denied</h2>';
  $return .= '<p>You do not have permission to perform this action</p>';
  $return .= '</div>';
  return $return;
}