<?php

if (!defined('SECURE_BOOT')) exit;

$addPatientForm = new Form('create-patient');

$addPatientForm->hidden('type', 'create-patient');

if (defined('PATIENT_FORM_ID')) $addPatientForm->hidden('id', PATIENT_FORM_ID);

$addPatientForm->text('pesel', 'PESEL:')
  ->text('phone', 'Telefon:')
  ->text('street', 'Ulica:')
  ->text('house_no', 'Nr domu/nr mieszkania:')
  ->text('city', 'Miasto:')
  ->text('postcode', 'Kod pocztowy:')
  ->place();
