<?php
require_once('client.inc.php');
if (!is_object($thisuser) || !$thisuser->isValid())
    die('Access denied'); //Double check again.
define ('CLIENTSOFINC', true);
require(CLIENTINC_DIR.'orders.php');
?>