<?php

if (!defined('SECURE_BOOT')) exit;

$addPatientForm = new Form('create-patient');

$addPatientForm->hidden('type', 'create-patient')
  ->text('pesel', 'PESEL:')
  ->text('phone', 'Telefon:')
  ->text('street', 'Ulica:')
  ->text('house_no', 'Nr domu/nr mieszkania:')
  ->text('city', 'Miasto:')
  ->place();
