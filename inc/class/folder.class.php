 <?php
 Class folder {
   // Variables
   private $isSet;
   private $ID;
   private $title;
   private $category;
   private $description;

   public function __construct($id) {
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
   }

   public function getDocuments($orderBy = 'document_date', $desc = true) {
     if ($desc == true) {
       $order = 'DESC';
     } else {
       $order = 'ASC';
     }
     global $dbc;
     $statement = $dbc->prepare("SELECT ID FROM documents WHERE folder = ? ORDER BY ? $order");
     $statement->execute([$this->getID(), $orderBy]);
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

   public function buildDocumentTable($orderBy = 'document_date', $desc = true) {
     $output  = '<table class="fileTable">';
     $output .= '<tr>';
     $output .= '<th>Title
                   <a href="#"><i class="fa fa-angle-up"></i></a>
                   <a href="#"><i class="fa fa-angle-down"></i></a>
                 </th>';
     $output .= '<th>Date
                   <a href="#"><i class="fa fa-angle-up"></i></a>
                   <a href="#"><i class="fa fa-angle-down"></i></a>
                 </th>';
     $output .= '<th>File</th>';
     $output .= '<th>Uploaded
                   <a href="#"><i class="fa fa-angle-up"></i></a>
                   <a href="#"><i class="fa fa-angle-down"></i></a>
                 </th>';
     $output .= '<th>Edit</th>';
     $output .= '<th>Delete</th>';
     $output .= '<th>Download</th>';
     $output .= '</tr>';
     foreach ($this->getDocuments($orderBy, $desc) as $each) {
       $doc[$each['ID']] = new document($each['ID']);
       $output .= '<tr>';
       $output .= '<td><a href="' . document_uri($this->getID(), $this->getTitle(), $doc[$each['ID']]->getID(), $doc[$each['ID']]->getTitle()) . '">
                  ' . $doc[$each['ID']]->getTitle() . '
                  </a></td>';
       $output .= '<td>' . $doc[$each['ID']]->getDocDate() . '</td>';
       $output .= '<td>' . $doc[$each['ID']]->getFile() . '</td>';
       $output .= '<td>' . $doc[$each['ID']]->getUploadDate() . '</td>';
       $output .= '<td><a href="' . doc_edit_uri($doc[$each['ID']]->getID()) . '"><i class="fa fa-edit"></a></td>';
       $output .= '<td><a href="' . doc_del_uri($doc[$each['ID']]->getID()) . '"<i class="fa fa-minus-circle"></i></a></td>';
       $output .= '<td><a href="' . doc_download_uri($doc[$each['ID']]->getID()) . '"<i class="fa fa-download"></i></a></td>';
       $output .= '</tr>';
     }
     $output .= '</table>';
     return $output;
   }

 }
