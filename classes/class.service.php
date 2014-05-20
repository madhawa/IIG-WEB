<?php

class Service {
    function __construct() {

    }

    function get_all_services() {
        $sql = 'SELECT * FROM ' . SERVICE_CIN_TABLE;
        if ( $res = db_query($sql) ) {
            $result = db_assoc_array($res);
            return $result;
        } else {
            return false;
        }
    }

    function get_service($id) {
        $sql = 'SELECT * FROM ' . SERVICE_CIN_TABLE . ' WHERE id=' . db_input($id);
        if ( $res = db_query($sql) ) {
            $result = db_fetch_array($res);
            return $result;
        } else {
            return false;
        }
    }

    function get_clients_services($client_id) {
        $sql = 'SELECT * FROM ' . SERVICE_CIN_TABLE . ' WHERE client_id=' . db_input($client_id);
        if ( $res = db_query($sql) ) {
            $result = db_assoc_array($res);
            return $result;
        } else {
            return false;
        }
    }

    function save_service($vars) {
        $id = $vars['service_id'];
        $client_id = $vars['client_id'];

        $data = array(
                    'field_name'=>'ckt_diag',
                    'client_id'=>$vars['client_id'],
                    'service_type'=>$vars['service_type'],
                    'circuit_type'=>$vars['circuit_type'],
                    'cin'=>$vars['cin_no']
                    );
        if ( $_FILES['ckt_diag']['name'] && ( $new_name=Client::upload_ckt_diag($data, $errors) ) ) {
            $ckt_img_name = $new_name;
        } else {
            $ckt_img_name = '';
        }

        $sql = ' SET ' .
        'client_id=' . db_input($id) .
        ',service_type=' . db_input($vars['service_type']) .
        ',circuit_type=' . db_input($vars['circuit_type']) .
        ',cin=' . db_input($vars['cin_no']) .
        ',ckt_diag=' . db_input($ckt_img_name) .
        ',client_name=' . db_input($vars['client_name']) .
        ',from_location=' . db_input($vars['from']) .
        ',to_location=' . db_input($vars['to']) .
        ',link_details=' . db_input($vars['link_details']) .
        ',bw_speed_cir=' . db_input($vars['bw_speed_cir']) .
        ',max_burstable_limit=' . db_input($vars['max_burstable_limit']);

        if ( $id ) {
            $sql = 'UPDATE ' . SERVICE_CIN_TABLE . $sql . ' WHERE id='.db_input($id);
        } else {
            $sql = 'INSERT INTO ' . SERVICE_CIN_TABLE . $sql;
        }

        if ( !db_query($sql) ) {
            $errors['err'] .= ' save fail! ';
        } else {
            return true;
        }

    }


    //$data is an array
    /*
        array(
            'field_name'=>
            'client_id'=>
            'service_type'=>
            'circuit_type'=>
            'cin'=>
        )
    */
    function upload_ckt_diag($data, &$errors) {
        $err = '';
        $date = date('Y-m-d@H-i-s');
        $uploaddir = UPLOAD_DIR . 'ckt_diag';
        require_once(SCP_DIR . 'misc.php');
        $max_size = get_ini_limit();

        $field_name = $data['field_name'];

        if (isset($_FILES[$field_name]['tmp_name'])) {
            $file_ext = pathinfo(basename($_FILES[$field_name]['name']), PATHINFO_EXTENSION);
            $new_filename = $data['client_id'] . '_' . $date . '.' . $file_ext;
            $new_file_path = $uploaddir . '/' . $new_filename;

            if (!is_writable($uploaddir)) {
                $err = 'upload directory is not writable ';
            }

            if (!$err && $_FILES[$field_name]['error'] != UPLOAD_ERR_OK) {
                $err = "error! try again ";
            }

            if (!$err && $_FILES[$field_name]['size'] > $max_size) {
                $err = " filesize exceeds allowed limit " . ($max_size / 1024) . " KiloBytes";
            }

            if (!$err && !move_uploaded_file($_FILES[$field_name]['tmp_name'], $new_file_path)) {
                $err = "bad file upload!";
            }
            if ( !$err ) {
                return $new_filename;
            } else {
                $errors['err'] .= $err;
                return false;
            }
        } else {
            return false;
        }
    }
}

?>