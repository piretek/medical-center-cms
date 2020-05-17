<?php

if (!defined('SECURE_BOOT')) exit;

if (!defined('PATIENT_FORM_ID')) {
  throw new Exception('No PATIENT_FORM_ID constant defined.');
}

$editPatientForm = new Form('edit-patient');

$patients = $db->query(sprintf('SELECT * FROM patients WHERE user = \'%s\'',
  $db->real_escape_string(PATIENT_FORM_ID)
));

if ($patients->num_rows == 0) {
  echo '<p>Pacjent nie istnieje</p>';
}
else {
  $patient = $patients->fetch_assoc();

  $editPatientForm->hidden('type', 'edit-patient')
    ->hidden('id', PATIENT_FORM_ID)
    ->text('pesel', 'PESEL:', $patient['pesel'])
    ->text('phone', 'Telefon:', $patient['phone'])
    ->text('street', 'Ulica:', $patient['street'])
    ->text('house_no', 'Nr domu/nr mieszkania:', $patient['house_no'])
    ->text('city', 'Miasto:', $patient['city'])
    ->text('postcode', 'Kod pocztowy:', $patient['postcode'])
    ->place();
}
