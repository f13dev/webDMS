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
        $statement = $dbc->prepare("SELECT ID, title FROM documents WHERE title LIKE ?");
        $statement->execute([$term]);
        $results = $statement->fetchAll();

        $this->resultCount = sizeof($results);

        $resultObject = [];

        foreach ($results as $result) {
            $resultObject[$result['ID']] = new Document($result['ID']);
        }

        $this->results = $resultObject;


    }


    public function getSearchResults() {
        return $this->results;
    }

    public function buildSearchResultsTable($selected) {
        $return = '<table class="fileTable">';
            $return .= '<tr>';
                $return .= '<th>Title</th>';
                $return .= '<th>Date</th>';
                $return .= '<th>File</th>';
                $return .= '<th>Uploaded</th>';
                $return .= '<th>Folder</th>';
                $return .= '<th>Download</th>';
            $return .= '</tr>';

            foreach ($this->getSearchResults() as $result) {
                if ($result->getID() == $selected) {
                    $return .= '<tr class="selected">';
                } else {
                    $return .= '<tr>';
                }
                    $return .= '<td><a href="' . searchDocument_uri($this->getTerm(), $result->getID(), $result->getTitle()) . '">'. $result->getTitle() . '</a></td>';
                    $return .= '<td>' . $result->getDocDate() . '</td>';
                    $return .= '<td>' . $result->getFile() . '</td>';
                    $return .= '<td>' . $result->getUploadDate() . '</td>';
                    $return .= '<td><a href="' . folder_uri($result->getFolder(), $result->getFolderTitle()) . '">' . $result->getFolderTitle() . '</a></td>';
                    $return .= '<td><a href="' . doc_download_uri($result->getID()) . '"<i class="fa fa-download"></i></a></td>';
                $return .= '</tr>';
            }
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