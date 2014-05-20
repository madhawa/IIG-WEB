<?php
require('client.inc.php');
if (!is_object($thisuser) || !$thisuser->isValid())
    die('Access denied'); //Double check again.
// Path to the chat directory:
define('AJAX_CHAT_PATH', PLUGIN_AJAX_CHAT_DIR);


require_once(CLIENTINC_DIR . 'header.chat.inc.php');
require_once(TEMPLATE_DIR . 'chat.tpl.php');
require_once(CLIENTINC_DIR . 'footer.inc.php'); 
?>