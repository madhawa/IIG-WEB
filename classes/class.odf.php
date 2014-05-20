<?php
/*
    odf class
*/

Class Odf {
    
    var $id;
    var $db_array;
    
    function Odf($name) {
        return $this->lookup($name);
    }
    
    function lookup($name, $static_call=false) {
        $sql = 'SELECT * FROM ' . ODF_TABLE . ' WHERE odf_name='.db_input($name);
        $res = db_query($sql);
        
        if (!$res || !db_num_rows($res)) {
            return NULL;
        }
        if ( $static_call==true ) {
            return true;
        }
        $row = db_fetch_array($res);
        $this->db_array = $row;
        return true;
    }
    
    function getOdfObj() {
        return $this->db_array['odf_json_obj'];
    }
    
    
    function getClients() {
        return $this->db_array['clients'];
    }
    
    
    function update($vars, $id) {
        return Odf::save($vars, $id);
    }
    
    //this function will take asociative array or json string
    //ODF structure is defined in scp/js/ODF struture.docx
    function test_valid_odf($odf) { //checks for valid odf structure
        if ( is_array($odf) ) {
            if ( count($odf) >= 1 ) {
                foreach ( $odf as $odf_name => $data ) {
                    if ( !$data['div_id'] ) {
                        $data['div_id'] = uniqid('div_id_');
                    }
                    if ( is_array($data['data']) ) {
                        
                        foreach ( $data['data'] as $tray => $port ) {
                            if ( is_array( $port ) ) {
                                return true;
                            } else {
                                unset($port);
                                return true;
                            }
                        }
                        
                    } else {
                        return false;
                    }
                }
            } else {
                return false;
            }
        } elseif ( is_string($odf) ) { //probably json string
            $odf = json_decode($odf, TRUE);
            if ( json_last_error() == JSON_ERROR_NONE ) {
                Odf::test_valid_odf( $odf );
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    function create($vars) {
        return Odf::save($vars, false);
    }
    
    // $vars should be php associarive array or odf string
    function save($vars, $id=false) {
        $error = '';
        $saved = array();
        if ( is_array($vars) ) {
            $num = count($vars);
            foreach ( $vars as $key => $value ) {
            
                if ( is_array($value) ) {
                    
                    if ( Odf::test_valid_odf(array($key => $value)) ) { // $key is the odf name
                        $json = json_encode(array($key => $value));
                        if ( json_last_error() == JSON_ERROR_NONE ) { // in case
                            if ( Odf::lookup($key, true) ) { // if odf name exists in db
                                $sql = 'UPDATE ' . ODF_TABLE . ' SET updated=NOW(), odf_json_obj='.db_input($json).' WHERE odf_name='.db_input($key);
                            } else {
                                $sql = 'INSERT INTO ' . ODF_TABLE . ' SET created=NOW(),updated=NOW(), odf_json_obj='.db_input($json).',odf_name='.db_input($key);
                            }
                            
                            if ( $res=db_query($sql) && (db_affected_rows() != -1) ) {
                                $saved[] = $key;
                            } else {
                                //  echo   $sql.'<br>';
                                mysql_query($sql) or die(mysql_error());
                                $error = 'ODF: '.$key.' not saved. Error: query failed. query string:'.$sql.', error text:'.db_error();
                            }
                            
                        } else {
                            $error .= ', odf: '.$key . ' not saved. Error is: invalid json';
                        }
                        
                    } else {
                        $error .= ', odf: '.$key . ' not saved. Error is: invalid odf structure';
                    }
                
                } else {
                    $error .= ', ODF: '.$key.' not saved. Error: invalid odf structure';
                }
                
            }
            
        } elseif ( is_string($vars) ) {
            $json = json_decode($vars);
            if ( json_last_error() == JSON_ERROR_NONE ) {
                Odf::save($json); // if valid json then recurson
            } else {
                $error .= ',no odf saved. Error: invalid data sent to server. Data#'.db_output($vars).'#';
            }
        } else { // other data type, not accepted
            $error .= ',no odf saved. Error:invalid data. expected data type is: php assoc array or json string. but found data type is:'. gettype($vars);
        }
        
        
        if ( count($saved) && $error ) {
            return '<span class="success">odf: #'.implode('#', $saved).'# saved</span>'.', <span class="error">but '.$error.'</span>';
        } elseif ( count($saved) && !$error ) {
            return '<span class="success">all odfs saved</span>';
        } elseif ( !count($saved) && $error ) {
            return '<span class="error">Error: '.$error.'</span>';
        } else {
            return '<span class="error">Unknown error</span>';
        }
        
    }
}

?>