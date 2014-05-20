<?php
/*************************************************************************
    class.sys.php

    System core helper.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2010 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id: $
**********************************************************************/

require_once(INCLUDE_DIR.'class.config.php'); //Config helper

define('LOG_WARN',LOG_WARNING);

class Sys {

    var $loglevel=array(1=>'Error','Warning','Debug');


    //Load configuration info.
    function getConfig() {
        $cfg= new Config(1);
        return ($cfg && $cfg->getId())?$cfg:null;
    }


    function alertAdmin($subject,$message,$log=false) {
        global $cfg;
                
        //Set admin's email address
        if(!$cfg || !($to=$cfg->getAdminEmail()))
            $to=ADMIN_EMAIL;

        //Try getting the alert email.
        $email=null;
        if($cfg && !($email=$cfg->getAlertEmail())) 
            $email=$cfg->getDefaultEmail(); //will take the default email.

        if($email) {
            $email->send($to,$subject,$message);
        }else {//no luck - try the system mail.
            Email::sendmail($to,$subject,$message,sprintf('"Alerts"<%s>',$to));
        }

        //log the alert? Watch out for loops here.
        if($log && is_object($cfg)) { //if $cfg is not set then it means we don't have DB connection.
            Sys::log(LOG_CRIT,$subject,$message,false); //Log the enter...and make sure no alerts are resent.
        }

    }

    function log($priority,$title,$message,$alert=true) {
        global $cfg;

        switch($priority){ //We are providing only 3 levels of logs. Windows style.
            case LOG_EMERG:
            case LOG_ALERT: 
            case LOG_CRIT: 
            case LOG_ERR:
                $level=1;
                if($alert) {
                    Sys::alertAdmin($title,$message);
                }
                break;
            case LOG_WARN:
            case LOG_WARNING:
                //Warning...
                $level=2;
                break;
            case LOG_NOTICE:
            case LOG_INFO:
            case LOG_DEBUG:
            default:
                $level=3;
                //debug
        }
        //Save log based on system log level settings.
        if($cfg && $cfg->getLogLevel()>=$level){
            $loglevel=array(1=>'Error','Warning','Debug');
            $sql='INSERT INTO '.SYSLOG_TABLE.' SET created=NOW(),updated=NOW() '.
                 ',title='.db_input($title).
                 ',log_type='.db_input($loglevel[$level]).
                 ',log='.db_input($message).
                 ',ip_address='.db_input($_SERVER['REMOTE_ADDR']);
            //  echo   $sql;
            mysql_query($sql); //don't use db_query to avoid possible loop.
        }
    }

    function purgeLogs(){
        global $cfg;

        if($cfg && ($gp=$cfg->getLogGraceperiod()) && is_numeric($gp)) {
            $sql='DELETE  FROM '.SYSLOG_TABLE.' WHERE DATE_ADD(created, INTERVAL '.$gp.' MONTH)<=NOW()';
            db_query($sql);
        }

    }
    
    function unique_id() {
        $id = time() + mt_rand();
        //TODO: $ip here is for future use
        $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $ip = str_replace(".", "", "$ip");
        $ip = str_replace("::", "", "$ip");
        return $id;
    }
    
    /*
    TODO: this function has a problem, its generated uniqie id contains alphabets
    function unique_id($length=10) {
        if (is_numeric($length)) {
            $rnd_id = crypt(uniqid(rand(),1));
            $rnd_id = strip_tags(stripslashes($rnd_id));
            $rnd_id = str_replace(".","",$rnd_id); 
            $rnd_id = strrev(str_replace("/","",$rnd_id));
            $rnd_id = substr($rnd_id,0,$length);
            return $rnd_id;
        }
        else
            Sys:unique_id();
    }
    */
}

?>
