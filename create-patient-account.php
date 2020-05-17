<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'Stwórz konto pacjenta');
define('PAGE_NEEDS_AUTHORIZATION', true);

require_once "includes/init.php";

if (IS_PATIENT) {
  header("Location: {$config['site-url']}/user-reservations.php");
  exit;
}

if (isset($_POST['type']) && $_POST['type'] == 'create-patient') {

  $ok = true;

  $requiredFields = [
    'pesel',
    'phone',
    'street',
    'house_no',
    'city'
  ];

  foreach($requiredFields as $field) {
    if (!array_key_exists($field, $_POST) || empty($_POST[$field])) {
      $ok = false;
      $_SESSION['create-patient-account-create-patient-form-error-'.$field] = 'To pole nie może być puste.';
    }
  }

  $post = [];
  foreach ($_POST as $key => $value) {
    $post[$key] = htmlentities($value, ENT_QUOTES, "UTF-8");
  }

  try {
    $pesel = new PESEL($_POST['pesel']);
  }
  catch(PESEL_Exception $peselErr) {
    $ok = false;
    $_SESSION['create-patient-account-create-patient-form-error-pesel'] = $peselErr->getMessage();
  }

  if (strlen($post['phone']) != 9 || !is_numeric($post['phone'])) {
    $ok = false;
    $_SESSION['create-patient-account-create-patient-form-error-phone'] = 'Niepoprawny format numeru telefonu';
  }

  if (strlen($post['street']) > 30) {
    $ok = false;
    $_SESSION['create-patient-account-create-patient-form-error-street'] = 'Ulica może mieć maks. 30 znaków.';
  }

  if (strlen($post['house_no']) > 10) {
    $ok = false;
    $_SESSION['create-patient-account-create-patient-form-error-street'] = 'Numer domu i mieszkania może mieć maks. 10 znaków.';
  }

  if (strlen($post['city']) > 20) {
    $ok = false;
    $_SESSION['create-patient-account-create-patient-form-error-city'] = 'Miasto może mieć maks. 20 znaków.';
  }

  if (!preg_match('/^[0-9]{2}\-[0-9]{3}$/', $post['postcode'])) {
    $ok = false;
    $_SESSION['create-patient-account-create-patient-form-error-postcode'] = 'Kod musi być w formacie XX-XXX.';
  }

  if ($ok) {
    $insertQuery = sprintf("INSERT INTO patients VALUES (NULL, '%d', '%s', '%s', '%s', '%s', '%s', '%s')",
      $_SESSION['user'],
      $db->real_escape_string($pesel->get()),
      $db->real_escape_string($post['phone']),
      $db->real_escape_string($post['street']),
      $db->real_escape_string($post['house_no']),
      $db->real_escape_string($post['city']),
      $db->real_escape_string($post['postcode']),
    );

    $successful = $db->query($insertQuery);

    if ($successful) {
      $_SESSION['create-patient-success'] = 'Konto pacjenta utworzone! Od teraz możesz zarezerwować wizytę u naszych lekarzy.';
      header("Location: {$config['site_url']}/user-reservations.php");
      exit;
    }
    else {
      $_SESSION['create-patient-error'] = 'Błąd zapytania do bazy danych. Skontaktuj się z administratorem.';
      header("Location: {$config['site_url']}/create-patient-account.php");
      exit;
    }
  }
  else {
    $_SESSION['create-patient-error'] = 'Popraw wszystkie pola.';
    header("Location: {$config['site_url']}/create-patient-account.php");
    exit;
  }

}

include_once "views/header.php"; ?>

<main>
  <?php include_once 'views/navs/dashboard-nav.php' ?>

  <div class='paper'>
    <h1 class='paper-title'>Stwórz konto pacjenta</h1>
    <p>Prosimy o wypełnienie danych dot. miejsca zamieszkania, nr PESEL i danych kontaktowych. Przetwarzanie tych danych osobowych jest wymagane do realizowania Pana/Pani wizyt w naszej przychodni.</p>

    <?php if (isset($_SESSION['create-patient-error'])) : ?>
      <span class='error'>Błąd: <?= $_SESSION['create-patient-error'] ?></span>
      <?php unset($_SESSION['create-patient-error']); ?>
    <?php endif; ?>

    <?php include 'views/forms/add-patient.php'; ?>
  </div>
</main>

<?php include_once "views/footer.php"; ?>
