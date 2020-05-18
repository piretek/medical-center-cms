<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'Zarezerwuj nową wizytę');
define('PAGE_NEEDS_AUTHORIZATION', true);

require_once "includes/init.php";
include_once "views/header.php"; ?>

<main>
  <?php include_once 'views/navs/dashboard-nav.php' ?>

  <div class='paper'>
    <h1 class='paper-title'>Zarezerwuj nową wizytę</h1>
    <h2>1. Wybierz lekarza</h2>
    <?php

    $doctors = $db->query("SELECT doctors.*, users.firstname, users.lastname, specializations.name as specialization FROM (doctors INNER JOIN users ON doctors.user = users.id) INNER JOIN specializations ON doctors.specialization = specializations.id");
    $doctors = $doctors->fetch_all(MYSQLI_ASSOC);
    foreach($doctors as $doctor) { ?>
      <div class='card'>
        <div class='details'>
          <h4><?= $doctor['degree'].' '.$doctor['firstname'].' '.$doctor['lastname'] ?></h4>
          <p><?= $doctor['specialization'] ?></p>
        </div>
      </div>
    <?php }

    ?>
  </div>
</main>

<?php include_once "views/footer.php"; ?>
