<?php
require_once '../main.inc.php';
require_once INCLUDE_DIR.'class.ticket.php';
$ticket = new Ticket(318);

echo $ticket->getLastMsgId();
?>