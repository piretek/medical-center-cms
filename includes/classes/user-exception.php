<?php

if (!defined('SECURE_BOOT')) exit;

class UserException extends Exception  {
  
  private $inputErrors = [];

  public function addError($key, $value) {
    $this->inputErrors[$key] = $value;
  }

  public function getErrors() {
    return $this->inputErrors;
  }

  public function errorExists($key) {
    return array_key_exists($key, $this->inputErrors);
  }
}

