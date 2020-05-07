<?php

if (!defined('SECURE_BOOT')) exit();
session_start();

$_SESSION['user'] = 1;

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
  $userResult = $db->query(sprintf('SELECT users.id, users.email, users.firstname, users.lastname, roles.code as roleCode, roles.name as role FROM users INNER JOIN roles ON roles.id = users.role WHERE users.id = \'%s\'', $db->real_escape_string($_SESSION['user'])));

  if ($userResult->num_rows != 0) {
    define('AUTHORIZED', true);

    // Get user data
    $authorizedUser = $userResult->fetch_assoc();

    // Fetch all existing roles
    $userRole = $authorizedUser['roleCode'];

    $roles = $db->query("SELECT * FROM roles")->fetch_all(MYSQLI_ASSOC);
    $roles = array_map(function($role) {
      return $role['code'];
    }, $roles);

    $hasPatientInfo = $db->query(sprintf("SELECT * FROM patients WHERE user = '%d'", $db->real_escape_string($_SESSION['user'])))->num_rows != 0;
    $hasDoctorInfo = $db->query(sprintf("SELECT * FROM doctors WHERE user = '%d'", $db->real_escape_string($_SESSION['user'])))->num_rows != 0;

    // Define user permissions
    foreach($roles as $role) {

      if ($role == 'PATIENT' && $hasPatientInfo) {
        define('IS_'.$role, true);
      }
      else {
        define('IS_'.$role, false);
      }

      if ($role == 'DOCTOR' && $hasDoctorInfo) {
        define('IS_'.$role, true);
      }
      else {
        define('IS_'.$role, false);
      }

      if ($role != 'DOCTOR' && $role != 'PATIENT') {
        if ($userRole == $role) {
          define('IS_'.$role, true);
        }
        else {
          define('IS_'.$role, false);
        }
      }
    }
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
