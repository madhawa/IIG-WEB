<?php

class Services {

    var $db_array;

    function Services($client_id) {
        return $this->load($client_id);
    }

    function load($client_id, $static_call = 0) {
        $client_id = htmlspecialchars($client_id);
        $sql = 'SELECT * FROM ' . ADDED_SERVICES_TABLE . ' WHERE client_id=' . db_input($client_id);
        $res = db_query($sql);

        if (!$res || !db_num_rows($res))
            return NULL;
        if ($static_call)
            return db_num_rows($res);

        $row = db_fetch_array($res);
        $this->db_array = $row;
        return true;
    }

    function reload() {
        $this->load($this->getClientId());
    }

    function getId() {
        return $this->db_array['id'];
    }

    function getClientId() {
        return $this->client_id;
    }

    function getInfo() {
        return $this->db_array;
    }

    //this function will always return 1 for success and error string for error/failure
    function save($data) {
        $error = '';
        $_POST = $data;

        if ($_POST) {
            if ($_POST[all_odf_json])
                $all_odf = $_POST[all_odf_json];
            $_POST = Format::strip_slashes($_POST);
            $client_id = $_POST['client_id'];
            $staff_id = $_POST['staff_id'];
            $ipaddress = $_SERVER['REMOTE_ADDR'];

            if (!$client_id)
                $error .= ' select a client first ';

            if (!$error && $_POST['submit'] == 'add_service') {
                if (!$_POST['service'])
                    $error .= 'select a service first';
                $service_name_1 = $_POST['service'];
                $service_name_2 = '';
                $sql = '';

                switch ($_POST['service']) {
                    case 'ip_bw':
                        $service_name_2 = 'ip_bw';
                        if (!$_POST['ip_bw_amount'] ||
                                !$_POST['ip_bw_unit'] ||
                                !$_POST['ip_bw_1asiaahl_end_ip'] ||
                                !$_POST['ip_bw_client_end_ip']) {
                            $error .= ' fill required fields first ';
                        }
                        $sql = ' SET updated=NOW() ' .
                                ',client_id=' . db_input($_POST['client_id']) .
                                ',service_name=' . db_input($_POST['service']) .
                                ',ip_bw_amount=' . db_input($_POST['ip_bw_amount']) .
                                ',ip_bw_unit=' . db_input($_POST['ip_bw_unit']) .
                                ',ip_bw_1asiaahl_end_ip=' . db_input($_POST['ip_bw_1asiaahl_end_ip']) .
                                ',ip_bw_client_end_ip=' . db_input($_POST['ip_bw_client_end_ip']) .
                                ',ip_bw_remarks=' . db_input($_POST['ip_bw_remarks']) .
                                ',ip_transit_amount=' . db_input('') .
                                ',ip_transit_amount_unit=' . db_input('') .
                                ',ip_transit_1asiaahl_end_ip=' . db_input('') .
                                ',ip_transit_client_end_ip=' . db_input('') .
                                ',ip_transit_prefix=' . db_input('') .
                                ',iplc_fields_level=' . db_input('') .
                                ',iplc_fields_amount=' . db_input('') .
                                ',iplc_fields_circuit_type=' . db_input('') .
                                ',iplc_fields_circuit_diagram=' . db_input('') .
                                ',mpls_fields_primary_circuit_level=' . db_input('') .
                                ',mpls_fields_primary_circuit_amount=' . db_input('') .
                                ',mpls_fields_primary_circuit_type=' . db_input('') .
                                ',mpls_fields_primary_circuit_diagram=' . db_input('') .
                                ',mpls_fields_secondary_circuit_level=' . db_input('') .
                                ',mpls_fields_secondary_circuit_amount=' . db_input('') .
                                ',mpls_fields_secondary_circuit_type=' . db_input('') .
                                ',mpls_fields_secondary_circuit_diagram=' . db_input('') .
                                ',mpls_fields_tertiary_circuit_level=' . db_input('') .
                                ',mpls_fields_tertiary_circuit_amount=' . db_input('') .
                                ',mpls_fields_tertiary_circuit_type=' . db_input('') .
                                ',mpls_fields_tertiary_circuit_diagram=' . db_input('')
                        ;
                        break;

                    case 'ip_transit':
                        $service_name_2 = 'ip_transit';
                        if (!$_POST['ip_transit_amount'] ||
                                !$_POST['ip_transit_amount_unit'] ||
                                !$_POST['ip_transit_1asiaahl_end_ip'] ||
                                !$_POST['ip_transit_client_end_ip'] ||
                                !$_POST['ip_transit_prefix']) {
                            $error .= ' fill required fields first ';
                        }

                        $sql = 'SET updated=NOW() ' .
                                ',client_id=' . db_input($_POST['client_id']) .
                                ',service_name=' . db_input($_POST['service']) .
                                ',ip_bw_amount=' . db_input('') .
                                ',ip_bw_unit=' . db_input('') .
                                ',ip_bw_1asiaahl_end_ip=' . db_input('') .
                                ',ip_bw_client_end_ip=' . db_input('') .
                                ',ip_bw_remarks=' . db_input('') .
                                
                                ',ip_transit_amount=' . db_input($_POST['ip_transit_amount']) .
                                ',ip_transit_amount_unit=' . db_input($_POST['ip_transit_amount_unit']) .
                                ',ip_transit_1asiaahl_end_ip=' . db_input($_POST['ip_transit_1asiaahl_end_ip']) .
                                ',ip_transit_client_end_ip=' . db_input($_POST['ip_transit_client_end_ip']) .
                                ',ip_transit_prefix=' . db_input($_POST['ip_transit_prefix']) .
                                
                                ',iplc_fields_level=' . db_input('') .
                                ',iplc_fields_amount=' . db_input('') .
                                ',iplc_fields_circuit_type=' . db_input('') .
                                ',iplc_fields_circuit_diagram=' . db_input('') .
                                
                                ',mpls_fields_primary_circuit_level=' . db_input('') .
                                ',mpls_fields_primary_circuit_amount=' . db_input('') .
                                ',mpls_fields_primary_circuit_type=' . db_input('') .
                                ',mpls_fields_primary_circuit_diagram=' . db_input('') .
                                ',mpls_fields_secondary_circuit_level=' . db_input('') .
                                ',mpls_fields_secondary_circuit_amount=' . db_input('') .
                                ',mpls_fields_secondary_circuit_type=' . db_input('') .
                                ',mpls_fields_secondary_circuit_diagram=' . db_input('') .
                                ',mpls_fields_tertiary_circuit_level=' . db_input('') .
                                ',mpls_fields_tertiary_circuit_amount=' . db_input('') .
                                ',mpls_fields_tertiary_circuit_type=' . db_input('') .
                                ',mpls_fields_tertiary_circuit_diagram=' . db_input('')
                        ;
                        break;

                    case 'iplc':
                        $service_name_2 = 'iplc';
                        if (!$_POST['iplc_fields_level'] ||
                                !$_POST['iplc_fields_amount'] ||
                                !$_POST['iplc_fields_circuit_type']) {
                            $error .= ' fill required fields first ';
                        }

                        $sql = 'SET updated=NOW() ' .
                                ',client_id=' . db_input($_POST['client_id']) .
                                ',service_name=' . db_input($_POST['service']) .
                                ',ip_bw_amount=' . db_input('') .
                                ',ip_bw_unit=' . db_input('') .
                                ',ip_bw_1asiaahl_end_ip=' . db_input('') .
                                ',ip_bw_client_end_ip=' . db_input('') .
                                ',ip_bw_remarks=' . db_input('') .
                                
                                ',ip_transit_amount=' . db_input('') .
                                ',ip_transit_amount_unit=' . db_input('') .
                                ',ip_transit_1asiaahl_end_ip=' . db_input('') .
                                ',ip_transit_client_end_ip=' . db_input('') .
                                ',ip_transit_prefix=' . db_input('') .
                                
                                ',iplc_fields_level=' . db_input($_POST['iplc_fields_level']) .
                                ',iplc_fields_amount=' . db_input($_POST['iplc_fields_amount']) .
                                ',iplc_fields_circuit_type=' . db_input($_POST['iplc_fields_circuit_type']) .
                                
                                ',mpls_fields_primary_circuit_level=' . db_input('') .
                                ',mpls_fields_primary_circuit_amount=' . db_input('') .
                                ',mpls_fields_primary_circuit_type=' . db_input('') .
                                ',mpls_fields_secondary_circuit_level=' . db_input('') .
                                ',mpls_fields_secondary_circuit_amount=' . db_input('') .
                                ',mpls_fields_secondary_circuit_type=' . db_input('') .
                                ',mpls_fields_tertiary_circuit_level=' . db_input('') .
                                ',mpls_fields_tertiary_circuit_amount=' . db_input('') .
                                ',mpls_fields_tertiary_circuit_type=' . db_input('')
                        ;
                        break;

                    case 'mpls':
                        $service_name_2 = 'mpls';
                        if (!$_POST['mpls_fields_primary_circuit_level'] ||
                                !$_POST['mpls_fields_primary_circuit_amount'] ||
                                !$_POST['mpls_fields_primary_circuit_type'] ||
                                !$_POST['mpls_fields_secondary_circuit_level'] ||
                                !$_POST['mpls_fields_secondary_circuit_amount'] ||
                                !$_POST['mpls_fields_secondary_circuit_type'] ||
                                !$_POST['mpls_fields_tertiary_circuit_level'] ||
                                !$_POST['mpls_fields_tertiary_circuit_amount'] ||
                                !$_POST['mpls_fields_tertiary_circuit_type']) {
                            $error .= ' fill required fields first ';
                        }

                        $sql = 'SET updated=NOW() ' .
                                ',client_id=' . db_input($_POST['client_id']) .
                                ',service_name=' . db_input($_POST['service']) .
                                ',ip_bw_amount=' . db_input('') .
                                ',ip_bw_unit=' . db_input('') .
                                ',ip_bw_1asiaahl_end_ip=' . db_input('') .
                                ',ip_bw_client_end_ip=' . db_input('') .
                                ',ip_bw_remarks=' . db_input('') .
                                
                                ',ip_transit_amount=' . db_input('') .
                                ',ip_transit_amount_unit=' . db_input('') .
                                ',ip_transit_1asiaahl_end_ip=' . db_input('') .
                                ',ip_transit_client_end_ip=' . db_input('') .
                                ',ip_transit_prefix=' . db_input('') .
                                
                                ',iplc_fields_level=' . db_input('') .
                                ',iplc_fields_amount=' . db_input('') .
                                ',iplc_fields_circuit_type=' . db_input('') .
                                
                                ',mpls_fields_primary_circuit_level=' . db_input($_POST['mpls_fields_primary_circuit_level']) .
                                ',mpls_fields_primary_circuit_amount=' . db_input($_POST['mpls_fields_primary_circuit_amount']) .
                                ',mpls_fields_primary_circuit_type=' . db_input($_POST['mpls_fields_primary_circuit_type']) .
                                ',mpls_fields_secondary_circuit_level=' . db_input($_POST['mpls_fields_secondary_circuit_level']) .
                                ',mpls_fields_secondary_circuit_amount=' . db_input($_POST['mpls_fields_secondary_circuit_amount']) .
                                ',mpls_fields_secondary_circuit_type=' . db_input($_POST['mpls_fields_secondary_circuit_type']) .
                                ',mpls_fields_tertiary_circuit_level=' . db_input($_POST['mpls_fields_tertiary_circuit_level']) .
                                ',mpls_fields_tertiary_circuit_amount=' . db_input($_POST['mpls_fields_tertiary_circuit_amount']) .
                                ',mpls_fields_tertiary_circuit_type=' . db_input($_POST['mpls_fields_tertiary_circuit_type']);
                        break;
                }
                if ($service_name_1 != $service_name_2) {
                    $error .= 'html spoofing protection, ';
                    Sys::log(LOG_ALERT, 'html spoofing attack', 'staff ' . $staff_id . ' tried adding service with incompatible fields, this is probably html spoofing/firebug hack. from ip:' . $ipaddress);
                }

                if (!$error) {
                    if ($_POST['service_id'])
                        $sql = 'UPDATE ' . ADDED_SERVICES_TABLE . ' ' . $sql . ' WHERE client_id=' . db_input($client_id);
                    else
                        $sql = 'INSERT INTO ' . ADDED_SERVICES_TABLE . ' ' . $sql . ',created=NOW()';
                    if (db_query($sql) && db_affected_rows()) {
                        Sys::log(LOG_ALERT, 'service added to client', 'staff ' . $staff_id . ' added service (id: ' . $uID . ' to client: ' . $client_id . ' from ip:' . $ipaddress);
                    }
                    else
                        $error .= ' failed to save ';
                }
                
            } elseif ($_POST['submit'] == 'con_details') {
                $sql = '';
                $local_loop_1 = $_POST['con_details_local_loop'];
                $local_loop_2 = '';

                switch ($_POST['con_details_local_loop']) {
                    case 'nttn':
                        $local_loop_2 = 'nttn';
                        if (!$_POST['con_details_local_loop_nttn_fields_nttn'] ||
                                !$_POST['con_details_nttn_odf_circuit_type']) {
                            $error .= ' fill required fields first ';
                        }
                        $sql .= ' SET updated=NOW() ' .
                                ',con_details_local_loop=' . db_input($_POST['con_details_local_loop']) .
                                ',con_details_local_loop_nttn_fields_nttn=' . db_input($_POST['con_details_local_loop_nttn_fields_nttn']) .
                                ',con_details_nttn_odf_circuit_type=' . db_input($_POST['con_details_nttn_odf_circuit_type']) .
                                ',con_details_local_loop_mixed_fields_nttn=' . db_input('') .
                                ',con_details_local_loop_mixed_fields_nttn_point_a=' . db_input('') .
                                ',con_details_local_loop_mixed_fields_nttn_point_b=' . db_input('') .
                                ',con_details_local_loop_mixed_fields_overhead=' . db_input('') .
                                ',con_details_local_loop_mixed_fields_overhead_point_a=' . db_input('') .
                                ',con_details_local_loop_mixed_fields_overhead_point_b=' . db_input('')
                        ;
                        break;

                    case 'overhead':
                        $local_loop_2 = 'overhead';
                        $sql .= 'SET updated=NOW() ' .
                                ',con_details_local_loop=' . db_input($_POST['con_details_local_loop']);
                        break;

                    case 'mixed':
                        $local_loop_2 = 'mixed';
                        if (!$_POST['con_details_local_loop_mixed_fields_nttn'] ||
                                !$_POST['con_details_local_loop_mixed_fields_nttn_point_a'] ||
                                !$_POST['con_details_local_loop_mixed_fields_nttn_point_b'] ||
                                !$_POST['con_details_local_loop_mixed_fields_overhead'] ||
                                !$_POST['con_details_local_loop_mixed_fields_overhead_point_a'] ||
                                !$_POST['con_details_local_loop_mixed_fields_overhead_point_b']) {
                            $error .= ' fill required fields first ';
                        }
                        $sql .= 'SET updated=NOW() ' .
                                ',con_details_local_loop=' . db_input($_POST['con_details_local_loop']) .
                                ',con_details_local_loop_nttn_fields_nttn=' . db_input('') .
                                ',con_details_nttn_odf_circuit_type=' . db_input('') .
                                ',con_details_local_loop_mixed_fields_nttn=' . db_input($_POST['con_details_local_loop_mixed_fields_nttn']) .
                                ',con_details_local_loop_mixed_fields_nttn_point_a=' . db_input($_POST['con_details_local_loop_mixed_fields_nttn_point_a']) .
                                ',con_details_local_loop_mixed_fields_nttn_point_b=' . db_input($_POST['con_details_local_loop_mixed_fields_nttn_point_b']) .
                                ',con_details_local_loop_mixed_fields_overhead=' . db_input($_POST['con_details_local_loop_mixed_fields_overhead']) .
                                ',con_details_local_loop_mixed_fields_overhead_point_a=' . db_input($_POST['con_details_local_loop_mixed_fields_overhead_point_a']) .
                                ',con_details_local_loop_mixed_fields_overhead_point_b=' . db_input($_POST['con_details_local_loop_mixed_fields_overhead_point_b']);
                        break;
                }

                if (!$local_loop_1) {
                    $error .= ' select local loop ';
                } elseif ($local_loop_1 != $local_loop_2) {
                    $error .= ' spoofing protection, ';
                    Sys::log(LOG_ALERT, 'html spoofing attack', 'staff ' . $staff_id . ' tried adding service with incompatible fields, this is probably html spoofing/firebug hack. from ip:' . $ipaddress);
                }

                if (!$_POST['interface_type_router'] && !$_POST['interface_type_mux'] && !$_POST['interface_type_mix']) {
                    $error .= ' select interface type ';
                }

                if (!$error && $_POST['interface_type_router']) {
                    $sql .= ',interface_type_router=' . db_input($_POST['interface_type_router']) .
                            ',interface_router_name=' . db_input($_POST['interface_router_name']) .
                            ',interface_router_port=' . db_input($_POST['interface_router_port']);
                } else {
                    $sql .= ',interface_type_router=' . db_input('') .
                            ',interface_router_name=' . db_input('') .
                            ',interface_router_port=' . db_input('');
                }

                if (!$error && $_POST['interface_type_mux']) {
                    $sql .= ',interface_type_mux=' . db_input($_POST['interface_type_mux']) .
                            ',interface_mux_name=' . db_input($_POST['interface_mux_name']) .
                            ',interface_mux_port=' . db_input($_POST['interface_mux_port']);
                } else {
                    $sql .= ',interface_type_mux=' . db_input('') .
                            ',interface_mux_name=' . db_input('') .
                            ',interface_mux_port=' . db_input('');
                }

                if (!$error && $_POST['interface_type_mix']) {
                    $sql .= ',interface_type_mix=' . db_input($_POST['interface_type_mix']) .
                            ',interface_mixed_router_name=' . db_input($_POST['interface_mixed_router_name']) .
                            ',interface_mixed_router_port=' . db_input($_POST['interface_mixed_router_port']) .
                            ',interface_mixed_mux_name=' . db_input($_POST['interface_mixed_mux_name']) .
                            ',interface_mixed_mux_port=' . db_input($_POST['interface_mixed_mux_port']);
                } else {
                    $sql .= ',interface_type_mix=' . db_input('') .
                            ',interface_mixed_router_name=' . db_input('') .
                            ',interface_mixed_router_port=' . db_input('') .
                            ',interface_mixed_mux_name=' . db_input('') .
                            ',interface_mixed_mux_port=' . db_input('');
                }

                if (!$error) {
                    if (!Services::load($client_id, 1)) {
                        $error .= ' fill previous fields ';
                        Sys::log(LOG_ALERT, 'possible html spoofing', 'staff ' . $staff_id . ' tried adding service with incompatible fields, this is probably html spoofing. from ip:' . $ipaddress);
                    }
                    $sql = 'UPDATE ' . ADDED_SERVICES_TABLE . ' ' . $sql . ' WHERE client_id=' . db_input($client_id);
                    if (db_query($sql) && db_affected_rows()) {
                        Sys::log(LOG_ALERT, 'service added to client', 'service added by staff:' . $staff_id . ' service id:' . $uID . ' client id: ' . $client_id . ' from ip:' . $ipaddress);
                    } else {
                        $error .= ' failed to save ';
                    }
                }
            } elseif ($_POST['submit'] == 'con_dates') {
                if (!$_POST['link_act_date'] ||
                        !$_POST['test_alloc_from'] ||
                        !$_POST['test_alloc_to'] ||
                        !$_POST['billing_statement_date']) {
                    $error .= ' fill required fields ';
                }

                $sql = '';
                $sql = ' SET updated=NOW() ' .
                        ',link_act_date=' . db_input($_POST['link_act_date']) .
                        ',test_alloc_from=' . db_input($_POST['test_alloc_from']) .
                        ',test_alloc_to=' . db_input($_POST['test_alloc_to']) .
                        ',billing_statement_date=' . db_input($_POST['billing_statement_date']) .
                        ',con_details_remarks=' . db_input($_POST['con_details_remarks']);
                if (!$error) {
                    $sql = 'UPDATE ' . ADDED_SERVICES_TABLE . ' ' . $sql . ' WHERE client_id=' . db_input($client_id);
                    if (db_query($sql) && db_affected_rows()) {
                        Sys::log(LOG_ALERT, 'service added to client', 'service added by staff:' . $staff_id . ' service id:' . $uID . ' client id: ' . $client_id . ' from ip:' . $ipaddress);
                    }
                    else
                        $error .= ' failed to save ';
                }
            }
        }
        if (!$error)
            return 1;
        else
            return $error;
    }

}

?>