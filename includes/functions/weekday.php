<?php

function weekday($day) {
  if ($day == 0) {
    return "Niedziela";
  }
  else if ($day == 1) {
    return "Poniedziałek";
  }
  else if ($day == 2) {
    return "Wtorek";
  }
  else if ($day == 3) {
    return "Środa";
  }
  else if ($day == 4) {
    return "Czwartek";
  }
  else if ($day == 5) {
    return "Piątek";
  }
  else if ($day == 6) {
    return "Sobota";
  }
}
