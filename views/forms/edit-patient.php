<?php

if (!defined('SECURE_BOOT')) exit;

$editPatientForm = new Form('edit-patient');

$editPatientForm->hidden('type', 'edit-patient')
  ->text('pesel', 'PESEL:')
  ->text('phone', 'Telefon:')
  ->text('street', 'Ulica:')
  ->text('house_no', 'Nr domu/nr mieszkania:')
  ->text('city', 'Miasto:')
  ->text('postcode', 'Kod pocztowy:')
  ->place();
