<?php

if (!defined('SECURE_BOOT')) exit();

$loadedFile = basename($_SERVER['PHP_SELF']);

$cards = [
  [
    'url' => 'new-reservation.php',
    'name' => 'Zarezerwuj wizytę',
    'permission' => IS_PATIENT
  ],
  [
    'url' => 'create-patient-account.php',
    'name' => 'Zarejestruj konto pacjenta',
    'permission' => !IS_PATIENT
  ],
  [
    'url' => 'user-reservations.php',
    'name' => 'Historia wizyt',
    'permission' => IS_PATIENT
  ],
  [
    'url' => 'reservations.php',
    'name' => 'Zarządzanie rezerwacjami',
    'permission' => IS_ADMIN || IS_DOCTOR || IS_EMPLOYEE
  ],
  [
    'url' => 'schedule.php',
    'name' => 'Grafik lekarzy',
    'permission' => IS_ADMIN || IS_EMPLOYEE
  ],
  [
    'url' => 'users.php',
    'name' => 'Użytkownicy',
    'permission' => IS_ADMIN
  ],
  [
    'url' => 'user-account.php',
    'name' => 'Twoje konto'
  ],
];

?>

<nav class='dashboard-navigation'>
  <ul class='dashboard'>
    <?php foreach($cards as $card) : if (!array_key_exists('permission', $card) || (array_key_exists('permission', $card) && $card['permission'])) : ?>
      <li>
        <a <?= $loadedFile == $card['url'] ? 'class="active"' : '' ?>  href='<?= $card['url'] ?>'><?= $card['name'] ?></a>
      </li>
    <?php endif; endforeach; ?>
  </ul>
</nav>
