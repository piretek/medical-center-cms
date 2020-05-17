<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'Twoje konto');
define('PAGE_NEEDS_AUTHORIZATION', true);

require_once "includes/init.php";

if (isset($_POST['type'])) {
  switch($_POST['type']) {
    case 'user-account' :

      $acceptedKeys = [
        'email',
        'password',
        'repeat-password',
        'old-password',
        'firstname',
        'lastname'
      ];

      foreach($acceptedKeys as $key) {
        if (!array_key_exists($key, $_POST)) {
          $_SESSION['error'] = 'Niepoprawne pola.';
          header("Location: {$config['site_url']}/auth.php");
          exit();
        }
      }

      $ok = true;

      $toCheckIfEmpty = ['firstname', 'lastname'];
      foreach($_POST as $key => $value) {
        if (empty($_POST[$key]) && in_array($key, $toCheckIfEmpty)) {
          $ok = false;
          $_SESSION["user-account-form-error-{$key}"] = 'Pole nie może być puste';
        }
      }

      $email = htmlentities(strtolower($_POST['email']), ENT_QUOTES, "UTF-8");
      $password = $_POST['password'] ;
      $repeatedPassword = $_POST['repeat-password'];
      $oldPassword = $_POST['old-password'];
      $firstname = htmlentities($_POST ['firstname'], ENT_QUOTES, "UTF-8");
      $lastname = htmlentities($_POST['lastname'], ENT_QUOTES, "UTF-8");

      $verifyPassword = false;

      if (!empty($email)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $ok = false;
          $_SESSION["user-account-form-error-email"] = "Niepoprawny email";
        }

        $verifyPassword = true;
      }

      if (!empty($password)) {
        if (!preg_match("/^(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password)) {
          $ok = false;
          $_SESSION["user-account-form-error-password"] = "Hasło musi się składać z 8 znaków, oraz musi mieć 1 dużą literę i znak specjalny.";
        }

        if ($password != $repeatedPassword) {
          $ok = false;
          $_SESSION["user-account-form-error-repeat-password"] = "Hasła muszą być takie same";
        }

        $verifyPassword = true;
      }

      if (strlen($firstname) > 20){
        $ok = false;
        $_SESSION["user-account-form-error-firstname"] = "Zbyt duża ilość znaków (maksymalnie 20).";
      }

      if (strlen($lastname) > 25){
        $ok = false;
        $_SESSION["user-account-form-error-lastname"] = "Zbyt duża ilość znaków (maksymalnie 25).";
      }

      if ($verifyPassword) {
        $userPassword = $db->query(sprintf('SELECT password FROM users WHERE id = \'%d\'', $_SESSION['user']))->fetch_assoc()['password'];

        if (!password_verify($oldPassword, $userPassword)) {
          $ok = false;
          $_SESSION["user-account-form-error-old-password"] = "Niepoprawne hasło.";
        }
      }

      if (!$ok) {
        $_SESSION['error'] = 'Popraw wszystkie pola!';
        header("Location: {$config['site_url']}/user-account.php");
        exit();
      }
      else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if (!empty($password)) {
          $passwordSQL = ", password = '{$password}'";
        }
        else {
          $passwordSQL = '';
        }

        if (!empty($email)) {
          $emailSQL = ", email = '{$email}'";
        }
        else {
          $emailSQL = '';
        }

        $query = sprintf("UPDATE users SET firstname = '%s', lastname = '%s'{$passwordSQL}{$emailSQL} WHERE id = '%d'",
          $db->real_escape_string($firstname),
          $db->real_escape_string($lastname),
          $db->real_escape_string($_SESSION['user'])
        );

        $successful = $db->query($query);

        if ($successful) {
          $_SESSION['success'] = 'Zaktualizowano';
          header("Location: {$config['site_url']}/user-account.php");
          exit();
        }
        else {
          $_SESSION['error'] = 'Błąd podczas wykonywania zapytania do bazy danych. '.$query;
          header("Location: {$config['site_url']}/user-account.php");
          exit();
        }
      }

      break;

    case 'edit-patient' :

      $ok = true;

      $requiredFields = [
        'phone',
        'street',
        'house_no',
        'city',
        'postcode'
      ];

      foreach($requiredFields as $field) {
        if (!array_key_exists($field, $_POST) || empty($_POST[$field])) {
          $ok = false;
          $_SESSION['user-account-edit-patient-form-error-'.$field] = 'To pole nie może być puste.';
        }
      }

      $post = [];
      foreach ($_POST as $key => $value) {
        $post[$key] = htmlentities($value, ENT_QUOTES, "UTF-8");
      }

      if (strlen($post['phone']) != 9 || !is_numeric($post['phone'])) {
        $ok = false;
        $_SESSION['user-account-edit-patient-form-error-phone'] = 'Niepoprawny format numeru telefonu';
      }

      if (strlen($post['street']) > 30) {
        $ok = false;
        $_SESSION['user-account-edit-patient-form-error-street'] = 'Ulica może mieć maks. 30 znaków.';
      }

      if (strlen($post['house_no']) > 10) {
        $ok = false;
        $_SESSION['user-account-edit-patient-form-error-house_no'] = 'Numer domu i mieszkania może mieć maks. 10 znaków.';
      }

      if (strlen($post['city']) > 20) {
        $ok = false;
        $_SESSION['user-account-edit-patient-form-error-city'] = 'Miasto może mieć maks. 20 znaków.';
      }

      if (!preg_match('/^[0-9]{2}\-[0-9]{3}$/', $post['postcode'])) {
        $ok = false;
        $_SESSION['user-account-edit-patient-form-error-postcode'] = 'Kod musi być w formacie XX-XXX.';
      }

      if ($ok) {
        $query = sprintf("UPDATE patients SET phone = '%s', street = '%s', house_no = '%s', city = '%s', postcode = '%s' WHERE user = '%d'",
          $db->real_escape_string($post['phone']),
          $db->real_escape_string($post['street']),
          $db->real_escape_string($post['house_no']),
          $db->real_escape_string($post['city']),
          $db->real_escape_string($post['postcode']),
          $_SESSION['user']
        );

        $successful = $db->query($query);

        if ($successful) {
          $_SESSION['success'] = 'Zaktualizowano!';
          header("Location: {$config['site_url']}/user-account.php");
          exit;
        }
        else {
          $_SESSION['error'] = 'Błąd zapytania do bazy danych. Skontaktuj się z administratorem.';
          header("Location: {$config['site_url']}/user-account.php");
          exit;
        }
      }
      else {
        $_SESSION['error'] = 'Popraw wszystkie pola!';
        header("Location: {$config['site_url']}/user-account.php");
        exit;
      }
      break;

    case 'doctor' :

      $ok = true;

      $checkedKeys = ['specialization', 'degree'];
      foreach($checkedKeys as $key) {
        if (!array_key_exists($key, $_POST) || empty($_POST[$key])) {
          $ok = false;
          $_SESSION['user-account-doctor-form-error-'.$key] = 'Pole nie może być puste.';
        }
      }

      if ($ok) {
        $doctorExists = $db->query(sprintf("SELECT * FROM doctors WHERE id = '%d'", $db->real_escape_string($_POST['id'])))->num_rows == 0 ? false : true;

        if ($doctorExists) {
          $query = sprintf('UPDATE doctors SET specialization = \'%d\', degree = \'%s\' WHERE user = \'%d\'',
            $db->real_escape_string($_POST['specialization']),
            $db->real_escape_string(htmlentities($_POST['degree'], ENT_QUOTES, "UTF-8")),
            $db->real_escape_string($_POST['id'])
          );
        }
        else {
          $query = sprintf("INSERT INTO doctors VALUES (NULL, '%d', '%d', '%s')",
            $db->real_escape_string($_POST['id']),
            $db->real_escape_string($_POST['specialization']),
            $db->real_escape_string(htmlentities($_POST['degree'], ENT_QUOTES, "UTF-8"))
          );
        }

        $successful = $db->query($query);

        if ($successful) {
          $_SESSION['success'] = $doctorExists ? 'Zmieniono!' : 'Stworzono!';
          header("Location: {$config['site_url']}/user-account.php");
          exit;
        }
        else {
          $_SESSION['error'] = 'Błąd zapytania do bazy danych. Skontaktuj się z administratorem.';
          header("Location: {$config['site_url']}/user-account.php");
          exit;
        }
      }
      else {
        $_SESSION['error'] = 'Popraw wszystkie pola!';
        header("Location: {$config['site_url']}/user-account.php");
        exit;
      }

      break;
  }
}

if (IS_PATIENT) {
  $patient = $db->query("SELECT * FROM patients WHERE user = '{$_SESSION['user']}'")->fetch_assoc();
  $pesel = new PESEL($patient['pesel']);
}

include_once "views/header.php"; ?>

<main>
  <?php include_once 'views/navs/dashboard-nav.php' ?>

  <div class='paper'>
    <h1 class='paper-title'>Twoje konto</h1>

    <?php notification('success', 'success'); ?>
    <?php notification('error', 'error'); ?>

    <div class='columns'>
      <div class="column col-50">
        <h2>Informacje o Twoim koncie</h2>
        <ul>
          <li><strong>Imię i nazwisko:</strong> <?= $authorizedUser['firstname'].' '.$authorizedUser['lastname'] ?></li>
          <?php if (IS_PATIENT): ?>
          <li><strong>Płeć:</strong> <?= ucfirst($pesel->getSex()) ?></li>
          <li><strong>Wiek:</strong> <?= $pesel->getAge() ?> lat</li>
          <li><strong>Data urodzenia:</strong> <?= $pesel->getBirthDate() ?></li>
          <li><strong>PESEL:</strong> <?= $pesel->get() ?></li>
          <?php endif; ?>
          <li><strong>E-mail:</strong> <?= $authorizedUser['email'] ?></li>
        </ul>

        <h2>Zmień dane konta</h2>
        <span>Pola nie wypełnione nie zostaną nadpisane.</span>
        <?php

        $form = new Form('user');

        $form->setErrorPrefix('user-account');

        $form->hidden('type', 'user-account')
          ->text('firstname', 'Imię', $authorizedUser['firstname'], )
          ->text('lastname', 'Nazwisko', $authorizedUser['lastname'])
          ->email('email', 'E-mail')
          ->password('password', 'Nowe hasło')
          ->password('repeat-password', 'Powtórz nowe hasło')
          ->password('old-password', 'Aktualne hasło')
          ->place('Zatwierdź');

        ?>
      </div>
      <div class="column col-50">
        <?php

        if (IS_PATIENT) :
          echo "<h2>Ustawienia konta pacjenta</h2>";
          define('PATIENT_FORM_ID', $_SESSION['user']);
          require_once 'views/forms/edit-patient.php';
        endif;

        if (IS_DOCTOR || $authorizedUser['roleCode'] == 'DOCTOR') :
          if (IS_DOCTOR) {
            echo "<h2>Ustawienia konta lekarza</h2>";
          }
          else {
            echo "<h2>Stwórz konto lekarza</h2>";
          }

          define('DOCTOR_FORM_ID', $_SESSION['user']);
          require_once 'views/forms/doctor.php';
        endif;

        ?>
      </div>
    </div>
  </div>
</main>

<?php include_once "views/footer.php"; ?>
