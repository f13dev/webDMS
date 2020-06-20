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

  public function __construct($array) {
    $this->isSet = false;
    if (array_key_exists('ID',$array)) {
      return $this->get($array['ID']);
    } else if (array_key_exists('title',$array) && array_key_exists('folder',$array) && array_key_exists('document_date',$array) && array_key_exists('file',$array)) {
      return $this->set($array['title'],$array['folder'],$array['description'],$array['document_date'],$array['file']);
    } else {
      return false;
    }
  }

  public function get($id) {
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
    return $this->isSet;
  }

  public function set($title,$folder,$description,$date,$file) {
    global $dbc;
    if ($this->setFile($file)) {
      $statement = $dbc->prepare("INSERT INTO documents (title,folder,description,document_date,upload_date,file) VALUES (?,?,?,?,?,?)");
      $statement->execute([$title,$folder,$decription,$date,$upload_date,$file]);
      return $dbc->lastInsertId();
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
    $folder = new folder(['ID' => $this->getFolder()]);
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
  public function getFilePath($asPDF = false) {
    global $uri;
    $file = $this->getFile();
    if ($asPDF == true) {
      $file = $this->getFileAsPDF();
    }
    return $uri->downloadDocument($file);
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
    return SITE_DOCS . $file;
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
    return '<object data="' . $this->getFilePath(true) . '#toolbar=0" type="application/pdf" width="100%" height="99%">
      There was an error displaying this PDF.
    </object>';
  }

  /**
   * Returns the HTML value to embed the file as an image
   */
  private function showImage() {
    return '<img src="'.$this->getFilePath().'" class="imgPreview">';
  }

  /**
   * Returns the HTML value to embed the file as audio
   */
  private function showAudio($type) {
    return '<h2>' . $this->getPsuedoName() . '</h2>
    <audio controls controlslist="nodownload">
      <source src="' . $this->getFilePath() . '" type="audio/' . $type . '">
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
    $cmd = 'export HOME=/tmp && soffice --headless --convert-to pdf --outdir ' . SITE_DOCS . ' ' . SITE_DOCS . $this->getFile();
    exec($cmd);
    chmod(SITE_DOCS . $this->getFile(true), 777);
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
