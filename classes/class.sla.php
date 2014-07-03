<?php

class SLA {
function __construct() {
    
}
function get_ticket($ticket_id) {
    $sql = 'SELECT * FROM '.SLA_TABLE.' WHERE ticket_id='.db_input($ticket_id);
    if ( ($res=db_query($sql)) && (db_num_rows($res)) ) {
        require_once CLASS_DIR.'class.ticket-lite.php';
        $row = db_fetch_row($res);
        $sla_ticket = new SLA_TICKET($row['ticket_info']);
        return $sla_ticket;
    } else {
        return false;
    }
}
function save_ticket($ticket) {
    $sql = '';
}


}

?>