<?php 
 // block direct access
 if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    header("Location: ../../");
  }

  
Class recycle {

    /**
     * Returns the numer of documents in the recycling bin
     */
    public function getCount($folder = '%') {
        global $dbc;
        $statement = $dbc->prepare("SELECT count(*) FROM documents WHERE recycle = 1 AND folder LIKE ?");
        $statement->execute([$folder]);
        return $statement->fetchColumn();
    }

    public function getDocuments() {
        global $dbc;
        $statement = $dbc->prepare("SELECT ID FROM documents WHERE recycle = 1");
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Returns a HTML table for the documents in the recycling bin
     */
    public function buildRecycleTable($selected = -1) {
        global $d,$title,$uri;
        $output  = '<table class="fileTable">';
        $output .= '<tr class="thead">';
        $output .= '<th>Title</th>';
        $output .= '<th>FileType</th>';
        $output .= '<th>Recycled</th>';
        $output .= '<th>Folder</th>';
        $output .= '<th>Delete</th>';
        $output .= '<th>Download</th>';
        $output .= '<th>Restore</th>';
        $output .= '</tr>';
        foreach ($this->getDocuments() as $each) {
          $doc[$each['ID']] = new document(['ID'=>$each['ID']]);
          if ($doc[$each['ID']]->getID() == $selected) {
            $output .= '<tr class="selected">';
          } else {
            $output .= '<tr>';
          }
          $output .= '<td><a href="' . $uri->recycleBinDocument($doc[$each['ID']]->getID(), $doc[$each['ID']]->getTitle()) . '">
                     ' . $doc[$each['ID']]->getTitle() . '
                     </a></td>';
          $output .= '<td>' . $doc[$each['ID']]->getFileType() . '</td>';
          
          
          $output .= '<td>' . $doc[$each['ID']]->getRecycleDate() . '</td>';
          $output .= '<td><a href="' . $uri->folder($doc[$each['ID']]->getFolder(),$doc[$each['ID']]->getFolderTitle()) . '">' . $doc[$each['ID']]->getFolderTitle() . '</a></td>';
          $output .= '<td><a href="' . $uri->deleteDocument($doc[$each['ID']]->getID()) . '" onclick="return confirm(\'Proceeding will permanently delete the file: ' . $doc[$each['ID']]->getTitle() . '\')"><i class="fa fa-minus-circle"></i></a></td>';
          $output .= '<td><a download="' . $doc[$each['ID']]->getTitle() . '.' . $doc[$each['ID']]->getExtension() . '" href="' . $uri->downloadDocument($doc[$each['ID']]->getFile()) . '"><i class="fa fa-download"></i></a></td>';
          $output .= '<td><a href="' . $uri->restoreDocument($doc[$each['ID']]->getID()) . '"><i class="fa fa-undo"></i></a></td>';
          $output .= '</tr>';
        }
        $output .= '</table>';
        return $output;   
    }
}