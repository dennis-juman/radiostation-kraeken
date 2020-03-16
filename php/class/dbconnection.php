<?php
class dbconnection {
    private $dbtype;
    private $host;
    private $port;
    private $dbname;
    private $user;
    private $pass;

    function __construct(){
        $this->dbtype = 'mysql';
        $this->host = '127.0.0.1';
        $this->port = '3306';
        $this->dbname = 'kraeken'; 
        $this->user = 'root';
        $this->pass = '';
    }

    function connect(){
        try {
            $dbh = new PDO($this->dbtype . ':host=' . $this->host . ';port=' . $this->port . ';dbname='. $this->dbname, $this->user, $this->pass);
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        } return $dbh;
    }
}