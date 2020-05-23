<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'Ustawienia systemu');
define('PAGE_NEEDS_AUTHORIZATION', true);

require_once "includes/init.php";

if (!IS_ADMIN) {
  header("Location: {$config['site_url']}/auth.php");
  exit;
}

if (isset($_POST['type'])) {
  list($action, $type) = explode('-', $_POST['type']);
  $ok = true;

  switch ($_POST['type']) {
    case "add-room" :
    case "add-specialization" :
    case "edit-room" :
    case "edit-specialization" :

      if (empty($_POST['name'])) {
        $ok = false;
        $_SESSION['settings-form-error-name'] = 'Pole nie może być puste.';
      }

      if ($action == 'edit' && (!isset($_POST['id']) || empty($_POST['id']))) {
        $ok = false;
      }

      if ($type == 'room') {
        if (strlen($_POST['name']) > 10) {
          $ok = false;
          $_SESSION['settings-form-error-name'] = 'Numer pokoju może mieć maks. 10 znaków.';
        }
      }
      else if ($type == 'specialization') {
        if (strlen($_POST['name']) > 20) {
          $ok = false;
          $_SESSION['settings-form-error-name'] = 'Nazwa specjalizacji może mieć maks. 20 znaków.';
        }
      }

      if ($ok) {
        if ($action == 'add') {
          $query = sprintf("INSERT INTO %s VALUES (NULL, '%s')",
            $db->real_escape_string($type).'s',
            $db->real_escape_string(htmlentities($_POST['name'], ENT_QUOTES, 'UTF-8'))
          );
        }
        else if ($action == 'edit') {
          $query = sprintf("UPDATE %s SET %s = '%s' WHERE id = '%s'",
            $db->real_escape_string($type).'s',
            $db->real_escape_string(($type == 'room' ? 'number' : 'name')),
            $db->real_escape_string(htmlentities($_POST['name'], ENT_QUOTES, 'UTF-8')),
            $db->real_escape_string($_POST['id'])
          );
        }

        $successful = $db->query($query);

        if ($successful) {
          $_SESSION['success'] = $action == 'add' ? 'Dodano' : 'Zmieniono';
          header("Location: {$config['site_url']}/settings.php#{$type}s");
          exit;
        }
        else {
          $_SESSION['error'] = 'Błąd podczas wykonywania zapytania do bazy danych.';
          header("Location: {$config['site_url']}/settings.php?action={$_POST['type']}&id={$_POST['id']}");
          exit;
        }
      }
      else {
        $_SESSION['error'] = 'Popraw wszelkie błędy';
        header("Location: {$config['site_url']}/settings.php?action={$_POST['type']}&id={$_POST['id']}");
        exit;
      }

      break;

    case "remove-room" :
    case "remove-specialization" :

      if (!isset($_POST['id']) || empty($_POST['id'])) {
        $_SESSION['error'] = 'Błąd usuwania.';
        header("Location: {$config['site_url']}/settings.php#{$type}s");
        exit;
      }

      $query = sprintf("DELETE FROM %s WHERE id = '%s'",
        $db->real_escape_string($type).'s',
        $db->real_escape_string($_POST['id'])
      );

      $successful = $db->query($query);

      if ($successful) {
        $_SESSION['success'] = 'Usunięto';
        header("Location: {$config['site_url']}/settings.php#{$type}s");
        exit;
      }
      else {
        $_SESSION['error'] = 'Błąd podczas wykonywania zapytania do bazy danych.';
        header("Location: {$config['site_url']}/settings.php#{$type}s");
        exit;
      }

      break;

    case "closing-hours" :
      $setting = $db->query("SELECT * FROM settings WHERE name = 'CLOSING-HOURS'");

      $requiredArrayKeys = [
        'close-hour',
        'open-hour',
      ];

      $requiredArraySubKeys = ['0', '1', '2', '3', '4', '5', '6'];

      $post = [
        'close-hour' => [],
        'open-hour' => [],
      ];

      $ok = true;

      foreach($requiredArrayKeys as $key) {
        if (is_array($_POST[$key])) {
          foreach($requiredArraySubKeys as $subKey) {
            $remold = false;

            if (is_string($_POST[$key][$subKey])) {
              if (!empty($_POST[$key][$subKey])) {
                if (!preg_match('/^(0[0-9]|1[0-9]|2[0-3])(:[0-5][0-9])$/', $_POST[$key][$subKey])) {
                  $ok = false;
                  $_SESSION["settings-closing-hours-form-error-{$key}[{$subKey}]"] = 'Wartość musi być w formacie HH:MM';
                }
                else {
                  $remold = true;
                }
              }
              else {
                $remold = true;
              }
            }
            else {
              $ok = false;
              $_SESSION["settings-closing-hours-form-error-{$key}[{$subKey}]"] = 'Niepoprawna wartość pola';
            }

            if ($remold) {
              if(!empty($_POST[$key][$subKey])){
                list($h, $m) = explode(':', $db->real_escape_string($_POST[$key][$subKey]));
                $post[$key][$subKey] = mktime($h, $m, 0, 1, 1, 1970);
              }
              else{
                $post[$key][$subKey] = 0;
              }
            }
          }
        }
        else {
          $_SESSION['error'] = 'Nieprawidłowe pole';
          header("Location: {$config['site_url']}/settings.php#system");
          exit;
        }
      }

      if ($ok) {
        if ($setting->num_rows == 0) {
          $query = sprintf("INSERT INTO settings VALUES (NULL, 'CLOSING-HOURS', '%s')",
            serialize($post)
          );
        }
        else {
          $query = sprintf("UPDATE settings SET value = '%s' WHERE name = 'CLOSING-HOURS'",
            serialize($post)
          );
        }

        $successful = $db->query($query);

        if ($successful) {
          $_SESSION['success'] = 'Zmieniono';
          header("Location: {$config['site_url']}/settings.php#system");
          exit;
        }
        else {
          $_SESSION['error'] = 'Błąd wykonywania zapytania do bazy danych. Skontaktuj się z administratorem.';
          header("Location: {$config['site_url']}/settings.php#system");
          exit;
        }
      }
      else {
        $_SESSION['error'] = 'Popraw błędy';
        header("Location: {$config['site_url']}/settings.php#system");
        exit;
      }

      break;
  }
  exit;
}

if (isset($_GET['action']) && !empty($_GET['action'])) {
  list($action, $type) = explode('-', $_GET['action']);

  if ($action == 'edit' && (!isset($_GET['id']) || (empty($_GET['id']) || !is_numeric($_GET['id'])))) {
    header("Location: {$config['site_url']}/settings.php");
    exit;
  }
  else if ($action == 'edit') {
    $result = $db->query(sprintf("SELECT * FROM %s WHERE id = '%d'",
      $db->real_escape_string($type.'s'),
      $db->real_escape_string($_GET['id'])
    ));

    if ($result->num_rows == 0) {
      header("Location: {$config['site_url']}/settings.php");
      exit;
    }

    $editForm = $result->fetch_assoc();
  }
}

include_once "views/header.php"; ?>

<main>
  <?php include_once 'views/navs/dashboard-nav.php' ?>

  <div class='paper'>
    <h1 class='paper-title'>
      <?php

      if (isset($action) && $action == 'edit') {
        echo 'Edytuj '.($type == 'room' ? 'gabinet' : 'specjalizację');
      }
      elseif (isset($action) && $action == 'add') {
        echo 'Dodaj '.($type == 'room' ? 'gabinet' : 'specjalizację');
      }
      else {
        echo 'Ustawienia systemu';
      }

      ?>
    </h1>

    <?php

    if (isset($_GET['action']) && !empty($_GET['action'])) :
      switch($_GET['action']) {

        case 'add-room' :
        case 'add-specialization' :
        case 'edit-room' :
        case 'edit-specialization' :

          notification('error', 'error');

          $form = new Form($_GET['action']);

          $form->hidden('type', $_GET['action']);

          if ($action == 'edit') $form->hidden('id', $_GET['id']);

          $form->text(
            'name',
            ($type == 'room'
              ? 'Numer pokoju'
              : ($type == 'specialization'
                ? 'Nazwa specjalizacji'
                : 'Nazwa')),
            ($action == 'edit'
              ? $editForm[$type == 'room' ? 'number' : 'name']
              : '')
          );

          $form->place($action == 'add' ? 'Dodaj' : 'Edytuj');

          break;

      }
    else : ?>
      <div class='cards'>
        <div class='cards-tabs'>
          <div for='specializations' class='cards-tabs--tab'>Specjalizacje</div>
          <div for='rooms' class='cards-tabs--tab'>Gabinety</div>
          <div for='system' class='cards-tabs--tab'>Systemowe</div>
        </div>

        <?php notification('success', 'success'); ?>
        <?php notification('error', 'error'); ?>

        <div class='cards-sections'>
          <div id='specializations' class='cards-sections--section'>
            <a href='settings.php?action=add-specialization'><button class='add'>Dodaj nową specjalizację</button></a>
            <table>
              <tr>
                <th>Nazwa</th>
                <th>Akcje</th>
              </tr>
              <?php

              $specializations = $db->query("SELECT * FROM specializations ORDER BY name");
              if ($specializations->num_rows != 0) : ?>

              <?php while($specialization = $specializations->fetch_assoc()) : ?>

                <tr>
                  <td><?= $specialization['name'] ?></td>
                  <td>
                    <a class='action-anchor' href='settings.php?action=edit-specialization&id=<?= $specialization['id'] ?>'>Edytuj</a> | <?php $r = new Form('remove-specialization-'.$specialization['id'], 'POST', '', 'as-anchor remove-prompt'); $r->hidden('type', 'remove-specialization')->hidden('id', $specialization['id'])->place('Usuń'); ?>
                  </td>
                </tr>

              <?php endwhile; ?>

              <?php else : ?>

              <tr>
                <td class='no-entries' colspan='2'>Brak wyników</td>
              </tr>

              <?php endif; ?>
            </table>
          </div>
          <div id='rooms' class='cards-sections--section'>
            <a href='settings.php?action=add-room'><button class='add'>Dodaj nowy gabinet</button></a>
            <table>
              <tr>
                <th>Numer</th>
                <th>Akcje</th>
              </tr>
              <?php

              $rooms = $db->query("SELECT * FROM rooms ORDER BY number");
              if ($rooms->num_rows != 0) : ?>

              <?php while($room = $rooms->fetch_assoc()) : ?>

                <tr>
                  <td><?= $room['number'] ?></td>
                  <td>
                    <a class='action-anchor' href='settings.php?action=edit-room&id=<?= $room['id'] ?>'>Edytuj</a> | <?php $r = new Form('remove-room-'.$room['id'], 'POST', '', 'as-anchor remove-prompt'); $r->hidden('type', 'remove-room')->hidden('id', $room['id'])->place('Usuń'); ?>
                  </td>
                </tr>

              <?php endwhile; ?>

              <?php else : ?>

              <tr>
                <td class='no-entries' colspan='2'>Brak wyników</td>
              </tr>

              <?php endif; ?>
            </table>
          </div>
          <div id='system' class='cards-sections--section'>
            <h2>Godziny otwarcia</h2>
            <div class="columns">
              <div class="column col-25">
                <p>Pozostawiając pole puste, system potraktuje dzień jako taki, w który przychodnia jest zamknięta. Aby stwierdzić, że przychodnia jest w dany dzień otwarta, obydwa pola - godzina otwarcia i zamknięcia muszą być wypełnione.</p>
                <?php

                $hours = $db->query("SELECT * FROM settings WHERE name = 'CLOSING-HOURS'");
                if ($hours->num_rows != 0) {
                  $hours = unserialize($hours->fetch_assoc()['value']);

                  foreach($hours as $key => $hoursArray) {
                    $hours[$key] = array_map(function($unix) {
                      return $unix != '0' ? date('H:i', $unix) : '';
                    }, $hours[$key]);
                  }
                }

                $days = ['1' => 'poniedziałek', '2' => 'wtorek', '3' => 'środę', '4' => 'czwartek', '5' => 'piątek', '6' => 'sobotę', '0' => 'niedzielę'];
                $closingHours = new Form('closing-hours');

                $closingHours->hidden('type', 'closing-hours');

                foreach($days as $index => $day) {
                  $closingHours->text('open-hour['.$index.']', 'Godzina otwarcia w '.$day, isset($hours['open-hour'][$index]) ? $hours['open-hour'][$index] : '', 'HH:MM');
                  $closingHours->text('close-hour['.$index.']', 'Godzina zamknięcia w '.$day,  isset($hours['close-hour'][$index]) ? $hours['close-hour'][$index] : '', 'HH:MM');
                }

                $closingHours->place();

                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php include_once "views/footer.php"; ?>
