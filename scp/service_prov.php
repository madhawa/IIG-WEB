<?php 
/*
service provisioning,
only provisioning dept and engineering staffs have access
*/

require('staff.inc.php');
define('PROV_INC', true); //provisioning side include
define('ENGINEER_INC', true); // for engineers

require_once(STAFFINC_DIR . 'header.inc.php');
require_once(STAFFINC_DIR.'services.inc.php');
require_once(STAFFINC_DIR . 'footer.inc.php');
?>