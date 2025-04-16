<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Identifiants pour la base de données. Nécessaires à PDO2.
define('SQL_DSN','mysql:dbname=GSBVTTMobile;host=localhost');
define('SQL_USERNAME', 'login4242');
define('SQL_PASSWORD', 'wYQrXfJEPQMnxMl');

define('SQL_OPTIONS', 'array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")');

define('CHEMIN_MODELE', 'modeles/');
define('CHEMIN_LIB',    'libs/');
define('CHEMIN_ENTITY',    'entity/');


