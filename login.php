<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

require_once './includes/init.php';

// - - - - - - - - - - - - - - - - L O G I N - - - - - - - - - - - - - - - - - - - - - //

if (isset($_POST['action']) && $_POST['action'] == 'login') {

  if (isset($_SESSION['user']) && $_SESSION['user'] == 0) {
    header("Location: {$config['site_url']}/auth.php");
    exit();
  }

  $acceptedKeys = [
    'email',
    'password'
  ];

  foreach($acceptedKeys as $key) {
    if (!array_key_exists($key, $_POST)) {
      $_SESSION['auth-error'] = 'Niepoprawny login lub hasło.';
      header("Location: {$config['site_url']}/auth.php");
      exit();
    }
  }

  $email = $db->real_escape_string( htmlentities(strtolower($_POST['email']), ENT_QUOTES, "UTF-8") );
  $password = $db->real_escape_string( $_POST['password'] );

  $users = $db->query(sprintf("SELECT * FROM users WHERE email = '%s'",
    $email
  ));

  if ($users->num_rows == 0) {
    $_SESSION['auth-error'] = 'Niepoprawny login lub hasło.';
      header("Location: {$config['site_url']}/auth.php");
      exit();
  }
  else {
    $user = $users->fetch_assoc();

    if (password_verify($password, $user['password'])) {

      $_SESSION['user'] = (int) $user['id'];

      require_once 'includes/init.php';

      if (IS_PATIENT) {

        header("Location: {$config['site_url']}/new-reservation.php");
        exit();

      }
      elseif (IS_DOCTOR || IS_ADMIN || IS_EMPLOYEE) {

        header("Location: {$config['site_url']}/reservations.php");
        exit();

      }
    }
    else {
      $_SESSION['auth-error'] = 'Niepoprawny login lub hasło.';
      header("Location: {$config['site_url']}/auth.php");
      exit();
    }
  }
}

// - - - - - - - - - - - - - - - R E G I S T E R - - - - - - - - - - - - - - - - - - //

else if (isset($_POST['action']) && $_POST['action'] == 'register') {

  if (isset($_SESSION['user']) && $_SESSION['user'] == 0) {
    header("Location: {$config['site_url']}/auth.php");
    exit();
  }

  $acceptedKeys = [
    'email',
    'password',
    'name',
    'sname',
    'confirm-password'
  ];

  foreach($acceptedKeys as $key) {
    if (!array_key_exists($key, $_POST)) {
      $_SESSION['auth-error'] = 'Niepoprawne pola.';
      header("Location: {$config['site_url']}/auth.php");
      exit();
    }
  }

  $email = htmlentities(strtolower($_POST['email']), ENT_QUOTES, "UTF-8");
  $password = $_POST['password'] ;
  $repeatedPassword = $_POST['confirm-password'];
  $name = htmlentities($_POST ['name'], ENT_QUOTES, "UTF-8");
  $sname = htmlentities($_POST['sname'], ENT_QUOTES, "UTF-8");

  $ok = true;

  foreach($_POST as $key => $value) {
    if (empty($_POST[$key])) {
      $ok = false;
      $_SESSION["register-form-error-{$key}"] = 'Pole nie może być puste';
    }
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $ok = false;
    $_SESSION["register-form-error-email"] = "Niepoprawny email";
  }

  if (!preg_match("/^(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password)) {
    $ok = false;
    $_SESSION["register-form-error-password"] = "Niepoprawne hasło";
  }

  if ($password != $repeatedPassword) {
    $ok = false;
    $_SESSION["register-form-error-repeat-password"] = "Hasła muszą być takie same";
  }

  if (strlen($name) > 20){
    $ok = false;
    $_SESSION["register-form-error-name"] = "Zbyt duża ilość znaków (maksymalnie 20).";
  }

  if (strlen($sname) > 25){
    $ok = false;
    $_SESSION["register-form-error-sname"] = "Zbyt duża ilość znaków (maksymalnie 25).";
  }

  if (!$ok) {
    $_SESSION['auth-error'] = 'Popraw pola';
    header("Location: {$config['site_url']}/auth.php");
    exit();
  }
  else {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $createUserQuery = sprintf("INSERT INTO users (email, password, firstname, lastname, role) VALUES ('%s', '%s','%s','%s', '4')",
      $db->real_escape_string($email),
      $db->real_escape_string($hashedPassword),
      $db->real_escape_string($name),
      $db->real_escape_string($sname)
    );

    $response = $db->query($createUserQuery);
    if ($response) {
      $_SESSION['auth-success'] = 'Użytkownik pomyślnie zarejestrowany.';
      header("Location: {$config['site_url']}/auth.php");
      exit();
    }
    else {
      $_SESSION['auth-error'] = 'Błąd podczas tworzenia użytkownika w bazie danych.';
      header("Location: {$config['site_url']}/auth.php");
      exit();
    }
  }
}

//- - - - - - - - - - - - - - - - - - - - L O G O U T - - - - - - - - - - - - - - - - - - - - -//

else if (isset($_POST['action']) && $_POST['action'] == 'logout') {

  session_destroy();

  header("Location: {$config['site_url']}");
  exit();
}
else {
  header("Location: {$config['site_url']}/auth.php");
  exit();
}
