<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'Twoje konto');
define('PAGE_NEEDS_AUTHORIZATION', true);

require_once "includes/init.php";

if (isset($_POST['type'])) {
  switch($_POST['type']) {
    case 'user-account' :

      break;

    case 'edit-patient' :

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
        $_SESSION['error'] = 'Popraw wszystkie pola';
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
        <?php

        $form = new Form('user');

        $form->hidden('type', 'user-account')
          ->text('firstname', 'Imię', $authorizedUser['firstname'])
          ->text('lastname', 'Nazwisko', $authorizedUser['lastname'])
          ->email('email', 'E-mail', $authorizedUser['email'])
          ->password('password', 'Nowe hasło')
          ->password('repeat-password', 'Powtórz nowe hasło')
          ->password('old-password', 'Stare hasło')
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
