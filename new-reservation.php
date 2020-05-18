<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'Zarezerwuj nową wizytę');
define('PAGE_NEEDS_AUTHORIZATION', true);

require_once "includes/init.php";

if (!IS_PATIENT) {
  header("Location: {$config['site_url']}/create-patient-account.php");
  exit;
}

if (isset($_POST) && !empty($_POST)) {
  $requiredKeys = ['type', 'schedule'];

  foreach($requiredKeys as $key) {
    if (!array_key_exists($key, $_POST) || (array_key_exists($key, $_POST) && empty($_POST[$key]))) {
      header("Location: {$config['site_url']}/new-reservation.php");
      exit;
    }
  }

  $query = sprintf("INSERT INTO reservations VALUES (NULL, '%s', '%s', '%s', '0')",
    $db->real_escape_string($_POST['schedule']),
    $db->real_escape_string(PATIENT_ID),
    $db->real_escape_string($_POST['type'])
  );

  $successful = $db->query($query);

  if ($successful) {
    $_SESSION['success'] = 'Zarezerwowałeś wizytę! Szczegóły dot. niej znajdziesz poniżej.';
    header("Location: {$config['site_url']}/user-reservations.php?id={$db->insert_id}");
    exit;
  }
  else {
    $_SESSION['error'] = 'Błąd podczas wykonywania zapytania do bazy danych. Skontaktuj się z administratorem.';
    header("Location: {$config['site_url']}/new-reservation.php");
    exit;
  }
}

include_once "views/header.php"; ?>

<main>
  <?php include_once 'views/navs/dashboard-nav.php' ?>

  <div class='paper'>
    <h1 class='paper-title'>Zarezerwuj nową wizytę</h1>

    <?php notification('error', 'error'); ?>

    <section class='choose-1'>
      <h2>1. Wybierz lekarza</h2>
      <div class='choice-card--set'>
        <?php

        $doctors = $db->query("SELECT doctors.*, users.firstname, users.lastname, specializations.name as specialization FROM (doctors INNER JOIN users ON doctors.user = users.id) INNER JOIN specializations ON doctors.specialization = specializations.id");
        if ($doctors->num_rows != 0) {
          $doctors = $doctors->fetch_all(MYSQLI_ASSOC);
          foreach($doctors as $doctor) { ?>
            <div class='choice-card doctor-card' data-doctor="<?= $doctor['id'] ?>">
              <div class='details'>
                <h4><?= $doctor['degree'].' '.$doctor['firstname'].' '.$doctor['lastname'] ?></h4>
                <p><?= $doctor['specialization'] ?></p>
              </div>
            </div>
          <?php }
        }
        else {
          echo '<p>Brak dostępnych lekarzy :(</p>';
        }

        ?>
      </div>
    </section>
    <section class='choose choose-2'>
      <h2>2. Znajdź dogodny termin</h2>

      <?php foreach($doctors as $doctor) : ?>
        <div class='doctor-schedule doctor-schedule--<?= $doctor['id'] ?>'>
          <p>
            <strong>Lekarz:</strong>
            <span><?= $doctor['degree'].' '.$doctor['firstname'].' '.$doctor['lastname'] ?> - <?= $doctor['specialization'] ?></span>
          </p>
          <?php

          $maxDays = 7; // max days to present
          $limit = 5; // time when reservation is able to create from in hours

          $maxDaysUnix = time() + ($maxDays * 24 * 60 * 60);

          $timeStart = time() + ($limit * 60 * 60);
          $timeEnd = mktime(23,59,59,date('m', $maxDaysUnix), date('d', $maxDaysUnix), date('Y', $maxDaysUnix));

          $query = sprintf("SELECT schedule.id, schedule.doctor, schedule.date, schedule.room, reservations.patient, reservations.type, reservations.status FROM schedule LEFT JOIN reservations ON schedule.id = reservations.date WHERE doctor = '%s' AND schedule.date BETWEEN '%s' AND '%s'", $doctor['id'], $timeStart, $timeEnd);

          $dates = $db->query($query);
          if ($dates->num_rows == 0) {
            echo "<p>Lekarz nie posiada zaplanowanych wizyt na najbliższy tydzień. Zapraszamy do śledzenia jego aktywności w przyszłości</p>";
          }
          else {
            $dates = $dates->fetch_all(MYSQLI_ASSOC);

            $lastDay = 0; ?>

            <div class='choice-card--set-v-container'>

            <?php foreach($dates as $index => $date) {
              $day = (int) date('d', $date['date']);

              if ($day != $lastDay) : ?>
                <div class='choice-card--set-v'>
                <h2 class='schedule-date'><?= date('d.m.Y', $date['date']) ?></h2>
              <?php endif;

              if ($date['status'] === null || $date['status'] == 2) : ?>
                <div class='choice-card schedule-card' data-schedule='<?= $date['id'] ?>'>
                  <div class='details'>
                    <h4><?= date('H:i', $date['date']) ?></h4>
                  </div>
                </div>
              <?php endif;

              if (!isset($dates[$index + 1]) || $day != (int) date('d', $dates[$index + 1]['date'])) : ?>
                </div>
              <?php endif;

              $lastDay = $day;
            } ?>
            </div>
          <?php } ?>
        </div>
      <?php endforeach; ?>
    </section>
    <section class='choose choose-3'>
      <?php
      $reservation = new Form('new-reservation');
      $reservation->setErrorPrefix('new-reservation');
      ?>

      <h2>3. Rodzaj wizyty</h2>
      <?php
      $reservation->radio('type-1', 'NFZ', 'type', '1', ['checked' => 'checked']);
      $reservation->radio('type-2', 'Prywatna', 'type', '2');

      $reservation->hidden('schedule', '0');
      $reservation->place('Zarezerwuj');

      ?>
    </section>
  </div>
</main>
<script src='assets/js/reservation.js'></script>

<?php include_once "views/footer.php"; ?>
