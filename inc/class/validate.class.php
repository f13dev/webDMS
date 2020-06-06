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
        return preg_match($pattern, $input);
    }

    public function numeric($input) {
        return preg_match("/[0-9]+/", $input);
    }

    public function length($input, $length=32) {
        return strlen($input) <= $length;
    }

    public function hasUpper($input) {
        return preg_match("/[A-Z]/", $input);
    }

    public function hasLower($input) {
        return preg_match("/[a-z]/", $input);
    }

    public function hasSpecial($input) {
        return preg_match("/\W/", $input);
    }

    public function password($input) {
        return $this->length($input, 7) && $this->hasUpper($input) && $this->hasLower($input) && $this->hasSpecial($input);
    }

    public function title($input) {
        // alpha numeric, between 1 and 32 characters
        return $this->alphaNum($input) && !$this->length($input, 0) && $this->length($input);
    }

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
 }