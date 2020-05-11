<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'Stwórz konto pacjenta');
define('PAGE_NEEDS_AUTHORIZATION', true);

require_once "includes/init.php";

if (IS_PATIENT) {
  header("Location: {$config['site-url']}/user-reservations.php");
  exit;
}

include_once "views/header.php"; ?>

<main>
  <?php include_once 'views/navs/dashboard-nav.php' ?>

  <div class='paper'>
    <h1 class='paper-title'>Stwórz konto pacjenta</h1>
    <p>Prosimy o wypełnienie danych dot. miejsca zamieszkania, nr PESEL i danych kontaktowych. Przetwarzanie tych danych osobowych jest wymagane do realizowania Pana/Pani wizyt w naszej przychodni.</p>
    <?php include 'views/forms/add-patient.php'; ?>
  </div>
</main>

<?php include_once "views/footer.php"; ?>
