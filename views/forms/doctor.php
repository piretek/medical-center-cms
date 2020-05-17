<?php

if (!defined('SECURE_BOOT')) exit;

if (!defined('DOCTOR_FORM_ID')) {
  throw new Exception('No DOCTOR_FORM_ID constant defined.');
}

$doctorForm = new Form('doctor');

$doctorForm->hidden('type', 'doctor');

$doctorForm->hidden('id', DOCTOR_FORM_ID);

$result = $db->query("SELECT * FROM doctors WHERE user = '".DOCTOR_FORM_ID."'");
if ($result->num_rows == 0) {
  $columns = $db->query('SHOW COLUMNS IN doctors');
  $columns = $columns->fetch_all(MYSQLI_ASSOC);

  $columns = array_map(function($fieldset) {
    return $fieldset['Field'];
  }, $columns);

  $doctor = array_fill_keys($columns, '');
}
else {
  $doctor = $result->fetch_assoc();
}

$specializations = [];
foreach($db->query("SELECT * FROM specializations")->fetch_all(MYSQLI_ASSOC) as $row) {
  $specializations[$row['id']] = $row['name'];
}

$doctorForm->select(
  'specialization',
  'Specjalizacja:',
  $specializations,
  $doctor['specialization']
);

$doctorForm->text('degree', 'Stopień lekarski (przed imieniem):', $doctor['degree'])
  ->place('Zatwierdź');
