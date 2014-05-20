<?php

require_once INCLUDE_DIR . 'class.email.php';
require_once INCLUDE_DIR . 'class.staff.php';

class Inhouse_Service {

    var $service_id;
    var $db_array;

    function __construct($sid) {
        if ( $sid ) {
            $this->service_id = $sid;
            $sql = 'SELECT * FROM ' . INHOUSE_SERVICES_TABLE . ' WHERE id=' . db_input($sid);
            if ( $res = db_query($sql) ) {
                $row = db_fetch_array($res);
                $this->db_array = $row;
            }
        }
    }

    function get_data() {
        return $this->db_array;
    }

    function save($vars, &$errors) {
        global $cfg;

        $now = date('d m Y H:i:s');
        $sql = 'INSERT INTO ' . INHOUSE_SERVICES_TABLE .' SET client_name=' . db_input($vars['name']).
            ',service_type=' . db_input($vars['service_type']).
            ',circuit_type=' . db_input($vars['circuit_type']).
            ',circuit_id=' . db_input($vars['ckt_id']).
            ',circuit_details=' . db_input($vars['ckt_details']).
            ',circuit_location=' . db_input($vars['ckt_location']).
            ',activation_date=' . db_input($now);

        if ( db_query($sql) && db_insert_id($sql) ) {
            //now send email notification
            $email = $cfg->getDefaultEmail();

            if ( file_exists( TEMPLATE_DIR . 'email.add-inhouse.tpl.html' ) ) {
                $body = file_get_contents( TEMPLATE_DIR . 'email.add-inhouse.tpl.html' );

                $today = date('D, d M Y h:i:s a');
                //now handling template variables
                $body = str_replace('%name', $vars['name'], $body);
                $body = str_replace('%service_type', $vars['service_type'], $body);
                $body = str_replace('%circuit_type', $vars['circuit_type'], $body);
                $body = str_replace('%circuit_id', $vars['circuit_id'], $body);
                $body = str_replace('%circuit_details', $vars['circuit_details'], $body);
                $body = str_replace('%circuit_location', $vars['circuit_location'], $body);
                $body = str_replace('%staff_name', $vars['staff_name'], $body);
                $body = str_replace('%today', $today, $body);

                $subj = 'New inhouse service added';

                $noc_mail = Email::getNOCmail();
                //$noc_mail = 'polarglow06@gmail.com';
                $email->send($noc_mail, $subj, $body);
            }
            return true;
        } else {
              echo   $sql;
            return false;
        }
    }

    function discontinue($id) {
        global $cfg, $thisuser;

        $now = date('d m Y H:i:s');
        $sql = 'UPDATE ' . INHOUSE_SERVICES_TABLE . ' SET discontinue_date=' . db_input($now) . ' WHERE id=' . db_input($id);

        if ( db_query($sql) ) {
            //now send email notification
            $email = $cfg->getDefaultEmail();

            if ( file_exists( TEMPLATE_DIR . 'email.discontinue-inhouse.tpl.html' ) ) {
                $body = file_get_contents( TEMPLATE_DIR . 'email.discontinue-inhouse.tpl.html' );

                //now handling template variables
                $s = new Service($id);
                $info = $s->get_data();
                $today = date('D, d M Y h:i:s a');
                $staff_name = $thisuser->getName();

                $body = str_replace('%name', $info['client_name'], $body);
                $body = str_replace('%service_type', $info['service_type'], $body);
                $body = str_replace('%circuit_type', $info['circuit_type'], $body);
                $body = str_replace('%circuit_id', $info['circuit_id'], $body);
                $body = str_replace('%circuit_details', $info['circuit_details'], $body);
                $body = str_replace('%circuit_location', $info['circuit_location'], $body);
                $body = str_replace('%staff_name', $staff_name, $body);
                $body = str_replace('%today', $today, $body);

                $subj = 'inhouse service dicontinued';

                $noc_mail = Email::getNOCmail();
                //$noc_mail = 'polarglow06@gmail.com';
                $email->send($noc_mail, $subj, $body);
            }
            return true;
        } else {
            return false;
        }
    }

    function get_inhouse_services($active=true) {
        if ( $active ) {
            $sql = 'SELECT * FROM ' . INHOUSE_SERVICES_TABLE . ' WHERE discontinue_date=' . db_input('');
            if ( $res = db_query($sql) ) {
                $rows = db_assoc_array($res);
                return $rows;
            } else {
                return false;
            }
        } else {
            $sql = 'SELECT * FROM ' . INHOUSE_SERVICES_TABLE . ' WHERE discontinue_date != ' . db_input('');
            if ( ($res = db_query($sql)) && ($rows = db_assoc_array($res)) ) {
                return $rows;
            } else {
                return false;
            }
        }
    }


}

?>