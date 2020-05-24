<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'Zarządzanie rezerwacjami');
define('PAGE_NEEDS_AUTHORIZATION', true);

require_once "includes/init.php";

if (!IS_ADMIN && !IS_EMPLOYEE && !IS_DOCTOR) {
  header("Location: {$config['site_url']}/auth.php");
  exit;
}

if (isset($_POST['type'])) {
  if ($_POST['type'] == 'change-status') {
    $ok = true;

    $requiredKeys = ['treatment', 'id'];

    foreach($keys as $key) {
      if (!array_key_exists($key, $_POST)) {
        $_SESSION['error'] = 'Niepoprawne pola.';
        header("Location: {$config['site_url']}/reservations.php?action=manage&id={$_POST['id']}");
        exit;
      }
    }

    $toCheckIfEmpty = $keys;
    foreach($_POST as $key => $value) {
      if (empty($_POST[$key]) && in_array($key, $toCheckIfEmpty)) {
        $ok = false;
        $_SESSION["reservations-{$_POST['type']}-form-error-{$key}"] = 'Pole nie może być puste';
      }
    }

    if ($ok) {
      $successful = $db->query(sprintf("UPDATE reservations SET status = '1', treatment = '%s' WHERE id = '%s'",
        $db->real_escape_string($_POST['treatment']),
        $db->real_escape_string($_POST['id'])
      ));

      if ($successful) {
        $_SESSION['success'] = 'Zaktualizowano.';
        header("Location: {$config['site_url']}/reservations.php?action=manage&id={$_POST['id']}");
        exit;
      }
      else {
        $_SESSION['error'] = 'Błąd wykonywania zapytania do bazy danych. Skontaktuj się z administratorem.';
        header("Location: {$config['site_url']}/reservations.php?action=manage&id={$_POST['id']}");
        exit;
      }
    }
    else {
      $_SESSION['error'] = 'Popraw błędy.';
      header("Location: {$config['site_url']}/reservations.php?action=manage&id={$_POST['id']}");
      exit;
    }
  }
  else if ($_POST['type'] == 'cancel') {
    $successful = $db->query(sprintf("UPDATE reservations SET status = '2' WHERE id = '%s'", $db->real_escape_string($_POST['id'])));
    if ($successful) {
      $_SESSION['success'] = 'Wizyta została anulowana.';
    }
    else {
      $_SESSION['error'] = 'Błąd podczas wykonywania zapytania do bazy danych.';
    }

    header("Location: {$config['site_url']}/reservations.php?action=manage&id=".$_POST['id']);
    exit;
  }
}

if (isset($_GET['action']) && !empty($_GET['action'])) {
  if ($_GET['action'] == 'manage' && (!isset($_GET['id']) || empty ($_GET['id']))) {
    header("Location: {$config['site_url']}/reservations.php");
    exit;
  }
  else {
    $query = sprintf("SELECT reservations.*, CONCAT(users.firstname, ' ', users.lastname) as patient, patients.pesel, patients.phone, CONCAT(patients.street, ' ', patients.house_no, '<br />', patients.postcode, ' ',patients.city) as address, schedule.date, schedule.doctor FROM ((reservations JOIN patients ON reservations.patient = patients.id) JOIN schedule ON schedule.id = reservations.date) JOIN users ON patients.user = users.id WHERE reservations.id = '%s' %s ORDER BY schedule.date DESC", $db->real_escape_string($_GET['id']), !IS_ADMIN && !IS_EMPLOYEE ? "AND schedule.doctor = '".DOCOR_ID."'" : '');

    $reservations = $db->query($query);
    if ($reservations->num_rows == 0) {
      header("Location: {$config['site_url']}/reservations.php");
      exit;
    }
    else {
      $reservation = $reservations->fetch_assoc();

      if (!IS_ADMIN && !IS_EMPLOYEE && DOCTOR_ID != $reservation['doctor']) {
        header("Location: {$config['site_url']}/reservations.php");
        exit;
      }

      $pesel = new PESEL($reservation['pesel']);
    }
  }
}

include_once "views/header.php"; ?>

<main>
  <?php include_once 'views/navs/dashboard-nav.php' ?>

  <div class='paper'>
    <h1 class='paper-title'>Zarządzanie rezerwacjami</h1>

    <?php notification('success', 'success'); ?>
    <?php notification('error', 'error'); ?>

    <?php if (isset($_GET['action']) && !empty($_GET['action'])) :

      switch($_GET['action']) {
        case 'manage' : ?>

          <div class="columns">
            <div class="column col-60">
              <h2>Szczeóły rezerwacji</h2>
              <ul>
                <li><strong>Termin wizyty:</strong> <?= date('d.m.Y, H:i', $reservation['date']) ?></li>
                <li><strong>Pacjent:</strong> <?= $reservation['patient'] ?></li>
                <li><strong>Wiek:</strong> <?= $pesel->getAge(); ?> lat</li>
                <li><strong>Data urodzenia:</strong> <?= $pesel->getBirthDate(); ?></li>
                <li><strong>PESEL:</strong> <?= $pesel->get(); ?></li>
                <li><strong>Telefon:</strong> <?= chunk_split($reservation['phone'], 3) ?></li>
                <li><strong>Adres:</strong> <?= $reservation['address'] ?></li>
                <li><strong>Rodzaj wizyty:</strong> <?= reservationType($reservation['type']) ?></li>
              </ul>
              <?php

              if ($reservation['status'] == 0) {
                $cancelForm = new Form('cancel-'.$reservation['id']);
                $cancelForm->hidden('type', 'cancel');
                $cancelForm->hidden('id', $reservation['id']);
                $cancelForm->place('Anuluj wizytę');
              }

              ?>
            </div>
            <div class="column col-40">
              <h2>Status rezerwacji</h2>
              <p><?= reservationStatus($reservation['status']) ?></p>
              <?php

              if ($reservation['status'] == 0) {
                $changeStatus = new Form('change-status');
                $changeStatus->hidden('type', 'change-status');
                $changeStatus->hidden('id', $reservation['id']);
                $changeStatus->textarea('treatment', 'Zalecenia lekarskie:')
                  ->place('Zamknij wizytę');
              }
              else if ($reservation['status'] == 1) {
                echo "<p><strong>Zalecenia lekarskie:</strong><br>{$reservation['treatment']}</p>";
              }
              ?>
            </div>
          </div>

          <?php  break;
      }

    else : ?>
      <div class='m-tb-2'>
        <a href='new-reservation.php'>
          <button>Dodaj nową rezerwację</button>
        </a>
      </div>
      <table>
        <tr>
          <th>Data i godzina</th>
          <?php if (IS_ADMIN || IS_EMPLOYEE) : ?><th>Lekarz</th><?php endif; ?>
          <th>Pacjent</th>
          <th>Rodzaj</th>
          <th>Status</th>
          <th>Akcje</th>
        </tr>
        <?php

        $query = sprintf("SELECT reservations.*, CONCAT(pu.firstname, ' ', pu.lastname) as patient, CONCAT(doctors.degree, ' ', du.firstname, ' ', du.lastname) as doctor, schedule.date FROM (( ( ( reservations JOIN patients ON reservations.patient = patients.id) JOIN schedule ON schedule.id = reservations.date) JOIN users AS pu ON patients.user = pu.id ) JOIN doctors ON schedule.doctor = doctors.id) JOIN users AS du ON doctors.user = du.id %s ORDER BY schedule.date DESC", !IS_ADMIN && !IS_EMPLOYEE ? "WHERE schedule.doctor = '".DOCTOR_ID."'" : '');

        $perPage = 15;
        $allReservations = $db->query($query)->num_rows;

        $page = isset($_GET['page']) && !empty($_GET['page']) ? $_GET['page'] : 1;
        if ($allReservations > $perPage) {
          $totalPages = ceil($allReservations / $perPage);
          $offset = ($page - 1) * $perPage;
        }
        else {
          $page = $totalPages = 1;
          $offset = 0;
        }

        $nextPage = $totalPages == $page ? null : $page + 1;
        $prevPage = $page - 1 == 0 ? null : $page - 1;

        $limitQuery = $query." LIMIT {$offset}, {$perPage}";

        $reservations = $db->query($limitQuery);

        if ($reservations->num_rows == 0) : ?>
          <tr>
            <td colspan='5'>Brak rezerwacji w systemie.</td>
          </tr>
        <?php else : while($reservation = $reservations->fetch_assoc()) : ?>
          <tr>
            <td><?= date('d.m.Y, H:i', $reservation['date']) ?></td>
            <td><?= $reservation['doctor'] ?></td>
            <td><?= $reservation['patient'] ?></td>
            <td><?= reservationType($reservation['type']) ?></td>
            <td><?= reservationStatus($reservation['status']) ?></td>
            <td>
              <a class='action-anchor' href='reservations.php?action=manage&id=<?= $reservation['id'] ?>'>Zarządzaj</a>
              <?php

              if ($reservation['status'] == 0) {
                echo " | ";
                $cancelForm = new Form('cancel-'.$reservation['id'], 'POST', '', ['as-anchor']);
                $cancelForm->hidden('type', 'cancel');
                $cancelForm->hidden('id', $reservation['id']);
                $cancelForm->place('Anuluj');
              }
              ?>
            </td>
          </tr>
        <?php endwhile; endif; ?>
      </table>

      <?php if ($totalPages > 1) : ?>
        <div class='pagination--container'>
          <?php if ($prevPage !== null) : ?>
            <a class='pagination' href='reservations.php?page=<?= $prevPage ?>'>Poprzednia</a>
          <?php endif; ?>

          <?php if ($prevPage !== null && $page != 1) : ?>
            <a class='pagination' href='reservations.php?'>1</a>
            <span class='three-dots'>...</span>
          <?php endif; ?>

          <span class='pagination'><?= $page ?></span>

          <?php if ($totalPages !== $nextPage && $nextPage !== null) : ?>
            <span class='three-dots'>...</span>
            <a class='pagination' href='reservations.php?page=<?= $totalPages ?>'><?= $totalPages ?></a>
          <?php endif; ?>

          <?php if ($nextPage !== null) : ?>
            <a class='pagination' href='reservations.php?page=<?= $nextPage ?>'>Następna</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>

    <?php endif; ?>
  </div>
</main>

<?php include_once "views/footer.php"; ?>
