<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class PDO2 extends PDO {
    private static $instance = null;
    
    // Updated constants to use values from config.php
    private function __construct() {
        try {
            require_once __DIR__ . '/global/config.php';
            parent::__construct(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
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
