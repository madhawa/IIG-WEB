<?php
/*
* this will add new format ticket id to each tt
*/

require_once '../main.inc.php';
require_once INCLUDE_DIR.'class.ticket.php';

$sql = 'SELECT * FROM '.TICKET_TABLE;

if ( ($res=db_query($sql)) && ($total=db_num_rows($res)) ) {
    $num = 0;
    while( $row=db_fetch_array($res) ) {
        $ticket = new Ticket($row['ticket_id']);
        if ($ticket->save_tt_id()) {
            $num++;
        }
    }
    echo sprintf('%d updated out of %d', $num, $total);
} else {
    echo 'query failure';
}

?>