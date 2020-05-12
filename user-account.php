<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'Twoje konto');
define('PAGE_NEEDS_AUTHORIZATION', true);

require_once "includes/init.php";
include_once "views/header.php"; ?>

<main>
  <?php include_once 'views/navs/dashboard-nav.php' ?>

  <?php

  $patient = $db->query("SELECT * FROM patients WHERE user = '{$_SESSION['user']}'")->fetch_assoc();
  $pesel = new PESEL($patient['pesel']);

  ?>

  <div class='paper'>
    <h1 class='paper-title'>Twoje konto</h1>
    <p><strong>Imię i nazwisko:</strong> <?= $authorizedUser['firstname'] ?> <?= $authorizedUser['lastname'] ?></p>
    <p><strong>Płeć:</strong> <?= $pesel->getSex(); ?></p>
    <p><strong>Data urodzenia:</strong>  <?= $pesel->getBirthDate(); ?></p>
  </div>
</main>

<?php include_once "views/footer.php"; ?>
