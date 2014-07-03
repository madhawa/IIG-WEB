<?php

require('staff.inc.php');

//Make sure the user is admin type LOCKDOWN BABY!
if (!$thisuser && !$thisuser->isSuperAdmin()) {
    header('Location: index.php');
    require('index.php'); // just in case!
    exit;
}

//Access checked out OK...lets do the do 
define('OSTADMININC', TRUE); //checked by admin include files
define('PROV_INC', true); //service provisioning pages
define('ENGINEER_INC', true); // for engineers
define('ADMINPAGE', TRUE);   //Used by the header to swap menus.
//Files we might need.
//TODO: Do on-demand require...save some mem.
require_once(INCLUDE_DIR . 'class.ticket.php');
require_once(INCLUDE_DIR . 'class.dept.php');
require_once(INCLUDE_DIR . 'class.group.php');
require_once(INCLUDE_DIR . 'class.email.php');
require_once(INCLUDE_DIR . 'class.mailfetch.php');
require_once(CLASS_DIR . 'class.client.php');


$thistab = strtolower($_REQUEST['t'] ? $_REQUEST['t'] : 'dashboard');
$inc = $page = ''; //No outside crap please!
$submenu = array();
switch ($thistab) {
    case 'dashboard':
        //dashboard shows charts and syslogs
        $nav->setTabActive('dashboard');
        $page = 'dashboard.inc.php';
        break;
    case 'syslog':
        $nav->setTabActive('dashboard');
        $nav->addSubMenu(array('desc' => 'Dashboard', 'href' => 'dashboard.php?t=dashboard', 'iconclass' => 'syslogs'));
        $page = 'syslogs.inc.php';
        break;
    case 'orderlogs':
        $nav->setTabActive('dashboard');
        $nav->addSubMenu(array('desc' => 'Dashboard', 'href' => 'dashboard.php?t=dashboard', 'iconclass' => 'syslogs'));
        $page = 'orderlogs.inc.php';
        break;
}
//========================= END ADMIN PAGE LOGIC ==============================//

$inc = ($page) ? STAFFINC_DIR . $page : '';
//Now lets render the page...
require(STAFFINC_DIR . 'header.inc.php');
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
include_once(STAFFINC_DIR . 'footer.inc.php');
?>