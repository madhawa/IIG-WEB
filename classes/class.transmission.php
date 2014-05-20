<?php

class Transmission {
    var $db_array;
    var $trans_data;
    
    function Transmission($field_val, $field_name) {
        return $this->lookup($field_val, $field_name);
    }
    
    //typically field name can be client_id or client_name
    function lookup($field_val, $field_name) {
        $sql = 'SELECT * FROM ' . TRANSMISSION_TABLE . ' WHERE ' . $field_name . '=' . db_input($field_val);
        $res = db_query($sql);
        $data = db_fetch_array($res);
        
        $this->db_array = $data;
        $trans_data = $data['transmission_data'];
        $this->trans_data = $trans_data;
        
        return $this->db_array;
    }
    
    function reload() {
        $this->lookup('client_id' ,$this->getClientId());
    }
    
    function getInfo() {
        return $this->db_array;
    }
    
    function getTransmissionData() {
        return $this->trans_data;
    }
    
    function getId() {
        return $this->db_array['id'];
    }
    
    function getClientId() {
        return $this->db_array['client_id'];
    }
    
    function getClientName() {
        return $this->db_array['client_name'];
    }
    
    function update($vars, &$errors) {
        $client_id = $this->getClientId();
        if ($this->save($vars, $errors)) {
            $this->reload();
            return true;
        }
        return false;
    }
    
    function save($vars, &$errors) {
        global $thisuser;
        
        $client_id = $vars['client_id'];
        $client_name = $vars['client_name'];
        
        /*
        $fields = array();
        $fields['select_client'] = array('type'=>'string','required'=>1,'error'=>'client required');
        $fields['service_level'] = array('type'=>'string','required'=>1,'error'=>'select service level');
        
        $fields['protection_status'] = array('type'=>'string','required'=>1,'error'=>'select transmission protection ststus');
        $fields['protection_status_confirm'] = array('type'=>'string','required'=>0,'error'=>'confirm transmission protection status');
        $fields['protection_status_type'] = array('type'=>'string','required'=>0,'error'=>'select transmission protection status type');
        
        $fields['link_cap'] = array('type'=>'string','required'=>1,'error'=>'link capacity value empty');
        
        $fields['spf_info'] = array('type'=>'string','required'=>1,'error'=>'spf infomation field empty');
        $fields['spf_info_mode'] = array('type'=>'string','required'=>0,'error'=>'select spf mode');
        
        $fields['name_of_nttn'] = array('type'=>'string','required'=>1,'error'=>'nttn name empty');
        $fields['path_distance'] = array('type'=>'string','required'=>1,'error'=>'path distance field empty');
        $fields['path_loss'] = array('type'=>'string','required'=>1,'error'=>'path loss field empty');
        $fields['poc_start'] = array('type'=>'string','required'=>1,'error'=>'poc in start field empty');
        $fields['poc_end'] = array('type'=>'string','required'=>1,'error'=>'poc in end field empty');
        
        $fields['odf_port_info'] = array('type'=>'string', 'required'=>1, 'error'=>'');
        
        $fields['client_end_tx'] = array('type'=>'string','required'=>1,'error'=>'client end tx field empty');
        $fields['client_end_rx'] = array('type'=>'string','required'=>1,'error'=>'client end rx field empty');
        
        $fields['1asia_end_tx'] = array('type'=>'string','required'=>1,'error'=>'1asia end tx field empty');
        $fields['1asia_end_rx'] = array('type'=>'string','required'=>1,'error'=>'1asia end rx field empty');
        
        $fields['link_status'] = array('type'=>'string','required'=>1,'error'=>'select current link status');
        
        $validate = new Validator($fields);
        if (!$validate->validate($vars)) {
            $errors = array_merge($errors, $validate->errors());
        }
        */
        
        if ($errors) {
            //$errors['err'] .= ' error in validate ';
            return 0;
        }
        
        //filter data
        $vars = Format::strip_slashes($vars);
        
        //now convert all data into json
        $data = json_encode($vars);
        
        //TODO: check for dependent fields
        
        if ( !$errors ) {
            $sql = ' SET updated=NOW() ' .
                    ',client_id=' . db_input($vars['select_client']).
                    ',client_name=' . db_input($vars['client_name']).
                    ',transmission_data=' . db_input($data);
        }
        
        if ( $client_id ) {
            $sql = 'UPDATE ' . TRANSMISSION_TABLE . $sql . ' WHERE client_id=' . db_input($client_id);
        } else {
            $sql = 'INSERT INTO ' . TRANSMISSION_TABLE . $sql . ',created=NOW()';
        }
        
        if( db_query($sql) ) {
            return true;
        } else {
            mysql_query($sql) or die(mysql_error());
            $errors['err'] = ' database query failure ';
        }
        
        return $errors?false:true;
    }
}



?>