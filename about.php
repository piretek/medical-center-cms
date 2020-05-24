<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'O firmie');
define('PAGE_NEEDS_AUTHORIZATION', false);

require_once "includes/init.php";
include_once "views/header.php";

?>

<div class="info">
  <div class="about--columns">
    <div class="about--column">
      <div class="about--header">
        <h2>Dlaczego warto leczyć się
        <br>
        w przychodni Korona Center?</h2>
      </div>
      <div class="about--text">
        <p>Pacjenci wybierający naszą przychodnię mogą liczyć na profesjonalną pomoc, trafną diagnozę, a co za tym idzie skuteczne leczenie i jak najszybszy powrót do zdrowia. Staramy się, aby nasi pacjenci odczuwali komfort przy każdej wizycie. Wiemy, że nie ma drugiego takiego samego pacjenta i każdy medyczny problem jest niepowtarzalny, dlatego duży nacisk kładziemy na indywidualne podejście.</p>
      </div>
    </div>
    <div class="about--column">
      <div class="about--header">
        <h2>Wykwalifikowana kadra</h2>
      </div>
      <div class="about--text">
        <p>Profesjonalna opieka zdrowotna, jak i wysoki standard udzielanych świadczeń zdrowotnych jest wynikiem umiejętności naszej kadry medycznej o wieloletnim stażu. Obecnie świadczymy usługi z zakresu między innymi ortopedii, chirurgii, kardiologii, neurologii, urologii, stomatologii, dermatologii czy ginekologii. Dzięki temu jest ona jednym z wiodących centrów medyczno-diagnostycznych w Lublinie.</p>
      </div>
    </div>
    <div class="about--column">
      <div class="about--header">
        <h2>Umowa z NFZ</h2>
      </div>
      <div class="about--text">
        <p>Nasza przychodnia posiada aktualną umowę z Narodowym Funduszem Zdrowia na świadczenie bezpłatnych usług w zakresie podstawowej opieki zdrowotnej. Dzięki temu mogą Państwo skorzystać z bezpłatnych badań diagnostycznych, zabiegów rehabilitacyjnych czy zabiegów szpitalnych. By zarezerwować wizytę refundowaną przez NFZ należy jednak posiadać skierowanie do poradni specjalistycznej. </p>
      </div>
    </div>
  </div>
</div>
<div class='doctors-container'>
  <?php

    $week = [
      '1' => 'Pn|Monday',
      '2' => 'Wt|Tuesday',
      '3' => 'Śr|Wednesday',
      '4' => 'Czw|Thursday',
      '5' => 'Ptk|Friday',
      '6' => 'Sob|Saturday',
      '0' => 'Nie|Sunday'
    ];

    $doctors = $db->query("SELECT doctors.id, CONCAT(users.firstname , ' ', users.lastname) AS 'doctor' , doctors.degree, specializations.name as spec FROM (doctors JOIN users ON users.id = doctors.user) JOIN specializations ON doctors.specialization = specializations.id");
    if ($doctors->num_rows == 0) {
      echo "Brak lekarzy w bazie danych.";
    }
    else {
      while($doctor = $doctors->fetch_assoc()) : ?>

            <div class="doc-profile paper">
            <a href="<?= $config['site_url'].'/new-reservation.php?car='.$doctor['id']?>" class="doctor">
              <div class="title-doc">
                <h2><?= $doctor['degree'].' '.$doctor['doctor'] ?></h2>
                <h4><?= $doctor['spec'] ?></h4>
              </div>
                <strong>Godziny przyjęć:</strong>
                <ul class="doc-card-worktime">
                  <?php
                    foreach($week as $index => $day) {
                      list($polish, $english) = explode('|', $day);
                      list($day, $month, $year) = explode('.', date('d.m.Y', strtotime("{$english} this week")));

                      $timeDayStart = mktime(0, 0, 0, $month, $day, $year);
                      $timeDayEnd = mktime(23, 59, 59, $month, $day, $year);

                      $worktimeQuery = "SELECT * FROM schedule WHERE doctor = '{$doctor['id']}' AND date BETWEEN '{$timeDayStart}' AND '{$timeDayEnd}'";
                      $worktime = $db->query($worktimeQuery);

                      if($worktime->num_rows == 0) : ?>

                        <li><strong><?= $polish ?>:</strong><br/> Nie pracuje</li>

                      <?php else :

                        $worktime = $worktime->fetch_all(MYSQLI_ASSOC);?>

                        <li><strong><?= $polish ?>:</strong> <?= date('H:i', $worktime[0]['date']); ?> - <?= date('H:i', $worktime[count($worktime) - 1]['date'] + ($worktime[count($worktime) - 1]['date'] - $worktime[count($worktime) - 2]['date'])); ?>

                      <?php endif;
                    }
                  ?>
                </ul>
              </a>
              </div>
      <?php endwhile;
    }?>
</div>

<?php include_once "views/footer.php"; ?>
