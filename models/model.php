<?php
require_once __DIR__.'/../config/config.php';
class Model {
    protected mysqli $db;
    function __construct() {
        $this->db=db();
    }
}
