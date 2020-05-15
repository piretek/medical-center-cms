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
  list($section, $action) = explode('-', $_POST['type']);
  $ok = true;

  switch ($_POST['type']) {
    case "add-room" :
    case "add-specialization" :

      if ($section == 'rooms') {
        if (strlen($_POST['value']) > 10) {
          $ok = false;
          $errors['value'] = 'Numer pokoju może mieć maks. 10 znaków.';
        }
      }
      else if ($section == 'specializations') {
        if (strlen($_POST['value']) > 20) {
          $ok = false;
          $errors['value'] = 'Nazwa specjalizacji może mieć maks. 20 znaków.';
        }
      }

      if ($ok) {
        $successful = $db->query(sprintf("INSERT INTO %s VALUES (NULL, '%s')",
          $db->real_escape_string($action),
          $db->real_escape_string(htmlentities($_POST['value'], ENT_QUOTES, 'UTF-8'))
        ));

        if ($successful) {
          $_SESSION['success'] = 'Dodano';
          header("Location: {$config['site_address']}/settings.php");
          exit;
        }
        else {
          $_SESSION['error'] = 'Błąd podczas wykonywania zapytania do bazy danych.';
          header("Location: {$config['site_address']}/settings.php?action=".$_POST['type']);
          exit;
        }
      }
      else {
        $_SESSION['error'] = 'Popraw wszelkie błędy';
        header("Location: {$config['site_address']}/settings.php?action=".$_POST['type']);
        exit;
      }

    break;

    case "edit-room" :
    case "edit-specialization" :

      echo 'edited - '.var_export($_POST, true);
    break;

    case "remove-room" :
    case "remove-specialization" :

      echo 'removed - '.var_export($_POST, true);
    break;
  }
  exit;
}

include_once "views/header.php"; ?>

<main>
  <?php include_once 'views/navs/dashboard-nav.php' ?>

  <div class='paper'>
    <h1 class='paper-title'>Ustawienia systemu</h1>
    <?php

    if (isset($_GET['action']) && !empty(isset($_GET['action']))) :
      switch($_GET['action']) {

        case 'add-room' :
        case 'add-specialization' :
        case 'edit-room' :
        case 'edit-specialization' :
          list($action, $type) = explode('-', $_GET['action']);



          break;

      }
    else : ?>
      <div class='cards'>
        <div class='cards-tabs'>
          <div for='specializations' class='cards-tabs--tab'>Specjalizacje</div>
          <div for='rooms' class='cards-tabs--tab'>Gabinety</div>
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

              $rooms = $db->query("SELECT * FROM specializations");
              if ($rooms->num_rows != 0) : ?>

              <?php while($room = $rooms->fetch_assoc()) : ?>

                <tr>
                  <td><?= $room['number'] ?></td>
                  <td>
                    <a href='settings.php?action=edit-specialization'>Edytuj</a> | <a href='#'>Usuń</a>
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

              $rooms = $db->query("SELECT * FROM rooms");
              if ($rooms->num_rows != 0) : ?>

              <?php while($room = $rooms->fetch_assoc()) : ?>

                <tr>
                  <td><?= $room['number'] ?></td>
                  <td>
                    <a href='settings.php?action=edit-room'>Edytuj</a> | <a href='#'>Usuń</a>
                    <a href='settings.php?action=edit-room'>Edytuj</a> | <a href='#'>Usuń</a>
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
        </div>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php include_once "views/footer.php"; ?>
