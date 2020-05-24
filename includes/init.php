<?php

if (!defined('SECURE_BOOT')) exit();
session_start();

// Check if config file exists
if(!file_exists(__DIR__.'/config.php')) {
  echo "Brak pliku konfiguracyjnego, skopiuj zawartość pliku config.sample.php do pliku config.php utworzonego w folderze includes";
  exit();
}

// Load config
$config = require_once __DIR__.'/config.php';

// Load database connect functions
require_once __DIR__.'/database.php';

// Load functions
foreach ( array_diff( scandir( __DIR__.'/functions' ) , ['.','..']) as $i => $name) {
  require_once __DIR__.'/functions/'.$name;
}

// Load classes
foreach ( array_diff( scandir( __DIR__.'/classes' ) , ['.','..']) as $i => $name) {
  require_once __DIR__.'/classes/'.$name;
}

// Check if config file syntax is correct
$configErrors = verifyConfig($config);
if (!empty($configErrors)) {
  echo "Błąd w pliku konfiguracyjnym: <br />";

  foreach($configErrors as $error) {
    echo $error.'<br />';
  }
  exit;
}

// Create database connection
$db = create_database_connection( $config['db'] );

date_default_timezone_set('Europe/Warsaw');

// Authorize user
if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
  // Check if user exists with provided ID in database
  $userResult = $db->query(sprintf('SELECT users.id, users.email, users.firstname, users.lastname, roles.code as roleCode, roles.name as role FROM users INNER JOIN roles ON roles.id = users.role WHERE users.id = \'%s\'', $db->real_escape_string($_SESSION['user'])));

  if ($userResult->num_rows != 0) {
    // Authorize this user, he exists in database
    define('AUTHORIZED', true);

    // Now we need to get user permissions

    // Get user data
    $authorizedUser = $userResult->fetch_assoc();

    // Fetch all existing roles
    $userRole = $authorizedUser['roleCode'];

    $roles = $db->query("SELECT * FROM roles")->fetch_all(MYSQLI_ASSOC);
    $roles = array_map(function($role) {
      return $role['code'];
    }, $roles);

    $patientQuery = $db->query(sprintf("SELECT * FROM patients WHERE user = '%d'", $db->real_escape_string($_SESSION['user'])));
    $doctorQuery = $db->query(sprintf("SELECT * FROM doctors WHERE user = '%d'", $db->real_escape_string($_SESSION['user'])));

    $hasPatientInfo = $patientQuery->num_rows != 0;
    $hasDoctorInfo = $doctorQuery->num_rows != 0;

    // Define user permissions
    foreach($roles as $role) {
      $ruleName = 'IS_'.$role;

      if ($role == 'DOCTOR' || $role == 'PATIENT') {
        if ($role == 'PATIENT' && $hasPatientInfo) {
          define($ruleName, true);
          define('PATIENT_ID', $patientQuery->fetch_assoc()['id']);
        }
        else if ($role == 'PATIENT' && !defined($ruleName)) {
          define($ruleName, false);
        }

        if ($role == 'DOCTOR' && $hasDoctorInfo) {
          define($ruleName, true);
          define('DOCTOR_ID', $doctorQuery->fetch_assoc()['id']);
        }
        else if ($role == 'DOCTOR' && !defined($ruleName)) {
          define($ruleName, false);
        }
      }

      if ($role != 'DOCTOR' && $role != 'PATIENT') {
        if ($userRole == $role) {
          define($ruleName, true);
        }
        else if (!defined($ruleName)) {
          define($ruleName, false);
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

// Check if page needs user to be logged in. If user is not authorized and page needs it, then redirect him to main url.
if (defined('PAGE_NEEDS_AUTHORIZATION') && PAGE_NEEDS_AUTHORIZATION && !AUTHORIZED && basename($_SERVER['PHP_SELF']) !== 'index.php') {
  header("Location: {$config['site_url']}/auth.php");
  exit;
}
