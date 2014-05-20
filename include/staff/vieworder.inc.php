<?php
//Note that order is initiated in orders.php.
if(!defined('OSTSCPINC') || !@$thisuser->isStaff() || !is_object($order) ) die('Invalid path');

require_once(CLASS_DIR .'class.order.php');

if(!$order->getId() or (!$thisuser->canAccessDept($order->getDeptId()))) die('Access Denied, order is not on your department');

if ($order->getStaffId() and ($order->getStaffId() != $thisuser->getId())) die('Access Denied, order is already assigned to another staff ');

if($order->getLock($order->getId()))
    $errors['err']='order is already locked to staff id ' . $order->getLock($order->getId()) . ' from ' . $order->getLockTime($order->getId()) ;

//We are ready baby...lets roll. Akon rocks! 
$dept  = $order->getDept();  //Dept
$staff = $order->getStaff(); //Assiged staff.
$id=$order->getId(); //order ID.


//TODO: set lock on opening the order and clear it on any action
   
$do = 'staff_view';

$rep = $order->db_array;
?>

<?php if ($errors['err']) { ?>
    <p align="center" id="errormessage"><?php   echo   $errors['err'] ?></p>
<?php } elseif ($msg) { ?>
    <p align="center" id="infomessage"><?php   echo   $msg ?></p>
<?php } elseif ($warn) { ?>
    <p id="warnmessage"><?php   echo   $warn ?></p>
<?php } ?>

<?php
require(TEMPLATE_DIR.'order.tpl.php');
?>