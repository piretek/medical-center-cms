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

    if (is_string($additionalClasses)) {
      $additionalClasses = [ $additionalClasses ];
    }

    $this->classes = array_merge($this->classes, $additionalClasses);

    return $this;
  }

  /**
   * Changes default error key prefix
   *
   * @param string $prefix Error prefix
   * @return void
   */
  public function setErrorPrefix($prefix) {
    $this->errorPrefix = $prefix;
  }

  /**
   * Genertes HTML input node
   *
   * @param string $id Input ID
   * @param string $label Input label
   * @param string|array $value Default input value or array if its a select field. Default: empty string.
   * @param string $placeholder Input placeholder text. Default: empty string
   * @param string $type Input type. Default: text
   * @param string|null $errorPrefix Error prefix. Default: null
   * @param array $additionalAttributes Additional input attributes
   * @return void
   */
  private function input( $id, $label, $value = '', $placeholder = '', $type = 'text', $errorPrefix = null, $additionalAttributes = []) {
    global $_SESSION;

    $defaultAttributes = [
      'id' => $id,
      'name' => $type == 'checkbox' ? $placeholder.'[]' : $type == 'radio' ? $placeholder : $id,
    ];

    if ($type == 'textarea') {
      $defaultAttributes['placeholder'] = $placeholder;
    }
    else if ($type != 'select') {
      $defaultAttributes['type'] = $type;
      $defaultAttributes['placeholder'] = $placeholder;
      $defaultAttributes['value'] = $value;
    }

    $attributes = array_merge($defaultAttributes, $additionalAttributes);

    $attributesText = '';
    foreach($attributes as $attribute => $attr) {
      $attributesText .= "{$attribute}='{$attr}' ";
    }

    if ($errorPrefix === null) $errorPrefix = pathinfo(__DIR__.$_SERVER['PHP_SELF'], PATHINFO_FILENAME).'-'.$this->name;

    $errField = $id;

    if ($type == 'checkbox' || $type == 'radio') {

      $input = "
        <div class='input--container input-{$this->name}-id--{$id} rc'>
          <input class='input' {$attributesText}>
          <label class='input--label' for='{$id}'>{$label}</label>
        </div>
        <span class='input--error'>".(isset($_SESSION[$errorPrefix.'-form-error-'.$errField]) ? $_SESSION[$errorPrefix.'-form-error-'.$errField] : '')."</span>
      ";

    }
    else if ($type == 'select') {
      $input = "
        <div class='input--container input-{$this->name}-id--{$id}'>
          <label class='input--label' for='{$id}'>{$label}</label>
          <select class='input-{$this->name}-id--{$id}' {$attributesText}>";
          foreach($value as $okey => $oval) {
            $input .= "<option".($okey == $placeholder ? ' selected' : '')." value='{$okey}'>{$oval}</option>";
          }

      $input .= "
          </select>
        </div>
      <span class='input--error'>".(isset($_SESSION[$errorPrefix.'-form-error-'.$errField]) ? $_SESSION[$errorPrefix.'-form-error-'.$errField] : '')."</span>";
    }
    else if ($type == 'textarea') {
      $input = "
        <div class='input--container input-{$this->name}-id--{$id}'>
          <label class='input--label' for='{$id}'>{$label}</label>
          <textarea class='input' {$attributesText} rows='3'>{$value}</textarea>
          <span class='input--error'>".(isset($_SESSION[$errorPrefix.'-form-error-'.$errField]) ? $_SESSION[$errorPrefix.'-form-error-'.$errField] : '')."</span>
        </div>
      ";
    }
    else {
      $input = "
        <div class='input--container input-{$this->name}-id--{$id}'>
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
   * @param array $additionalAttributes Additional input attributes
   * @return object
   */
  public function text($id, $label, $value = '', $placeholder = '', $additionalAttributes = []) {

    $this->input($id, $label, $value, $placeholder, 'text', $this->errorPrefix, $additionalAttributes);

    return $this;
  }

  /**
   * Generates email input field
   *
   * @param string $id Input ID
   * @param string $label Input label
   * @param string $value Default input value. Default: empty string.
   * @param string $placeholder Input placeholder text. Default: empty string
   * @param array $additionalAttributes Additional input attributes
   * @return object
   */
  public function email($id, $label, $value = '', $placeholder = '', $additionalAttributes = []) {

    $this->input($id, $label, $value, $placeholder, 'email', $this->errorPrefix, $additionalAttributes);

    return $this;
  }

  /**
   * Generates password input field
   *
   * @param string $id Input ID
   * @param string $label Input label
   * @param string $placeholder Input placeholder text. Default: empty string
   * @param array $additionalAttributes Additional input attributes
   * @return object
   */
  public function password($id, $label, $placeholder = '', $additionalAttributes = []) {

    $this->input($id, $label, '', $placeholder, 'password', $this->errorPrefix, $additionalAttributes);

    return $this;
  }

  /**
   * Generates text input field
   *
   * @param string $id Select ID
   * @param array $label Select label text.
   * @param array $options Select options array.
   * @param string|null $value Default input value. Default: empty null.
   * @param array $additionalAttributes Additional input attributes
   * @param string|null $errorPrefix Error prefix. Default: null
   * @return object
   */
  public function select($id, $label, $options, $value = null, $additionalAttributes = [], $errorPrefix = null) {
    $this->input($id, $label, $options, $value, 'select', $this->errorPrefix, $additionalAttributes);

    return $this;
  }

  /**
   * Generates radio input field
   *
   * @param string $id Input ID
   * @param string $label Input label
   * @param string $value Default input value. Default: empty string.
   * @param array $additionalAttributes Additional input attributes
   * @return object
   */
  public function radio($id, $label, $name, $value = '', $additionalAttributes = []) {

    $this->input($id, $label, $value, $name, 'radio', $this->errorPrefix, $additionalAttributes);

    return $this;
  }

  /**
   * Generates checkbox input field
   *
   * @param string $id Input ID
   * @param string $label Input label
   * @param string $value Default input value. Default: empty string.
   * @param array $additionalAttributes Additional input attributes
   * @return object
   */
  public function checkbox($id, $label, $name, $value = '', $additionalAttributes = []) {

    $this->input($id, $label, $value, $name, 'checkbox', $this->errorPrefix, $additionalAttributes);

    return $this;
  }

  /**
   * Generates select field
   *
   * @param string $id Input ID
   * @param string $value Default input value. Default: empty string.
   * @param array $additionalAttributes Additional input attributes
   * @return object
   */
  public function hidden($id, $value, $additionalAttributes = []) {

    $attributesText = '';
    foreach($additionalAttributes as $attribute => $value) {
      $attributesText .= "{$attribute}='{$value}' ";
    }

    $this->inputs[] = "<input type='hidden' name='{$id}' value='{$value}' {$attributesText} />";

    return $this;
  }

  /**
   * Generates textarea input field
   *
   * @param string $id Input ID
   * @param string $label Input label
   * @param string $value Default input value. Default: empty string.
   * @param string $placeholder Input placeholder text. Default: empty string
   * @param array $additionalAttributes Additional input attributes
   * @return object
   */
  public function textarea($id, $label, $value = '', $placeholder = '', $additionalAttributes = []) {

    $this->input($id, $label, $value, $placeholder, 'textarea', $this->errorPrefix, $additionalAttributes);

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
