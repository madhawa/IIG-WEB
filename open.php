<?php

/** *******************************************************************
  open.php

  New tickets handle.

  vim: expandtab sw=4 ts=4 sts=4:
  $Id: $
 * ******************************************************************** */
/**
 * handles new ticket request from client form
 */
require('client.inc.php');
$inc = 'open.inc.php';    //default include. it contains the form to create ticket
$errors = array();
if ($_POST){
    $_POST['deptId'] = $_POST['emailId'] = 0; //Just Making sure we don't accept crap...only topicId is expected.
    //Ticket::create...checks for errors..
    if (($ticket = Ticket::create($_POST, $errors, 'client'))) {
        $msg = 'Support ticket request created';
        
    }else {
        $errors['err'] = $errors['err'] ? $errors['err'] : 'Unable to create a ticket. Please correct errors below and try again!';
    }
}

//page
require(CLIENTINC_DIR . 'header.inc.php');
require(CLIENTINC_DIR . $inc);
require(CLIENTINC_DIR . 'footer.inc.php');
?>
