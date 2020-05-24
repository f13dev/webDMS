<?php

define('REWRITE', true);
/**
  * Get folder URI
  * $f = numeric folder ID
  * $n = name of folder
  **/
function folder_uri($f,$n) {
  if (REWRITE) {
    return SITE_URL . 'F' . $f . '/' . urlencode($n) . '/';
  } else {
    return SITE_URL . '?f=' . $f . '&folder=' . urlencode($n);
  }
}

/**
  * $f = numeric folder ID
  * $n = name of folder
  * $d = numeric document ID
  * $t = title of document
  **/
function document_uri($f,$n,$d,$t) {
  if (REWRITE) {
    return folder_uri($f,$n) . 'D' . $d . '/' . urlencode($t) . '/';
  } else {
    return folder_uri($f,$n) . '&d=' . $d . '&titlle=' . urlencode($t);
  }
}

function page_uri($p) {
  if (REWRITE) {
    return SITE_URL . $p;
  } else {
    return SITE_URL . '?p=' . $p;
  }
}
