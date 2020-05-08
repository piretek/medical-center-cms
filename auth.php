<?php

define ('PAGE_TITLE','Logowanie');
define ('PAGE_NEEDS_AUTHORIZATION', false);
require_once 'includes/init.php';

if (AUTHORIZED) {
  header ('Location: dashboard.php');
  exit();
}

include_once 'views/header.php';
?>

<div class="columns col-center">
  <div class="columns">
    <div class="column col-100">
      <h1>Zaloguj się</h1>

      <?php if (isset($_SESSION['auth-error'])) : ?>
        <span class='error'>Błąd: <?= $_SESSION['auth-error'] ?></span>
        <?php unset($_SESSION['auth-error']); ?>
      <?php endif; ?>

      <form action = "login.php" method="POST">
        <div class="input--container">
          <label class="input--label" for="email"> E-mail: </label>
          <input class="input" id="email" type="text" name="email" placeholder="Podaj e-mail">
        </div>
        <div class="input--container">
          <label class="input--label" for="password">Hasło: </label>
          <input class="input" type="password" name="password" id="password" placeholder="Podaj hasło">
        </div>
        <div>
          <button class='beautiful' type="submit">Zaloguj</button>
        </div>
      </form>
    </div>
    <div class="column col-100">
      <h1>Nie masz konta? Zarejestruj się</h1>

      <?php if (isset($_SESSION['auth-error'])) : ?>
            <span class='error'>Błąd: <?= $_SESSION['auth-error'] ?></span>
            <?php unset($_SESSION['auth-error']); ?>
      <?php endif; ?>

      <form action = "login.php" method="POST">
        <div class="input--container">
          <label class="input--label" for="email"> Imię: </label>
          <input class="input" id="name" type="text" name="name" placeholder="Podaj swoje imię">
        </div>
        <div class="input--container">
          <label class="input--label" for="password">Nazwisko: </label>
          <input class="input" type="text" name="sname" id="password" placeholder="Podaj swoje nazwisko">
        </div>
        <div class="input--container">
          <label class="input--label" for="email"> E-mail: </label>
          <input class="input" id="email" type="text" name="email" placeholder="Podaj e-mail">
        </div>
        <div class="input--container">
          <label class="input--label" for="password">Hasło: </label>
          <input class="input" type="password" name="password" id="password" placeholder="Podaj hasło">
        </div>
        <div class="input--container">
          <label class="input--label" for="confirm-password">Hasło: </label>
          <input class="input" type="password" name="confirm-password" id="confirm-password" placeholder="Potwierdź hasło">
        </div>
        <div>
          <button class='beautiful' type="submit">Zarejestruj</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
  include_once 'views/footer.php';
?>