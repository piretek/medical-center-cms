<?php

if (!defined('SECURE_BOOT')) exit;

function reservationStatus($status, $stripHTML = false) {
  if ($status == 0) {
    return $stripHTML ? "Zaplanowana" : "<span class='reservation-type reserved'>Zaplanowana</span>";
  }
  else if ($status == 1) {
    return $stripHTML ? "Odbyła się" : "<span class='reservation-type done'>Odbyła się</span>";
  }
  else if ($status == 2) {
    return $stripHTML ? "Anulowana" : "<span class='reservation-type cancelled'>Anulowana</span>";
  }
}

function reservationType($status) {
  if ($status == 1) {
    return "NFZ";
  }
  else if ($status == 2) {
    return "Prywatna";
  }
}
