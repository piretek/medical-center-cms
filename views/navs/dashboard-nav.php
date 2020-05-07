<?php
  $loadedFile = basename($_SERVER['PHP_SELF']);

  $cards = [
    [
      'url' => 'new-reservation.php',
      'name' => 'Zarezerwuj wizytę',
    ],
    [
      'url' => 'create-patient-account.php',
      'name' => 'Zarejestruj konto pacjenta'
    ],
    [
      'url' => 'user-reservations.php',
      'name' => 'Historia wizyt'
    ],
    [
      'url' => 'reservations.php',
      'name' => 'Zarządzanie rezerwacjami'
    ],
    [
      'url' => 'schedule.php',
      'name' => 'Grafik lekarzy'
    ],
    [
      'url' => 'users.php',
      'name' => 'Użytkownicy'
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
