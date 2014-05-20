<?php

#Disable url fopen && url include
ini_set('allow_url_fopen', 0);
ini_set('allow_url_include', 0);

#Display errors
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);


define( 'IP', $_SERVER['REMOTE_ADDR'] );


if (!defined('ROOT_PATH'))
    define('ROOT_PATH', '../'); //root path. Damn directories
define('INCLUDE_DIR', '../include/'); //Change this if include is moved outside the web path.
define('CLASS_DIR', '../classes/');
define('PEAR_DIR', INCLUDE_DIR . 'pear/');

#load config info
$configfile = '';
if (file_exists(INCLUDE_DIR . 'ost-config.php')) //NEW config file v 1.6 stable ++
    $configfile = INCLUDE_DIR . 'ost-config.php';
if (!$configfile || !file_exists($configfile))
    die('<b>Error loading settings. Contact admin.</b>');
require($configfile);
define('CONFIG_FILE', $configfile); //used in admin.php to check perm.

define('ADDED_SERVICES_TABLE', TABLE_PREFIX . 'services');
define('CLIENT_TABLE', TABLE_PREFIX . 'client');
define('CONFIG_TABLE', TABLE_PREFIX . 'config');
define('SYSLOG_TABLE', TABLE_PREFIX . 'syslog');
define('CONFIG_TABLE', TABLE_PREFIX . 'config');
define('EMAIL_TABLE', TABLE_PREFIX . 'email');
define('ODF_TABLE', TABLE_PREFIX . 'odfs');

//Path separator
if (!defined('PATH_SEPARATOR')) {
    if (strpos($_ENV['OS'], 'Win') !== false || !strcasecmp(substr(PHP_OS, 0, 3), 'WIN'))
        define('PATH_SEPARATOR', ';'); //Windows
    else
        define('PATH_SEPARATOR', ':'); //Linux
}

//Set include paths. Overwrite the default paths.
ini_set('include_path', './' . PATH_SEPARATOR . INCLUDE_DIR . PATH_SEPARATOR . PEAR_DIR);

require(INCLUDE_DIR . 'class.misc.php');
require_once('../include/class.format.php');
require_once('../include/mysql.php');
require_once('../include/class.config.php');


$ferror = null;
if (!db_connect(DBHOST, DBUSER, DBPASS) || !db_select_database(DBNAME)) {
    $ferror = 'Unable to connect to the database';
} elseif (!($cfg = Sys::getConfig())) {
    $ferror = 'Unable to load config info from DB. Get tech support.';
}

if ($ferror) { //Fatal error
    Sys::alertAdmin('Fatal Error', $ferror); //try alerting admin.
    die("<b>Fatal Error:</b> Contact system adminstrator."); //Generic error.
    exit;
}

require_once(CLASS_DIR . 'class.service.php');


//odf pulling request
if ($_REQUEST['q'] == 'GET_ODF') {
    /*
      will send:
      0  if no odf data found(db_num_rows($res)==0)
      error string  if any fatal error happens that cannot return json
      json encoded string on success
      so, on the js side, you have to check for valid json string
     */
    $sql = 'SELECT * FROM ' . ODF_TABLE;
    $res = db_query($sql);
    $num_row = db_num_rows($res);
    if (!$num_row) {
          echo   0;
        exit;
    }

    $data;
    $error = '';
    for ($i = 0; $i < $num_row; $i++) {
        //improve this to catch errors
        $each_row = db_fetch_array($res);
        $odf_name = $each_row['odf_name'];

        $json_str = $each_row['odf_json_obj'];
        $dict = json_decode($json_str, TRUE); //associative array
        if ($json_str && ( json_last_error() != JSON_ERROR_NONE )) {
            $error = ' invalid json strng in database odf id ' . $each_row['id'];
            //Sys::log(LOG_CRIT, 'crippled odf object in database', 'odf name: '.$odf_name);
              echo   $error;
            exit;
        } elseif (json_last_error() == JSON_ERROR_NONE && $dict !== NULL) {
            if (!$odf_name) {
                $odf_name = 'EmptyName_' . $each_row['id'];
            }
            $data[$odf_name] = $dict;
        }
    }
    $all_odf_json_str = json_encode($data); //$data should be all odf in standard odf format in an assiciative array
    if (json_last_error() == JSON_ERROR_NONE) {
          echo   $all_odf_json_str;
        exit;
    } else {
        $error = 'php cannot encode json';
          echo   $error;
        exit;
    }
//finished pulling json objects
//save json
}
if ($_POST['submit'] == 'SAVE_ODF') {
    require_once(CLASS_DIR . 'class.odf.php');
    $scp_staff = $_POST['scp_staff'];
    $client_odf = $_POST['selected_odfs']; //json string of all odf for the client
    $dict = json_decode($client_odf, TRUE);
    $data = '';
    if (json_last_error() == JSON_ERROR_NONE) {
        $res = Odf::save($dict);
          echo   $res;
        exit;
    } else {
          echo   'invalid odf data';
        exit;
    }
} else { //save form data
    $response = Services::save($_POST);
      echo   $response;
    exit;
}

exit;
?>