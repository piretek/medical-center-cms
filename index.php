<?php

define('PAGE_TITLE', 'Strona główna');
define('PAGE_NEEDS_AUTHORIZATION', false);

require_once "includes/init.php";
include_once "views/header.php";

include "views/navs/index-nav.php";

?>

<h1>Strona główna</h1>

<?php include_once "views/footer.php"; ?>
