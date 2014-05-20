<?php
/* * ***********************************************************************
  orders.php

  Handles all orders related actions.

  Peter Rotich <peter@osticket.com>
  Copyright (c)  2006-2010 osTicket
  http://www.osticket.com

  Released under the GNU General Public License WITHOUT ANY WARRANTY.
  See LICENSE.TXT for details.

  vim: expandtab sw=4 ts=4 sts=4:
  $Id: $
 * ******************************************************************** */
require('staff.inc.php');
require_once(CLASS_DIR . 'class.order.php');
require_once(INCLUDE_DIR . 'class.dept.php');
require_once(INCLUDE_DIR . 'class.banlist.php');

$page = '';
$order = null; //clean start.
$nav->setTabActive('orders');
$nav->addSubMenu(array('desc' => 'new order', 'href' => './orders.php?do=new_order', 'iconclass' => 'users'));
if ( $_REQUEST['do'] == 'new_order' ) { //staff adding new order
    $page = 'neworder.inc.php';
}

if ( $_POST['submit_order'] && !$_POST['order_id'] ) {
    ServiceOrder::create($_POST, $errors);
    $page = 'neworder.inc.php';
}
//LOCKDOWN...See if the id provided is actually valid and if the user has access.
if ( !$errors && !$_POST['submit_order'] && ( $id = $_REQUEST['id'] ? $_REQUEST['id'] : $_POST['order_id'] ) ) {
    $deptID = 0;
    $order = new ServiceOrder($id);
    if (!$errors['err'] && !$thisuser->isadmin() && ($thisuser->getDeptId() != $order->getDeptId()))
        $errors['err'] = 'Access denied. this order is on ' . $order->getDeptName() . ' department';
    if (!$order or !$order->getDeptId())
        $errors['err'] = 'Unknown order ID#' . $id; //Sucker...invalid id
    elseif (!$thisuser->isadmin() && !$thisuser->canAccessDept($order->getDeptId()))
        $errors['err'] = 'Access denied. Contact admin if you believe this is in error';

    if (!$errors && $order->getId() == $id)
        $page = 'vieworder.inc.php'; //Default - view

    if (!$errors && $_REQUEST['a'] == 'edit') { //If it's an edit  check permission.
        if ($thisuser->isadmin)
            $page = 'editticket.inc.php';
        else
            $errors['err'] = 'Access denied. You are not allowed to edit';
    }
}

//At this stage we know the access status. we can process the post.
if ($_POST && !$_POST['submit_order'] && !$errors) {
    //TODO: add more security by checking order_id and do field values from form submission
    if ($order && $order->getId()) {
        //More tea please.
        $errors = array();
        if ($_POST['accept'] == 'accept') {
            if ($order->accept()) {
                if (!$order->Log($order->getId(), 'accepted', $thisuser))
                    die('cannot log order accept actions');
                $msg = 'order #' . $order->getId() . ' accepted';
                if ((strtolower($thisuser->getDeptName()) != 'provisioning') && (strtolower($order->getDeptName()) != 'provisioning'))
                    $msg = $msg . ' and moved into ' . $order->getDeptName() . ' department';
                $note = 'order accepted by ' . $thisuser->getName();
                //$order->logActivity('order Closed', $note);
                $page = $order = null; //Going back to main listing.
            } else {
                if (!$errors['err'])
                    $errors['err'] = 'Problems accepting the order. Try again';
            }
        }

        elseif ($_POST['reject'] == 'reject') {
            if ($order->reject()) {
                if (!$order->Log($order->getId(), 'rejected', $thisuser))
                    die('cannot log actions');
                $msg = 'order #' . $order->getId() . ' status set to REJECTED';
                $note = 'order rejected without response by ' . $thisuser->getName();
                //$order->logActivity('order Closed', $note);
                $page = $order = null; //Going back to main listing.
            } else {
                if (!$errors['err'])
                    $errors['err'] = 'Problems rejecting the order. Try again';
            }
        }

        /*
        elseif ($_POST['cancel'] == 'cancel') {
            if ($order->cancel()) {
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
        }
        */


        if ($order && is_object($order))
            $order->reload(); //Reload order info following post processing
    }
}

//update order
if ( $_POST['submit_order'] && $_POST['order_id'] ) {
    $order = ServiceOrder::create($_POST, $errors);
    if ( is_object($order) && $order->getId() ) {
        $msg = 'order updated';
        $page = 'vieworder.inc.php';
    }
}

//deleting orders
if ( $_POST['delete'] ) { //delete an order
    $orders_to_delete = $_POST['delete_orders'];
    $number_of_orders = count($orders_to_delete);
    $deleted = array();
    $not_deleted = array();
    foreach( $orders_to_delete as $order_id ) {
        $sql = 'DELETE FROM ' . ORDER_TABLE . ' WHERE order_id=' . db_input($order_id);
        if ( $res = db_query($sql) && (db_affected_rows($res)!=-1) ) {
            $deleted[] = $order_id;
        } else {
            $not_deleted[] = $order_id;
        }
    }
    $msg = count($deleted) . ' of ' . count($number_of_orders) . ' orders deleted: ids are: ' . implode(',', $deleted);
    if ( count($not_deleted) ) {
        $errors['err'] = ' orders:  ' . implode(',',$not_deleted) . ' not deleted';
    }
}


//Render the page...
$inc = $page ? $page : 'orders.inc.php';

//If we're on orders page...set refresh rate if the user has it configured. No refresh on search and POST to avoid repost.
if (!$_POST && $_REQUEST['a'] != 'search' && !strcmp($inc, 'orders.inc.php') && ($min = $thisuser->getRefreshRate())) {
    define('AUTO_REFRESH', 1);
}

require_once(STAFFINC_DIR . 'header.inc.php');
require_once(STAFFINC_DIR . $inc);
require_once(STAFFINC_DIR . 'footer.inc.php');
?>