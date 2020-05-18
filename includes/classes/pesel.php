<?php

if (!defined('SECURE_BOOT')) exit;

/**
 * PESEL class
 *
 * Parses and validates PESEL no.
 * @author Piotr Czarnecki <piretek@piretek.pro.
 * @version 1.0
 */
class PESEL {

  private $pesel;
  private $birthdate_day;
  private $birthdate_month;
  private $birthdate_year;
  private $sex;

  /**
   * Creates new PESEL object
   *
   * @param string $pesel PESEL number
   * @return object
   */
  public function __construct( $pesel ) {

    return $this->set($pesel)->parse();
  }

  /**
   * Sets new PESEL
   *
   * @param string $pesel PESEL number
   * @return object
   */
  public function set( $pesel ) {

    if (is_integer($pesel)) throw new Exception('PESEL is required to be a string type.');

    if ( self::validate($pesel) ) {
      $this->pesel = $pesel;
    }
    else {
      throw new PESEL_Exception('PESEL jest niepoprawny');
    }

    return $this;
  }

  /**
   * Returns PESEL number
   *
   * @return string
   */
  public function get() {
    return $this->pesel;
  }

  /**
   * Validates PESEL number
   *
   * @param string $pesel PESEL number
   * @return bool
   */
  public static function validate( $pesel ) {

    if (strlen($pesel) == 11 && is_numeric($pesel)) {

      $peselWithoutChecksum = substr($pesel, 0, -1);

      $splittedPesel = str_split($peselWithoutChecksum);

      $i = 0;
      $calculatedControls = array_map(function($chunk) use (&$i) {

        if ($i % 4 == 0) {
          $multiplier = 1;
        }
        else if ($i % 4 == 1) {
          $multiplier = 3;
        }
        else if ($i % 4 == 2) {
          $multiplier = 7;
        }
        else if ($i % 4 == 3) {
          $multiplier = 9;
        }

        $i++;

        return substr((string) ($chunk * $multiplier), -1);

      }, $splittedPesel);

      $toSubstract = 0;
      foreach($calculatedControls as $toSum) {
        $toSubstract = $toSubstract + $toSum;
      }

      $controlInt = 10 - (int) substr($toSubstract, -1);

      if ($controlInt == substr($pesel, -1)) {
        return true;
      }
      else {
        return false;
      }
    }
    else {
      return false;
    }
  }

  /**
   * Parses info about person from PESEL number
   *
   * @return array|bool
   */
  public function parse() {

    if (self::validate($this->pesel)) {
      $year = substr($this->pesel, 0, 2);
      $month = substr($this->pesel, 2, 2);
      $day = substr($this->pesel, 4, 2);

      $monthPrefix = substr($month, 0, 1);

      $scopes = [
        '1800' => ['8', '9'],
        '1900' => ['0', '1'],
        '2000' => ['2', '3'],
        '2100' => ['4', '5'],
        '2200' => ['6', '7'],
      ];

      if (in_array($monthPrefix, $scopes['1800'])) {
        $yearPrefix = '18';
        $month = array_search(substr($month, 0, 1), $scopes['1800']).substr($month, 1,1);
      }
      else if (in_array($monthPrefix, $scopes['1900'])) {
        $yearPrefix = '19';
        $month = array_search(substr($month, 0, 1), $scopes['1900']).substr($month, 1,1);
      }
      else if (in_array($monthPrefix, $scopes['2000'])) {
        $yearPrefix = '20';
        $month = array_search(substr($month, 0, 1), $scopes['2000']).substr($month, 1,1);
      }
      else if (in_array($monthPrefix, $scopes['2100'])) {
        $yearPrefix = '21';
        $month = array_search(substr($month, 0, 1), $scopes['2100']).substr($month, 1,1);
      }
      else if (in_array($monthPrefix, $scopes['2200'])) {
        $yearPrefix = '22';
        $month = array_search(substr($month, 0, 1), $scopes['2200']).substr($month, 1,1);
      }

      $year = $yearPrefix.$year;

      $this->birthdate_day = $day;
      $this->birthdate_month = $month;
      $this->birthdate_year = $year;

      $sexInt = (int) substr($this->pesel, 9, 1);

      $this->sex = $sexInt % 2 == 0 ? 'kobieta' : 'mężczyzna';
    }
    else {
      return false;
    }
  }

  /**
   * Returns person sex
   *
   * @return string|null
   */
  public function getSex() {
    return $this->sex;
  }

  /**
   * Returns person birthdate
   *
   * @return string|null
   */
  public function getBirthDate() {
    return "{$this->birthdate_day}.{$this->birthdate_month}.{$this->birthdate_year}";
  }

  /**
   * Returns person age
   *
   * @return string|null
   */
  public function getAge() {
    return (int) date('Y', time() - mktime(0,0,0,$this->birthdate_month, $this->birthdate_day, $this->birthdate_year)) - 1970;
  }
}
