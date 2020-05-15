window.addEventListener('load', function() {
  // Performed after page load

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
