<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'Strona główna');
define('PAGE_NEEDS_AUTHORIZATION', false);

require_once "includes/init.php";
include_once "views/header.php"; ?>

<main>
  <div class="main--1">
    <h1>Przychodnia Korona Center</h1>
    <p>Od ponad 10 lat prezentujemy wysoką jakość usług medycznych. Przychodnia Korona Center powstała z myślą o zaspokojeniu stale rosnących potrzeb z zakresu specjalistycznych usług medycznych. Naszą misję realizujemy poprzez zapewnienie profesjonalnej, życzliwej opieki medycznej oraz poczucia bezpieczeństwa zdrowotnego. Najbardziej liczy się dla nas zdrowie oraz samopoczucie naszych pacjentów, o co zawsze dbamy najbardziej. Każdy pacjent jest dla nas ważny i traktowany równie dobrze, nie ma tu miejsca na jakąkolwiek dyskryminację.</p>
  </div>
  <div class="main--2">
    <div class="main--2--c main--2--1 paper no-shadow">
      <h1>Kadra medyczna</h1>
      <p>Wieloletnie doświadczenie i indywidualne podejście do pacjenta to gwarantowany sukces leczenia.</p>
      <p>Oferujemy profesjonalne leczenie m.in. z zakresu:</p>
      <ul>
        <li>kardiologii,</li>
        <li>gastrologii,</li>
        <li>neurologii,</li>
        <li>onkologii.</li>
      </ul>
      <a href="about.php">Dowiedz się więcej</a>
    </div>
    <div class="main--2--c main--2--2 paper no-shadow">
      <h1>UWAGA</h1>
      <p>W związku z zagrożeniem epidemią koronawirusa zostają wprowadzone nadzwyczajne rozwiązania dotyczące przyjęć pacjentów:</p>
      <ul>
        <li>REJESTRACJA I PORADY UDZIELANE BĘDĄ TYLKO TELEFONICZNIE</li>
        <li>W SYTUACJI, KIEDY JEST TO NIEZBĘDNE PACJENT ZOSTANIE UMÓWIONY NA KONKRETNĄ GODZINĘ Z MOŻLIWOŚCIĄ WEJŚCIA DO PRZYCHODNI</li>
        <li>PROŚBĘ O PRZEPISANIE LEKÓW DO KONTYNUACJI LECZENIA RÓWNIEŻ NALEŻY ZGŁOSIĆ TELEFONICZNIE</li>
      </ul>
      <p>Ze względu na Państwa bezpieczeństwo zwracamy się z prośbą o zastosowanie się do zaleceń.<br />NUMER TELEFONU: 555-555-555</p>
    </div>
    <div class="main--2--c main--2--3 paper no-shadow">
      <h1>Badania i diagnostyka</h1>
      <p>W przychodni Korona Center prowadzone są m.in. następujące badania:</p>
      <ul>
        <li>badania profilaktyczne,</li>
        <li>badania diagnostyczne,</li>
        <li>badania laboratoryjne,</li>
        <li>badanie poziomu cukru,</li>
        <li>badanie EKG z opisem.</li>
      </ul>
      <p>
        <a href="about.php">Dowiedz się więcej</a>
      </p>
    </div>
  </div>
  <div class="main--3">
    <div class="main--3--c main--3--1 paper">
      <h1>Najważniejsze jest Twoje zdrowie</h1>
    </div>
    <div class="main--3--c main--3--2">
      <p>Ludzkie zdrowie jest dla nas priorytetem, dlatego dbamy o profesjonalne i indywidualne podejście do każdego naszego pacjenta. Kadra lekarzy Korona Center służy swoim doświadczeniem i gwarantuje opiekę na najwyższym poziomie. Czy odczuwasz objawy przeziębienia, czy też chcesz się profilaktycznie zbadać - przyjdź do naszej przychodni i pozwól sobie pomóc. Gwarantujemy wyniki w trybie natychmiastowym, wykonane precyzyjnie.</p>
    </div>
  </div>
  <div class="main--4">
    <div class="main--4--c main--4--1">
      <p>Nasza placówka bierze udział w: Programie szczepiennym refundowanym przez Urząd Miasta Lublin - Grypa 65+, programie profilaktyki zdrowia, programie chorób układu krążenia, profilaktyce gruźlicy, programie działań profilaktycznych obniżających występowanie czynników ryzyka chorób cywilizacyjnych tj. małej aktywności fizycznej, nadwagi i otyłości oraz palenia tytoniu.</p>
    </div>
    <div class="main--4--c main--4--2 paper">
      <h1>Programy szczepienne i profilaktyka</h2>
    </div>
  </div>

  </main>

<?php include_once "views/footer.php"; ?>
