<?php
if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);

require_once "includes/init.php";
include_once 'views/header.php';
?>
<div class="covid-info">
  <div class="column col-center">
    <h1>COVID-19 - Co musisz wiedzieć</h1>
    <div id="sympt" class="covid-info-issue">
      <img src="assets/images/cough.png">
      <h2>Objawy</h2>
      <p>
        Od zakażenia do pojawienia się objawów może minąć 1–14 dni. Najczęstsze objawy choroby koronawirusowej (COVID-19) to gorączka, zmęczenie i suchy kaszel. Większość ludzi (około 80%) zdrowieje bez potrzeby specjalnego leczenia.
        W rzadszych przypadkach choroba ma ciężki przebieg i może nawet być śmiertelna. U osób starszych i mających inne schorzenia (takie jak astma, cukrzyca lub choroby serca), zakażenie koronawirusem może prowadzić do ciężkiej choroby.
      </p>
      <div class="content">
        <div class="column col-50">
          <h3>Możliwe objawy:</h3>
            <ul>
              <li><b>kaszel,</b></li>
              <li><b>gorączka,</b></li>
              <li><b>zmęczenie,</b></li>
              <li><b>trudności z oddychaniem (w ciężkich przypadkach).</b></li>
            </ul>
        </div>
        <div class="column col-50">
          <h3>Jeśli masz takie objawy:</h3>
            <ul>
              <li><b>zadzwoń</b> natychmiast do najbliższej stacji sanitarno-epidemiologicznej i powiedz o swoich objawach;</li>
              <li><b>własnym środkiem transportu</b> pojedź do szpitala z oddziałem zakaźnym lub oddziałem obserwacyjno-zakaźnym, gdzie lekarze określą dalszy tryb postępowania medycznego. Pod żadnym pozorem nie korzystaj ze środków komunikacji publicznej czy taksówek – w ten sposób narażasz innych na zakażenie.</li>
            </ul>
        </div>   
    </div>    
      Jeśli miałeś kontakt z osobą zakażoną koronawirusem lub chorą, to natychmiast zadzwoń do stacji sanitarno-epidemiologicznej i powiadom o swojej sytuacji. Otrzymasz informację, jak masz dalej postępować.</br></br>
      Jeśli wróciłeś z zagranicy przed 15 marca, czyli przed wprowadzeniem obowiązkowej kwarantanny dla wszystkich podróżnych powracających do Polski, i obecnie nie jesteś objęty obowiązkową kwarantanną i nie masz objawów choroby, mimo wszystko przez 14 dni od powrotu do kraju kontroluj codziennie swój stan zdrowia. Mierz temperaturę, zwróć uwagę na to, czy kaszlesz albo czy masz trudności z oddychaniem. Ogranicz kontakt z innymi. Pamiętaj, że możesz przechodzić chorobę bezobjawowo i możesz zarażać innych.</br></br>
      Jeśli tylko zaobserwujesz któryś z objawów choroby, zadzwoń natychmiast do <a href="https://www.gov.pl/web/koronawirus/stacje-sanitarno-epidemiologiczne">stacji sanitarno-epidemiologicznej</a>. Każdy pacjent manifestujący objawy ostrej infekcji dróg oddechowych <b>(gorączka powyżej 38°C wraz z kaszlem lub dusznością)</b> w powiązaniu z kryteriami epidemiologicznymi powinien ponadto trafić do oddziału zakaźnego lub obserwacyjno-zakaźnego. Jeśli zaobserwujesz takie objawy, własnym środkiem transportu pojedź do szpitala z oddziałem zakaźnym lub oddziałem obserwacyjno-zakaźnym. Jeśli nie możesz dotrzeć do szpitala własnym transportem, to lekarz POZ (w ramach teleporady) i stacja sanitarno-epidemiologiczna mają możliwość zlecenia dla Ciebie transportu sanitarnego.</br></br>
      Jeśli masz pytania, wątpliwości, zadzwoń na infolinię Narodowego Funduszu Zdrowia: <b>800 190 590</b>.</br></br>
      Korzystaj z wiarygodnych źródeł informacji, nie daj się panice. Dbaj o siebie i swoich bliskich. 
  </div>
  <div id="prev" class="covid-info-issue">
    <img src="assets/images/prevent.png">
    <h2>Profilaktyka</h2>
    <h3>Stosuj 5 zasad</h3>
      <ul>
        <li><b>Regularnie myj ręce przez 20 sekund mydłem i wodą lub środkiem dezynfekującym na bazie alkoholu.</b></li>
        <li><b>Gdy kaszlesz lub kichasz, zakrywaj usta i nos chusteczką jednorazową lub wewnętrzną stroną łokcia.</b></li>
        <li><b>Unikaj bliskiego (mniej niż 1 metr) kontaktu z osobami, które źle się czują.</b></li>
        <li><b>Jeśli źle się czujesz, nie wychodź z domu i odizoluj się od innych domowników.</b></li>
        <li><b>Nie dotykaj oczu, nosa ani ust, gdy masz brudne ręce.</b></li>
      </ul>
  </div>
  <div id="cure" class="covid-info-issue">
  <img src="assets/images/cure.png">
    <h2>Leczenie</h2>
      <p>Nie ma leku pozwalającego zapobiegać chorobie koronawirusowej (COVID-19) lub ją leczyć. Chorzy mogą wymagać wspomagania przy oddychaniu.</p>
    <h3>Samoopieka</h3>
      Jeśli masz łagodne objawy, nie wychodź z domu, dopóki nie wyzdrowiejesz. Aby złagodzić objawy:</br>
      <ul>
        <li><b>dużo odpoczywaj i śpij,</b></li>
        <li><b>wygrzewaj się,</b></li>
        <li><b>pij dużo płynów,</b></li>
        <li><b>żywaj nawilżacza powietrza lub bierz gorące prysznice, by złagodzić kaszel i ból gardła.</b></li>
      </ul>
    <h3>Zabiegi medyczne</h3>
      <p>Jeśli pojawi się u Ciebie gorączka, kaszel lub trudności z oddychaniem, niezwłocznie zgłoś się po pomoc medyczną. Wcześniej powiadom telefonicznie swojego lekarza o odbytych niedawno podróżach lub kontaktach z osobami, które ostatnio podróżowały.</p>
  </div>
</div>
</div>
<?php include_once 'views/footer.php';?>
