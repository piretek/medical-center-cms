<?php

define('PAGE_TITLE', 'O firmie');
define('PAGE_NEEDS_AUTHORIZATION', false);

require_once "includes/init.php";
include_once "views/header.php";

include "views/navs/index-nav.php"; ?>

<div class="info">
    <div class="info--content">
      <div class="about--headers">
        <div class="header--1">
          <h2>Dlaczego warto leczyć się
          <br>
          w przychodni Korona Center?</h2>
        </div>
        <div class="header--2">
          <h2>Wykwalifikowana kadra</h2>
        </div>
        <div class="header--3">
          <h2>Umowa z NFZ</h2>
        </div>
      </div>
    <div class="about--texts">
      <div class="text--1">
        Pacjenci wybierający naszą przychodnię mogą liczyć na profesjonalną pomoc, trafną diagnozę, a co za tym idzie skuteczne leczenie i jak najszybszy powrót do zdrowia. Staramy się, aby nasi pacjenci odczuwali komfort przy każdej wizycie. Wiemy, że nie ma drugiego takiego samego pacjenta i każdy medyczny problem jest niepowtarzalny, dlatego duży nacisk kładziemy na indywidualne podejście.
      </div>
      <div class="text--2">
        Profesjonalna opieka zdrowotna, jak i wysoki standard udzielanych świadczeń zdrowotnych jest wynikiem umiejętności naszej kadry medycznej. Obecnie świadczymy usługi z zakresu między innymi ortopedii, chirurgii, kardiologii, neurologii, urologii, stomatologii, dermatologii czy ginekologii. Nasza przychodnia to zespół lekarzy z wieloletnim stażem, fenomenalnymi umiejętnościami i bardzo dobrym podejściem do pacjentów. Dzięki temu przychodnia Korona Center jest jednym z wiodących centrów medyczno-diagnostycznych w Lublinie.
      </div>
      <div class="text--3">
        Nasza przychodnia posiada aktualną umowę z Narodowym Funduszem Zdrowia na świadczenie bezpłatnych usług w zakresie podstawowej opieki zdrowotnej.
      </div>
    </div>
  </div>
</div>

<?php include_once "views/footer.php"; ?>
