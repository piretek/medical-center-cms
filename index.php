<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

define('PAGE_TITLE', 'Strona główna');
define('PAGE_NEEDS_AUTHORIZATION', false);

require_once "includes/init.php";
include_once "views/header.php"; ?>

<main>

  <div class="main--1">
  <h1>Przychodnia Korona Center</h1>
    Od ponad 10 lat prezentujemy wysoką jakość usług medycznych. Przychodnia Korona Center powstała z myślą o zaspokojeniu stale rosnących potrzeb z zakresu specjalistycznych usług medycznych. Naszą misję realizujemy poprzez zapewnienie profesjonalnej, życzliwej opieki medycznej oraz poczucia bezpieczeństwa zdrowotnego. Najbardziej liczy się dla nas zdrowie oraz samopoczucie naszych pacjentów, o co zawsze dbamy najbardziej. Każdy pacjent jest dla nas ważny i traktowany równie dobrze, nie ma tu miejsca na jakąkolwiek dyskryminację.
  </div>
  <div class="main--2">
    <div class="main--2--1 paper no-shadow">
      <h1>Kadra medyczna</h1>
        Wieloletnie doświadczenie i indywidualne podejście do pacjenta to gwarantowany sukces leczenia.
        <br>Oferujemy profesjonalne leczenie m.in. z zakresu:
          <br>• kardiologii,
          <br>• gastrologii,
          <br>• neurologii,
          <br>• onkologii.
        <br>
      <br><a href="about.php">Dowiedz się więcej</a>
    </div>
    <div class="main--2--1 paper no-shadow">
      <h1>UWAGA</h1>
      W związku z zagrożeniem epidemią koronawirusa zostają wprowadzone nadzwyczajne rozwiązania dotyczące przyjęć pacjentów:
          <br>• REJESTRACJA I PORADY UDZIELANE BĘDĄ TYLKO TELEFONICZNIE
          <br>• W SYTUACJI, KIEDY JEST TO NIEZBĘDNE PACJENT ZOSTANIE UMÓWIONY NA KONKRETNĄ GODZINĘ Z MOŻLIWOŚCIĄ WEJŚCIA DO PRZYCHODNI
          <br>• PROŚBĘ O PRZEPISANIE LEKÓW DO KONTYNUACJI LECZENIA RÓWNIEŻ NALEŻY ZGŁOSIĆ TELEFONICZNIE
        <br>Ze względu na Państwa bezpieczeństwo zwracamy się z prośbą o zastosowanie się do zaleceń.
        <br>NUMER TELEFONU: 555-555-555
    </div>
    <div class="main--2--1 paper no-shadow">
      <h1>Badania i diagnostyka</h1>
      W przychodni Korona Center prowadzone są m.in. następujące badania:
          <br>• badania profilaktyczne,
          <br>• badania diagnostyczne,
          <br>• badania laboratoryjne,
          <br>• badanie poziomu cukru,
          <br>• badanie EKG z opisem.
          <br>
        <br><a href="about.php">Dowiedz się więcej</a>
    </div>
  </div>
  <div class="main--3">
    <div class="main--3--1 paper">
      <h1>Najważniejsze jest
      Twoje zdrowie</h1>
    </div>
    <div class="main--3--2">
      Ludzkie zdrowie jest dla nas priorytetem, dlatego dbamy o profesjonalne i indywidualne podejście do każdego naszego pacjenta. Kadra lekarzy Korona Center służy swoim doświadczeniem i gwarantuje opiekę na najwyższym poziomie. Czy odczuwasz objawy przeziębienia, czy też chcesz się profilaktycznie zbadać - przyjdź do naszej przychodni i pozwól sobie pomóc. Gwarantujemy wyniki w trybie natychmiastowym, wykonane precyzyjnie.
    </div>
  </div>
  <div class="main--4">
    <div class="main--4--1">
    Nasza placówka bierze udział w: Programie szczepiennym refundowanym przez Urząd Miasta Lublin - Grypa 65+, programie profilaktyki zdrowia, programie chorób układu krążenia, profilaktyce gruźlicy, programie działań profilaktycznych obniżających występowanie czynników ryzyka chorób cywilizacyjnych tj. małej aktywności fizycznej, nadwagi i otyłości oraz palenia tytoniu.
    </div>
    <div class="main--4--2 paper">
    <h1>Programy szczepienne
    i profilaktyka</h2>
    </div>
  </div>

  </main>

<?php include_once "views/footer.php"; ?>
