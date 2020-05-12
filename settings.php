<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'Ustawienia systemu');
define('PAGE_NEEDS_AUTHORIZATION', true);

require_once "includes/init.php";

if (!IS_ADMIN) {
  header("Location: {$config['site_url']}/auth.php");
  exit;
}

include_once "views/header.php"; ?>

<main>
  <?php include_once 'views/navs/dashboard-nav.php' ?>

  <div class='paper'>
    <h1 class='paper-title'>Ustawienia systemu</h1>
    <div class='cards'>
      <div class='cards-tabs'>
        <div for='specializations' class='cards-tabs--tab'>Specjalizacje</div>
        <div for='rooms' class='cards-tabs--tab'>Gabinety</div>
      </div>
      <div class='cards-sections'>
        <div id='specializations' class='cards-sections--section'>
          <h3>Specjalizacje</h3>
        </div>
        <div id='rooms' class='cards-sections--section'>
          <h3>Gabinety</h3>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include_once "views/footer.php"; ?>
