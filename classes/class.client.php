<?php

/* * *******************************************************************
  new class.client.php

  Everything about client.


 * ******************************************************************** */

class Client {

    var $db_array;

    function Client($search, $static_call = 0, $field_name='id') {
        $this->client_id = 0;
        return ($this->lookup( $search, $static_call, $field_name ));
    }

    function lookup($search, $static_call = 0, $field_name='id') {
        switch ( $field_name ) {
            case 'id':
                $sql = 'SELECT * FROM ' . CLIENT_TABLE . ' WHERE client_id=' . db_input($search);
                break;
            case 'username':
            case 'uname':
                $sql = 'SELECT * FROM ' . CLIENT_TABLE . ' WHERE username=' . db_input($search);
                break;
        }

        $res = db_query($sql);

        if (!$res || !db_num_rows($res)) {
            return NULL;
        }

        if ($static_call) {
            if (!$res || !db_num_rows($res))
                return false;
            elseif ($row = db_fetch_array($res))
                return $row;
            else
                return false;
        }

            $row = db_fetch_array($res);
            $this->db_array = $row;
    }


    function get_all_clients() {
        $sql = 'SELECT * FROM ' . CLIENT_TABLE .' ORDER BY client_name ASC';
        if ( $res = db_query($sql) ) {
            $rows = db_assoc_array($res);
            return $rows;
        } else {
            return array();
        }
    }


    function get_clients_of_type($client_type) {
        $sql = 'SELECT * FROM ' . CLIENT_TABLE . ' WHERE client_of LIKE ' . db_input('%'.$client_type.'%');
        if ( $res = db_query($sql) ) {
            $rows = db_assoc_array($res);
            return $rows;
        } else {
            return false;
        }
    }

    //to check if the inputed client name is unique
    function is_uniq_name($name) {
        if ( Client::lookup($name, 1, 'uname') ) {
            return false;
        } else {
            return true;
        }
    }


    //$service string
    function has_service($service) {
        $sql = 'SELECT * FROM '.SERVICE_CIN_TABLE.' WHERE client_id='.db_input($this->getId()).' AND service_type='.db_input($service);
        if($res=db_query($sql)) {
            if(db_num_rows($res)) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    //$cin should be an array
    function remove_service_except($client_id, $service) {
        if ( count($service)>1 ) {
            $service = explode(',', $service);
            $sql = 'DELETE FROM ' . SERVICE_CIN_TABLE . ' WHERE client_id=' . db_input($client_id) . ' AND service_type NOT IN ( ' . db_input($service) . ' )';
        } else {
            $sql = 'DELETE FROM ' . SERVICE_CIN_TABLE . ' WHERE client_id=' . db_input($client_id);
        }
        if ( $res = db_query($sql) ) {
            return TRUE;
        } else {
            mysql_query($sql) or die(mysql_error());
            return FALSE;
        }
    }

    function reload() {
        $this->lookup($this->getId());
    }


    function isClient() {
        return true;
    }
    function isStaff() {
        return FALSE;
    }

    function isSCPStaff() {
        return false;
    }

    function getPermission() {
        return $this->permission;
    }


    function isClientAdmin() {
        return true;
    }

    function canCreateOrder() {
        if ( $this->isClientAdmin() ) {
            return true;
        } else {
            return false;
        }
    }

    function canManageUsers() {
        if ($this->isClientAdmin()) return true;
        else return false;
    }

    function onlyView() {
        if ($this->permission == 'view_only')
            return true;
        else
            return false;
    }

    function onlyMonitor() {
        if ($this->permission == 'monitor')
            return true;
        else
            return false;
    }

    function onlyAccounts() {
        if ($this->permission == 'accounts')
            return true;
        else
            return false;
    }

    function getInfo() {
        return $this->db_array;
    }

    /* compares user password */

    //TODO: MD5 is not much secured, make it SHA1
    function check_passwd($password) {
        return (strlen($this->db_array['password']) && strcmp($this->db_array['password'], MD5($password)) == 0) ? (TRUE) : (FALSE);
    }

    function getTZoffset() {
        global $cfg;

        $offset = $this->db_array['timezone_offset'];
        return $offset ? $offset : $cfg->getTZoffset();
    }

    function observeDaylight() {
        return $this->db_array['daylight_saving'] ? true : false;
    }

    function getRefreshRate() {
        return $this->db_array['auto_refresh_rate'];
    }

    function getPageLimit() {
        global $cfg;
        $limit = $this->db_array['max_page_size'];
        return $limit ? $limit : $cfg->getPageSize();
    }

    function get_db_id() {
        return $this->db_array['id'];
    }

    function get_login_name() {
        return $this->db_array['username'];
    }

    function getId() {
        return $this->db_array['client_id'];
    }

    function getBossId() {
        if ($this->db_array['boss_id'] != $this->getId())
            return $this->db_array['boss_id'];
        else
            return $this->getId();
    }

    function getEmail() {
        return($this->db_array['email']);
    }

    function getUserName() {
        return($this->db_array['username']);
    }

    function getName() {
        return($this->db_array['client_name']);
    }

    function getEmployeeName() {
        return $this->db_array['client_org_name'];
    }

    function getPhone() {
        return $this->db_array['phone'];
    }

    function getMobile() {
        return $this->db_array['single_point_phone'];
    }

    function getDeptId() {
        return $this->dept_id;
    }

    function getGroupId() {
        return $this->group_id;
    }

    function getSignature() {
        return($this->signature);
    }

    function appendMySignature() {
        return $this->signature ? true : false;
    }

    function forcepasswordChange() {
        return $this->db_array['change_password'] ? true : false;
    }

    function getDepts() {
        //Departments the user is allowed to access...based on the group they belong to + user's dept.
        return array_filter(array_unique(array_merge(explode(',', $this->db_array['dept_access']), array($this->dept_id)))); //Neptune help us
    }

    function getDept() {

        if (!$this->dept && $this->dept_id)
            $this->dept = new Dept($this->dept_id);

        return $this->dept;
    }

    function isactive() {
        return ($this->db_array['isactive']) ? true : false;
    }

    function isVisible() {
        return ($this->db_array['isvisible']) ? true : false;
    }

    function onVacation() {
        return ($this->db_array['onvacation']) ? true : false;
    }

    function isAvailable() {
        return (!$this->isactive() || !$this->isGroupActive() || $this->onVacation()) ? false : true;
    }

    /* canDos' logic explained
      1) First check id the user is super admin...if yes...super..allow
      2) Check if the user is allowed to do the Do...or a manager in some cases -- if yes...allow
      3) Check if he user's group is allowed...if yes...allow
      5) If I-2-3 fails...it is a NO.. you can cry yourself to sleep.
     */

    function canAccessDept($deptid) {
        return ($this->isadmin() || in_array($deptid, $this->getDepts())) ? true : false;
    }

    function canCreateTickets() {
        return ($this->isadmin() || $this->db_array['can_create_tickets']) ? true : false;
    }

    function canEditTickets() {
        return ($this->isadmin() || $this->db_array['can_edit_tickets']) ? true : false;
    }

    function canDeleteTickets() {
        return ($this->isadmin() || $this->db_array['can_delete_tickets']) ? true : false;
    }

    function canCloseTickets() {
        return ($this->isadmin() || $this->db_array['can_close_tickets']) ? true : false;
    }

    function canTransferTickets() {
        return ($this->isadmin() || $this->isManager() || $this->db_array['can_transfer_tickets']) ? true : false;
    }

    function canManageBanList() {
        return ($this->isadmin() || $this->isManager() || $this->db_array['can_ban_emails']) ? true : false;
    }

    function canManageTickets() {
        return ($this->isadmin()
                || $this->canDeleteTickets()
                || $this->canManageBanList()
                || $this->canCloseTickets()) ? true : false;
    }

    function canManageKb() { //kb = knowledge base.
        return ($this->isadmin() || $this->db_array['can_manage_kb']) ? true : false;
    }


    function get_all_staff() {
        $sql = 'SELECT * FROM ' . CLIENT_STAFF_TABLE . ' WHERE client_id=' . db_input($this->getId());
        $res = db_query($sql);

        $num_of_users = db_num_rows($res);
        if ( $num_of_users ) {
            for ( $i = 0; $i < $num_of_users; $i++ ) {
                $rows[$i] = db_fetch_array($res);
            }

            return $rows;
        } else {
            return false;
        }
    }

    function get_staff($id) {
        $sql = 'SELECT * FROM ' . CLIENT_STAFF_TABLE . ' WHERE id=' . db_input($id);
        $res = db_query($sql);
        if ( db_num_rows($res) ) {
            return db_fetch_array($res);
        } else {
            return false;
        }
    }

    //to check if an email address is already for a staff under the specified boss
    function isStaffEmail($boss_id, $email) {
        $sql = 'SELECT * FROM ' . CLIENT_TABLE . ' WHERE staff_email='.db_input($email);
        $res = db_query($sql);
        if ( db_num_rows($res) ) {
            return true;
        } else {
            //mysql_query($sql) or die(mysql_error());
            return false;
        }
    }

    function update($vars, &$errors) {
        if ($this->save($this->getId(), $vars, $errors)) {
            $this->reload();
            return true;
        }
        return false;
    }

    function create($vars, &$errors) {
        global $thisuser;
        if ($thisuser->isClient() && !$thisuser->canManageUsers()) {
            $errors['err'] = 'Error. You have no permission for this action.';
            return false;
        }
        if ($res = self::lookup($vars['client_id'], 1)) {
            $errors[err] = 'user already in database with id: '. $vars['client_id'];
            return false;
        }
        return Client::save(0, $vars, $errors);
    }

    function gen_unique_id() {
        require_once(CLASS_DIR . 'class.sys.php');
        if (!Client::lookup($id = Sys::unique_id(), 1))
            return $id;
        else
            Client::gen_unique_id();
    }

    function save($id, $vars, &$errors, $boss_id = 0) {
        global $thisuser;

        //checking the uniqueness of the client name, this will be login
        if ( !$id && !Client::is_uniq_name($vars['client_name']) ) {
            $errors['err'] .= 'please select a unique name, current name already in the database';
            return false;
        }

        /*
        if ($thisuser->isClient() && !$thisuser->canManageUsers()) {

            $errors['err'] .= 'Error. You have no permission for this action.';
            return false;
        }
        */

        include_once(INCLUDE_DIR . 'class.dept.php');

        /*
        $fields = array();
        $fields['client_name'] = array('type'=>'string','required'=>1,'error'=>'login name required');
        $fields['client_company_name'] = array('type'=>'string','required'=>1,'error'=>'client company name required');
        $fields['client_type'] = array('type'=>'string','required'=>1,'error'=>'client type required');
        $fields['client_type_other'] = array('type'=>'string','required'=>0,'error'=>'*');
        $fields['single_point_email'] = array('type'=>'email','required'=>1,'error'=>' single point email address required');
        $fields['single_point_phone'] = array('type'=>'string','required'=>1,'error'=>'single point phone number required');
        if (!$id) {
            $fields['client_password'] = array('type'=>'password','required'=>1,'error'=>'password required');
            $fields['client_password_again'] = array('type'=>'password','required'=>1,'error'=>'re enter the password');
        } else {
            $fields['client_password'] = array('type'=>'password','required'=>0,'error'=>'*');
            $fields['client_password_again'] = array('type'=>'password','required'=>0,'error'=>'*');
        }
        $fields['client_org_name'] = array('type'=>'string','required'=>1,'error'=>'organogram name required');
        $fields['client_org_email'] = array('type'=>'email','required'=>1,'error'=>'organogram email required');
        $fields['client_org_asn'] = array('type'=>'string','required'=>0,'error'=>'*');
        $fields['client_org_designation'] = array('type'=>'string','required'=>1,'error'=>'organogram designation required');
        $fields['client_org_department'] = array('type'=>'string','required'=>1,'error'=>'organogram department required');

        $validate = new Validator($fields);
        if (!$validate->validate($vars)) {
            $errors = array_merge($errors, $validate->errors());
            //  echo   'error in validation';
        }
        */
        //Any error above is fatal.
        if ($errors) {
            return 0;
        }


        $ipaddress = $var['ip'] ? $var['ip'] : $_SERVER['REMOTE_ADDR'];
        $new_user_id = Sys::unique_id();

        //transform client type array to string
        $client_type = implode(',', $vars['client_type']);

        if (!$errors) {
            $sql = ' SET updated=NOW() ' .
                    ',client_name=' . db_input($vars['client_name']) .
                    ',username=' . db_input($vars['username']) .
                    ',client_of=' . db_input($vars['client_of']) .
                    ',client_type=' . db_input($vars['client_type']) .
                    ',other_type=' . db_input($vars['other_type']) .
                    ',email=' . db_input($vars['email']) .
                    ',phone=' . db_input($vars['phone']) .
                    ',client_asn=' . db_input($vars['client_asn']);


            if ( $vars['password'] && ( strcmp($vars['password'], $vars['password_again']) ) ) {
                $errors['password'] = 'Password(s) do not match';
            }

            if ( $vars['password'] && ( strcmp($vars['password'], $vars['password_again']) == 0 ) ) {
                $sql.=',password=' . db_input(md5($vars['password']));
            }

            if ($id) { // if id presents, means update
                $sql = 'UPDATE ' . CLIENT_TABLE . ' ' . $sql . ' WHERE client_id=' . db_input($id);
                if (!db_query($sql) || !db_affected_rows()) {
                    $errors['err'] .= 'Unable to update the user. db query failure.'.$sql;
                }
            } else { // new user
                $new_user_id = Sys::unique_id();
                $sql = 'INSERT INTO ' . CLIENT_TABLE . ' ' . $sql . ' ,client_id=' . db_input($new_user_id) . ',created=NOW()';
                if (db_query($sql)) {
                    return $new_user_id;
                } else {
                    $errors['err'] .= 'Unable to create user. db query failure. ';
                }
            }
        }
        return $errors ? false : true;
    }


    function save_cin($vars, &$errors) {
        if ( $vars['client_id'] ) {
            $id = $vars['client_id'];
        } else {
            $id = 0;
        }
        if ( $id ) {
            //first delete all cin
            $sql_del = 'DELETE FROM ' . SERVICE_CIN_TABLE . ' WHERE client_id=' . db_input($id);
            if ( !db_query($sql_del) ) {
                $errors['err'] .= ' oh man! cin cleansing failure b4 saving!! do something!!! ';
            } else { //onlt after clearing
                foreach( $vars['cin_no'] as $key=>$cin ) {
                    //upload image
                    $data = array(
                        'field_name'=>'ckt_diag',
                        'index'=>$key,
                        'client_id'=>$vars['client_id'],
                        'service_type'=>$vars['service_type'][$key],
                        'circuit_type'=>$vars['circuit_type'][$key],
                        'cin'=>$vars['cin_no'][$key]
                    );
                    if ( $_FILES['ckt_diag']['name'][$key] && ( $new_name=Client::upload_ckt_diag($data, $errors) ) ) {
                        $ckt_img_name = $new_name;
                        $sql = 'INSERT INTO ' . SERVICE_CIN_TABLE . ' SET client_id=' . db_input($id) . ',service_type=' . db_input($vars['service_type'][$key]) . ',circuit_type=' . db_input($vars['circuit_type'][$key]) . ',cin=' . db_input($vars['cin_no'][$key]) . ',ckt_diag=' . db_input($ckt_img_name) . ',client_name=' . db_input($vars['client_name']) . ',from_location=' . db_input($vars['from'][$key]) . ',to_location=' . db_input($vars['to'][$key]) . ',link_details=' . db_input($vars['link_details'][$key]) . ',bw_speed_cir=' . db_input($vars['bw_speed_cir'][$key]) . ',max_burstable_limit=' . db_input($vars['max_burstable_limit'][$key]);
                    } else {
                        $sql = 'INSERT INTO ' . SERVICE_CIN_TABLE . ' SET client_id=' . db_input($id) . ',service_type=' . db_input($vars['service_type'][$key]) . ',circuit_type=' . db_input($vars['circuit_type'][$key]) . ',cin=' . db_input($vars['cin_no'][$key]) . ',ckt_diag=' . db_input($vars['ckt_diag'][$key]) . ',client_name=' . db_input($vars['client_name']) . ',from_location=' . db_input($vars['from'][$key]) . ',to_location=' . db_input($vars['to'][$key]) . ',link_details=' . db_input($vars['link_details'][$key]) . ',bw_speed_cir=' . db_input($vars['bw_speed_cir'][$key]) . ',max_burstable_limit=' . db_input($vars['max_burstable_limit'][$key]);
                    }

                    if ( $cin ) { //save only if cin number present
                        if ( !db_query($sql) ) {
                            $errors['err'] .= ' cin:' . $vars['cin_no'][$key] . ' save fail! ';
                        }
                    }
                }
            }
        }
    }

    //get all cin
    function get_all_cin($client_id) {
        $sql = 'SELECT * FROM ' . SERVICE_CIN_TABLE . ' WHERE client_id=' . db_input($client_id);
        if ( $res = db_query($sql) ) {
            $result = db_assoc_array($res);
            return $result;
        } else {
            return array();
        }
    }

    //build cin array from post data
    function build_service_array($vars) {
        $services = array();
        if ( $vars['service_type'] && $vars['cin_no'] ) {
            foreach( $vars['service_type'] as $index=>$val ) {
                $services['service_type'] = $val;
                $services['cin'] = $vars['cin_no'][$index];
            }

        }
        return $services;
    }

    //$data is an array
    /*
        array(
            'field_name'=>
            'index'=>
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
        $index = $data['index'];

        if (isset($_FILES[$field_name]['tmp_name'][$index])) {
            $file_ext = pathinfo(basename($_FILES[$field_name]['name'][$index]), PATHINFO_EXTENSION);
            $new_filename = $data['client_id'] . '_' . $date . '.' . $file_ext;
            $new_file_path = $uploaddir . '/' . $new_filename;

            if (!is_writable($uploaddir)) {
                $err = 'upload directory is not writable ';
            }

            if (!$err && $_FILES[$field_name]['error'][$index] != UPLOAD_ERR_OK) {
                $err = "error! try again ";
            }

            if (!$err && $_FILES[$field_name]['size'][$index] > $max_size) {
                $err = " filesize exceeds allowed limit " . ($max_size / 1024) . " KiloBytes";
            }

            if (!$err && !move_uploaded_file($_FILES[$field_name]['tmp_name'][$index], $new_file_path)) {
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


    function save_staff($vars, &$errors) {
        $sql = ' SET client_id=' . db_input($vars['client_id']) .
                    ',staff_name=' . db_input($vars['staff_name']) .
                    ',email=' . db_input($vars['email']) .
                    ',phone=' . db_input($vars['phone']) .
                    ',designation=' . db_input($vars['designation']) .
                    ',department=' . db_input($vars['department']);

        if ($vars['staff_id']) { // if id presents, means update
            $sql = 'UPDATE ' . CLIENT_STAFF_TABLE . ' ' . $sql . ' WHERE id=' . db_input($vars['staff_id']);
            if (db_query($sql) && db_affected_rows()) {
                return true;
            } else {
                $errors['err'] .= 'Unable to update the user. db query failure.';
                return false;
            }
        } else { // new user
            $sql = 'INSERT INTO ' . CLIENT_STAFF_TABLE . ' ' . $sql;
            if (db_query($sql)) {
                return db_insert_id();
            } else {
                $errors['err'] .= 'Unable to create staff. db query failure. ' . $sql;
                return false;
            }
        }
    }

    //$client can be array of client id or a single id
    function delete_client($client, &$errors) {
        if ( is_array($client) && count($client) ) {
            $i = 0;
            foreach( $client as $c ) {
                $sql_1 = 'DELETE FROM ' . CLIENT_TABLE . ' WHERE client_id=' . db_input($c);
                $sql_2 = 'DELETE FROM ' . SERVICE_CIN_TABLE . ' WHERE client_id=' . db_input($c);
                $sql_3 = 'DELETE FROM ' . CLIENT_STAFF_TABLE . ' WHERE client_id=' . db_input($c);
                if ( !db_query($sql_1) ) {
                    $i++;
                } else {
                    db_query($sql_2);
                    db_query($sql_3);
                }
            }
            if ($i) {
                $errors['err'] = $i . ' client remove failure ';
            }
            if ( $i == count($client) ) {
                return false;
            } else {
                return true;
            }
        } else {
            $sql_1 = 'DELETE FROM ' . CLIENT_TABLE . ' WHERE client_id=' . db_input($client);
            $sql_2 = 'DELETE FROM ' . SERVICE_CIN_TABLE . ' WHERE client_id=' . db_input($client);
            $sql_3 = 'DELETE FROM ' . CLIENT_STAFF_TABLE . ' WHERE client_id=' . db_input($client);
            if ( !db_query($sql_1) ) {
                return false;
            } else {
                db_query($sql_2);
                db_query($sql_3);
                return true;
            }
        }
    }

    //$ids is the array or single string of staff id
    function delete_staff($ids, &$errors) {
        if ( is_array($ids) && count($ids) ) {
            $i = 0;
            foreach( $ids as $id ) {
                $sql = 'DELETE FROM ' . CLIENT_STAFF_TABLE . ' WHERE id=' . db_input($id);
                if ( !db_query($sql) ) {
                    $i++;
                }
            }
            if ($i) {
                $errors['err'] = $i . ' staff remove failure';
            }
            if ( $i == count($ids) ) {
                return false;
            } else {
                return true;
            }
        } else {
            $sql = 'DELETE FROM ' . CLIENT_STAFF_TABLE . ' WHERE id=' . db_input($ids);
            if ( !db_query($sql) ) {
                return false;
            } else {
                return true;
            }
        }
    }

}

?>
