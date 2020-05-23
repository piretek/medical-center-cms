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
        header("Location: {$config['site_url']}/users.php?action=add");
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
      header("Location: {$config['site_url']}/users.php?action=add");
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
        header("Location: {$config['site_url']}/users.php?action=add");
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
        header("Location: {$config['site_url']}/users.php?action=edit&id=".$_POST['id']);
        exit;
      }
    }
    else {
      $_SESSION['error'] = 'Użytkownik nie istnieje';
      header("Location: {$config['site_url']}/users.php?action=edit&id=".$_POST['id']);
      exit;
    }
  }
  else if ($_POST['type'] == 'edit-user') {
    $acceptedKeys = [
      'email',
      'firstname',
      'lastname',
      'id',
      'role'
    ];

    foreach($acceptedKeys as $key) {
      if (!array_key_exists($key, $_POST)) {
        $_SESSION['error'] = 'Niepoprawne pola.';
        header("Location: {$config['site_url']}/users.php?action=edit&id=".$_POST['id']);
        exit();
      }
    }

    $ok = true;

    $toCheckIfEmpty = $acceptedKeys;
    foreach($_POST as $key => $value) {
      if (empty($_POST[$key]) && in_array($key, $toCheckIfEmpty)) {
        $ok = false;
        $_SESSION["users-edit-user-form-error-{$key}"] = 'Pole nie może być puste';
      }
    }

    $email = htmlentities(strtolower($_POST['email']), ENT_QUOTES, "UTF-8");
    $firstname = htmlentities($_POST ['firstname'], ENT_QUOTES, "UTF-8");
    $lastname = htmlentities($_POST['lastname'], ENT_QUOTES, "UTF-8");
    $role = htmlentities($_POST['role'], ENT_QUOTES, "UTF-8");

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $ok = false;
      $_SESSION["users-edit-user-form-error-email"] = "Niepoprawny email";
    }

    if (strlen($firstname) > 20){
      $ok = false;
      $_SESSION["users-edit-user-form-error-firstname"] = "Zbyt duża ilość znaków (maksymalnie 20).";
    }

    if (strlen($lastname) > 25){
      $ok = false;
      $_SESSION["users-edit-user-form-error-lastname"] = "Zbyt duża ilość znaków (maksymalnie 25).";
    }

    if (!is_numeric($role)){
      $ok = false;
      $_SESSION["users-edit-user-form-error-role"] = "Niepoprawna rola.";
    }

    if (!$ok) {
      $_SESSION['error'] = 'Popraw wszystkie pola!';
      header("Location: {$config['site_url']}/users.php?action=edit&id=".$post['id']);
      exit();
    }
    else {

      $query = sprintf("UPDATE users SET firstname = '%s', lastname = '%s', email = '%s', role='%s' WHERE id = '%d'",
        $db->real_escape_string($firstname),
        $db->real_escape_string($lastname),
        $db->real_escape_string($email),
        $db->real_escape_string($role),
        $db->real_escape_string($_POST['id'])
      );

      $successful = $db->query($query);

      if ($successful) {
        $_SESSION['success'] = 'Zaktualizowano';
        header("Location: {$config['site_url']}/users.php?action=edit&id=".$_POST['id']);
        exit;
      }
      else {
        $_SESSION['error'] = 'Błąd podczas wykonywania zapytania do bazy danych. '.$query;
        header("Location: {$config['site_url']}/users.php?action=edit&id=".$_POST['id']);
        exit;
      }
    }
  }
  else if ($_POST['type'] == 'edit-patient' || $_POST['type'] == 'create-patient') {
    $ok = true;

    $requiredFields = [
      'pesel',
      'phone',
      'street',
      'house_no',
      'city',
      'postcode'
    ];

    foreach($requiredFields as $field) {
      if (!array_key_exists($field, $_POST) || empty($_POST[$field])) {
        $ok = false;
        $_SESSION['users-'.$_POST['type'].'-form-error-'.$field] = 'To pole nie może być puste.';
      }
    }

    $post = [];
    foreach ($_POST as $key => $value) {
      $post[$key] = htmlentities($value, ENT_QUOTES, "UTF-8");
    }

    try {
      $pesel = new PESEL($post['pesel']);
    }
    catch(PESEL_Exception $e) {
      $ok = false;
      $_SESSION['users-'.$_POST['type'].'-form-error-pesel'] = $e->getMessage();
    }

    if (strlen($post['phone']) != 9 || !is_numeric($post['phone'])) {
      $ok = false;
      $_SESSION['users-'.$_POST['type'].'-form-error-phone'] = 'Niepoprawny format numeru telefonu';
    }

    if (strlen($post['street']) > 30) {
      $ok = false;
      $_SESSION['users-'.$_POST['type'].'-form-error-street'] = 'Ulica może mieć maks. 30 znaków.';
    }

    if (strlen($post['house_no']) > 10) {
      $ok = false;
      $_SESSION['users-'.$_POST['type'].'-form-error-house_no'] = 'Numer domu i mieszkania może mieć maks. 10 znaków.';
    }

    if (strlen($post['city']) > 20) {
      $ok = false;
      $_SESSION['users-'.$_POST['type'].'-form-error-city'] = 'Miasto może mieć maks. 20 znaków.';
    }

    if (!preg_match('/^[0-9]{2}\-[0-9]{3}$/', $post['postcode'])) {
      $ok = false;
      $_SESSION['users-'.$_POST['type'].'-form-error-postcode'] = 'Kod musi być w formacie XX-XXX.';
    }

    if ($ok) {
      if ($_POST['type'] == 'create-patient') {
        $query = sprintf("INSERT INTO patients VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
          $db->real_escape_string($post['id']),
          $db->real_escape_string($pesel->get()),
          $db->real_escape_string($post['phone']),
          $db->real_escape_string($post['street']),
          $db->real_escape_string($post['house_no']),
          $db->real_escape_string($post['city']),
          $db->real_escape_string($post['postcode']),
        );
      }
      else {
        $query = sprintf("UPDATE patients SET pesel = '%s', phone = '%s', street = '%s', house_no = '%s', city = '%s', postcode = '%s' WHERE user = '%d'",
          $db->real_escape_string($pesel->get()),
          $db->real_escape_string($post['phone']),
          $db->real_escape_string($post['street']),
          $db->real_escape_string($post['house_no']),
          $db->real_escape_string($post['city']),
          $db->real_escape_string($post['postcode']),
          $db->real_escape_string($post['id'])
        );
      }

      $successful = $db->query($query);

      if ($successful) {
        $_SESSION['success'] = 'Zaktualizowano!';
        header("Location: {$config['site_url']}/users.php?action=edit&id=".$post['id']);
        exit;
      }
      else {
        $_SESSION['error'] = 'Błąd zapytania do bazy danych. Skontaktuj się z administratorem.';
        header("Location: {$config['site_url']}/users.php?action=edit&id=".$post['id']);
        exit;
      }
    }
    else {
      $_SESSION['error'] = 'Popraw wszystkie pola!';
      header("Location: {$config['site_url']}/users.php?action=edit&id=".$post['id']);
      exit;
    }
  }
  else if ($_POST['type'] == 'doctor') {
    $doctorExists = $db->query(sprintf("SELECT * FROM doctors WHERE user = '%s'", $db->real_escape_string($_POST['id'])))->num_rows == 0 ? false : true;

    if ($doctorExists) {
      $query = sprintf('UPDATE doctors SET specialization = \'%d\', degree = \'%s\' WHERE user = \'%d\'',
        $db->real_escape_string($_POST['specialization']),
        $db->real_escape_string(htmlentities($_POST['degree'], ENT_QUOTES, "UTF-8")),
        $db->real_escape_string($_POST['id'])
      );
    }
    else {
      $query = sprintf("INSERT INTO doctors VALUES (NULL, '%d', '%d', '%s')",
        $db->real_escape_string($_POST['id']),
        $db->real_escape_string($_POST['specialization']),
        $db->real_escape_string(htmlentities($_POST['degree'], ENT_QUOTES, "UTF-8"))
      );
    }

    $successful = $db->query($query);

    if ($successful) {
      $_SESSION['success'] = $doctorExists ? 'Zmieniono!' : 'Stworzono!';
      header("Location: {$config['site_url']}/users.php?action=edit&id=".$_POST['id']);
      exit;
    }
    else {
      $_SESSION['error'] = 'Błąd zapytania do bazy danych. Skontaktuj się z administratorem.';
      header("Location: {$config['site_url']}/users.php?action=edit&id=".$_POST['id']);
      exit;
    }
  }
  else if ($_POST['type'] == 'change-password') {
    $acceptedKeys = [
      'password',
      'repeat-password',
    ];

    foreach($acceptedKeys as $key) {
      if (!array_key_exists($key, $_POST)) {
        $_SESSION['error'] = 'Niepoprawne pola.';
        header("Location: {$config['site_url']}/users.php?action=edit&id=".$_POST['id']);
        exit();
      }
    }

    $ok = true;

    $toCheckIfEmpty = $acceptedKeys;
    foreach($_POST as $key => $value) {
      if (empty($_POST[$key]) && in_array($key, $toCheckIfEmpty)) {
        $ok = false;
        $_SESSION["users-change-password-form-error-{$key}"] = 'Pole nie może być puste';
      }
    }

    $password = $_POST['password'];
    $repeatedPassword = $_POST['repeat-password'];

    if (!preg_match("/^(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password)) {
      $ok = false;
      $_SESSION["users-change-password-form-error-password"] = "Hasło musi się składać z 8 znaków, oraz musi mieć 1 dużą literę i znak specjalny.";
    }

    if ($password != $repeatedPassword) {
      $ok = false;
      $_SESSION["users-change-password-form-error-repeat-password"] = "Hasła muszą być takie same";
    }

    if (!$ok) {
      $_SESSION['error'] = 'Popraw wszystkie pola!';
      header("Location: {$config['site_url']}/users.php?action=edit&id=".$_POST['id']);
      exit;
    }
    else {
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      $query = sprintf("UPDATE users SET password = '%s' WHERE id = '%d'",
        $db->real_escape_string($hashedPassword),
        $db->real_escape_string($_POST['id'])
      );

      $successful = $db->query($query);

      if ($successful) {
        $_SESSION['success'] = 'Zaktualizowano';
        header("Location: {$config['site_url']}/users.php?action=edit&id=".$_POST['id']);
        exit;
      }
      else {
        $_SESSION['error'] = 'Błąd podczas wykonywania zapytania do bazy danych. '.$query;
        header("Location: {$config['site_url']}/users.php?action=edit&id=".$_POST['id']);
        exit;
      }
    }
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

$rolesArray = $db->query("SELECT * FROM roles ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
$roles = [];
foreach($rolesArray as $role) {
  $roles[$role['id']] = $role['name'];
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
              ->hidden('id', $user['id'])
              ->text('firstname', 'Imię', $user['firstname'])
              ->text('lastname', 'Nazwisko', $user['lastname'])
              ->email('email', 'E-mail', $user['email'])
              ->select('role', 'Rola', $roles, $user['role'])
              ->place('Zatwierdź');

            ?>
            <h2>Zmiana hasła</h2>
            <?php
            $changePassword->hidden('type', 'change-password')
              ->hidden('id', $user['id'])
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
              if ($db->query(sprintf("SELECT * FROM patients WHERE user = '%s'", $_GET['id']))->num_rows != 0) {
                include 'views/forms/edit-patient.php';
              }
              else {
                include 'views/forms/add-patient.php';
              }

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
