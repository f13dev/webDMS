<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}

Class users {

    public function getAll($orderby = 'ID', $order = 'ASC') {
        global $dbc;
        if ($order != 'ASC') $order = 'DESC';
        $statement = $dbc->prepare("SELECT ID,first_name,last_name,email,type FROM users ORDER BY ? $order");
        $statement->execute([$orderby]);
        return $statement->fetchAll();
    }

    public function getAllTable($orderby = 'ID', $order = 'ASC') {
        global $security, $uri;
        $users = $this->getAll($orderby,$order);
        // Table header

        $return  = '<table id="docTable" class="display" style="width:100%">';
        $return .= '<thead>';
            $return .= '<th>ID</th>';
            $return .= '<th>Name</th>';
            $return .= '<th>Emal</th>';
            $return .= '<th>Type</th>';
            if ($_SESSION['type'] <= PERM_USER_EDIT) {
                $return .= '<th>Edit</th>';
            }
            if ($_SESSION['type'] <= PERM_USER_DELETE) {
                $return .= '<th>Delete</th>';
            }
        $return .= '</thead>';
        $return .= '<tbody>';


        //$return = '<table class="fileTable">';
        //$return .= '<tr class="thead">';
        //$return .= '<th>ID</th>';
        //$return .= '<th>Name</th>';
        //$return .= '<th>Email</th>';
        //$return .= '<th>Type</th>';
        //if ($_SESSION['type'] <= PERM_USER_EDIT) {
        //    $return .= '<th>Edit</th>';
        //}
        //if ($_SESSION['type'] <= PERM_USER_DELETE) {
        //    $return .= '<th>Delete</th>';
        //}
        //$return .= '</tr>';
        foreach ($users as $user) {
            $return .= '<tr>';
            $return .= '<td>' . $user['ID'] . '</td>';
            $return .= '<td>' . $user['first_name'] . ' ' . $user['last_name'] . '</td>';
            $return .= '<td>' . $security->revert_secure($user['email']) . '</td>';
            $return .= '<td>' . USER_TYPES[$user['type']] . '</td>';
            if ($_SESSION['type'] <= PERM_USER_EDIT) {
                $return .= '<td><a class="link" href="' . $uri->user($user['ID'], $user['first_name'] . ' ' . $user['last_name']) . '"><i class="fa fa-edit"></i></a></td>';
            }
            if ($_SESSION['type'] <= PERM_USER_DELETE) {
                if ($user['ID'] == 1) {
                    $return .= '<td><a class="link inactive"><i class="fa fa-trash"></i></a></td>';
                } else {
                    $return .= '<td><a class="link" href="' . $uri->userDelete($user['ID']) . '" onclick="return confirm(\'Are you sure you want to delete this user account\')"><i class="fa fa-trash"></i></a></td>';
                }
            }
            $return .= '</tr>';
        }
        $return .= '</tbody>';  
        $return .= '</table>';
        return $return;
    }
}