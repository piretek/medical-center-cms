<?php
if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);


require_once 'includes/init.php';
include_once 'views/header.php';
?>

<div class="column col-center">
  <div class="contact-info">
    <div class="column col-50">
      <h1>Kontakt</h1>
        <p>Telefon: 555-555-555</p>
        <p>E-mail:korona.center@medical.center</p>
        <p>Adres: Lublin, ul. Radziwiłowska 13</p>
    </div>
    <div class="column col-50">
        <h1>Gdzie nas znajdziesz?</h1>
        <iframe class="map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2497.338573505332!2d22.556438951358313!3d51.24967523723501!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x472257680fd8cd17%3A0xc8a0244d385c1cb2!2sRadziwi%C5%82%C5%82owska%2013%2C%2020-400%20Lublin!5e0!3m2!1spl!2spl!4v1589837208288!5m2!1spl!2spl" width="600" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
    </div>
  </div>
</div>


<?php include_once 'views/footer.php'; ?>