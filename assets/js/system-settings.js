const editableSections = ['rooms', 'specializations'];

editableSections.forEach((editableSection) => {
  const editableTab = document.querySelector(`.cards-sections--section#${editableSection}`)

  // Add action
  const addBttn = editableTab.querySelector('button.add')
  addBttn.addEventListener('click', function(event) {
    event.preventDefault()

    let promptMessage
    if (editableSection == 'rooms') {
      promptMessage = 'Podaj numer nowego gabinetu:'
    }
    else if (editableSection == 'specializations') {
      promptMessage = 'Podaj nazwę nowej specjalizacji:'
    }

    const value = prompt(promptMessage)

    const xhr = new XMLHttpRequest();

    xhr.addEventListener("load", function() {
        if (xhr.status === 200) {
          console.log(xhr.response);
        }
    });

    xhr.open("POST", "./settings.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(encodeURI(`type=${editableSection}-add&value=${value}`));
  })

  // Edit action
  const editBttns = editableTab.querySelectorAll('a.edit')
  editBttns.forEach((editBttn) => {
    editBttn.addEventListener('click', function(event) {
      event.preventDefault()

      let promptMessage
      if (editableSection == 'rooms') {
        promptMessage = 'Podaj nowy numer gabinetu:'
      }
      else if (editableSection == 'specializations') {
        promptMessage = 'Podaj nową nazwę specjalizacji:'
      }

      const lastValue = editBttn.parentNode.parentNode.children[0].textContent
      const value = prompt(promptMessage, lastValue)

      const id = editBttn.getAttribute('data-id')

      const xhr = new XMLHttpRequest();

      xhr.addEventListener("load", function() {
          if (xhr.status === 200) {
            console.log(xhr.response);
          }
      });

      xhr.open("POST", "./settings.php", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send(encodeURI(`type=${editableSection}-edit&id=${id}&value=${value}`));
    })
  })

  // Remove action
  const removeBttns = editableTab.querySelectorAll('a.remove')
  removeBttns.forEach((removeBttn) => {
    removeBttn.addEventListener('click', function(event) {
      event.preventDefault()

      if (confirm('Czy na pewno chcesz usunąć ten rekord?')) {
        const id = removeBttn.getAttribute('data-id')

        const xhr = new XMLHttpRequest();

        xhr.addEventListener("load", function() {
            if (xhr.status === 200) {
              console.log(xhr.response);
            }
        });

        xhr.open("POST", "./settings.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send(encodeURI(`type=${editableSection}-remove&id=${id}`));
      }
      else {
        return false;
      }
    })
  })
})
