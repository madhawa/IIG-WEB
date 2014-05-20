<?php
require('client.inc.php');
require ( CLASS_DIR . 'class.transmission.php' );

$trans = new Transmission($thisuser->getId(), 'client_id');

$rep = $trans->getInfo();    
$tr = $trans->getTransmissionData(); //get the transmission data json string
$tr = json_decode($tr, true); //decode the json string and turn it into an associative array
$rep = array_merge($rep, $tr);//merge this two array to view in the template easily
$rep['transmission_data'] = ''; //remove this data for clarity

$spaces = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

require_once(CLIENTINC_DIR . 'header.inc.php');
require_once(TEMPLATE_DIR . 'transmission.client.tpl.php');
require_once(CLIENTINC_DIR . 'footer.inc.php');
?>