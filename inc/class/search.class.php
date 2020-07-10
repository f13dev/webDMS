<?php
 // block direct access
 if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
   header("Location: ../../");
 }
 Class search {
    private $results;
    private $term;
    private $resultCount;

    public function __construct($term) {
        $this->term = $term;
        $term = '%' . str_replace('*','%',$term) . '%';
        global $dbc;
        $statement = $dbc->prepare("SELECT ID, title FROM documents WHERE title LIKE ? AND recycle = 0 ORDER BY document_date DESC");
        $statement->execute([$term]);
        $results = $statement->fetchAll();

        $this->resultCount = sizeof($results);

        $resultObject = [];

        foreach ($results as $result) {
            $resultObject[$result['ID']] = new Document(['ID'=>$result['ID']]);
        }
        $this->results = $resultObject;
    }


    public function getSearchResults() {
        return $this->results;
    }

    public function buildSearchResultsTable($selected) {
        global $uri;

        $return  = '<table id="docTable" class="display" style="width:100%">';
        $return .= '<thead>';
            $return .= '<th>Title</th>';
            $return .= '<th>Date</th>';
            $return .= '<th>Filetype</th>';
            $return .= '<th>Uploaded</th>';
            $return .= '<th>Folder</th>';
            $return .= '<th>Download</th>';
        $return .= '</thead>';
        $return .= '<tbody>';




        //$return = '<table class="fileTable">';
        //    $return .= '<tr>';
        //        $return .= '<th>Title</th>';
        //        $return .= '<th>Date</th>';
        //        $return .= '<th>File</th>';
        //        $return .= '<th>Uploaded</th>';
        //        $return .= '<th>Folder</th>';
        //        $return .= '<th>Download</th>';
        //    $return .= '</tr>';

            foreach ($this->getSearchResults() as $result) {

                $sortDoc = new DateTime($result->getDocDate());
                $sortUp  = new DateTime($result->getUploadDate());



                if ($result->getID() == $selected) {
                    $return .= '<tr class="selected">';
                } else {
                    $return .= '<tr>';
                }
                $return .= '<td><a class="doc" href="' . $uri->downloadDocument($result->showFile()) . '" target="_blank" data-rel="lightcase:group">' . $result->getTitle() . '</a></td>';
                $return .= '<td data-sort="' . $sortDoc->format('Y-m-d') . '">' . $result->getDocDate() . '</td>';
                    $return .= '<td>' . $result->getFile() . '</td>';
                    $return .= '<td data-sort="' . $sortUp->format('Y-m-d') . '">' . $result->getUploadDate() . '</td>';
                    $return .= '<td><a class="doc" href="' . $uri->folder($result->getFolder(), $result->getFolderTitle()) . '">' . $result->getFolderTitle() . '</a></td>';
                    $return .= '<td><a class="link" download="' . $result->getTitle() . '.' . $result->getExtension() . '" href="' . $uri->downloadDocument($result->getFile()) . '" download=""><i class="fa fa-download"></i></a></td>';
                $return .= '</tr>';
            }
        $return .= '<tbody>';
        $return .= '</table>';

        return $return;
    }

    public function getTerm() {
        return $this->term;
    }

    public function getResultCount() {
        return $this->resultCount;
    }
 }