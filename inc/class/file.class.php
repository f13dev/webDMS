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
  private $recycle;
  private $recycleDate;

  /**
   * Constructor
   * Input of an array containing either:
   * 1. an ID key
   * 2. a title, folder, document_date and file key 
   * ID = numeric, title = string, document_date = date, file = array
   */
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

  /**
   * Assigns an existing file to the document object
   * ID = numeric identifier of a database row
   */
  public function get($id) {
    global $dbc;
    $statement = $dbc->prepare("SELECT ID, title, notes, folder, upload_date, document_date, file, recycle, recycledate FROM documents WHERE ID = ?");
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
      $this->recycle = $document['recycle'];
      $this->recycleDate = $document['recycledate'];
    }
    return $this->isSet;
  }

  /**
   * Uploads a file to the documents directory and enters a row in the database to reflect the new document 
   * title = string, folder = numeric ID of a folder, description = string, date = date, file = array
   * 
   * Returns false if the database or file upload failed, returns newly created ID if successful
   */
  public function set($title,$folder,$description,$date,$file) {
    global $dbc, $security;
    $ext = explode('.',$file['file']['name']);
    $ext = end($ext);
    $filename = $security->generateFileName() . '.' . $ext;
    $today = date('Y-m-d');
    if ($this->setFile($file['file'],$filename)) {
      $statement = $dbc->prepare("INSERT INTO documents (title,notes,folder,upload_date,document_date,file) VALUES (?,?,?,?,?,?)");
      $statement->execute([$title,$description,$folder,$today,$date,$filename]);
      if (in_array($ext, FILE_TYPES['doc']) || in_array($ext, FILE_TYPES['sheet'])) {
        $this->file = $filename;
        $this->generatePDF();
      }
      return $dbc->lastInsertId();
    } else {
      return false;
    }
  }

  public function setRecycle($bool) {
    global $dbc;
    $today = date('Y-m-d');
    if ($bool) {
      // Set as true
      $statement = $dbc->prepare("UPDATE documents SET recycle = 1, recycledate = ? WHERE ID = ?");
      return $statement->execute([$today,$this->getID()]);
    } else {
      // Set as false 
      $statement = $dbc->prepare("UPDATE documents SET recycle = 0, recycledate = null WHERE ID = ?");
      return $statement->execute([$this->getID()]);
    }
  }

  public function setTitle($title) {
    global $dbc;
    $statement = $dbc->prepare("UPDATE documents SET title = ? WHERE ID = ?");
    return $statement->execute([$title,$this->getID()]);
    
  }

  public function setDocDate($date) {
    global $dbc;
    $statement = $dbc->prepare("UPDATE documents SET document_date = ? WHERE ID = ?");
    return $statement->execute([$date,$this->getID()]);
  }

  public function setNotes($notes) {
    global $dbc;
    $statement = $dbc->prepare("UPDATE documents SET notes = ? WHERE ID = ?");
    return $statement->execute([$notes,$this->getID()]);
  }

  public function setFolder($folder) {
    global $dbc;
    $statement = $dbc->prepare("UPDATE documents SET folder = ? WHERE ID = ?");
    return $statement->execute([$folder,$this->getID()]);
  }

  public function getRecycleDate($format = DATE_FORMAT) {
    return date($format, strtotime($this->recycleDate));
  }
  /**
   * Uploads and renames a file to associated with the document object 
   * file = array(tmp_name), filename = new unique filename
   * 
   * returns true if successful, otherwise false
   */
  public function setFile($file,$filename) {
    $target = SITE_DOCS . $filename;
    return (move_uploaded_file($file['tmp_name'], str_replace("'","",$target)));
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
    if ($this->upload_date == '0000-00-00') {
      return date(DATE_FORMAT, strtotime('1970-01-01'));
    }
    return date(DATE_FORMAT, strtotime($this->upload_date));
  }

  /**
   * Returns the date of the file
   */
  public function getDocDate($format = DATE_FORMAT) {
    return date($format, strtotime($this->document_date));
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

  public function getFileType() {
    if (strtolower($this->getExtension()) == 'pdf') {
      return '<i class="fa fa-file-pdf"></i> PDF';
    } else if (in_array(strtolower($this->getExtension()),FILE_TYPES['doc'])) {
      return '<i class="fa fa-file-word"></i> Document';
    } else if (in_array(strtolower($this->getExtension()),FILE_TYPES['sheet'])) {
      return '<i class="fa fa-file-excel"></i> Sheet';
    } else if (in_array(strtolower($this->getExtension()),FILE_TYPES['image'])) {
      return '<i class="fa fa-image"></i> Image';
    } else if (in_array(strtolower($this->getExtension()),FILE_TYPES['audio'])) {
      return '<i class="fa fa-file-audio"></i> Audio';
    } else {
      return '<i class="fa fa-file"></i> Unknown';
    }
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
    // If extension in array FILE_TYPES['doc] or sheet return pdf, else return original
    return str_replace($this->getExtension(), 'pdf', $this->getFile());
  }

  /**
   * Returns the HTML value to embed the file as a PDF
   */
  private function showPDF() {
    return '<object data="' . $this->getFilePath(true) . '#toolbar=0" type="application/pdf" width="100%" height="100%" style="position:absolute">
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
    $cmd = 'export HOME=/tmp && soffice --headless --invisible --nolockcheck --convert-to pdf --outdir ' . SITE_DOCS . ' ' . SITE_DOCS . $this->getFile();
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
  public function showFile1() {
    if (file_exists($this->getFileLocation())) {
      $ext = $this->getExtension();
      if (in_array($ext, FILE_TYPES['image'])) {
        return $this->showImage();
      } else if ($ext == 'pdf') {
        return $this->showPDF();
      } else if (in_array($ext, FILE_TYPES['audio'])) {
        if ($ext == 'mp3') { $ext = 'mpeg'; }
        return $this->showAudio($ext);
      } else if (in_array($ext, FILE_TYPES['doc']) || in_array($ext,FILE_TYPES['sheet'])) {
        return $this->showOfficeFile();
      }
      return $this->showFileNotSupported();
    }
    return $this->showFileNotExist();
  }

  /**
   * Returns a file to be shown in a lightbox
   */
  public function showFile() {
    if (file_exists($this->getFileLocation())) {
      $ext = $this->getExtension();
      if (in_array($ext, FILE_TYPES['image'])) {
        return $this->getFile();
      } else if ($ext == 'pdf') {
        return $this->getFile();
      } else if (in_array($ext, FILE_TYPES['audio'])) {
        if ($ext == 'mp3') { $ext = 'mpeg'; }
        return $this->getFile();
      } else if (in_array($ext, FILE_TYPES['doc']) || in_array($ext,FILE_TYPES['sheet'])) {
        return $this->getFileAsPDF();
      }
    }
  }

  /**
   * Deletes the associated files from the document folder
   */
  public function unsetFile() {
    $file = SITE_DOCS . $this->getFile();
    $pdf = SITE_DOCS . $this->getFileAsPDF(); // some files have a PDF version 
    if (file_exists($pdf)) {
      if (!unlink($pdf)) {
        return false;
      }
    }
    if (file_exists($file)) {
      if (!unlink($file)) {
        return false;
      }
    }
    return true;
  }

  /**
   * Deletes the associated database entry for the file
   */
  public function unsetEntry() {
    global $dbc;
    $statement = $dbc->prepare("DELETE from documents WHERE ID = ?");
    return $statement->execute([$this->getID()]);
  }

}
