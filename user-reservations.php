<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'Twoje rezerwacje');
define('PAGE_NEEDS_AUTHORIZATION', true);

require_once "includes/init.php";
include_once "views/header.php"; ?>

<main>
  <?php include_once 'views/navs/dashboard-nav.php' ?>

  <div class='paper'>
    <h1 class='paper-title'>Historia wizyt</h1>

    <?php if (isset($_SESSION['create-patient-success'])) : ?>
      <span class='success'><?= $_SESSION['create-patient-success'] ?></span>
      <?php unset($_SESSION['create-patient-success']); ?>
    <?php endif; ?>

  </div>
</main>

<?php include_once "views/footer.php"; ?>
