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
  * Get document URI
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

/**
  * Get page URI
  * $p = page
  **/
function page_uri($p) {
  if (REWRITE) {
    return SITE_URL . $p . '/';
  } else {
    return SITE_URL . '?p=' . $p;
  }
}

/**
  * Gete document edit URI
  **/
function doc_edit_uri($id) {
  if (REWRITE) {
    return SITE_URL . 'editDoc/D' . $id . '/';
  } else {
    return SITE_URL . '?p=editDoc&id=' . $id;
  }
}

/**
  * Get document delete URI
  **/
function doc_del_uri($id) {
  if (REWRITE) {
    return SITE_URL . 'delDoc/D' . $id . '/';
  } else {
    return SITE_URL . '?p=delDoc&id=' . $id;
  }
}

/**
  * Get document download URI
  **/
function doc_download_uri($id) {
  if (REWRITE) {
    return SITE_URL . 'download/D' . $id . '/';
  } else {
    return SITE_URL . '?p=download&id=' . $id;
  }
}
