<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    header("Location: ../../");
  }

Class Uri {

    /**
     * Get folder URI
    * $f = numeric folder ID
    * $n = name of folder
    **/
    function folder($f,$n,$orderBy = 'document_date', $asc = 'false') {
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
    function document($f,$n,$d = false,$t = false,$orderBy = 'document_date', $asc = 'false') {
    $baseURI = $this->folder($f,$n,$orderBy,$asc);
    if (REWRITE) {
        if ($d == false) {
        return $baseURI;
        } else {
        return $this->folder($f,$n,$orderBy,$asc) . 'D' . $d . '/' . urlencode($t) . '/';
        }
        //return folder_uri($f,$n,$orderBy,$asc) . 'D' . $d . '/' . urlencode($t) . '/';
    } else {
        if ($d == false) {
        return $baseURI;
        } else {
        return $this->folder($f,$n,$orderBy,$asc) . '&d=' . $d . '&titlle=' . urlencode($t);
        }
        //return folder_uri($f,$n,$orderBy,$asc) . '&d=' . $d . '&titlle=' . urlencode($t);
    }
    }

    /**
     * Get page URI
    * $p = page
    **/
    function page($p) {
    if (REWRITE) {
        return SITE_URL . $p . '/';
    } else {
        return SITE_URL . '?p=' . $p;
    }
    }

    /**
     * Gete document edit URI
    **/
    function editDocument($id,$title) {
    if (REWRITE) {
        return SITE_URL . 'editDoc/D' . $id . '/' . urlencode($title) . '/';
    } else {
        return SITE_URL . '?p=editDoc&id=' . $id . '&title=' . urlencode($title);
    }
    }

    /**
     * Get document delete URI
    **/
    function deleteDocument($id) {
    if (REWRITE) {
        return SITE_URL . 'deleteFile/D' . $id . '/';
    } else {
        return SITE_URL . '?p=deleteFile&id=' . $id;
    }
    }

    /**
     * Get document download URI
    **/
    function downloadDocument($file) {
    if (REWRITE) {
        return SITE_URL . 'download/' . $file . '/';
    } else {
        return SITE_URL . 'fileScraper.php?file=' . $file;
    }
    }

    /**
     * Get document upload URI for a given folder
     */
    function newDocument($id, $title) {
    if (REWRITE) {
        return SITE_URL . 'newFile/F' . $id . '/' . urlencode($title) . '/';
    } else {
        return SITE_URL . '?p=newFile&id=' . $id . '&title=' . urlencode($title);
    }
    }

    /**
     * Get edit folder URI for a given folder
     */
    function editFolder($id, $title) {
    if (REWRITE) {
        return SITE_URL . 'editFolder/F' . $id . '/' . urlencode($title) . '/';
    } else {
        return SITE_URL . '?p=editFolder&id=' . $id . '&title=' . urlencode($title);
    }
    }

    /**
     * Get delete folder URI for a given folder
     */
    function deleteFolder($id, $title) {
    if (REWRITE) {
        return SITE_URL . 'deleteFolder/F' . $id . '/' . urlencode($title) . '/';
    } else {
        return SITE_URL . '?p=deleteFolder&id=' . $id . '&title=' . urlencode($title);
    }
    }

    /**
     * Get search term URI
     */
    function search($term) {
    if (REWRITE) {
        return SITE_URL . 'search/' . urlencode($term) . '/';
    } else {
        return SITE_URL . '?p=search&searchString=' . urlencode($term);
    }
    }

    /**
     * Get search results document URI for a given search term and document 
     */
    function searchDocument($term, $doc, $title) {
    if (REWRITE) {
        return SITE_URL . 'search/' . urlencode($term) . '/D' . $doc . '/' . urlencode($title) . '/';
    } else {
        return SITE_URL . '?p=search&searchString=' . $term . '&d=' . $doc . '&title=' . urlencode($title);
    }
    }

    function recycleBin() {
        if (REWRITE) {
            return SITE_URL . 'recycleBin/';
        } else {
            return SITE_URL . '?recycleBin';
        }
    }

    function recycleBinFolder($f,$t) {
        if (REWRITE) {
            return SITE_URL . 'recycleBin/F' . $f . '/' . $t . '/';
        } else {
            return SITE_URL . '?recycleBin&f=' . $f . '&t=' . $t;
        }
    }

    function recycleDocument($d,$t) {
        if (REWRITE) {
            return SITE_URL . 'recycleBin/D' . $d . '/' . $t . '/';
        } else {
            return SITE_URL . '?recycleBin&d=' . $d . '&t=' . $t;
        }
    }

    function restoreDocument($d) {
        if (REWRITE) {
            return SITE_URL . 'restore/D' . $d . '/';
        } else {
            return SITE_URL . '?p=restore&d=' . $d;
        }
    }
}