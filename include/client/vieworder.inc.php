<?php
//Note that order is initiated in orders.php.
if (!defined('CLIENTSOFINC') || !is_object($order))
    die('Invalid path');

if ($thisuser->isClientAdmin() || $thisuser->onlyView()) {
    require_once(CLASS_DIR . 'class.order.php');
    $dept = $order->getDept();  //Dept
    $staff = $order->getStaff(); //Assiged staff.
    $id = $order->getId(); //order ID.
    //TODO: set lock on opening the order and clear it on any action
    if (!$do)
        $do = 'client_view';
    $rep = $order->db_array;
} else $errors['err'] = 'access denied, you have no permission for this action';

?>

<?php if ($errors['err']) { ?>
    <p align="center" id="errormessage"><?php   echo   $errors['err'] ?></p>
<?php } ?>

<?php if ($msg) { ?>
    <p align="center" id="infomessage"><?php   echo   $msg ?></p>
<?php } ?>

<?php if ($warn) { ?>
    <p id="warnmessage"><?php   echo   $warn ?></p>
<?php } ?>

<?php
if ($thisuser->isClientAdmin() || $thisuser->onlyView())
    require(TEMPLATE_DIR . 'order.tpl.php');
?>