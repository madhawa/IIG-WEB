<?php 
require('staff.inc.php');
$nav->setTabActive('chat');

require_once(STAFFINC_DIR . 'header.chat.inc.php');
require_once(TEMPLATE_DIR . 'chat.tpl.php');
require_once(STAFFINC_DIR . 'footer.inc.php');
?>