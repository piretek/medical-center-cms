<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'Użytkownicy');
define('PAGE_NEEDS_AUTHORIZATION', true);

require_once "includes/init.php";

if (!IS_ADMIN) {
  header("Location: {$config['site_address']}/users.php");
  exit;
}

if (isset($_POST['type'])) {
  if ($_POST['type'] == 'add-user') {
    $acceptedKeys = [
      'email',
      'password',
      'firstname',
      'lastname',
      'repeat-password'
    ];

    foreach($acceptedKeys as $key) {
      if (!array_key_exists($key, $_POST)) {
        $_SESSION['error'] = 'Niepoprawne pola.';
        header("Location: {$config['site_url']}/users.php");
        exit();
      }
    }

    $email = htmlentities(strtolower($_POST['email']), ENT_QUOTES, "UTF-8");
    $password = $_POST['password'] ;
    $repeatedPassword = $_POST['repeat-password'];
    $firstname = htmlentities($_POST ['firstname'], ENT_QUOTES, "UTF-8");
    $lastname = htmlentities($_POST['lastname'], ENT_QUOTES, "UTF-8");

    $ok = true;

    foreach($_POST as $key => $value) {
      if (empty($_POST[$key])) {
        $ok = false;
        $_SESSION["users-add-user-form-error-{$key}"] = 'Pole nie może być puste';
      }
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $ok = false;
      $_SESSION["users-add-user-form-error-email"] = "Niepoprawny email";
    }

    if (!preg_match("/^(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password)) {
      $ok = false;
      $_SESSION["users-add-user-form-error-password"] = "Niepoprawne hasło";
    }

    if ($password != $repeatedPassword) {
      $ok = false;
      $_SESSION["users-add-user-form-error-repeat-password"] = "Hasła muszą być takie same";
    }

    if (strlen($firstname) > 20){
      $ok = false;
      $_SESSION["users-add-user-form-error-firstname"] = "Zbyt duża ilość znaków (maksymalnie 20).";
    }

    if (strlen($lastname) > 25){
      $ok = false;
      $_SESSION["users-add-user-form-error-lastname"] = "Zbyt duża ilość znaków (maksymalnie 25).";
    }

    if (!$ok) {
      $_SESSION['error'] = 'Popraw pola';
      header("Location: {$config['site_url']}/users.php");
      exit();
    }
    else {
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      $createUserQuery = sprintf("INSERT INTO users (email, password, firstname, lastname, role) VALUES ('%s', '%s','%s','%s', '%s')",
        $db->real_escape_string($email),
        $db->real_escape_string($hashedPassword),
        $db->real_escape_string($firstname),
        $db->real_escape_string($lastname),
        $db->real_escape_string($_POST['role'])
      );

      $successful = $db->query($createUserQuery);
      if ($successful) {
        $_SESSION['success'] = 'Użytkownik pomyślnie zarejestrowany.';
        header("Location: {$config['site_url']}/users.php");
        exit;
      }
      else {
        $_SESSION['error'] = 'Błąd podczas tworzenia użytkownika w bazie danych.';
        header("Location: {$config['site_url']}/users.php");
        exit;
      }
    }
  }
  else if ($_POST['type'] == 'remove-user') {
    $users = $db->query(sprintf("SELECT * FROM users WHERE id = '%s'", $db->real_escape_string($_POST['id'])));
    if ($users->num_rows != 0) {

      $successful = $db->query(sprintf("DELETE FROM users WHERE id = '%s'", $db->real_escape_string($_POST['id'])));
      if ($successful) {
        $_SESSION['success'] = 'Użytkownik pomyślnie usunięty.';
        header("Location: {$config['site_url']}/users.php");
        exit;
      }
      else {
        $_SESSION['error'] = 'Błąd podczas usuwania użytkownika w bazie danych.';
        header("Location: {$config['site_url']}/users.php");
        exit;
      }
    }
    else {
      $_SESSION['error'] = 'Użytkownik nie istnieje';
      header("Location: {$config['site_url']}/users.php");
      exit;
    }
  }
  else if ($_POST['type'] == 'edit-user') {
    var_dump($_POST); exit;
  }
  else if ($_POST['type'] == 'edit-patient') {
    var_dump($_POST); exit;
  }
  else if ($_POST['type'] == 'doctor') {
    var_dump($_POST); exit;
  }
  else if ($_POST['type'] == 'change-password') {
    var_dump($_POST); exit;
  }
}

if (isset($_GET['action']) && !empty($_GET['action'])) {
  if ($_GET['action'] == 'edit' & isset($_GET['id']) && !empty($_GET['id'])) {
    $users = $db->query(sprintf("SELECT * FROM users WHERE id = '%s'", $db->real_escape_string($_GET['id'])));

    if ($users->num_rows == 0) {
      header("Location: {$config['site_url']}/users.php");
      exit;
    }
    else {
      $user = $users->fetch_assoc();
    }
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

        notification('error', 'error');
        notification('success', 'success');

        $remove = new Form('remove');
        $remove->hidden('type', 'remove-user');
        $remove->hidden('id', $user['id']);
        $remove->place('Usuń użytkownika');

        ?>
        <div class='columns'>
          <div class="column col-1per3">
            <h2>Ustawienia konta</h2>
            <?php

            $form = new Form('edit');
            $changePassword = new Form('change-password');

            $form->hidden('type', 'edit-user')
              ->text('firstname', 'Imię', $user['firstname'])
              ->text('lastname', 'Nazwisko', $user['lastname'])
              ->email('email', 'E-mail', $user['email'])
              ->place('Zatwierdź');

            ?>
            <h2>Zmiana hasła</h2>
            <?php
            $changePassword->hidden('type', 'change-password')
              ->password('password', 'Nowe hasło')
              ->password('repeat-password', 'Powtórz nowe hasło')
              ->place('Zatwierdź');

            ?>
          </div>
          <div class="column col-1per3">
            <h2>Ustawienia konta pacjenta</h2>
            <?php
              define('PATIENT_FORM_ID', $_GET['id']);
              define('PATIENT_FORM_ALLOW_PESEL', true);
              include 'views/forms/edit-patient.php'
            ?>
          </div>
          <div class="column col-1per3">
            <h2>Ustawienia konta lekarza</h2>
            <?php
              define('DOCTOR_FORM_ID', $_GET['id']);
              include 'views/forms/doctor.php'
            ?>
          </div>
        </div>
      <?php elseif ($_GET['action'] == 'add') : ?>
        <h1 class='paper-title'>Dodaj użytkownika</h1>
        <?php

        notification('error', 'error');

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
      <div class='m-tb-2'>
        <?php notification('success', 'success'); ?>
      </div>
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
                <a class='action-anchor' href='users.php?action=edit&id=<?= $user['id'] ?>'>Edytuj</a>
              </td>
            </tr>

          <?php endwhile; endif; ?>
        </tr>
      </table>
    <?php endif; ?>
  </div>
</main>

<?php include_once "views/footer.php"; ?>
