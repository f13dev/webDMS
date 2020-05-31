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

  public function getUploadDate() {
    return date(DATE_FORMAT, strtotime($this->upload_date));
  }

  public function getDocDate() {
    return date(DATE_FORMAT, strtotime($this->document_date));
  }

  public function getFile() {
    return $this->file;
  }

}
