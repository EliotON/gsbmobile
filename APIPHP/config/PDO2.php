<?php
class PDO2 extends PDO {
    private static $instance = null;
    
    const DB_HOST = 'localhost';
    const DB_NAME = 'gsbvtt';
    const DB_USER = 'root';
    const DB_PASS = '';
    
    private function __construct() {
        try {
            $dsn = 'mysql:host=' . self::DB_HOST . ';dbname=' . self::DB_NAME . ';charset=utf8';
            parent::__construct($dsn, self::DB_USER, self::DB_PASS);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connexion échouée : ' . $e->getMessage();
            exit;
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new PDO2();
        }
        return self::$instance;
    }

    private function __clone() {}
}
