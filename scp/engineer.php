<?php


require('staff.inc.php');
//Make sure the user is admin type LOCKDOWN BABY!
if (!$thisuser && !$thisuser->isEngineer()) {
    header('Location: index.php');
    require('index.php'); // just in case!
    exit;
}

if (!$cfg->isHelpDeskOffline()) {

    if (file_exists('../setup/')) {
        $sysnotice = 'Please take a minute to delete <strong>setup/install</strong> directory for security reasons.';
    } else {

        if (CONFIG_FILE && file_exists(CONFIG_FILE) && is_writable(CONFIG_FILE)) {
            //Confirm for real that the file is writable by group or world.
            clearstatcache(); //clear the cache!
            $perms = @fileperms(CONFIG_FILE);
            if (($perms & 0x0002) || ($perms & 0x0010)) {
                $sysnotice = sprintf('Please change permission of config file (%s) to remove write access. e.g <i>chmod 644 %s</i>', basename(CONFIG_FILE), basename(CONFIG_FILE));
            }
        }
    }
    if (!$sysnotice && ini_get('register_globals'))
        $sysnotice = 'Please consider turning off register globals if possible';
}

define('PROV_INC', true);//service provisioning pages
define('ENGINEER_INC', true); // for engineers

require_once(INCLUDE_DIR . 'class.email.php');
require_once(INCLUDE_DIR . 'class.mailfetch.php');
require_once(CLASS_DIR . 'class.client.php');

//Handle a POST.
if ($_POST && $_REQUEST['t'] && !$errors) {
    //print_r($_POST);
    //WELCOME TO THE HOUSE OF PAIN.
    $errors = array(); //do it anyways.

    switch (strtolower($_REQUEST['t'])) {
        
    
    }
}
?>