<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}
Class document {
  // Variables
  private $isSet;
  private $ID;
  private $title;
  private $notes;
  private $folder;
  private $upload_date;
  private $document_date;
  private $file;

  public function __construct($id) {
    global $dbc;
    $statement = $dbc->prepare("SELECT ID, title, notes, folder, upload_date, document_date, file FROM documents WHERE ID = ?");
    $statement->execute([$id]);
    $document = $statement->fetch();

    if ($statement->rowCount() == 0) {
      // set false variable
      $this->isSet = false;
    } else {
      // set document variables
      $this->isSet = true;
      $this->ID = $document['ID'];
      $this->title = $document['title'];
      $this->notes = $document['notes'];
      $this->folder = $document['folder'];
      $this->upload_date = $document['upload_date'];
      $this->document_date = $document['document_date'];
      $this->file = $document['file'];
    }
  }

  public function isSet() {
    return $this->isSet;
  }

  public function getID() {
    return $this->ID;
  }

  public function getTitle() {
    return $this->title;
  }

  public function getNotes() {
    return $this->notes;
  }

  public function getFolder() {
    return $this->folder;
  }

  public function getFolderTitle() {
    $folder = new folder($this->getFolder());
    return $folder->getTitle();
  }

  public function getUploadDate() {
    return date(DATE_FORMAT, strtotime($this->upload_date));
  }

  public function getDocDate() {
    return date(DATE_FORMAT, strtotime($this->document_date));
  }

  public function getFile() {
    return $this->file;
  }

  public function getExtension() {
    $explode = explode('.', $this->getFile());
    return strtolower(end($explode));
  }

  public function getFileURL() {
    return SITE_URL . SITE_DOCS . $this->getFile();
  }

  public function getPsuedoName() {
    return str_replace(' ','_',$this->getTitle() . '.' . $this->getExtension());
  }

  public function getFileLocation() {
    return SITE_ROOT . SITE_DOCS . $this->getFile();
  }

  public function showFile() {
    if (file_exists($this->getFileLocation())) {
      $ext = $this->getExtension();
      if (in_array($ext, array('jpg','jpeg','png','gif','tiff'))) {
        return '<img src="' . $this->getFileURL() . '" class="imgPreview">';
      } else if ($ext == 'pdf') {
        return '<object data="' . $this->getFileURL() . '#toolbar=0" type="application/pdf" width="100%" height="99%">
                  alt : <a href="' . $this->getFileURL() . '">' . $this->getPsuedoName() . '</a>
                </object>';
      } else if (in_array($ext, array('mp3', 'wav', 'ogg', 'aac', 'webm', 'flac'))) {
        if ($ext == 'mp3') { $ext = 'mpeg'; }
        return '<h2>' . $this->getPsuedoName() . '</h2>
                <audio controls controlslist="nodownload">
                  <source src="' . $this->getFileURL() . '" type="audio/' . $ext . '">
                  Your browser does not support HTML5 audio
                </audio>';
      } else if (in_array($ext, array('doc', 'docx', 'xls', 'odf', 'ods'))) {
        // Process docs and sheets
      }
      // File exists, but is not a supported filetype
      return '<h2>Error: Filetype not supported<h2>';
    }
    // File does not exist, return an error
    return '<h2>Error: File could not be found</h2>';
  }

}
