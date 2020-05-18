<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'Użytkownicy');
define('PAGE_NEEDS_AUTHORIZATION', true);

require_once "includes/init.php";

if (!IS_ADMIN) {
  header("Location: {$config['site_address']}/auth.php");
  exit;
}

if (isset($_POST['type'])) {
  if ($_POST['type'] == 'add-user') {

  }
  else if ($_POST['type'] == 'edit-user') {

  }
  else if ($_POST['type'] == 'remove-user') {

  }
  else if ($_POST['type'] == 'edit-patient') {

  }
  else if ($_POST['type'] == 'doctor') {

  }
}

include_once "views/header.php"; ?>

<main>
  <?php include_once 'views/navs/dashboard-nav.php' ?>

  <div class='paper'>
    <?php if (isset($_GET['action']) && !empty($_GET['action'])) : ?>
      <?php if ($_GET['action'] == 'edit') : ?>
        <h1 class='paper-title'>Edycja użytkownika</h1>
        <?php

        $remove = new Form('remove');
        $remove->hidden('type', 'remove-user');
        $remove->place('Usuń użytkownika');

        ?>
        <div class='columns'>
          <div class="column col-1per3">
            <?php

            $form = new Form('edit');

            $form->hidden('type', 'edit-user')
              ->text('firstname', 'Imię')
              ->text('lastname', 'Nazwisko')
              ->email('email', 'E-mail')
              ->password('password', 'Nowe hasło')
              ->password('repeat-password', 'Powtórz nowe hasło')
              ->place('Zatwierdź');

            ?>
          </div>
          <div class="column col-1per3">
            <?php
              define('PATIENT_FORM_ID', $_GET['id']);
              define('PATIENT_FORM_ALLOW_PESEL', true);
              include 'views/forms/edit-patient.php'
            ?>
          </div>
          <div class="column col-1per3">
            <?php
              define('DOCTOR_FORM_ID', $_GET['id']);
              include 'views/forms/doctor.php'
            ?>
          </div>
        </div>
      <?php elseif ($_GET['action'] == 'add') : ?>
        <h1 class='paper-title'>Dodaj użytkownika</h1>
        <?php

        $rolesArray = $db->query("SELECT * FROM roles ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
        $roles = [];
        foreach($rolesArray as $role) {
          $roles[$role['id']] = $role['name'];
        }

        $form = new Form('add');
        $form->hidden('type', 'add-user')
          ->text('firstname', 'Imię')
          ->text('lastname', 'Nazwisko')
          ->email('email', 'E-mail')
          ->password('password', 'Nowe hasło')
          ->password('repeat-password', 'Powtórz nowe hasło')
          ->select('role', 'Rola', $roles)
          ->place('Zatwierdź');

      endif; ?>
    <?php else : ?>
      <h1 class='paper-title'>Użytkownicy</h1>
      <a href="<?= $config['site_url']."/users.php?action=add" ?>">
        <button>Dodaj nowego użytkownika</button>
      </a>
      <table>
        <tr>
          <th>ID</th>
          <th>Imię i nazwisko</th>
          <th>E-mail</th>
          <th>Rola</th>
          <th>Akcje</th>
        </tr>
        <tr>
        <?php

        $users = $db->query('SELECT users.*, roles.name AS role FROM users INNER JOIN roles ON users.role = roles.id');
        if ($users->num_rows == 0) : ?>

        <tr>
          <td colspan="5">Brak użytkowników</td>
        </tr>

        <?php else :

          while($user = $users->fetch_assoc()) : ?>

            <tr>
              <td><?= $user['id'] ?></td>
              <td><?= $user['firstname'].' '.$user['lastname'] ?></td>
              <td><?= $user['email'] ?></td>
              <td><?= $user['role'] ?></td>
              <td>
                <a class='action-anchor' href='users.php?action=edit&id=<?= $user['id'] ?>'>Zarządzaj</a>
              </td>
            </tr>

          <?php endwhile; endif; ?>
        </tr>
      </table>
    <?php endif; ?>
  </div>
</main>

<?php include_once "views/footer.php"; ?>
