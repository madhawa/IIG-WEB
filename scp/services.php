<?php
require('staff.inc.php');
require_once(CLASS_DIR . 'class.service.php');
require_once(CLASS_DIR . 'class.client.php');
$nav->setTabActive('services');

$errors = array();

//routing to different pages
$page=  $_GET['page'];
$tpl = '';
switch($page) {
    case 'add_service':
        $title = 'Add new service';
        $tpl = 'scp.services.add.tpl.php';
        break;
    case 'view_service':
        $title = 'View service';
        $tpl = 'view_service.tpl.php';
        break;
    default:
        $title = 'view added services';
        $services = Service::get_all_services();
        $tpl = 'view_services.tpl.php';
        break;
}


require_once(STAFFINC_DIR . 'header.inc.php');
require_once(STAFFINC_DIR . 'services.inc.php');
require_once(STAFFINC_DIR . 'footer.inc.php');

?>