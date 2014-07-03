<?php
require('staff.inc.php');
$nav->setTabActive('sla');

$clients = Client::get_all_clients();

$client_id = $_REQUEST['client_id']?$_REQUEST['client_id']:0;

$client = new Client($client_id);

if ( $client->getId() ) {
    require_once 'calculate_sla.php';
}

require_once(STAFFINC_DIR . 'header.inc.php');
require_once(STAFFINC_DIR.'sla.inc.php');
require_once(STAFFINC_DIR . 'footer.inc.php');
?>