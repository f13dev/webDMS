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

   public function set($title, $category, $description) {
      global $dbc;
      $statement = $dbc->prepare("INSERT INTO folders (title, category, description) VALUES (?,?,?)");
      $statement->execute([$title, $category,$description]);
      return $dbc->lastInsertId();
   }

   public function getCategory() {
     return $this->category;
   }

   public function setCategory($category) {
     global $dbc;
     $statement = $dbc->prepare("UPDATE folders SET category=? WHERE ID=?");
     return $statement->execute([$category, $this->getID()]);
   }

   public function setDescription($description) {
     global $dbc;
     $statement = $dbc->prepare("UPDATE folders SET description=? WHERE ID=?");
     return $statement->execute([$description, $this->getID()]);
   }

   public function setTitle($title) {
     global $dbc;
     $statement = $dbc->prepare("UPDATE folders SET title=? WHERE ID=?");
     return $statement->execute([$title, $this->getID()]);
   }

   public function getDescription() {
     return $this->description;
   }

   public function getNumberFiles() {
     global $dbc;
     $statement=$dbc->prepare("SELECT count(*) FROM documents WHERE folder = ?");
     $statement->execute([$this->getID()]);
     return $statement->fetchColumn();
   }

   public function buildFolderHead() {
     global $uri;
     $return = '<table width="100%">';
     if ($this->getDescription() != '') {
       $return .= '<tr><td colspan="3"><strong>Description: </strong>' . nl2br($this->getDescription()) . '</td></tr>';
     }
     $return .= '<tr>';
      $return .= '<td><a href="' . $uri->newDocument($this->getID(), $this->getTitle()) . '">Upload document</a></td>';
      $return .= '<td><a href="' . $uri->editFolder($this->getID(), $this->getTitle()) . '">Edit folder</a></td>';
      $return .= '<td><a href="' . $uri->deleteFolder($this->getID(), $this->getTitle()) . '">Delete folder</a></td>';
     $return .= '</tr>';
     $return .= '</table>';
     return $return;
   }

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
     $statement = $dbc->prepare("SELECT ID FROM documents WHERE folder = ? ORDER BY $orderBy");
     $statement->execute([$this->getID()]);
     return $statement->fetchAll();
   }

   public function isSet() {
     return $this->isSet;
   }

   public function getTitle() {
     return $this->title;
   }

   public function getID() {
     return $this->ID;
   }

   public function buildDocumentTable($selected, $orderBy = 'document_date', $asc = 'false') {
     global $d,$title,$uri;

     $output  = '<table class="fileTable">';
     $output .= '<tr class="thead">';
     $output .= '<th>Title
                   <a href="' .  $uri->document($this->getID(), $this->getTitle(),$d,$title,'title','true') . '"><i class="fa fa-angle-up"></i></a>
                   <a href="' .  $uri->document($this->getID(), $this->getTitle(),$d,$title,'title','false') . '"><i class="fa fa-angle-down"></i></a>
                 </th>';
     $output .= '<th>Date
                   <a href="' .  $uri->document($this->getID(), $this->getTitle(),$d,$title,'document_date','true') . '"><i class="fa fa-angle-up"></i></a>
                   <a href="' .  $uri->document($this->getID(), $this->getTitle(),$d,$title,'document_date','false') . '"><i class="fa fa-angle-down"></i></a>
                 </th>';
     $output .= '<th>File</th>';
     $output .= '<th>Uploaded
                   <a href="' .  $uri->document($this->getID(), $this->getTitle(),$d,$title,'upload_date','true') . '"><i class="fa fa-angle-up"></i></a>
                   <a href="' .  $uri->document($this->getID(), $this->getTitle(),$d,$title,'upload_date','false') . '"><i class="fa fa-angle-down"></i></a>
                 </th>';
     $output .= '<th>Edit</th>';
     $output .= '<th>Delete</th>';
     $output .= '<th>Download</th>';
     $output .= '</tr>';
     foreach ($this->getDocuments($orderBy, $asc) as $each) {
       $doc[$each['ID']] = new document(['ID'=>$each['ID']]);
       if ($doc[$each['ID']]->getID() == $selected) {
         $output .= '<tr class="selected">';
       } else {
         $output .= '<tr>';
       }
       $output .= '<td><a href="' . $uri->document($this->getID(), $this->getTitle(), $doc[$each['ID']]->getID(), $doc[$each['ID']]->getTitle(), $orderBy, $asc) . '">
                  ' . $doc[$each['ID']]->getTitle() . '
                  </a></td>';
       $output .= '<td>' . $doc[$each['ID']]->getDocDate() . '</td>';
       $output .= '<td>' . $doc[$each['ID']]->getFile() . '</td>';
       $output .= '<td>' . $doc[$each['ID']]->getUploadDate() . '</td>';
       $output .= '<td><a href="' . $uri->editDocument($doc[$each['ID']]->getID()) . '"><i class="fa fa-edit"></a></td>';
       $output .= '<td><a href="' . $uri->deleteDocument($doc[$each['ID']]->getID()) . '"<i class="fa fa-minus-circle"></i></a></td>';
       $output .= '<td><a download="' . $doc[$each['ID']]->getTitle() . '.' . $doc[$each['ID']]->getExtension() . '" href="' . $uri->downloadDocument($doc[$each['ID']]->getFile()) . '"><i class="fa fa-download"></i></a></td>';
       $output .= '</tr>';
     }
     $output .= '</table>';
     return $output;
   }

 }
