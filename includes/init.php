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

if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
  $userResult = $db->query(sprintf('SELECT users.id, users.email, users.firstname, users.lastname, roles.name as role FROM users INNER JOIN roles ON roles.id = users.role WHERE users.id = \'%s\'', $db->real_escape_string($_SESSION['user'])));

  if ($userResult->num_rows != 0) {
    define('AUTHORIZED', true);

    $authorizedUser = $userResult->fetch_assoc();
  }
  else {
    define('AUTHORIZED', false);
  }
}
else {
  define('AUTHORIZED', false);
}

if (defined('PAGE_NEEDS_AUTHORIZATION') && PAGE_NEEDS_AUTHORIZATION && !AUTHORIZED && basename($_SERVER['PHP_SELF']) !== 'index.php') {
  header("Location: {$config['site_url']}/");
  exit;
}
