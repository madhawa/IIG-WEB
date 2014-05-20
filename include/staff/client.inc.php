<?php

/**
 * 
 * 
 * 
 */
if (!defined('ENGINEER_INC') || !$thisuser->isEngineer())
    die('Access Denied'); //engineer side include, engineers have permissions
$rep = null;
$newuser = true;
if ($client && $_REQUEST['a'] != 'new') {
    $rep = $client->getInfo();
    $action = 'update';
    $title = 'View: ' . $rep['client_name'];
    $pwdinfo = 'To reset the password enter a new one below';
    $newuser = false;

    $all_client_staff = $client->getAllStaff(); //an array of all staffs including the main admin
} else {
    $title = 'New client';
    $pwdinfo = 'Temp password required';
    $action = 'create';
    $rep['resetpasswd'] = isset($rep['resetpasswd']) ? $rep['resetpasswd'] : 1;
    $rep['isactive'] = isset($rep['isactive']) ? $rep['isactive'] : 1;
    $rep['dept_id'] = $rep['dept_id'] ? $rep['dept_id'] : $_GET['dept'];
    $rep['isvisible'] = isset($rep['isvisible']) ? $rep['isvisible'] : 1;
}
$rep = ($errors && $_POST) ? Format::input($_POST) : Format::htmlchars($rep);
if ($_POST) {
    $rep['client_type'] = implode(',',$_POST['client_type']);
}

require_once(CLASS_DIR . 'class.service.php');

if ($service = new Services($rep['client_id'])) {
    $services = $service->getInfo();
    //print_r($services);
}
if ($_GET['a']=='new_staff') {
    require_once(TEMPLATE_DIR . 'new-client-staff.tpl.php');
} else {
    require_once(TEMPLATE_DIR . 'new-client.tpl.php');
}
?>