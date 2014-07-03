<?php
/*********************************************************************
    client.inc.php

    File included on every client page

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2010 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id: $
**********************************************************************/
if(!strcasecmp(basename($_SERVER['SCRIPT_NAME']),basename(__FILE__))) die('kwaheri rafiki!');

if(!file_exists('main.inc.php')) die('Fatal Error.');

if ( !defined('OSTCLIENTINC') ) define('OSTCLIENTINC',TRUE);

require_once('main.inc.php');

if(!defined('INCLUDE_DIR')) die('Fatal error');

/*Some more include defines specific to client only */
if(!defined('CLIENTINC_DIR')) define('CLIENTINC_DIR',INCLUDE_DIR.'client/');
define('CCP_DIR',str_replace('//','/',dirname(__FILE__).'/'));//CCP:Client Control Panel

/* include what is needed on client stuff */
require_once(CLASS_DIR.'class.client.php');
require_once(INCLUDE_DIR.'class.ticket.php');
require_once(INCLUDE_DIR.'class.dept.php');

//Check the status of the HelpDesk.
if(!is_object($cfg) || !$cfg->getId() || $cfg->isHelpDeskOffline()) {
    include('./offline.php');
    exit;
}

function clientLoginPage($msg) {
    //$_SESSION['_client']['auth']['dest']=THISPAGE;
    //$_SESSION['_client']['auth']['msg']=$msg;
    $loginmsg = $msg;
    require(CCP_DIR.'login.php');
    exit;
}

$thisuser = new ClientSession($_SESSION['_client']['userID']); /* always reload */
//is the user logged in for real && is client
if( !is_object($thisuser) || !$thisuser->getID() || !$thisuser->isValid() ) {
    $msg = (!$thisuser || !$thisuser->isValid())?'Authentication Required':'Session timed out';
    clientLoginPage($msg);
}
//Keep the session activity alive
$thisuser->refreshSession();
//set clients timezone offset
//$_SESSION['TZ_OFFSET']=$thisuser->getTZoffset();
//$_SESSION['daylight']=$thisuser->observeDaylight();
define('AUTO_REFRESH_RATE',$thisuser->getRefreshRate()*60);

//Clear some vars, we use in all pages.
$errors=array();
$msg=$warn=$sysnotice='';
$tabs=array();
$submenu=array();

?>
