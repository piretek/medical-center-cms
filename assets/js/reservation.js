const searchInput = document.querySelector('input#search');
const searchResults = document.querySelector('table.search-results tbody')

if (searchInput) {
  searchInput.addEventListener('input', () => {
    const { value } = searchInput

    if (value.length < 2) {
      if (!document.querySelector('table .no-results')) {
        searchResults.innerHTML = ''

        const td = document.createElement('td')
        td.setAttribute('colspan', '3')
        td.classList.add('no-results')
        td.textContent = 'Wpisz min. 2 znaki.'

        const tr = document.createElement('tr')
        tr.appendChild(td)
        searchResults.appendChild(tr)
      }
    }
    else {
      searchResults.innerHTML = ''

      const foundIndexes = []
      const searchables = value.split(' ')

      searchables.forEach((searchable) => {
        patients.forEach((patient, index) => {
          Object.keys(patient).forEach((key) => {
            if (key !== 'id' && searchable !== '') {
              if (patient[key].toLowerCase().search(searchable.toLowerCase()) !== -1 && !foundIndexes.includes(index)) {
                foundIndexes.push(index)
              }
            }
          })
        })
      })

      if (foundIndexes.length == 0) {
        const td = document.createElement('td')
        td.setAttribute('colspan', '3')
        td.classList.add('no-results')
        td.textContent = 'Brak wyników... Utwórz nowego klienta.'

        const tr = document.createElement('tr')
        tr.appendChild(td)
        searchResults.appendChild(tr)
      }
      else {
        foundIndexes.forEach((index) => {
          const { id, firstname, lastname, pesel } = patients[index]

          const tdName = document.createElement('td')
          tdName.textContent = `${firstname} ${lastname}`

          const tdPesel = document.createElement('td')
          tdPesel.textContent = pesel

          const tdBttn = document.createElement('td')
          tdBttn.textContent = 'Wybierz'
          tdBttn.addEventListener('click', () => {
            document.querySelector('p.search-results').style.display = 'block'
            searchResults.parentNode.style.display = 'none'
            document.querySelector('p.search-results span').textContent = `${firstname} ${lastname} (${pesel})`
            document.querySelector('p.search-results span').textContent = `${firstname} ${lastname} (${pesel})`
            document.querySelector('input[name=\'patient\']').value = id
            searchInput.value = ''
            document.querySelector('section.choose-doctor').style.display = 'block'
          })

          const tr = document.createElement('tr')
          tr.setAttribute('data-id', id)
          tr.appendChild(tdName)
          tr.appendChild(tdPesel)
          tr.appendChild(tdBttn)

          searchResults.appendChild(tr)
        })
      }
    }
  })
}

const doctorCards = document.querySelectorAll('div.choice-card.doctor-card')
doctorCards.forEach((card) => {

  card.addEventListener('click', () => {
    doctorCards.forEach((all) => {
      all.classList.remove('active')
      if (all.querySelector('img.checked')) all.querySelector('img.checked').remove()
    })

    document.querySelectorAll('.doctor-schedule').forEach((schedule) => { schedule.style.display = 'none'; })

    card.classList.add('active')
    const check = document.createElement('img')
    check.setAttribute('class', 'checked')
    check.setAttribute('src', 'assets/img/done.svg')
    card.appendChild(check)

    document.querySelector('section.choose-schedule').style.display = 'block'
    document.querySelector('.doctor-schedule--' + card.getAttribute('data-doctor')).style.display = 'block'
  })
})

const scheduleCards = document.querySelectorAll('div.choice-card.schedule-card')
scheduleCards.forEach((card) => {

  card.addEventListener('click', () => {
    scheduleCards.forEach((all) => {
      all.classList.remove('active')
      if (all.querySelector('img.checked')) all.querySelector('img.checked').remove()
    })


    card.classList.add('active')
    const check = document.createElement('img')
    check.setAttribute('class', 'checked')
    check.setAttribute('src', 'assets/img/done.svg')
    card.appendChild(check)

    document.querySelector('section.choose-type').style.display = 'block'
    document.querySelector("input[name='schedule']").value = card.getAttribute('data-schedule')
  })
})
