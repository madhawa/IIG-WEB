<?php

/* * ***********************************************************************
  orders.php

  Handles all orders related actions.


 * ******************************************************************** */
//TODO: log permission denied issues, access violations
if (!defined('CLIENTSOFINC'))
    die('Access Denied');
/*
  require_once('client.inc.php');
 */
if (!is_object($thisuser) || !$thisuser->isValid())
    die('Access denied'); //Double check again.



require_once(CLASS_DIR . 'class.order.php');
require_once(INCLUDE_DIR . 'class.dept.php');
require_once(INCLUDE_DIR . 'class.banlist.php');

$page = '';
$flag = '';
$order = null; //clean start.
//LOCKDOWN...See if the id provided is actually valid and if the user has access.
if (!$errors && ($id = $_REQUEST['id'] ? $_REQUEST['id'] : $_POST['order_id'])) {
    //TODO: log permission denied issues, access violations
    if ($thisuser->isClientAdmin() || $thisuser->onlyView()) {
        $order = new ServiceOrder($id);
        if (!$errors && $order->getId() == $id)
            $page = 'vieworder.inc.php'; //Default - view
    } else
        $errors['err'] = 'Access denied, you have no permission for this action';
}

$asked_status = Format::striptags($_REQUEST['status']);

//At this stage we know the access status. we can process the post.
if ($_POST && !$errors) {
    //TODO: add more security by checking order_id and do field values from form submission
    if ($order && $order->getId()) {
        //More tea please.
        $errors = array();

        if ($_POST['cancel'] == 'cancel') {
            if ($thisuser->isClientAdmin() && $order->client_can_cancel($errors)) {
                if ($order->cancel_by_client($errors)) {
                    if (!$order->Log($order->getId(), 'cancelled', $thisuser))
                        die('cannot log actions');
                    $msg = 'order #' . $order->getId() . ' status set to CANCELLED';
                    $note = 'order cancelled without response by ' . $thisuser->getName();
                    //$order->logActivity('order Closed', $note);
                    $page = $order = null; //Going back to main listing.
                } else {
                    if (!$errors['err'])
                        $errors['err'] = 'Problems cancelling the order. Try again';
                }
            } elseif (!$thisuser->isClientAdmin()) {
                //TODO: log permission denied issues, access violations
                $errors['err'] = 'Access denied, you have no permission for this action';
            } else {
                if (!$errors['err'])
                    $errors['err'] = 'You cant cancel the order now. Service already delivered. order id is ' . $order->getId();
            }
        }


        if ($order && is_object($order))
            $order->reload(); //Reload order info following post processing
    }
}

//Render the page...
$inc = $page ? $page : 'orders.inc.php';

//If we're on orders page...set refresh rate if the user has it configured. No refresh on search and POST to avoid repost.
if (!$_POST && $_REQUEST['a'] != 'search' && !strcmp($inc, 'orders.inc.php') && ($min = $thisuser->getRefreshRate())) {
    define('AUTO_REFRESH', 1);
}

require_once(CLIENTINC_DIR . 'header.inc.php');
require_once(CLIENTINC_DIR . $inc);
require_once(CLIENTINC_DIR . 'footer.inc.php');
?>