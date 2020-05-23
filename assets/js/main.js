
function clock() {
  var date = new Date();
  var hour = date.getHours();
  var minute = date.getMinutes();
  var second = date.getSeconds();
  var day = date.getDate();
  var dayN = date.getDay();
  var month = date.getMonth();
  var year = date.getFullYear();

  if (minute < 10) minute = "0" + minute;
  if (second < 10) second = "0" + second;

  var days = new Array("Niedziela", "Poniedziałek", "Wtorek", "Środa", "Czwartek", "Piątek", "Sobota");
  var months = new Array("stycznia", "lutego", "marca", "kwietnia", "maja", "czerwca", "lipca", "sierpnia", "września", "października", "listopada", "grudnia");

  var showDate = days[dayN] + ', ' + day + ' ' + months[month] + ' ' + year + "</br><b>" + hour + ':' + minute + ':' + second + "</b>";
  document.getElementById('date').innerHTML = showDate;
}

window.addEventListener('load', function() {
  clock();
  // Perform after page load

  var interval = setInterval(clock, 1000);

  // Cards code
  const cardsContainers = document.querySelectorAll('.cards')
  cardsContainers.forEach((cardsContainer) => {
    const tabs = cardsContainer.querySelectorAll('.cards-tabs--tab')
    const sections = cardsContainer.querySelectorAll('.cards-sections--section')

    tabs.forEach((tab) => {
      tab.addEventListener('click', () => {
        const targetSection = tab.getAttribute('for')

        tabs.forEach((tab) => tab.classList.remove('active'))
        tab.classList.add('active')

        sections.forEach((section) => {
          section.classList.remove('active')

          const toggledSection = cardsContainer.querySelector('.cards-sections--section#' + targetSection)
          toggledSection.classList.add('active')
        })
      })
    })

    if (!window.location.hash) {
      tabs[0].classList.add('active')
      sections[0].classList.add('active')
    }
    else {
      console.log(cardsContainer.querySelector(`.cards-sections--section`));
      cardsContainer.querySelector(`.cards-tabs--tab[for='${window.location.hash.substr(1)}']`).classList.add('active')
      cardsContainer.querySelector(`.cards-sections--section${window.location.hash}`).classList.add('active')
    }
  })

  document.querySelectorAll('form.remove-prompt').forEach((form) => {
    form.addEventListener('submit', (e) => {
      e.preventDefault();

      if (confirm('Czy na pewno chcesz usunąć?')) {
        form.submit();
      }
      else {
        return null;
      }
    })
  })
})
