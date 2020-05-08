<?php

if (!defined('SECURE_BOOT')) exit();

$cards = [
  [
    'url' => "{$config['site_url']}/",
    'name' => 'Strona główna',
  ],
  [
    'url' => 'about.php',
    'name' => 'O firmie'
  ],
  [
    'url' => 'new-reservation.php',
    'name' => 'Umów wizytę'
  ],
  [
    'url' => 'contact.php',
    'name' => 'Kontakt'
  ],
];

?>
<nav class='header-navigation'>
  <ul class='navigation'>
  <?php foreach($cards as $card) : ?>
      <li>
        <a href='<?= $card['url'] ?>'><?= $card['name'] ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>
