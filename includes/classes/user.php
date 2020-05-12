<?php

if (!defined('SECURE_BOOT')) exit;

class User {

  public $id;
  public $email;
  public $firstname;
  public $lastname;

  public function __construct($id = null) {
    
    if (is_integer($id) || is_numeric($id)) {
      $this->id = $id;
      $this->updateProps();
    }

    return $this;
  }

  private function updateProps() {
    global $db;

    $users = $db->query(sprintf("SELECT * FROM users WHERE id = '%s'", 
      $this->id
    ));

    if ($users->num_rows == 0) {
      return false;
    }
    else {
      $user = $users->fetch_assoc();

      $this->email = $user['email'];
      $this->firstname = $user['firstname'];
      $this->lastname = $user['lastname'];
      
      return $this;
    }
  }
  
  public function register() {
    if ($this->email !== null && $this->firstname !== null && $this->lastname !== null) {
      return $this->modify();
    }
    else {
      throw new Exception('Object properties are empty');
    }
  }

  public function modify($email, $firstname, $lastname, $role = null) {
    global $db;

    try {
      // Define helping variables
      $ok = true;
      $exception = new UserException('Błąd danych');
      $errors = [];

      // Values to check if they are empty
      $variablesToCheck = [
        'email' => $email,
        'firstname' => $firstname,
        'lastname' => $lastname
      ];

      // Checking these values
      foreach($variablesToCheck as $key => $value) {
        if (empty($value)) {
          $ok = false;
          $exception->addError($key, 'Pole nie może być puste');
        }
      }

      // Data sanitization
      $email = htmlentities(strtolower($email), ENT_QUOTES, "UTF-8");
      $firstname = htmlentities($firstname, ENT_QUOTES, "UTF-8");
      $lastname = htmlentities($lastname, ENT_QUOTES, "UTF-8");

      // Email validation
      if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !$exception->errorExists('email')) {
        $ok = false;
        $exception->addError('email', "Niepoprawny email");
      }

      // Get requested user
      $users = $db->query(sprintf("SELECT * FROM users WHERE email LIKE '%s'",
        $db->real_escape_string($email)
      ));

      if (!$ok) {
        throw $exception;
      }
      else {
        // Checking if user exists
        if ($users->num_rows != 0) {

          // If user exists get his data
          $user = $users->fetch_assoc();

          // Update user's data
          $successful = $db->query(sprintf("UPDATE users SET email = '%s', firstname = '%s', lastname = '%s', role = '%d' WHERE id = '{$user['id']}'",
            $db->real_escape_string($email),
            $db->real_escape_string($firstname),
            $db->real_escape_string($lastname)
            $db->real_escape_string($lastname)
          ));

          // If UPDATE is succesful then overwrite values
          if ($successful) {
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;

            return $this;
          }
          else {
            throw $exception;
          }
        }
        else {
          // Insert user's data
          $successful = $db->query(sprintf("INSERT INTO users VALUES (NULL, '%s', '%s', '%s', '%s', '%d')",
            $db->real_escape_string($email),
            '',
            $db->real_escape_string($firstname),
            $db->real_escape_string($lastname),
            
          ));

          // If UPDATE is succesful then overwrite values
          if ($successful) {
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;

            return $this;
          }
          else {
            throw $exception;
          }
        }
      }
    }
    catch (UserException $e) {
      return $e;   
    }
  }
}
