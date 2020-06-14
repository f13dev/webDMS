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

  /**
   * Returns true if a file is set
   */
  public function isSet() {
    return $this->isSet;
  }

  /**
   * Returns the ID of the file
   */
  public function getID() {
    return $this->ID;
  }

  /**
   * Returns the title of the file
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * Returns the notes of the file
   */
  public function getNotes() {
    return $this->notes;
  }

  /**
   * Returns the ID of the folder the file is assoicated with
   */
  public function getFolder() {
    return $this->folder;
  }

  /**
   * Returns the title of the folder the file is associated with
   */
  public function getFolderTitle() {
    $folder = new folder($this->getFolder());
    return $folder->getTitle();
  }

  /**
   * Returns the date the file was uploaded
   */
  public function getUploadDate() {
    return date(DATE_FORMAT, strtotime($this->upload_date));
  }

  /**
   * Returns the date of the file
   */
  public function getDocDate() {
    return date(DATE_FORMAT, strtotime($this->document_date));
  }

  /**
   * Returns the filename of the file
   */
  public function getFile() {
    return $this->file;
  }

  /**
   * Returns the extension of the file
   */
  public function getExtension() {
    $explode = explode('.', $this->getFile());
    return strtolower(end($explode));
  }

  /**
   * Returns the absolute URL of the file, this will become a temporary URL for the file
   */
  public function getFileURL($asPDF = false) {
    $file = $this->getFile();
    if ($asPDF == true) {
      $file = $this->getFileAsPDF();
    }
    return SITE_URL . SITE_DOCS . $file;
  }

  /**
   * Returns the psuedo name of the file
   */
  public function getPsuedoName() {
    return str_replace(' ','_',$this->getTitle() . '.' . $this->getExtension());
  }

  /**
   * Returns the absolute OS path of the file
   */
  public function getFileLocation($asPDF = false) {
    $file = $this->getFile();
    if ($asPDF == true) {
      $file = $this->getFileAsPDF();
    }
    return SITE_ROOT . SITE_DOCS . $file;
  }

  /**
   * Returns the file name as a PDF
   */
  private function getFileAsPDF() {
    return str_replace($this->getExtension(), 'pdf', $this->getFile());
  }

  /**
   * Returns the HTML value to embed the file as a PDF
   */
  private function showPDF() {
    $file = str_replace($this->getExtension(), 'pdf', $this->getFile());
    $fileURL = SITE_URL.'inc/dms/fileScraper.php?file='.$file;
    return '<object data="' . $fileURL . '#toolbar=0" type="application/pdf" width="100%" height="99%">
      alt : <a href="' . $fileURL . '">' . $this->getPsuedoName() . '</a>
    </object>';

    return '<embed src="'.$fileURL.'" width="100%" height="100%" />';
  }

  /**
   * Returns the HTML value to embed the file as an image
   */
  private function showImage() {
    $fileURL = SITE_URL.'inc/dms/fileScraper.php?file='.$this->getFile();
    return '<img src="'.$fileURL.'" class="imgPreview">';
  }

  /**
   * Returns the HTML value to embed the file as audio
   */
  private function showAudio($type) {
    $fileURL = SITE_URL.'inc/dms/fileScraper.php?file='.$this->getFile();
    return '<h2>' . $this->getPsuedoName() . '</h2>
    <audio controls controlslist="nodownload">
      <source src="' . $fileURL . '" type="audio/' . $type . '">
      Your browser does not support HTML5 audio
    </audio>';
  }

  /**
   * Returns the HTML value to embed an office file as a PDF
   */
  private function showOfficeFile() {
    if (file_exists($this->getFileLocation(true)) || $this->generatePDF()) {
      return $this->showPDF();
    } else {
      return $this->showFileNotSupported();
    }
  }

  /**
   * Generate a PDF file from an existing office file
   */
  private function generatePDF() {
    $cmd = 'export HOME=/tmp && soffice --headless --convert-to pdf --outdir ' .  SITE_ROOT . SITE_DOCS . ' ' .  SITE_ROOT . SITE_DOCS . $this->getFile();
    exec($cmd);
    chmod(SITE_ROOT . SITE_DOCS . $this->getFile(true), 777);
    $cmd = 'chmod 0777 ' . SITE_ROOT . SITE_DOCS . $this->getFile(true);

    if (file_exists($this->getFileLocation(true))) {
    }
    return file_exists($this->getFileLocation(true));
  }

  /**
   * Returns a message for files which are not supported
   */
  private function showFileNotSupported() {
    return '<h2>Error: Filetype not supported<h2>';
  }

  /**
   * Returns an error message for files which do not exist
   */
  private function showFileNotExist() {
    return '<h2>Error: File could not be found</h2>';
  }

  /**
   * Returns HTML to embed a file, or an error message
   */
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
