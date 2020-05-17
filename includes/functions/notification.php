<?php

if (!defined('SECURE_BOOT')) exit;

function notification($name, $class = null) {
  if (isset($_SESSION[$name])) {
    echo "<span class='{$class}'>{$_SESSION[$name]}</span>";
    unset($_SESSION[$name]);
  }
}
