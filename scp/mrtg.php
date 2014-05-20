<?php
require('staff.inc.php');
$nav->setTabActive('mrtg');
$page = 'mrtg.inc.php';
require_once(STAFFINC_DIR . 'header.inc.php');
require_once(STAFFINC_DIR . $page);
require_once(STAFFINC_DIR . 'footer.inc.php');
?>