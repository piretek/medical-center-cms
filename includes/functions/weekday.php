<?php

function weekday($day) {
  if ($day == 0) {
    return "Poniedziałek";
  }
  else if ($day == 1) {
    return "Wtorek";
  }
  else if ($day == 2) {
    return "Środa";
  }
  else if ($day == 3) {
    return "Czwartek";
  }
  else if ($day == 4) {
    return "Piątek";
  }
  else if ($day == 5) {
    return "Sobota";
  }
  else if ($day == 6) {
    return "Niedziela";
  }
}
