<?php

define('PAGE_TITLE', 'Twoje konto');
define('PAGE_NEEDS_AUTHORIZATION', true);

require_once "includes/init.php";
include_once "views/header.php"; ?>

<main>
  <?php include_once 'views/navs/dashboard-nav.php' ?>

  <div class='paper'>
    <h1 class='paper-title'>Twoje konto</h1>
  </div>
</main>

<?php include_once "views/footer.php"; ?>
