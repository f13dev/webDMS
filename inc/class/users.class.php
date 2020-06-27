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
        $return = '<table class="fileTable">';
        $return .= '<tr class="thead">';
        $return .= '<th>ID</th>';
        $return .= '<th>Name</th>';
        $return .= '<th>Email</th>';
        $return .= '<th>Type</th>';
        if ($_SESSION['type'] <= PERM_USER_EDIT) {
            $return .= '<th>Edit</th>';
        }
        if ($_SESSION['type'] <= PERM_USER_DELETE) {
            $return .= '<th>Delete</th>';
        }
        $return .= '</tr>';
        foreach ($users as $user) {
            $return .= '<tr>';
            $return .= '<td>' . $user['ID'] . '</td>';
            $return .= '<td>' . $user['first_name'] . ' ' . $user['last_name'] . '</td>';
            $return .= '<td>' . $security->revert_secure($user['email']) . '</td>';
            switch ($user['type']) {
                case 0:
                    $type = 'Super admin';
                    break;
                case 1:
                    $type = 'Admin';
                    break;
                case 2:
                    $type = 'Manager';
                    break;
                case 3:
                    $type = 'Uploader';
                    break;
                case 4:
                    $type = 'Read only';
                    break;
            }
            $return .= '<td>' . $type . '</td>';
            if ($_SESSION['type'] <= PERM_USER_EDIT) {
                $return .= '<td><a href="' . $uri->user($user['ID']) . '"><i class="fa fa-edit"></i></a></td>';
            }
            if ($_SESSION['type'] <= PERM_USER_DELETE) {
                $return .= '<td><i class="fa fa-trash"></i></td>';
            }
            $return .= '</tr>';
        }
        $return .= '</table>';
        return $return;
    }
}