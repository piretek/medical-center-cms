<?php

if (!defined('SECURE_BOOT')) exit;

class User {

  public $id;
  public $email;
  public $firstname;
  public $lastname;

  public function modify($email, $firstname, $lastname) {
    global $db;

    try {
        
      // Define helping variables
      $ok = true;
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
          $errors[$key] = 'Pole nie może być puste';
        }
      }

      // Data sanitization
      $email = htmlentities(strtolower($email), ENT_QUOTES, "UTF-8");
      $firstname = htmlentities($firstname, ENT_QUOTES, "UTF-8");
      $lastname = htmlentities($lastname, ENT_QUOTES, "UTF-8");

      // Get requested user
      $users = $db->query(sprintf("SELECT * FROM users WHERE email LIKE '%s'",
        $db->real_escape_string($email)
      ));

      // Checking if user exists
      if ($users->num_rows != 0) {

        // If user exists get his data
        $user = $users->fetch_assoc();

        // Email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !array_key_exists('email', $errors)) {
          $ok = false;
          $errors['email'] = "Niepoprawny email";
        }

        if (!$ok) {
          // TODO: error handling
          throw new UserException();
        }
        else {
          // Update user's data
          $successful = $db->query(sprintf("UPDATE users SET email = '%s', firstname = '%s', lastname = '%s' WHERE id = '{$user['id']}'",
            $db->real_escape_string($email),
            $db->real_escape_string($firstname),
            $db->real_escape_string($lastname)
          ));

          // If UPDATE is succesful then overwrite values
          if ($successful) {
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
          }
          else {
            // TODO: error handling
          }
        }
      }
    }
    catch() {
      
    }
  }
}
