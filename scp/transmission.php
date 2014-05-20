<?php
require('staff.inc.php');
require ( CLASS_DIR . 'class.transmission.php' );
$nav->setTabActive('transmission');
$nav->addSubMenu(array('desc' => 'view', 'href' => './transmission.php?do=view', 'iconclass' => ''));
$nav->addSubMenu(array('desc' => 'add new', 'href' => './transmission.php?do=new', 'iconclass' => ''));


$rep = array();
$errors = array();

//view a transmission data
if ( $client_id = $_GET['id'] ) {
    $trans = new Transmission($client_id, 'client_id');
    $rep = $trans->getInfo();
    
    $tr = $trans->getTransmissionData(); //get the transmission data json string
    $tr = json_decode($tr, true); //decode the json string and turn it into an associative array
    
    $rep = array_merge($rep, $tr);//merge this two array to view in the template easily
    
    $rep['transmission_data'] = ''; //remove this data for clarity
}


//new transmission data
if ( ($_GET['do'] == 'new') || ($_GET['id']) ) {
    //get client list from db
    $sql_client = 'SELECT client_id, client_name FROM ' . CLIENT_TABLE;
    $res = db_query($sql_client);
    $client_data = array();
    while ( $data = db_fetch_array($res) ) {
        $client_data[] = $data;
    }
    
    if ( $_POST ) {
        if ( Transmission::save($_POST, $errors) ) {
        $msg = ' data saved ';
        } else {
            $errors['err'] .= ' data save failure '; 
        }
        $rep = $_POST;
    }
}



require_once(STAFFINC_DIR . 'header.inc.php');

if ( ($_GET['do'] == 'new') || $client_id ) {
    require_once(TEMPLATE_DIR . 'transmission.tpl.php');
} else {
    require_once(STAFFINC_DIR . 'transmission_data.inc.php');
}

require_once(STAFFINC_DIR . 'footer.inc.php');
?>