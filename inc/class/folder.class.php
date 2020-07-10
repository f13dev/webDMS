 <?php
 // block direct access
 if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
   header("Location: ../../");
 }
 Class folder {
   // Variables
   private $isSet;
   private $ID;
   private $title;
   private $category;
   private $description;

   /**
    * Constructor
    * Input of an array containing either:
    * 1. an ID key
    * 2. a title, category and description key
    * ID = numeric folder ID, title = string, category = nueric category ID, description = string
    */
   public function __construct($array) {
     $this->isSet = false;
     if (array_key_exists('ID', $array)) {
       return $this->get($array['ID']);
     } else if (array_key_exists('title',$array) && array_key_exists('category',$array) && array_key_exists('description', $array)) {
       return $this->set($array['title'], $array['category'], $array['description']);
     } else {
       return false;
     }
   }

   /**
    * Assigns an existinng folder to the object
    * ID = numeric identifier associated with the folders database row
    */
   public function get($id) {
     global $dbc;
     $statement = $dbc->prepare("SELECT ID, title, category, description FROM folders WHERE ID = ?");
     $statement->execute([$id]);
     $folder = $statement->fetch();

     if ($statement->rowCount() == 0) {
       //set false variabe
       $this->isSet = false;
     } else {
       // set folder variables
       $this->isSet = true;
       $this->ID = $folder['ID'];
       $this->title = $folder['title'];
       $this->category = $folder['category'];
       $this->description = $folder['description'];
     }
     return $this->isSet;
   }

   /**
    * Creates a enw file row in the databasee
    * 
    * Returns true if successful, otherwise false
    */
   public function set($title, $category, $description) {
      global $dbc;
      $statement = $dbc->prepare("INSERT INTO folders (title, category, description) VALUES (?,?,?)");
      $statement->execute([$title, $category,$description]);
      return $dbc->lastInsertId();
   }

   /**
    * Returns the category of the object
    */
   public function getCategory() {
     return $this->category;
   }

   /**
    * Updates the category of the object
    */
   public function setCategory($category) {
     global $dbc;
     $statement = $dbc->prepare("UPDATE folders SET category=? WHERE ID=?");
     return $statement->execute([$category, $this->getID()]);
   }

   /**
    * Sets the description of the object
    */
   public function setDescription($description) {
     global $dbc;
     $statement = $dbc->prepare("UPDATE folders SET description=? WHERE ID=?");
     return $statement->execute([$description, $this->getID()]);
   }

   /**
    * Sets the title of the object
    */
   public function setTitle($title) {
     global $dbc;
     $statement = $dbc->prepare("UPDATE folders SET title=? WHERE ID=?");
     return $statement->execute([$title, $this->getID()]);
   }

   /**
    * Returns the description of the object
    */
   public function getDescription() {
     return $this->description;
   }

   /**
    * Returns the numer of files associated with the folder object, this includes files in the recycling bin
    */
   public function getNumberFiles() {
     global $dbc;
     $statement=$dbc->prepare("SELECT count(*) FROM documents WHERE folder = ?");
     $statement->execute([$this->getID()]);
     return $statement->fetchColumn();
   }

   /**
    * Returns a HTML header for the folder object view
    */
   public function buildFolderHead() {
     global $uri;
     $return = '<table width="100%" style="border:1px solid black; margin-bottom:1em;">';
     if ($this->getDescription() != '') {
       $return .= '<tr><td colspan="3"><strong>Description: </strong>' . nl2br($this->getDescription()) . '</td></tr>';
     }
     $return .= '<tr>';
      $return .= '<td colspan="3" style="text-align:center">';
      if ($_SESSION['type'] <= PERM_DOC_CREATE ) {
        $return .= '<a class="href-button" href="' . $uri->newDocument($this->getID(), $this->getTitle()) . '">Upload document</a>';
      } else {
        $return .= '<strike>Upload document</strike>';
      }

      if ($_SESSION['type'] <= PERM_FOLDER_EDIT ) {
        $return .= '<a class="href-button" href="' . $uri->editFolder($this->getID(), $this->getTitle()) . '">Edit folder</a>';
      } else {
        $return .= '<strike>Edit folder</strike>';
      }

      if ($_SESSION['type'] <= PERM_FOLDER_EDIT ) {
        $return .= '<a class="href-button" href="' . $uri->deleteFolder($this->getID(), $this->getTitle()) . '" onclick="return confirm(\'Are you sure you want to delete the folder: ' . $this->getTitle() . '\')">Delete folder</a>';
      } else {
        $return .= '<strike>Delete folder</strike>';
      }
      $return .= '</td>';

     $return .= '</tr>';
     $return .= '</table>';
     return $return;
   }

   /**
    * Returns an array of document objects associated with the folder object
    */
   public function getDocuments($orderBy = 'document_date', $asc = 'false') {
     if ($asc == 'false') {
       $order = 'DESC';
     } else {
       $order = 'ASC';
     }
     $orders = array('document_date','title','upload_date');
     if (!in_array($orderBy,$orders)) { $orderBy = 'document_date'; }
     $orderBy = $orderBy . ' ' . $order;
     global $dbc;
     $statement = $dbc->prepare("SELECT ID FROM documents WHERE folder = ? AND recycle = 0 ORDER BY $orderBy");
     $statement->execute([$this->getID()]);
     return $statement->fetchAll();
   }

   /**
    * Returns true if a valid folder is set
    */
   public function isSet() {
     return $this->isSet;
   }

   /** 
    * Returns the title of the object
    */
   public function getTitle() {
     return $this->title;
   }

   /**
    * Returns the ID of the object
    */
   public function getID() {
     return $this->ID;
   }

   /**
    * Returns a HTML table showing the document objects associated with the folder object
    */
   public function buildDocumentTable($selected, $orderBy = 'document_date', $asc = 'false') {
     global $d,$title,$uri;

     $output  = '<table id="docTable" class="display" style="width:100%">';
        $output .= '<thead>';
            $output .= '<th>Title</th>';
            $output .= '<th>Date</th>';
            $output .= '<th>Filetype</th>';
            $output .= '<th>Uploaded</th>';
            if ($_SESSION['type'] <= PERM_DOC_EDIT) {
              $output .= '<th>Edit</th>';
            }
            if ($_SESSION['type'] <= PERM_DOC_DELETE) {
              $output .= '<th>Delete</th>';
            }
            $output .= '<th>Download</th>';
        $output .= '</thead>';
        $output .= '<tbody>';

     //$output  = '<table class="fileTable">';
     //$output .= '<tr class="thead">';
     //$output .= '<th>Title
     //              <a href="' .  $uri->document($this->getID(), $this->getTitle(),$d,$title,'title','true') . '"><i class="fa fa-angle-up"></i></a>
     //              <a href="' .  $uri->document($this->getID(), $this->getTitle(),$d,$title,'title','false') . '"><i class="fa fa-angle-down"></i></a>
     //           </th>';
     //$output .= '<th>Date
     //              <a href="' .  $uri->document($this->getID(), $this->getTitle(),$d,$title,'document_date','true') . '"><i class="fa fa-angle-up"></i></a>
     //              <a href="' .  $uri->document($this->getID(), $this->getTitle(),$d,$title,'document_date','false') . '"><i class="fa fa-angle-down"></i></a>
     //            </th>';
     //$output .= '<th>Filetype</th>';
     //$output .= '<th>Uploaded
     //              <a href="' .  $uri->document($this->getID(), $this->getTitle(),$d,$title,'upload_date','true') . '"><i class="fa fa-angle-up"></i></a>
     //              <a href="' .  $uri->document($this->getID(), $this->getTitle(),$d,$title,'upload_date','false') . '"><i class="fa fa-angle-down"></i></a>
     //            </th>';
     //if ($_SESSION['type'] <= PERM_DOC_EDIT) {
     //  $output .= '<th>Edit</th>';
     //}
     //if ($_SESSION['type'] <= PERM_DOC_DELETE) {
     //  $output .= '<th>Delete</th>';
     //}
     //$output .= '<th>Download</th>';
     //$output .= '</tr>';
     foreach ($this->getDocuments($orderBy, $asc) as $each) {
       $doc[$each['ID']] = new document(['ID'=>$each['ID']]);
       $sortDoc = new DateTime($doc[$each['ID']]->getDocDate());
       $sortUp  = new DateTime($doc[$each['ID']]->getUploadDate());

      $output .= '<tr>';
        $output .= '<td><a class="doc" href="' . $uri->downloadDocument($doc[$each['ID']]->showFile()) . '" target="_blank" data-rel="lightcase:collection" data-lc-caption="' . $doc[$each['ID']]->getTitle() . '">
                   ' . $doc[$each['ID']]->getTitle() . '
                   </a></td>';
        $output .= '<td data-sort="' . $sortDoc->format('Y-m-d') . '">' . $doc[$each['ID']]->getDocDate() . '</td>';
        $output .= '<td>' . $doc[$each['ID']]->getFileType() . '</td>';
        $output .= '<td data-sort="' . $sortUp->format('Y-m-d') . '">' . $doc[$each['ID']]->getUploadDate() . '</td>';
        if ($_SESSION['type'] <= PERM_DOC_EDIT) {
          $output .= '<td><a class="link" href="' . $uri->editDocument($doc[$each['ID']]->getID(),$doc[$each['ID']]->getTitle()) . '"><i class="fa fa-edit"></a></td>';
        }
        if ($_SESSION['type'] <= PERM_DOC_DELETE) {
          $output .= '<td><a class="link" href="' . $uri->recycleDocument($doc[$each['ID']]->getID()) . '" onclick="return confirm(\'Are you sure you wish to delete: ' . $doc[$each['ID']]->getTitle() . '\')"><i class="fa fa-minus-circle"></i></a></td>';
        }
        $output .= '<td><a class="link" download="' . $doc[$each['ID']]->getTitle() . '.' . $doc[$each['ID']]->getExtension() . '" href="' . $uri->downloadDocument($doc[$each['ID']]->getFile()) . '"><i class="fa fa-download"></i></a></td>';
      $output .= '</tr>'; 

       //if ($doc[$each['ID']]->getID() == $selected) {
       //  $output .= '<tr class="selected">';
       //} else {
       //  $output .= '<tr>';
       //}
       //$output .= '<td><a href="' . $uri->downloadDocument($doc[$each['ID']]->showFile()) . '" target="_blank" data-rel="lightcase:collection" data-lc-caption="' . $doc[$each['ID']]->getTitle() . '">
       //           ' . $doc[$each['ID']]->getTitle() . '
       //           </a></td>';
       //$output .= '<td>' . $doc[$each['ID']]->getDocDate() . '</td>';
       //$output .= '<td>' . $doc[$each['ID']]->getFileType() . '</td>';
       
       
       //$output .= '<td>' . $doc[$each['ID']]->getUploadDate() . '</td>';
       //if ($_SESSION['type'] <= PERM_DOC_EDIT) {
       //   $output .= '<td><a href="' . $uri->editDocument($doc[$each['ID']]->getID(),$doc[$each['ID']]->getTitle()) . '"><i class="fa fa-edit"></a></td>';
       //}
       //if ($_SESSION['type'] <= PERM_DOC_DELETE) {
       //   $output .= '<td><a href="' . $uri->recycleDocument($doc[$each['ID']]->getID()) . '" onclick="return confirm(\'Are you sure you wish to delete: ' . $doc[$each['ID']]->getTitle() . '\')"><i class="fa fa-minus-circle"></i></a></td>';
       //}
       //$output .= '<td><a download="' . $doc[$each['ID']]->getTitle() . '.' . $doc[$each['ID']]->getExtension() . '" href="' . $uri->downloadDocument($doc[$each['ID']]->getFile()) . '"><i class="fa fa-download"></i></a></td>';
       //$output .= '</tr>';
     }
     $output .= '</tbody>';
     $output .= '</table>';
     return $output;
   }

   public function unsetEntry() {
    global $dbc;
    $statement = $dbc->prepare("DELETE from folders WHERE ID = ?");
    return $statement->execute([$this->getID()]);
   }

 }
