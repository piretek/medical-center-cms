<?php

if (!defined('SECURE_BOOT')) exit();

?>

<nav class='authorize-nav'>
  <ul class='navigation'>
    <?php if (AUTHORIZED) : ?>

    <li>
      <a href='user-account.php'>
        <svg class='person-icon' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="18px" height="18px"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
        <span><?= "{$authorizedUser['firstname']} {$authorizedUser['lastname']}" ?></span>
      </a>
    </li>
    <li>
      <form action="login.php" method="POST" class='as-anchor'>
        <input type="hidden" value="logout" name="action">
        <button type="submit">Wyloguj się</button>
      </form>
    </li>

    <?php else : ?>

    <li>
      <a href='auth.php#login'>Zaloguj się</a>
    </li>
    <li>
      <a href='auth.php#register'>Zarejestruj się</a>
    </li>

    <?php endif; ?>
  </ul>
</nav>
