<?php

if (!defined('SECURE_BOOT')) exit;

class Form {

  public $name = '';
  public $method = '';
  public $action = '';
  public $classes = [];

  private $errorPrefix = null;
  private $inputs = [];

  public $submitText = 'Wyślij';


  public function __construct( $name, $method = 'POST', $action = '', $additionalClasses = [] ) {
    $this->name = $name;
    $this->method = $method;
    $this->action = $action;
    $this->classes = array_merge($this->classes, $additionalClasses);

    return $this;
  }

  private function input( $id, $label, $value = '', $placeholder = '', $type = 'text', $errorPrefix = null, $addtiotionalAttributes = []) {
    global $_SESSION;

    $defaultAttributes = [
      'id' => $id,
      'type' => $type,
      'name' => $type == 'checkbox' ? $placeholder.'[]' : $type == 'radio' ? $placeholder : $id,
      'placeholder' => $placeholder,
      'value' => $value,
    ];

    $attributes = array_merge($defaultAttributes, $addtiotionalAttributes);

    $attributesText = '';
    foreach($attributes as $attribute => $value) {
      $attributesText .= "{$attribute}='{$value}' ";
    }

    if ($errorPrefix === null) $errorPrefix = pathinfo(__DIR__.$_SERVER['PHP_SELF'], PATHINFO_FILENAME);

    $errField = $id;

    if ($type == 'checkbox' || $type == 'radio') {

      $input = "
        <div class='input--container input-id--{$id} rc'>
          <input class='input' {$attributesText}>
          <label class='input--label' for='{$id}'>{$label}</label>
        </div>
        <span class='input--error'>".(isset($_SESSION[$errorPrefix.'-form-error-'.$errField]) ? $_SESSION[$errorPrefix.'-form-error-'.$errField] : '')."</span>
      ";

    }
    else {
      $input = "
        <div class='input--container input-id--{$id}'>
          <label class='input--label' for='{$id}'>{$label}</label>
          <input class='input' {$attributesText}>
          <span class='input--error'>".(isset($_SESSION[$errorPrefix.'-form-error-'.$errField]) ? $_SESSION[$errorPrefix.'-form-error-'.$errField] : '')."</span>
        </div>
      ";
    }

    unset($_SESSION[$errorPrefix.'-form-error-'.$errField]);

    $this->inputs[] = $input;
  }

  public function text($id, $label, $value = '', $placeholder = '', $addtiotionalAttributes = []) {

    $this->input($id, $label, $value, $placeholder, 'text', $this->errorPrefix, $addtiotionalAttributes);

    return $this;
  }

  public function password($id, $label, $placeholder = '', $addtiotionalAttributes = []) {

    $this->input($id, $label, '', $placeholder, 'password', $this->errorPrefix, $addtiotionalAttributes);

    return $this;
  }

  public function hidden($id, $value = '', $addtiotionalAttributes = []) {

    $this->input($id, '', $value, '', 'hidden', $this->errorPrefix, $addtiotionalAttributes);

    return $this;
  }

  public function place($submitText = null) {
    echo "<form id='{$this->name}' ".(!empty($this->method) ? "action='{$this->method}' " : "")."method='{$this->method}' ".(!empty($this->classes) ? "class='".implode(' ', $this->classes)."'" : '').">";

    foreach($this->inputs as $input) {
      echo $input;
    }

    if ($submitText !== null) {
      $this->submitText = $submitText;
    }

    echo "<button type='submit'>{$this->submitText}</button>";

    echo "</form>";
  }
}
