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

   public function getDocuments($orderBy = 'ID', $desc = true) {
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
 }
