<?php
//  echo   "now im in login.php";
/* * *******************************************************************
  index.php

  Client Login

  BY MINHAJ

  vim: expandtab sw=4 ts=4 sts=4:
  $Id: $
 * ******************************************************************** */
require_once('main.inc.php');
if (!defined('INCLUDE_DIR'))
    die('Fatal Error');
if (!defined('CLIENTINC_DIR'))
    define('CLIENTINC_DIR', INCLUDE_DIR . 'client/');
if (!defined('OSTCLIENTINC'))
    define('OSTCLIENTINC', TRUE); //make includes happy

//require_once(INCLUDE_DIR . 'class.client.php');
require_once(CLASS_DIR . 'class.client.php');

$loginmsg = $loginmsg ? $loginmsg : 'Authentication Required';

//$loginmsg='Authentication Required';
//here 'login_field' field indicates userid
if ($_POST && (!empty($_POST['login_field']) && !empty($_POST['password']))) {

    //$loginmsg = 'Authentication Required';
    $login = trim($_POST['login_field']);
    //$_SESSION['_client']=array(); #Uncomment to disable login strikes.
    //Check time for last max failed login attempt strike.
    //$loginmsg = 'Invalid login';
    /*
    if ($_SESSION['_client']['laststrike']) {
        if ((time() - $_SESSION['_client']['laststrike']) < $cfg->getClientLoginTimeout()) {
            $loginmsg = 'Excessive failed login attempts';
            $errors['err'] = 'You\'ve reached maximum failed login attempts allowed. Try again after ' . $cfg->getClientLoginTimeout() . ' seconds';
        } else { //Timeout is over.
            //Reset the counter for next round of attempts after the timeout.
            $_SESSION['_client']['laststrike'] = null;
            $_SESSION['_client']['strikes'] = 0;
        }
    }
    */
    if (!$errors && ($user = new ClientSession($login)) && $user->getId() && $user->check_passwd($_POST['password'])) {
        //db_query('UPDATE ' . CLIENT_TABLE . ' SET lastlogin=NOW() WHERE client_id=' . db_input($user->getId()));
        //Figure out where the user is headed - destination!

        //Now set session crap and lets roll baby!
        $_SESSION['_client'] = array(); //clear.
        //$dest = $_SESSION['_client']['auth']['dest'];
        $_SESSION['_client']['userID'] = $login;
        $user->refreshSession(); //set the hash.
        //$_SESSION['TZ_OFFSET'] = $user->getTZoffset();
        //$_SESSION['daylight'] = $user->observeDaylight();
        //Sys::log(LOG_DEBUG,'User login',$msg);
        //Sys::log(LOG_DEBUG, 'Client login', sprintf("%s logged in [%s]", $user->getId(), $user->getIP())); //Debug.
        //Redirect to the original destination. (make sure it is not redirecting to login page.)
        //$dest = ($dest && (!strstr($dest, 'login.php') && !strstr($dest, 'ajax.php'))) ? $dest : 'index.php';
        session_write_close();
        session_regenerate_id();
        @header("Location: index.php");
        require_once('index.php'); //Just incase header is messed up.
        exit;
    }
    //If we get to this point we know the login failed.
    $loginmsg = 'wrong username or password';

    /*
    $_SESSION['_client']['strikes']+=1;
    if (!$errors && $_SESSION['_client']['strikes'] > $cfg->getClientMaxLogins()) {
        $loginmsg = 'Access Denied';
        $errors['err'] = 'Forgot your login info? Please <a href="#">reset system on progress</a>.';
        $_SESSION['_client']['laststrike'] = time();
        $alert = 'Excessive login attempts by a client?' . "\n" .
                'login name: ' . $login_field . "\n" .
                'IP: ' . isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'] . "\n" . 'Time:' . date('M j, Y, g:i a T') . "\n\n" .
                'Attempts #' . $_SESSION['_client']['strikes'];
        Sys::log(LOG_ALERT, 'Excessive login attempts (client)', $alert, ($cfg->alertONLoginError()));
    } elseif ($_SESSION['_client']['strikes'] % 2 == 0) { //Log every other failed login attempt as a warning.
        $alert = 'login name: ' . $login_field . "\n" . 'IP: ' . isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'] .
                "\n" . 'TIME: ' . date('M j, Y, g:i a T') . "\n\n" . 'Attempts #' . $_SESSION['_client']['strikes'];
        Sys::log(LOG_WARNING, 'Failed login attempt (client)', $alert);
    }
    */
} else {
require(CLIENTINC_DIR . 'header.inc.php');
require(TEMPLATE_DIR . 'client.login.tpl.php');
require(CLIENTINC_DIR . 'footer.inc.php');
}

?>
