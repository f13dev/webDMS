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

  public function getFileURL($asPDF = false) {
    $file = $this->getFile();
    if ($asPDF == true) {
      $file = $this->getFileAsPDF();
    }
    return SITE_URL . SITE_DOCS . $file;
  }

  public function getPsuedoName() {
    return str_replace(' ','_',$this->getTitle() . '.' . $this->getExtension());
  }

  public function getFileLocation($asPDF = false) {
    $file = $this->getFile();
    if ($asPDF == true) {
      $file = $this->getFileAsPDF();
    }
    return SITE_ROOT . SITE_DOCS . $file;
  }

  private function getFileAsPDF() {
    return str_replace($this->getExtension(), 'pdf', $this->getFile());
  }

  private function showPDF() {
    return '<object data="' . $this->getFileURL(true) . '#toolbar=0" type="application/pdf" width="100%" height="99%">
      alt : <a href="' . $this->getFileURL(true) . '">' . $this->getPsuedoName() . '</a>
    </object>';
  }

  private function showImage() {
    return '<img src="' . $this->getFileURL() . '" class="imgPreview">';
  }

  private function showAudio($type) {
    return '<h2>' . $this->getPsuedoName() . '</h2>
      <audio controls controlslist="nodownload">
        <source src="' . $this->getFileURL() . '" type="audio/' . $type . '">
        Your browser does not support HTML5 audio
      </audio>';
  }

  private function showOfficeFile() {
    if (file_exists($this->getFileLocation(true)) || $this->generatePDF()) {
      return $this->showPDF();
    } else {
      return $this->showFileNotSupported();
    }
  }

  private function generatePDF() {
    $cmd = 'export HOME=/tmp && soffice --headless --convert-to pdf --outdir ' .  SITE_ROOT . SITE_DOCS . ' ' .  SITE_ROOT . SITE_DOCS . $this->getFile();
    exec($cmd);
    if (file_exists($this->getFileLocation(true))) {
    }
    return file_exists($this->getFileLocation(true));
  }

  private function showFileNotSupported() {
    return '<h2>Error: Filetype not supported<h2>';
  }

  private function showFileNotExist() {
    return '<h2>Error: File could not be found</h2>';
  }

  public function showFile() {
    if (file_exists($this->getFileLocation())) {
      $ext = $this->getExtension();
      if (in_array($ext, array('jpg','jpeg','png','gif','tiff'))) {
        return $this->showImage();
      } else if ($ext == 'pdf') {
        return $this->showPDF();
      } else if (in_array($ext, array('mp3', 'wav', 'ogg', 'aac', 'webm', 'flac'))) {
        if ($ext == 'mp3') { $ext = 'mpeg'; }
        return $this->showAudio($ext);
      } else if (in_array($ext, array('doc', 'docx', 'xls', 'xlsx', 'odf', 'ods'))) {
        return $this->showOfficeFile();
      }
      return $this->showFileNotSupported();
    }
    return $this->showFileNotExist();
  }

}
