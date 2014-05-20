<?php

function getCIN() {
    return new cin($this->row['cin'], $this->getClientId());
}

function get_to_location() {
    
}

function get_from_location() {
    
}

function get_root_cause() {
    return $this->row['root_cause'];
}

function getLastMessage() {
    $sql = 'SELECT message FROM '.TICKET_MESSAGE_TABLE.' WHERE ticket_id='.db_input($this->getId()).' ORDER BY created LIMIT 1';
    if ( ($res = db_query($sql)) && db_num_rows($res) ) {
        $row = db_fetch_row($res);
        return $row['message']
    } else {
        return false;
    }
}

function getLastMsgPoster() {
    $sql = 'SELECT client_id FROM '.TICKET_MESSAGE_TABLE.' WHERE ticket_id='.db_input($this->getId()).' ORDER BY created LIMIT 1';
    if ( ($res = db_query($sql)) && db_num_rows($res) ) {
        $row = db_fetch_row($res);
        $client = new Client($row['client_id']);
        return $client;
    } else {
        return false;
    }
}

function getLastResponse() {
    $sql = 'SELECT message FROM '.TICKET_RESPONSE_TABLE.' WHERE ticket_id='.db_input($this->getId()).' ORDER BY created LIMIT 1';
    if ( ($res = db_query($sql)) && db_num_rows($res) ) {
        $row = db_fetch_row($res);
        return $row['response']
    } else {
        return false;
    }
}

function getLastResponsePoster() {
    $sql = 'SELECT staff_id FROM '.TICKET_RESPONSE_TABLE.' WHERE ticket_id='.db_input($this->getId()).' ORDER BY created LIMIT 1';
    if ( ($res = db_query($sql)) && db_num_rows($res) ) {
        $row = db_fetch_row($res);
        $staff = new Staff($row['staff_id']);
    } else {
        return false;
    }
}

function getLastNote() {
    $sql = 'SELECT message FROM '.TICKET_NOTE_TABLE.' WHERE ticket_id='.db_input($this->getId()).' ORDER BY created LIMIT 1';
    if ( ($res = db_query($sql)) && db_num_rows($res) ) {
        $row = db_fetch_row($res);
        return $row['note']
    } else {
        return false;
    }
}

function getLastNotePoster() {
    $sql = 'SELECT staff_id FROM '.TICKET_NOTE_TABLE.' WHERE ticket_id='.db_input($this->getId()).' ORDER BY created LIMIT 1';
    if ( ($res = db_query($sql)) && db_num_rows($res) ) {
        $row = db_fetch_row($res);
        $staff = new Staff($row['staff_id']);
    } else {
        return false;
    }
}


function getLastEditor() {
    if ( $this->row['last_editor'] ) {
        $staff = new Staff($row['last_editor']);
    } else {
        return false;
    }
}

function getCloser() {
    if ( $this->row['closer'] ) {
        $staff = new Staff($row['closer']);
    } else {
        return false;
    }
}

function getDeleter() {
    if ( $this->row['deleter'] ) {
        $staff = new Staff($row['deleter']);
    } else {
        return false;
    }
}




?>