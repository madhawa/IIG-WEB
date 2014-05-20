<?php

include(CLIENTINC_DIR . 'header.inc.php');

require_once 'client.inc.php';


$email = $cfg->getDefaultEmail();

$email->send('polarglow06@gmail.com', 'sample subject', 'sample email body');

?>