<?php

if (!defined('SECURE_BOOT')) exit;

/**
 * Form Class
 * Creates simple form via connected methods.
 *
 * @author Piotr Czarnecki <piretek@piretek.pro>
 * @version 1.0
 */
class Form {

  public $name = '';
  public $method = '';
  public $action = '';
  public $classes = [];

  private $errorPrefix = null;
  private $inputs = [];

  public $submitText = 'Wyślij';

  /**
   * Creates object with important data
   *
   * @param string $name Form ID
   * @param string $method Submit HTTP method. Default: POST
   * @param string $action Submit HTTP redirect. Default: empty string
   * @param array $additionalClasses Additional form css classes
   * @return object
   */
  public function __construct( $name, $method = 'POST', $action = '', $additionalClasses = [] ) {
    $this->name = $name;
    $this->method = $method;
    $this->action = $action;
    $this->classes = array_merge($this->classes, $additionalClasses);

    return $this;
  }

  /**
   * Genertes HTML input node
   *
   * @param string $id Input ID
   * @param string $label Input label
   * @param string $value Default input value. Default: empty string.
   * @param string $placeholder Input placeholder text. Default: empty string
   * @param string $type Input type. Default: text
   * @param string|null $errorPrefix Error prefix. Default: null
   * @param array $addtiotionalAttributes Additional input attributes
   * @return void
   */
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

  /**
   * Generates text input field
   *
   * @param string $id Input ID
   * @param string $label Input label
   * @param string $value Default input value. Default: empty string.
   * @param string $placeholder Input placeholder text. Default: empty string
   * @param array $addtiotionalAttributes Additional input attributes
   * @return object
   */
  public function text($id, $label, $value = '', $placeholder = '', $addtiotionalAttributes = []) {

    $this->input($id, $label, $value, $placeholder, 'text', $this->errorPrefix, $addtiotionalAttributes);

    return $this;
  }

  /**
   * Generates password input field
   *
   * @param string $id Input ID
   * @param string $label Input label
   * @param string $placeholder Input placeholder text. Default: empty string
   * @param array $addtiotionalAttributes Additional input attributes
   * @return object
   */
  public function password($id, $label, $placeholder = '', $addtiotionalAttributes = []) {

    $this->input($id, $label, '', $placeholder, 'password', $this->errorPrefix, $addtiotionalAttributes);

    return $this;
  }

  /**
   * Generates text input field
   *
   * @param string $id Input ID
   * @param string $value Default input value. Default: empty string.
   * @param array $addtiotionalAttributes Additional input attributes
   * @return object
   */
  public function hidden($id, $value = '', $addtiotionalAttributes = []) {

    $this->input($id, '', $value, '', 'hidden', $this->errorPrefix, $addtiotionalAttributes);

    return $this;
  }

  /**
   * Generates and shows complete form
   *
   * @param string|null $submitText Text for submit button. Default: null
   * @return void
   */
  public function place($submitText = null) {
    echo "<form id='{$this->name}' ".(!empty($this->action) ? "action='{$this->action}' " : "")."method='{$this->method}' ".(!empty($this->classes) ? "class='".implode(' ', $this->classes)."'" : '').">";

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
