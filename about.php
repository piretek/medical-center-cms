<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'O firmie');
define('PAGE_NEEDS_AUTHORIZATION', false);

require_once "includes/init.php";
include_once "views/header.php";

?>

<div class="info">
    <div class="info--content">
      <div class="about--headers">
        <div class="about--header">
          <h2>Dlaczego warto leczyć się
          <br>
          w przychodni Korona Center?</h2>
        </div>
        <div class="about--header">
          <h2>Wykwalifikowana kadra</h2>
        </div>
        <div class="about--header">
          <h2>Umowa z NFZ</h2>
        </div>
      </div>
    <div class="about--texts">
      <div class="about--text">
        <p>Pacjenci wybierający naszą przychodnię mogą liczyć na profesjonalną pomoc, trafną diagnozę, a co za tym idzie skuteczne leczenie i jak najszybszy powrót do zdrowia. Staramy się, aby nasi pacjenci odczuwali komfort przy każdej wizycie. Wiemy, że nie ma drugiego takiego samego pacjenta i każdy medyczny problem jest niepowtarzalny, dlatego duży nacisk kładziemy na indywidualne podejście.</p>
      </div>
      <div class="about--text">
        <p>Profesjonalna opieka zdrowotna, jak i wysoki standard udzielanych świadczeń zdrowotnych jest wynikiem umiejętności naszej kadry medycznej o wieloletnim stażu. Obecnie świadczymy usługi z zakresu między innymi ortopedii, chirurgii, kardiologii, neurologii, urologii, stomatologii, dermatologii czy ginekologii. Dzięki temu jest ona jednym z wiodących centrów medyczno-diagnostycznych w Lublinie.</p>
      </div>
      <div class="about--text">
        <p>Nasza przychodnia posiada aktualną umowę z Narodowym Funduszem Zdrowia na świadczenie bezpłatnych usług w zakresie podstawowej opieki zdrowotnej. Dzięki temu mogą Państwo skorzystać z bezpłatnych badań diagnostycznych, zabiegów rehabilitacyjnych czy zabiegów szpitalnych. By zarezerwować wizytę refundowaną przez NFZ należy jednak posiadać skierowanie do poradni specjalistycznej. </p>
      </div>
    </div>
  </div>
</div>
<div class='doctors-container'>
  <?php

    $doctors = $db->query();
    if ($doctors->num_rows == 0) {
      echo "Brak lekarzy w bazie danych.";
    }
    else {
      while($doctor = $doctors->fetch_assoc()) : ?>

          <a href="<?= $config['site_url'].'/new-reservation.php?car='.$doctor['id']?>" class="doctor">
            <div class="image-car" id='image-car-<?= $car['id'] ?>'>
              <div class="title-car">
                <h2><?= $doctor['firstname'] ?></h2>
              </div>
            </div>
            <div class="doc-supporting-text">
              <ul class="doc-info">
                <li><strong>Specjalizacja:</strong> </li>
                <li><strong>Godziny przyjęć:</strong> </li>
              </ul>
            </div>
          </a>
      <?php endwhile; 
    }?>
</div>

<?php include_once "views/footer.php"; ?>
