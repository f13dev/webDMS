<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

define('REWRITE', true);
/**
  * Get folder URI
  * $f = numeric folder ID
  * $n = name of folder
  **/
function folder_uri($f,$n,$orderBy = 'document_date', $asc = 'false') {
  if (REWRITE) {
    if ($orderBy == 'document_date') { $orderByString = ''; } else { $orderByString = '/order-' . $orderBy; }
    if ($asc == 'false') { $ascString = ''; } else { $ascString = '/asc-' . $asc; }
    return SITE_URL . 'F' . $f . '/' . urlencode($n) . $orderByString . $ascString . '/';
  } else {
    if ($orderBy == 'document_date') { $orderByString = ''; } else { $orderByString = '&orderBy=' . $orderBy; }
    if ($asc == 'false') { $ascString = ''; } else { $ascString = '&asc=' . $asc; }
    return SITE_URL . '?f=' . $f . '&folder=' . urlencode($n) . '&orderBy=' . $orderBy . '&asc=' . $asc . '/';
  }
}

/**
  * Get document URI
  * $f = numeric folder ID
  * $n = name of folder
  * $d = numeric document ID
  * $t = title of document
  **/
function document_uri($f,$n,$d = false,$t = false,$orderBy = 'document_date', $asc = 'false') {
  $baseURI = folder_uri($f,$n,$orderBy,$asc);
  if (REWRITE) {
    if ($d == false) {
      return $baseURI;
    } else {
      return folder_uri($f,$n,$orderBy,$asc) . 'D' . $d . '/' . urlencode($t) . '/';
    }
    //return folder_uri($f,$n,$orderBy,$asc) . 'D' . $d . '/' . urlencode($t) . '/';
  } else {
    if ($d == false) {
      return $baseURI;
    } else {
      return folder_uri($f,$n,$orderBy,$asc) . '&d=' . $d . '&titlle=' . urlencode($t);
    }
    //return folder_uri($f,$n,$orderBy,$asc) . '&d=' . $d . '&titlle=' . urlencode($t);
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

/**
 * Get document upload URI for a given folder
 */
function newFile_uri($id, $title) {
  if (REWRITE) {
    return SITE_URL . 'newFile/F' . $id . '/' . urlencode($title) . '/';
  } else {
    return SITE_URL . '?p=newFile&id=' . $id . '&title=' . urlencode($title);
  }
}

/**
 * Get edit folder URI for a given folder
 */
function editFolder_uri($id, $title) {
  if (REWRITE) {
    return SITE_URL . 'editFolder/F' . $id . '/' . urlencode($title) . '/';
  } else {
    return SITE_URL . '?p=editFolder&id=' . $id . '&title=' . urlencode($title);
  }
}

/**
 * Get delete folder URI for a given folder
 */
function deleteFolder_uri($id, $title) {
  if (REWRITE) {
    return SITE_URL . 'deleteFolder/F' . $id . '/' . urlencode($title) . '/';
  } else {
    return SITE_URL . '?p=deleteFolder&id=' . $id . '&title=' . urlencode($title);
  }
}

/**
 * Get search URI
 */
function search_uri() {
  if (REWRITE) {
    return SITE_URL . 'search/';
  } else {
    return SITE_URL . '?p=search';
  }
}