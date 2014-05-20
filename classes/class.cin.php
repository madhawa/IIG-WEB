<?php

class cin {
    var $db_array;
    var $cin;
    var $client_id;
    function __construct($cin, $client_id) {
        if ($cin) {
            $sql = 'SELECT * FROM ' . SERVICE_CIN_TABLE . ' WHERE cin=' . db_input($cin).' AND client_id='.db_input($client_id);
            if ( $res = db_query( $sql ) ) {
                $row = db_fetch_array($res);
                $this->db_array = $row;
                $this->cin = $cin;
                $this->client_id = $client_id;
                return true;
            } else {
                return false;
            }
        }
    }

    function get_cin_value() {
        return $this->cin;
    }

    function get_client_name() {
        return $this->db_array['client_name'];
    }

    function get_client_id() {
        return $this->db_array['client_id'];
    }

    function get_service_type () {
        return $this->db_array['service_type'];
    }

    function get_circuit_type() {
        return $this->db_array['circuit_type'];
    }
}

?>