<?php
/* * *******************************************************************
  client side admin.php

  Handles all admin related pages....everything admin!

 * ******************************************************************** */
require('client.inc.php');
//Make sure the user is admin type LOCKDOWN BABY!
//Access checked out OK...lets do the do
define('CLIENT_ADMIN_INC', TRUE); //checked by admin include files
define('ADMINPAGE', TRUE);   //Used by the header to swap menus.
//Files we might need.
//TODO: Do on-demand require...save some mem.
require_once(INCLUDE_DIR . 'class.email.php');
require_once(INCLUDE_DIR . 'class.mailfetch.php');

//Handle a POST.
if ($_POST && $_REQUEST['t'] && !$errors) {
    $errors = array(); //do it anyways.
    switch (strtolower($_REQUEST['t'])) {
        case 'client':
            include_once(CLASS_DIR . 'class.client.php');
            $do = strtolower($_POST['do']);
            switch ($do) {
                case 'view':
                    $client = new Client($_POST['username']);
                    if ($client && ($client->getId()==$_POST['client_id'])) {
                        if ($client->update($_POST, $errors))
                            $msg = 'Client profile updated successfully';
                        elseif (!$errors['err'])
                            $errors['err'] = 'Error updating the user';
                    } elseif ($client->getId()!=$_POST['id']) {
                        //TODO: log this security violation
                        $errors['err'] = 'Trying something illicit ? this action is reported.';
                    } elseif (!$client) {
                        $errors['err'] = 'Error updating user: invalid user';
                    } else {
                        $errors['err'] = 'Error updating user: unspecified error';
                    }
                    break;
                    /*
                case 'new_staff':
                    $client = new Client($_POST['boss_id']);
                    if ( is_object($client) ) {
                        if ( !$client->has_staff_name($_POST['staff_name']) ) {
                            if ($client->addClientStaff($_POST, $errors)) {
                                $msg = 'staff added successsfully';
                            } else {
                                $errors['err'] .= ' Unable to add the staff';
                            }
                        } else {
                            $erorrs['err'] = ' staff name already exists for this client';
                        }
                    } else {
                        $errors['err'] = ' invalid client boss id';
                    }
                    break;
                    */
                default:
                    $errors['err'] = 'Uknown command!';
            }
            break;
        default:
            $errors['err'] = 'Uknown command!';
    }
}

//================ADMIN MAIN PAGE LOGIC==========================
//Process requested tab.
$thistab = strtolower($_REQUEST['t'] ? $_REQUEST['t'] : '');
$inc = $page = ''; //No outside crap please!
$submenu = array();
//$do = strtolower($_POST['do']);
//if (!$do) {
    switch ($thistab) {
        case 'client':
            $group = null;
            $page = '';
            switch ($thistab) {
                case 'client':
                    $client = new Client($thisuser->getId());
                    $services = Client::get_all_cin($thisuser->getId());
                    $all_staff = $client->get_all_staff();//an array of all staffs including the main admin
                    $rep = $client->getInfo();
                    $page = 'client.inc.php';
                    break;
            }
            break;
            default:
            $errors['err'] = 'Uknown command!';
    }
//}
//========================= END ADMIN PAGE LOGIC ==============================//

$inc = ($page) ? CLIENTINC_DIR . $page : '';
//Now lets render the page...
require(CLIENTINC_DIR . 'header.inc.php');
?>
<div>
    <?php if ($errors['err']) { ?>
        <p align="center" id="errormessage"><?php   echo   $errors['err'] ?></p>
    <?php } elseif ($msg) { ?>
        <p align="center" id="infomessage"><?php   echo   $msg ?></p>
    <?php } elseif ($warn) { ?>
        <p align="center" id="warnmessage"><?php   echo   $warn ?></p>
    <?php } ?>
</div>

<div style="margin:0 5px 5px 5px;">
    <?php
    if ($inc && file_exists($inc)) {
        require($inc);
    } else {
        ?>
        <p align="center">
            <font class="error">Problems loading requested admin page. (<?php   echo   Format::htmlchars($thistab) ?>)</font>
            <br>Possibly access denied, if you believe this is in error please get technical support.
        </p>
    <?php } ?>
</div>

<?php
include_once(CLIENTINC_DIR . 'footer.inc.php');
?>