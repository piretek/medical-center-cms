<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'Twoje konto');
define('PAGE_NEEDS_AUTHORIZATION', true);

require_once "includes/init.php";

if (IS_PATIENT) {
  $patient = $db->query("SELECT * FROM patients WHERE user = '{$_SESSION['user']}'")->fetch_assoc();
  $pesel = new PESEL($patient['pesel']);
}

include_once "views/header.php"; ?>

<main>
  <?php include_once 'views/navs/dashboard-nav.php' ?>

  <div class='paper'>
    <h1 class='paper-title'>Twoje konto</h1>
    <div class='columns'>
      <div class="column col-50">
        <?php

        $form = new Form('user');

        $form->text('firstname', 'Imię', $authorizedUser['firstname'])
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
          require_once 'views/forms/edit-patient.php';
        endif;

        if (IS_DOCTOR) :
          define('DOCTOR_FORM_ID', $_SESSION['user']);
          require_once 'views/forms/doctor.php';
        endif;

        ?>
      </div>
    </div>
  </div>
</main>

<?php include_once "views/footer.php"; ?>
