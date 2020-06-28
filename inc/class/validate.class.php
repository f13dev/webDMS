<?php
 // block direct access
 if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
   header("Location: ../../");
 }
 Class Validate {
    /**
     * alpha, numeric, space, underscore and hyphen
     */
    public function alphaNum($input) {
        $pattern = '^[a-zA-Z]([\w -]*[a-zA-Z])?$^';
        return preg_match("/[a-zA-Z0-9]+/", $input);
    }

    /**
     * Pure numeric
     */
    public function numeric($input) {
        return preg_match("/[0-9]+/", $input);
    }

    /**
     * Check max length 
     */
    public function length($input, $length=32) {
        return (strlen($input) >= $length);
    }

    /**
     * Check the input has at least 1 upper case character
     */
    public function hasUpper($input) {
        return preg_match("/[A-Z]/", $input);
    }

    /**
     * Check the input has at least 1 lower case character
     */
    public function hasLower($input) {
        return preg_match("/[a-z]/", $input);
    }

    /**
     * Check the input has at least one special (non alpha numeric) character
     */
    public function hasSpecial($input) {
        return preg_match("/\W/", $input);
    }

    /**
     * Validate the strength of a password
     */
    public function password($input) {
        return $this->length($input, 7) && $this->hasUpper($input) && $this->hasLower($input) && $this->hasSpecial($input);
    }

    /**
     * Check a title string is acceptable
     */
    public function title($input) {
        // alpha numeric, between 1 and 32 characters
        return $this->alphaNum($input) && !$this->length($input, 0) && $this->length($input);
    }

    /**
     * Check if a titile already exists
    */
    public function titleExists($input) {
        global $dbc;
        $statement = $dbc->prepare("SELECT name FROM categories");
        $statement->execute();
        $results = $statement->fetchAll();
        foreach ($results as $result) {
            if ($input == $result['name']) {
                return false;
            }
        }
        return true;
    }

    public function date($input) {
        return true;
    }

    /**
     * Return true if a valid file extension is provided
     */
    public function file($input) {
        $ext = explode('.',$input);
        $ext = end($ext);
        $ext = strtolower($ext);
        $return = false;
        if ($ext == 'pdf' || 
            in_array($ext,FILE_TYPES['doc']) || 
            in_array($ext,FILE_TYPES['spread']) || 
            in_array($ext,FILE_TYPES['audio']) || 
            in_array($ext,FILE_TYPES['image'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return true if a valid email address is provided 
     */
    public function email($input) {
        return filter_var($input, FILTER_VALIDATE_EMAIL);
    }

    public function emailUnique($input) {
        global $dbc,$security;
        $statement = $dbc->prepare("SELECT email FROM users WHERE email = ?");
        $statement->execute([$security->make_secure($input)]);
        if ($statement->rowCount() != 0) {
            return false;
        } else {
            return true;
        }
        $count = $del->rowCount();
    }

    public function userType($type) {
        return array_key_exists($type,USER_TYPES);
    }
 }