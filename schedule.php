<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'Grafik lekarzy');
define('PAGE_NEEDS_AUTHORIZATION', true);

require_once "includes/init.php";

if (!IS_ADMIN && !IS_EMPLOYEE) {
  header("Location: {$config['site_url']}/auth.php");
  exit;
}

$mcWorkHours = $db->query("SELECT * FROM settings WHERE name = 'CLOSING-HOURS'");
if ($mcWorkHours->num_rows != 0) {
  $mcWorkHours = unserialize($mcWorkHours->fetch_assoc()['value']);
}
else {
  $mcWorkHours = null;
}

if (isset($_POST['type'])) {
  if ($_POST['type'] == 'add') {
    $ok = true;

    $keys = ['start', 'end', 'interval'];
    foreach($keys as $key) {
      if (!array_key_exists($key, $_POST)) {
        $_SESSION['error'] = 'Niepoprawne pola.';
        header("Location: {$config['site_url']}/schedule.php?action=edit&id=".$_POST['id']);
        exit;
      }
    }

    $toCheckIfEmpty = $keys;
    foreach($_POST as $key => $value) {
      if (empty($_POST[$key]) && in_array($key, $toCheckIfEmpty)) {
        $ok = false;
        $_SESSION["schedule-add-form-error-{$key}"] = 'Pole nie może być puste';
      }
    }

    $hours = ['start', 'end'];
    foreach($hours as $key) {
      if ($ok && !preg_match('/^(0[0-9]|1[0-9]|2[0-3])(:[0-5][0-9])$/', $_POST[$key])) {
        $ok = false;
        $_SESSION["schedule-add-form-error-{$key}"] = 'Pole jest niepoprawne. Godzina powinna być w formacie HH:MM';
      }
    }

    if (!is_numeric($_POST['interval']) || $_POST['interval'] > 60 || $_POST['interval'] < 5) {
      $ok = false;
      $_SESSION["schedule-add-form-error-interval"] = 'Niepoprawna wartość';
    }

    list($d, $mth, $y) = explode('-', $_POST['date']);

    $interval = (int) $_POST['interval'];

    list($h, $m) = explode(':', $_POST['start']);
    $start = mktime($h, $m, 0, $mth, $d, $y);

    if ($mcWorkHours['open-hour'][date('w', $start)] > mktime($h, $m, 0, 1, 1, 1970)) {
      $ok = false;
      $_SESSION["schedule-add-form-error-start"] = 'Godzina nie może być mniejsza niż godzina otwarcia.';
    }

    list($h, $m) = explode(':', $_POST['end']);
    $end = mktime($h, $m, 0, $mth, $d, $y);

    if ($mcWorkHours['close-hour'][date('w', $end)] < mktime($h, $m, 0, 1, 1, 1970)) {
      $ok = false;
      $_SESSION["schedule-add-form-error-end"] = 'Godzina nie może być większa niż godzina zamknięcia.';
    }

    if ($ok) {
      if ((($end - $start) / 60 % $interval) != 0 || (($end - $start) / 60) <= $interval) {
        $_SESSION['error'] = 'Lekarz musi mieć takie godziny pracy, aby zmieścił się przewidziany interwał między wizytami.';
        header("Location: {$config['site_url']}/schedule.php?action=add&doctor={$_POST['doctor']}&date={$_POST['date']}");
        exit;
      }

      $schedules = [];
      $lastMeeting = $end - ($interval * 60);

      $counter = $start;
      while($counter <= $lastMeeting) {
        $schedules[] = $counter;
        $counter += ($interval * 60);
      }

      if (count($schedules) <= 2) {
        $_SESSION['error'] = 'Lekarz musi w trakcie pracy mieć min. 2 przewidziane wizyty.';
        header("Location: {$config['site_url']}/schedule.php?action=add&doctor={$_POST['doctor']}&date={$_POST['date']}");
        exit;
      }

      $check = sprintf("SELECT * FROM schedule WHERE room = '%s' AND date BETWEEN '%s' AND '%s'",
        $db->real_escape_string($_POST['room']),
        $start,
        $lastMeeting
      );

      if ($db->query($check)->num_rows != 0) {
        $_SESSION['error'] = 'Ten gabinet jest zajęty w danych godzinach.';
        header("Location: {$config['site_url']}/schedule.php?action=add&doctor={$_POST['doctor']}&date={$_POST['date']}");
        exit;
      }

      $sqlValues = implode(sprintf("', '%s'), (NULL, '%s', '", $db->real_escape_string($_POST['room']), $db->real_escape_string($_POST['doctor'])), $schedules);
      $query = "INSERT INTO schedule VALUES ".sprintf("(NULL, '%s', '", $db->real_escape_string($_POST['doctor'])).$sqlValues.sprintf("', '%s')", $db->real_escape_string($_POST['room']));

      $successful = $db->query($query);

      if ($successful) {
        $_SESSION['success'] = 'Dodano';
        header("Location: {$config['site_url']}/schedule.php");
        exit;
      }
      else {
        $_SESSION['error'] = 'Błąd podczas wykonywania zapytania do bazy danych. Skontaktuj się z administratorem.';
        header("Location: {$config['site_url']}/schedule.php?action=add&doctor={$_POST['doctor']}&date={$_POST['date']}");
        exit;
      }
    }
    else {
      $_SESSION['error'] = 'Popraw błędy';
      header("Location: {$config['site_url']}/schedule.php?action=add&doctor={$_POST['doctor']}&date={$_POST['date']}");
      exit;
    }
  }
}

if (isset($_GET['action']) && !empty($_GET['action'])) {
  switch($_GET['action']) {
    case 'add' :
    case 'edit' :

      if (isset($_GET['doctor']) && !empty($_GET['doctor']) && isset($_GET['date']) && !empty($_GET['date'])) {
        $doctorCheck = $db->query(sprintf("SELECT doctors.*, users.firstname, users.lastname, specializations.name as specialization FROM (doctors INNER JOIN users ON doctors.user = users.id) INNER JOIN specializations ON doctors.specialization = specializations.id WHERE doctors.id = '%s'", $db->real_escape_string($_GET['doctor'])));
        if ($doctorCheck->num_rows == 0) {
          header("Location: {$config['site_url']}/schedule.php");
          exit;
        }
        else {
          $doctor = $doctorCheck->fetch_assoc();

          $date = $db->real_escape_string(str_replace('-', '.', $_GET['date']));

          $scheduleCheckQuery = sprintf("SELECT * FROM schedule WHERE doctor = '%s' AND date BETWEEN '%s' AND '%s'",
            $doctor['id'],
            strtotime($date),
            (strtotime($date.' +1 day') - 1)
          );

          $scheduleCheck = $db->query($scheduleCheckQuery);

          if ($scheduleCheck->num_rows == 0 && $_GET['action'] == 'edit') {
            header("Location: {$config['site_url']}/schedule.php?action=add&doctor={$_GET['doctor']}&date={$_GET['date']}");
            exit;
          }
          else if ( $scheduleCheck->num_rows != 0 && $_GET['action'] == 'add') {
            header("Location: {$config['site_url']}/schedule.php?action=edit&doctor={$_GET['doctor']}&date={$_GET['date']}");
            exit;
          }
          else if ($scheduleCheck->num_rows != 0 && $_GET['action'] == 'edit') {
            $schedule = $scheduleCheck->fetch_all(MYSQLI_ASSOC);
          }
        }
      }
      else {
        header("Location: {$config['site_url']}/schedule.php");
        exit;
      }

      break;

    default :
      header("Location: {$config['site_url']}/schedule.php");
      exit;
      break;
  }
}

$roomsArray = $db->query("SELECT * FROM rooms ORDER BY number ASC")->fetch_all(MYSQLI_ASSOC);
$rooms = [];
foreach($roomsArray as $room) {
  $rooms[$room['id']] = $room['number'];
}

include_once "views/header.php"; ?>

<main>
  <?php include_once 'views/navs/dashboard-nav.php' ?>

  <div class='paper'>
    <h1 class='paper-title'>Grafik lekarzy</h1>

    <?php notification('success', 'success'); ?>
    <?php notification('error', 'error'); ?>

    <?php if (isset($_GET['action']) && !empty($_GET['action'])) :
      list($d, $m, $y) = explode('-', $_GET['date']);
      $unixDate = mktime(0,0,0, $m, $d, $y);

      switch($_GET['action']) {
        case 'add' :

          echo "<h2>Dodawanie nowego wpisu</h2>";
          echo "<p><strong>Lekarz:</strong> {$doctor['degree']} {$doctor['firstname']} {$doctor['lastname']} - {$doctor['specialization']}</p>";
          echo "<p><strong>Dzień:</strong> ".weekday(date('w', $unixDate)).', '.str_replace('-','.', $_GET['date'])."</p>";
          echo "<p><strong>Godziny pracy przychodni:</strong> ".(
            $mcWorkHours === null
            ? 'Brak określonych godz. pracy przychodni'
            : (
              $mcWorkHours['open-hour'][date('w', $unixDate)] == 0 || $mcWorkHours['close-hour'][date('w', $unixDate)] == 0
              ? '<span class="error">Nieczynne!</span>'
              : date('H:i', $mcWorkHours['open-hour'][date('w', $unixDate)]).' - '.date('H:i', $mcWorkHours['close-hour'][date('w', $unixDate)])
            )
          )."</p>";

          $addForm = new Form('add');
          $addForm->hidden('type', 'add');
          $addForm->hidden('doctor', $_GET['doctor']);
          $addForm->hidden('date', $_GET['date']);
          $addForm->text('start', 'Godzina rozpoczęcia pracy:', '', 'HH:MM');
          $addForm->text('end', 'Godzina zakończenia pracy:', '', 'HH:MM');
          $addForm->select('interval', 'Interwał wizyt:', [
            '10' => 'co 10 min',
            '12' => 'co 12 min',
            '15' => 'co 15 min',
            '20' => 'co 20 min',
            '30' => 'co 30 min',
            '60' => 'co 60 min',
          ]);
          $addForm->select('room', 'Gabinet:', $rooms);
          $addForm->place('Dodaj');

          break;

        case 'edit' :
          echo "<h2>Edycja istniejącego wpisu</h2>";
          echo "<p><strong>Lekarz:</strong> {$doctor['degree']} {$doctor['firstname']} {$doctor['lastname']} - {$doctor['specialization']}</p>";
          echo "<p><strong>Dzień:</strong> ".weekday(date('w', $unixDate)).', '.str_replace('-','.', $_GET['date'])."</p>";
          echo "<p><strong>Godziny pracy przychodni:</strong> ".(
            $mcWorkHours === null
            ? 'Brak określonych godz. pracy przychodni'
            : (
              $mcWorkHours['open-hour'][date('w', $unixDate)] == 0 || $mcWorkHours['close-hour'][date('w', $unixDate)] == 0
              ? '<span class="error">Nieczynne!</span>'
              : date('H:i', $mcWorkHours['open-hour'][date('w', $unixDate)]).' - '.date('H:i', $mcWorkHours['close-hour'][date('w', $unixDate)])
            )
          )."</p>";

          $editForm = new Form('edit');
          $editForm->hidden('type', 'edit');
          $editForm->hidden('doctor', $_GET['doctor']);
          $editForm->hidden('date', $_GET['date']);
          $editForm->text('start', 'Godzina rozpoczęcia pracy:', date('H:i', $schedule[0]['date']), 'HH:MM');
          $editForm->text('end', 'Godzina zakończenia pracy:', date('H:i', $schedule[count($schedule) - 1]['date'] + ($schedule[count($schedule) - 1]['date'] - $schedule[count($schedule) - 2]['date'])), 'HH:MM');
          $editForm->select('interval', 'Interwał wizyt:', [
            '10' => 'co 10 min',
            '12' => 'co 12 min',
            '15' => 'co 15 min',
            '20' => 'co 20 min',
            '30' => 'co 30 min',
            '60' => 'co 60 min',
          ], ($schedule[count($schedule) - 1]['date'] - $schedule[count($schedule) - 2]['date']));
          $editForm->select('room', 'Gabinet:', $rooms, $schedule[0]['room']);
          $editForm->place('Edytuj');
          break;
      }

    else : ?>
      <p>Tabela w przypadku wielu lekarzy, może być przesuwana również w bok</p>
      <div class="doctor-schedule--table--container">
        <table class='doctor-schedule--table'>
          <tr>
            <th class='date'>Dni \ Lekarze</th>
            <?php
              $doctorsQuery = sprintf('SELECT doctors.*, users.firstname, users.lastname, specializations.name as specialization FROM (doctors INNER JOIN users ON doctors.user = users.id) INNER JOIN specializations ON doctors.specialization = specializations.id ORDER BY specializations.name, users.lastname ASC');

              $doctors = $db->query($doctorsQuery);
              $doctors = $doctors->fetch_all(MYSQLI_ASSOC);

              foreach($doctors as $doctor) : ?>

                <th><?= $doctor['degree'].' '.$doctor['firstname'].' '.$doctor['lastname'].'<br />'.$doctor['specialization'] ?></th>

              <?php endforeach;
            ?>
          </tr>
          <?php

          $days = 14;

          for($i = 0; $i < $days; $i++) {
            echo "<tr>";
            echo "<td class='date'>".date('d.m.Y', strtotime("today +{$i} days")).'<br>'.weekday(date('w', strtotime("today +{$i} days")))."</td>";

            foreach($doctors as $doctor) : ?>
              <td>
              <?php

              $morning = strtotime("today +{$i} days midnight");
              $evening = strtotime("today +".($i + 1)." days midnight");

              $times = $db->query(sprintf("SELECT schedule.*, rooms.number as room FROM schedule JOIN rooms ON schedule.room = rooms.id WHERE schedule.date BETWEEN '%d' AND '%d' AND schedule.doctor = '%s'",
                $morning,
                $evening - 1,
                $doctor['id']
              ));

              if ($times->num_rows == 0) { ?>
                <a class='action-anchor' href='schedule.php?action=add&doctor=<?= $doctor['id'] ?>&date=<?= date('d-m-Y', strtotime("today +{$i} days")) ?>'>Dodaj</a>
              <?php }
              else {
                $times = $times->fetch_all(MYSQLI_ASSOC); ?>

                <a class='action-anchor' href='schedule.php?action=edit&doctor=<?= $doctor['id'] ?>&date=<?= date('d-m-Y', strtotime("today +{$i} days")) ?>'><?= date('H:i', $times[0]['date']).' - '.date('H:i', $times[count($times) - 1]['date'] + ($times[count($times) - 1]['date'] - $times[count($times) - 2]['date'])).' | '.$times[0]['room']; ?></a>

              <?php }

              ?>
              </td>
            <?php endforeach;

            echo "</tr>";
          }
          ?>
        </table>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php include_once "views/footer.php"; ?>
