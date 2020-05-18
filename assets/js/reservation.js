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

    document.querySelector('section.choose-2').style.display = 'block'
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

    document.querySelector('section.choose-3').style.display = 'block'
    document.querySelector("input[name='schedule']").value = card.getAttribute('data-schedule')
  })
})
