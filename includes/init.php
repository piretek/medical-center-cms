<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);
session_start();

if(!file_exists('./includes/config.php')) {
  echo "Brak pliku konfiguracyjnego, skopiuj zawartość pliku config.sample.php do pliku config.php utworzonego w folderze includes";
  exit();
}

$config = require_once './includes/config.php';

require_once 'database.php';
require_once 'functions/verify-config.php';

$configErrors = verifyConfig($config);
if (!empty($configErrors)) {
  echo "Błąd w pliku konfiguracyjnym: <br />";

  foreach($configErrors as $error) {
    echo $error.'<br />';
  }
  exit;
}

$db = create_database_connection( $config['db'] );

date_default_timezone_set('Europe/Warsaw');
