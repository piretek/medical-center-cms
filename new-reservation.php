<?php

define('PAGE_TITLE', 'Zarezerwuj nową wizytę');
define('PAGE_NEEDS_AUTHORIZATION', true);

require_once "includes/init.php";
include_once "views/header.php"; ?>

<main>
  <?php include_once 'views/navs/dashboard-nav.php' ?>

  <div class='paper'>
    <h1 class='paper-title'>Zarezerwuj nową wizytę</h1>
  </div>
</main>

<?php include_once "views/footer.php"; ?>
