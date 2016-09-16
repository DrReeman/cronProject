<?php

namespace Connection;
use PDO;

class DBConnection extends PDO{

    private $DB_HOST = 'localhost';
    private $DB_NAME = 'CronProject';
    private $DB_USER = 'dmitrii';
    private $DB_PASS = 'reemanintegral1994';

    function __construct()
    {
        try {
            parent::__construct("mysql:host=$this->DB_HOST;dbname=$this->DB_NAME", $this->DB_USER, $this->DB_PASS);

        } catch (\pdoexception $e) {
            echo "database error: " . $e->getmessage();
            die();
        }
    }
}