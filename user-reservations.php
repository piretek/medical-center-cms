<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'Twoje rezerwacje');
define('PAGE_NEEDS_AUTHORIZATION', true);

require_once "includes/init.php";

if (!IS_PATIENT) {
  header("Location: {$config['site_url']}/create-patient-account.php");
  exit;
}

if (isset($_POST['type'])) {
  if ($_POST['type'] == 'cancel-reservation') {

    $successful = $db->query(sprintf("UPDATE reservations SET status = '2' WHERE id = '%s'", $db->real_escape_string($_POST['id'])));
    if ($successful) {
      $_SESSION['success'] = 'Wizyta została anulowana.';
    }
    else {
      $_SESSION['error'] = 'Błąd podczas wykonywania zapytania do bazy danych.';
    }

    header("Location: {$config['site_url']}/user-reservations.php?id=".$_POST['id']);
    exit;
  }
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
  $reservationsSelectQuery = sprintf("SELECT reservations.id, reservations.patient, reservations.type, reservations.status, schedule.date, rooms.number as room, CONCAT(doctors.degree, ' ', users.firstname, ' ', users.lastname ) as doctor, specializations.name AS specialization, reservations.treatment FROM ((((reservations INNER JOIN schedule ON schedule.id = reservations.date) INNER JOIN doctors ON doctors.id = schedule.doctor) INNER JOIN users ON users.id = doctors.user) INNER JOIN rooms ON rooms.id = schedule.room) INNER JOIN specializations ON specializations.id = doctors.specialization WHERE reservations.id = '%s'", $db->real_escape_string($_GET['id']));
  $reservations = $db->query($reservationsSelectQuery);

  if ($reservations->num_rows == 0) {
    header("Location: {$config['site_url']}/user-reservations.php");
    exit;
  }
  else {
    $reservation = $reservations->fetch_assoc();

    if ($reservation['patient'] != PATIENT_ID) {
      header("Location: {$config['site_url']}/user-reservations.php");
      exit;
    }
  }
}

include_once "views/header.php"; ?>

<main>
  <?php include_once 'views/navs/dashboard-nav.php' ?>

  <div class='paper'>
    <h1 class='paper-title'>Historia twoich wizyt</h1>

    <?php notification('success', 'success'); ?>

    <?php if (!isset($_GET['id']) || empty($_GET['id'])) : ?>
      <table>
        <tr>
          <th>Data i godzina</th>
          <th>Lekarz</th>
          <th>Gabinet</th>
          <th>Rodzaj</th>
          <th>Status</th>
          <th>Akcje</th>
        </tr>
        <?php

        $reservationsSelectQuery = sprintf("SELECT reservations.id, reservations.type, reservations.status, schedule.date, rooms.number as room, CONCAT(doctors.degree, ' ', users.firstname, ' ', users.lastname,' - ',specializations.name ) as doctor FROM ((((reservations INNER JOIN schedule ON schedule.id = reservations.date) INNER JOIN doctors ON doctors.id = schedule.doctor) INNER JOIN users ON users.id = doctors.user) INNER JOIN rooms ON rooms.id = schedule.room) INNER JOIN specializations ON specializations.id = doctors.specialization WHERE reservations.patient = '%s'", PATIENT_ID);
        $reservations = $db->query($reservationsSelectQuery);

        if ($reservations->num_rows == 0) : ?>
          <tr>
            <td colspan='6'>Brak dotychczasowych wizyt. Aby zarezerwować wizytę, przejdź <a href='<?= $config['site_url'].'/new-reservation.php' ?>'>tutaj</a>.
          </tr>
        <?php else : ?>
          <?php while($reservation = $reservations->fetch_assoc()) : ?>
            <tr>
              <td><?= date('d.m.Y, H:i', $reservation['date']) ?></td>
              <td><?= $reservation['doctor'] ?></td>
              <td><?= $reservation['room'] ?></td>
              <td><?= reservationType($reservation['type']) ?></td>
              <td><?= reservationStatus($reservation['status']) ?></td>
              <td>
                <a class='action-anchor' href='<?= "{$config['site_url']}/user-reservations.php?id=".$reservation['id'] ?>'>Zobacz</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </table>
    <?php else : ?>
      <div class="columns">
        <div class="column col-50">
          <ul>
            <li><strong>Termin wizyty:</strong> <?= date('d.m.Y, H:i', $reservation['date']) ?></li>
            <li><strong>Lekarz:</strong> <?= $reservation['doctor'] ?></li>
            <li><strong>Specjalizacja:</strong> <?= $reservation['specialization'] ?></li>
            <li><strong>Gabinet:</strong> <?= $reservation['room'] ?></li>
            <li><strong>Rodzaj wizyty:</strong> <?= reservationType($reservation['type']) ?></li>
          </ul>
          <p>Zapraszamy do zgłoszenia się w punkcie rejestracji na 15 minut przed rozpoczęciem wizyty, w przeciwnym wypadku rezerwacja zostanie anulowana.</p>
        </div>
        <div class="column col-50">
          <h3>Status rezerwacji</h3>
          <p class='history-status nm'><?= reservationStatus($reservation['status']); ?></p>
          <?php if ($reservation['status'] == 0) :

            $cancelReservation = new Form('cancel-reservation');
            $cancelReservation->hidden('type', 'cancel-reservation');
            $cancelReservation->hidden('id', $reservation['id']);
            $cancelReservation->place('Anuluj zaplanowaną wizytę');

          elseif ($reservation['status'] == 1) : ?>

            <h3>Zalecenia od lekarza</h3>
            <p class='nm'><?= $reservation['treatment'] ?></p>

          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php include_once "views/footer.php"; ?>
