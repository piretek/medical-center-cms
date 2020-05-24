<?php
if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'Kontakt');
define('PAGE_NEEDS_AUTHORIZATION', false);

require_once 'includes/init.php';

if (isset($_POST['type']) && $_POST['type'] == 'contact-form'){
  $_SESSION['success'] = "Wiadomość została wysłana!";
  header("Location: {$config['site_url']}/contact.php");
  exit;
}

include_once 'views/header.php';
?>

<div class="column col-center">
  <div class="contact-info">
    <div class="column col-30 info-1">
      <h1><img class='contact-icon' src="./assets/images/contact/info.png">Nasze dane</h1>
      <div class="contact-content">
        <p><b>Nazwa:</b> Centrum Medyczne Korona Center Sp. z.o.o. </p>
        <p><b>Telefon:</b> 555-555-555</p>
        <p><b>FAX:</b> 81 555 55 55</p>
        <p><b>REGON:</b> 876231234</p>
        <p><b>NIP:</b> 712-231-27-34</p>
        <p><b>E-mail:</b> korona.center@medical.center</p>
        <p><b>Adres:</b> Lublin, ul. Radziwiłowska 13</p>
        <p><b>Godziny otwarcia:</b> Pn - Pt: 8:00 - 20:00</p>
      </div>
    </div>
    <div class="column col-40 info-2 paper">
      <h1><img class='contact-icon' src="./assets/images/contact/write.png">Napisz do nas</h1>
      <div><?php notification('success', 'success'); ?></div>
      <?php
        $contactForm = new Form('contact');

        $contactForm->hidden('type', 'contact-form')
        ->text('firstname', 'Imię', AUTHORIZED ? $authorizedUser['firstname'] : '')
        ->text('lastname', 'Nazwisko', AUTHORIZED ? $authorizedUser['lastname'] : '')
        ->email('email', 'E-mail', AUTHORIZED ? $authorizedUser['email'] : '')
        ->text('content','Wiadomość')
        ->place('Wyślij');

      ?>
    </div>
    <div class="column col-30 info-3">
      <h1><img class='contact-icon' src="./assets/images/contact/map.png">Gdzie nas znajdziesz?</h1>
      <iframe class="map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2497.338573505332!2d22.556438951358313!3d51.24967523723501!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x472257680fd8cd17%3A0xc8a0244d385c1cb2!2sRadziwi%C5%82%C5%82owska%2013%2C%2020-400%20Lublin!5e0!3m2!1spl!2spl!4v1589837208288!5m2!1spl!2spl" width="600" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
    </div>
  </div>
</div>


<?php include_once 'views/footer.php'; ?>
