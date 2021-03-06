<?php
if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);


require_once 'includes/init.php';

if (AUTHORIZED) {
  header ("Location: {$config['site_url']}/create-patient-account.php");
  exit();
}

include_once 'views/header.php';
?>

<div class="columns col-center login-page">
  <div class="column col-30">
    <h1>Zaloguj się</h1>

    <?php if (isset($_SESSION['login-error'])) : ?>
      <span class='error'>Błąd: <?= $_SESSION['login-error'] ?></span>
      <?php unset($_SESSION['login-error']); ?>
    <?php endif; ?>

    <form action="login.php" method="POST">
      <input type="hidden" name="action" value="login">
      <div class="input--container">
        <label class="input--label" for="email"> E-mail: </label>
        <input class="input" id="email" type="text" name="email" placeholder="Podaj e-mail">
        <span class="input--error"><?php $error = 'login-form-error-email'; if (isset($_SESSION[$error])) { echo $_SESSION[$error]; unset($_SESSION[$error]); } ?></span>
      </div>
      <div class="input--container">
        <label class="input--label" for="password">Hasło: </label>
        <input class="input" type="password" name="password" id="password" placeholder="Podaj hasło">
        <span class="input--error"><?php $error = 'login-form-error-password'; if (isset($_SESSION[$error])) { echo $_SESSION[$error]; unset($_SESSION[$error]); } ?></span>
      </div>
      <div>
        <button class='beautiful' type="submit">Zaloguj</button>
      </div>
    </form>
  </div>
  <div class="column col-30a">
    <h1>Nie masz konta? Zarejestruj się</h1>

    <?php notification('auth-error', 'error'); ?>
    <?php notification('auth-success', 'success'); ?>

    <form action = "login.php" method="POST">
      <input type="hidden" name="action" value="register">
      <div class="input--container">
        <label class="input--label" for="name">Imię: </label>
        <input class="input" id="name" type="text" name="name" placeholder="Podaj swoje imię">
        <span class="input--error"><?php $error = 'register-form-error-name'; if (isset($_SESSION[$error])) { echo $_SESSION[$error]; unset($_SESSION[$error]); } ?></span>
      </div>
      <div class="input--container">
        <label class="input--label" for="sname">Nazwisko: </label>
        <input class="input" id='sname' type="text" name="sname" placeholder="Podaj swoje nazwisko">
        <span class="input--error"><?php $error = 'register-form-error-sname'; if (isset($_SESSION[$error])) { echo $_SESSION[$error]; unset($_SESSION[$error]); } ?></span>
      </div>
      <div class="input--container">
        <label class="input--label" for="email"> E-mail: </label>
        <input class="input" id="email" type="text" name="email" placeholder="Podaj e-mail">
        <span class="input--error"><?php $error = 'register-form-error-email'; if (isset($_SESSION[$error])) { echo $_SESSION[$error]; unset($_SESSION[$error]); } ?></span>
      </div>
      <div class="input--container">
        <label class="input--label" for="password">Hasło: </label>
        <input class="input" type="password" name="password" id="password" placeholder="Podaj hasło">
        <span class="input--error"><?php $error = 'register-form-error-password'; if (isset($_SESSION[$error])) { echo $_SESSION[$error]; unset($_SESSION[$error]); } ?></span>
      </div>
      <div class="input--container">
        <label class="input--label" for="confirm-password">Powtórz hasło: </label>
        <input class="input" type="password" name="confirm-password" id="confirm-password" placeholder="Potwierdź hasło">
        <span class="input--error"><?php $error = 'register-form-error-confirm-password'; if (isset($_SESSION[$error])) { echo $_SESSION[$error]; unset($_SESSION[$error]); } ?></span>
      </div>
      <div>
        <button class='beautiful' type="submit">Zarejestruj</button>
      </div>
    </form>
  </div>
</div>

<?php
  include_once 'views/footer.php';
?>
