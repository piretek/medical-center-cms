<?php

if (!defined('SECURE_BOOT')) exit();

?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= defined('PAGE_TITLE') && !empty('PAGE_TITLE') ? PAGE_TITLE.' - ' : '' ?>Korona Center</title>

  <link href="https://fonts.googleapis.com/css2?family=Jost:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <link rel='stylesheet' href='assets/css/style.css' type='text/css' />

  <script src='assets/js/main.js' type='text/javascript'></script>
</head>
<body>
  <div class='wrapper'>
    <header>
      <div class='header-logo'>
        <h2>Korona Center</h2>
      </div>
      <div class='header-navs'>
        <?php include "views/navs/index-nav.php"; ?>
        <?php include "views/navs/auth-nav.php"; ?>
      </div>
    </header>
