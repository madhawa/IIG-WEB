<?php
/**
 *order_create.php
 *new order handle
 * TODO: move this section into admin.php
 *
 *
 */
require('client.inc.php');
require(CLASS_DIR .'class.order.php');

$inc ='order.form.inc.php';    //default include. it contains the form to create order
$errors = array();
$order_id = $_GET['id'] ? $_GET['id'] : 0;

if ($_POST) {
    if (($order = ServiceOrder::create($_POST, $errors))) {
        $msg = 'New Order created';
        if (!$order->Log($order->getId(), 'created', $thisuser)) 
            $errors['err'] .= 'Log error!';
    } else {
        $rep = $_POST;
        $errors['err'] = 'Unable to create an order. ' . $errors['err'];
        $input_error="input_error";
    }
}

//for debugging
print_r($errors);

//page
require(CLIENTINC_DIR . 'header.inc.php');
require(ROOT_DIR . $inc);
require(CLIENTINC_DIR . 'footer.inc.php');
?>