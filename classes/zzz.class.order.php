<?php

/* * *******************************************************************
  class.order.php

  Service Order definition class

  minhaj <minhaj@vimmaniac.com>

  Released under the  License WITHOUT ANY WARRANTY.
  See LICENSE.TXT for details.

  vim: expandtab sw=4 ts=4 sts=4:
  $Id: $
 * ******************************************************************** */
include_once(CLASS_DIR . 'class.client.php');
include_once(INCLUDE_DIR . 'class.email.php');
include_once(INCLUDE_DIR . 'class.dept.php');
include_once(INCLUDE_DIR . 'class.topic.php');
include_once(INCLUDE_DIR . 'class.lock.php');
include_once(INCLUDE_DIR . 'class.banlist.php');

class ServiceOrder {

    var $db_array;
    var $staff_id;

    function ServiceOrder($id) {
        $this->load($id);
    }

    /**
     * 
     * 
     * @param <type> $id order_id
     * @param <type> $static_call whether the method is being called statically, without creating an object instance
     * 
     * @return <bool>
     */
    function load($id, $static_call = 0) {
        $sql = sprintf('SELECT * FROM ' . ORDER_TABLE .
                ' WHERE order_id=%s', db_input($id));
        //mysql_query($sql) or die(mysql_error());
        if (($res = db_query($sql)) && db_num_rows($res)) {
            if ($static_call)
                return true;

            $db_array = db_fetch_array($res);
            $this->staff_id = $db_array['assigned_staff_id'];
            $this->db_array = $db_array;

            return true;
        }
        else
            return false;
    }

    function reload() {
        return $this->load($this->getId());
    }

    //GET
    
    /**
     * if the order is accepted by provisioning dept
     * 
     * 
     * @return <type>
     */
    
    function finalized() {
        if ($this->Accepted() && (strtolower($this->getDeptName()) == 'provisioning'))
            return true;
        else
            return false;
    }
    
    function serviceDeliveredDate() {
        if ($this->finalized()) return $this->db_array['updated_date'];
        else return false;
    }
    
    function getId() {
        return $this->db_array['order_id'];
    }

    function getClientId() {
        return $this->db_array['client_id'];
    }

    function getClientRelNumber() {
        return $this->db_array['client_rel_no'];
    }

    //date
    function getCreateDate() {
        return $this->db_array['created_date'];
    }

    //status
    function getStatus() {
        return strtolower($this->db_array['status']);
    }
    
    function Pending() {
        if ($this->getStatus() == 'pending') return true;
        elseif ($this->getStatus() == 'updated') return true;
        else return false;
    }
    /*
    function Accepted() {
        if ($this->db_array['status'] == 'accepted')
            return true;
        else return false;
    } */
    
    function Accepted() {
        if ($this->getStatus() == 'accepted') return true;
        else return false;
    }
    
    function Rejected() {
        if ($this->getStatus() == 'rejected') return true;
        else return false;
    }
    
    function Cancelled() {
        if ($this->getStatus() == 'cancelled') return true;
        else return false;
    }

    function getDeptId() {
        return $this->db_array['dept_id'];
    }
    
    function getDept() {

        if (!$this->dept && $this->dept_id)
            $this->dept = new Dept($this->dept_id);
        return $this->dept;
    }
    
    function getDeptName() {
        $dept_name = Dept::getNameById($this->getDeptId());
        return $dept_name;
    }
    
    function client_can_cancel(&$errors) {
        if ( strtolower($this->getDeptName()) == 'provisioning' ) {
            $errors['err'] = 'Permission denied. This service is already delivered. order id is ' . $this->getId();
            return false;
        }
        elseif ( (strtolower($this->getDeptName()) == 'billing') || (strtolower($this->getDeptName()) == 'sales') ) return true;
        else {
            $errors['err'] = 'order department error, contact admin with this order id: ' . $this->getId();
            return false;
        }
    }
    
    function clientCancelled() {
        if ($this->db_array['client_cancelled'] || ($this->db_array['client_cancelled'] == '1')) return true;
        else return false;
    }
    
    function cancel_by_client(&$errors) {
        global $thisuser;
        if (!$thisuser->isClientAdmin()) {
            $errors['err'] = 'Permission denied, you have no permission to cancel order';
            return false;
        }
        if ($this->clientCancelled()) {
            return false;
        }
        if ($this->client_can_cancel()) {
            $sql = 'UPDATE ' . ORDER_TABLE . ' SET client_cancelled=' . db_input(1) .  ',updated_date=NOW() ' . ' WHERE order_id=' . db_input($this->getId());
            //mysql_query($sql) or die(mysql_error());
            if (db_query($sql) && db_affected_rows()) {
                if ($this->reload()) {
                    return true;
                } else {
                    return false;
                }
                return true;
            }
            else return false;
        } else {
            $errors['err'] = 'You cannot cancel an order at this stage';
        }
    
    }

    function getAssignedStaffId() {
        return $this->db_array['assigned_staff_id'];
    }

    //START order creator info
    function getCreatorName() {
        return $this->db_array['order_creator_name'];
    }

    //do can be 'set' or 'free'
    function setLock($order, $do = 'set') {
        if (!is_object($order))
            return false;
        $staff_id = '';
        global $thisuser;

        if ($do == 'set')
            $staff_id = $thisuser->getId();
        if ($do == 'free')
            $staff_id = '';

        $sql = 'INSERT INTO ' . ORDER_LOCK_TABLE . ' SET staff_id=' . db_input($staff_id) . ' ,order_id=' . db_input($order->getId()) . ' ,created=NOW()';
        return (db_query($sql) || db_affected_rows()) ? true : false;
    }

    /**
     * checks is an order is locked by another staff
     * 
     * @param <string> $order_id 
     * 
     * @return <bool> success
     */
    function getLock($order_id) {
        if (is_numeric($order_id) || is_string($order_id)) {
            $sql = 'SELECT staff_id FROM ' . ORDER_LOCK_TABLE . ' WHERE order_id=' . db_input($order_id);
            $res = db_query($sql);
            if (!$res || !db_num_rows($res))
                return NULL;
            $row = db_fetch_array($res);
            return $row['staff_id'];
        }
        else
            return false;
    }

    /**
     * get the time when was the order lock was created
     * 
     * @param <type> $order_id 
     * 
     * @return <type>
     */
    function getLockTime($order_id) {
        if (is_numeric($order_id) || is_string($order_id)) {
            $sql = 'SELECT created FROM ' . ORDER_LOCK_TABLE . ' WHERE order_id=' . db_input($order_id);
            $res = db_query($sql);
            if (!$res || !db_num_rows($res))
                return NULL;
            $row = db_fetch_array($res);
            return $row['created'];
        }
        else
            return false;
    }

    /*
    function getDeptName() {
        $dept = new Dept($this->db_array['dept_id']);
        return $dept->getName();
    }
    */

    function getStaffId() {
        return $this->db_array['assigned_staff_id'];
    }

    function getStaffName() {
        
    }

    function getStaff() {

        if (!$this->staff && $this->staff_id)
            $this->staff = new Staff($this->db_array['assigned_staff_id']);
        return $this->staff;
    }

    /**
     * 
     * 
     * @param <string> $order_id 
     * @param <string> $log_type can be only one of 'acepted','rejected','cancelled','created','updated'
     * @param <object> $user 
     * 
     * @return <bool>
     */
    
    function Log($order_id, $log_type, $user) {
        $client_side = 0;
        if (is_object($user)) {
            $user_id = $user->getId();
        } else {
            die('Order log error: not an user object');
            return false;
            }
        if ($user->isClient()) $client_side = 1;

        $sql = 'INSERT INTO ' . ORDER_LOG_TABLE . ' SET ' .
                'log_date=NOW()' .
                ',order_id=' . db_input($order_id) .
                ',log_type=' . db_input($log_type) .
                ',user_id=' . db_input($user_id) .
                ',client_side=' . db_input($client_side) .
                ',ip=' . db_input(isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'])
                ;

        return (db_query($sql) && db_affected_rows()) ? true : false;
    }

    /**
     * 
     * 
     * 
     * @return <bool>
     */
    
    function accept() {
        global $thisuser;
        global $errors;
        if (($thisuser->getDeptId() != $this->getDeptId())) {
            $errors['err'] = 'permission denied, this order is on ' . strtolower($this->getDeptName()) . ' department';
            return false;
        }
        if ( !$this->Accepted() && ($thisuser->getDeptId() == $this->getDeptId())) {
            $current_dept_id = $this->getDeptId();
            $current_dept_name = strtolower($this->getDeptName());
            $new_dept_id = '';

            if (strtolower($current_dept_name) == 'sales') {
                $new_dept_id = Dept::getIdByName('billing');
            } elseif (strtolower($current_dept_name) == 'billing') {
                $new_dept_id = Dept::getIdByName('provisioning');
            }
            
            $new_status = $current_dept_name == 'provisioning' ? 'accepted' : 'pending';
            $assigned_staff_id = $current_dept_name == 'provisioning' ? $thisuser->getId() : '0';
            
            $sql = 'UPDATE ' . ORDER_TABLE . ' SET ' .
                    'status=' . db_input($new_status) . ' ,assigned_staff_id=' . db_input($assigned_staff_id) . ' ,dept_id=' . db_input($new_dept_id) . ' ,updated_date=NOW()' . ' WHERE order_id=' . db_input($this->getId());
            
            if (db_query($sql) && db_affected_rows()) {
                global $order;
                if ($this->reload()) {
                    return true;
                } else {
                    return false;
                }
                return true;
            }
            else return false;
        } else {
            $errors['err'] = 'Permission denied,this order is already accepted and assigned to ' . $this->getAssignedStaffId();
            return false;
        }
    }

    function reject() {
        global $thisuser, $errors;
        if ($thisuser->getDeptId() != $this->getDeptId()) {
            $errors['err'] = 'permission denied, this order is on ' . strtolower($this->getDeptName()) . ' department';
        return false;
        }
        if ($this->Accepted()) {
            $errors['err'] = 'Permission denied, this order is already accepted by ' . $this->getAssignedStaffId();
            return false;
        }
        if (!$this->Rejected) {
            $sql = 'UPDATE ' . ORDER_TABLE . ' SET status=' . db_input('rejected') . ',assigned_staff_id=' . db_input($thisuser->getId()) . ',updated_date=NOW() ' .
                    ' WHERE order_id=' . db_input($this->getId());
            if (db_query($sql) && db_affected_rows()) {
                global $order;
                if ($this->reload()) {
                    return true;
                } else {
                    return false;
                }
                return true;
            }
            else return false;
        } else {
            return false;
        }
    }

    function cancel() {
        global $thisuser, $errors;
        if ($thisuser->getDeptId() != $this->getDeptId()) {
            $errors['err'] = 'permission denied, this order is on ' . strtolower($this->getDeptName()) . ' department';
        return false;
        }
        if ($this->Accepted()) {
            $errors['err'] = 'Permission denied, this order is already accepted by ' . $this->getAssignedStaffId();
            return false;
        }
        if ($this->getStatus() != 'cancelled') {
            $sql = 'UPDATE ' . ORDER_TABLE . ' SET status=' . db_input('cancelled') . ',assigned_staff_id=' . db_input($thisuser->getId()) . ',updated_date=NOW() ' . ' WHERE order_id=' . db_input($this->getId());
            if (db_query($sql) && db_affected_rows()) {
                global $order;
                if ($this->reload()) {
                    return true;
                } else {
                    return false;
                }
                return true;
            }
            else return false;
        } else {
            global $errors;
            return false;
        }
    }
    /*
    function client_can_cancel() {
        if ($this->Accepted()) {
            $errors['err'] = 'Permission denied, this order is already accepted by ' . $this->getAssignedStaffId();
            return false;
        }
        if ((Dept::getNameById($this->getId())) == 'provisioning') return false;
        else return true;
    }
    */

    //Replace base variables.
    function replaceTemplateVars($text) {
        global $cfg;

        $dept = $this->getDept();
        $staff = $this->getStaff();

        $search = array('/%id/', '/%ticket/', '/%email/', '/%name/', '/%subject/', '/%topic/', '/%mobile/', '/%status/', '/%priority/',
            '/%dept/', '/%assigned_staff/', '/%createdate/', '/%duedate/', '/%closedate/', '/%url/');
        $replace = array($this->getId(),
            $this->getExtId(),
            $this->getEmail(),
            $this->getName(),
            $this->getSubject(),
            $this->getHelpTopic(),
            $this->getMobileNumber(),
            $this->getStatus(),
            $this->getPriority(),
            ($dept ? $dept->getName() : ''),
            ($staff ? $staff->getName() : ''),
            Format::db_daydatetime($this->getCreateDate()),
            Format::db_daydatetime($this->getDueDate()),
            Format::db_daydatetime($this->getCloseDate()),
            $cfg->getBaseUrl());
        return preg_replace($search, $replace, $text);
    }

    function update($var, &$errors) {
        global $cfg, $thisuser;

        return false;
    }
    
    //get all user emails for a department
    function getAllEmailbyDept($dept_name) {
        $dept_id = Dept::getIdByName($dept_name);
        $sql = "SELECT email FROM " . STAFF_TABLE . " WHERE dept_id=" . db_input($dept_id);
        $res = db_query($sql);
        if (!$res || !db_num_rows($res))
            return NULL;
        $row = db_fetch_array($res);
        
        return $row;
    }
    
    function responseEmail($sub, $body, $dept_name='sales') {
        global $cfg;
        $dept_id = Dept::getIdByName($dept_name);
        $sql = "SELECT email FROM " . STAFF_TABLE . " WHERE dept_id=" . db_input($dept_id);
        $res = db_query($sql);
        //  echo   db_num_rows($res);
        //if (!$res || !db_num_rows($res))
        $email_list = db_fetch_array($res);
        
        $email = $cfg->getDefaultEmail();
        foreach ($email_list as $each_email)
            $email->send($each_email,$sub,$body);
    }

    /*
     * The mother of all functions...You break it you fix it!
     *
     *  $autorespond and $alertstaff overwrites config info...
     */

    function create($vars, &$errors, $autorespond = true) {
        global $cfg, $thisuser, $_FILES;
        
        //TODO:make a log
        if (!$thisuser->isClientAdmin()) {
            $errors['err'] = 'Access denied, you have no permission for this action.';
            return false;
        }

        //  echo   'im here';
        $id = 0;
        $fields = array();
        $fields['customer_rel_no'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['client_id'] = array('type' => 'string', 'required' => 1, 'error' => '');
        $fields['customer_name'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['customer_email'] = array('type' => 'email', 'required' => 1, 'error' => '*');
        $fields['customer_type'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['service_type'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['circuit_type'] = array('type' => 'string', 'required' => 1, 'error' => '*');

        $fields['order_creator_name'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_creator_designation'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_creator_dept_name'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_creator_address'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_creator_city'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_creator_zip_or_po'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_creator_zip_or_po'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_creator_country'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_creator_office_phone'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_creator_fax'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_creator_mobile'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_creator_service_ready_date'] = array('type' => 'date', 'required' => 1, 'error' => '*');
        //Customer End
        $fields['order_customer_name'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_designation'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_dept_name'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_address'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_city'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_zip_or_po'] = array('type' => 'int', 'required' => 1, 'error' => '*');
        $fields['order_customer_country'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_phone_office'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_fax'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_mobile'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_backhaul_provider'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_backhaul_responsibility'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_equipment_to_be_used'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_equipment_others'] = array('type' => 'string', 'required' => 0, 'error' => '*');
        $fields['order_customer_equipment_name'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_equipment_model'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_equipment_vendor'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_connectivity_interface'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_connectivity_interface_others'] = array('type' => 'string', 'required' => 0, 'error' => '*');
        $fields['order_customer_protocol_to_be_used'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_protocol_others'] = array('type' => 'string', 'required' => 0, 'error' => '');
        $fields['order_customer_connectivity_capacity'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_connectivity_capacity_others'] = array('type' => 'string', 'required' => 0, 'error' => '');
        $fields['order_customer_special_ins'] = array('type' => 'text', 'required' => 0, 'error' => '');

        //Technical contact info
        $fields['order_technical_contact_name'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_technical_contact_mobile'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_technical_contact_phone'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_technical_contact_email'] = array('type' => 'email', 'required' => 1, 'error' => '*');
        $fields['order_technical_contact_messengers'] = array('type' => 'string', 'required' => 1, 'error' => '*');


        $fields['order_routing_type'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_as_sys_name'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_customer_as_sys_num'] = array('type' => 'int', 'required' => 1, 'error' => '*');
        $fields['order_customer_as_set_num'] = array('type' => 'int', 'required' => 1, 'error' => '*');



        $fields['order_bgp_routing'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_router_name'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_bw_speed_cir'] = array('type' => 'int', 'required' => 1, 'error' => '*');
        $fields['order_max_burstable_limit'] = array('type' => 'int', 'required' => 1, 'error' => '*');
        $fields['connectivity_interface'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_fiber_type'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_ip_details_for_global'] = array('type' => 'string', 'required' => 1, 'error' => '*');
        $fields['order_special_routing_comments'] = array('type' => 'string', 'required' => 0, 'error' => '');


        $fields['order_billing_total_non_recurring_charges'] = array('type' => 'int', 'required' => 1, 'error' => '*');
        $fields['order_billing_total_monthly_recurring_charges'] = array('type' => 'int', 'required' => 1, 'error' => '*');
        $fields['order_billing_hw_charges'] = array('type' => 'int', 'required' => 1, 'error' => '*');
        $fields['order_billing_misc_charges'] = array('type' => 'int', 'required' => 0, 'error' => '');
        $fields['order_billing_special_discount'] = array('type' => 'int', 'required' => 0, 'error' => '');
        $fields['order_billing_vat_or_tax'] = array('type' => 'int', 'required' => 1, 'error' => '*');
        $fields['order_billing_deposit'] = array('type' => 'int', 'required' => 1, 'error' => '*');
        $fields['order_billing_total_payable_with_sof'] = array('type' => 'int', 'required' => 1, 'error' => '* empty');


        $fields['order_special_requests_if_any'] = array('type' => 'text', 'required' => 0, 'error' => '');

        $fields['applicants_name'] = array('type' => 'text', 'required' => 1, 'error' => '*');
        $fields['applicants_designation'] = array('type' => 'text', 'required' => 1, 'error' => '*');
        $fields['application_date'] = array('type' => 'text', 'required' => 1, 'error' => '*');
        $fields['applicants_sig'] = array('type' => 'text', 'required' => 0, 'error' => '');

        $validate = new Validator($fields);
        if (!$validate->validate($vars)) {
            $errors = array_merge($errors, $validate->errors());
            //  echo   'im here';
        }

        //Any error above is fatal.
        if ($errors) {
            return 0;
            //  echo   'im here';
        }

        $ipaddress = $var['ip'] ? $var['ip'] : $_SERVER['REMOTE_ADDR'];

        //$extId = ServiceOrder::genExtRandID();
        $order_id = Sys::unique_id();
        if (self::load($order_id, 1)) {
            $errors['err'] = 'order already created';
            return false;
        }

        $sql = 'INSERT INTO ' . ORDER_TABLE .
                ' SET created_date=NOW() ' .
                ',ip_order_created_from=' . db_input($ipaddress) .
                ',order_id=' . db_input($order_id) .
                ',status=' . db_input('pending') .
                ',customer_rel_no=' . db_input(Format::striptags($vars['customer_rel_no'])) .
                ',client_id=' . db_input(Format::striptags($vars['client_id'])) .
                ',customer_name=' . db_input(Format::striptags($vars['customer_name'])) .
                ',customer_email=' . db_input(Format::striptags($vars['customer_email'])) .
                ',customer_type=' . db_input(Format::striptags($vars['customer_type'])) .
                ',service_type=' . db_input(Format::striptags($vars['service_type'])) .
                ',circuit_type=' . db_input(Format::striptags($vars['circuit_type'])) .
                ',order_creator_name=' . db_input(Format::striptags($vars['order_creator_name'])) .
                ',order_creator_designation=' . db_input(Format::striptags($vars['order_creator_designation'])) .
                ',order_creator_dept_name=' . db_input(Format::striptags($vars['order_creator_dept_name'])) .
                ',order_creator_address=' . db_input(Format::striptags($vars['order_creator_address'])) .
                ',order_creator_city=' . db_input(Format::striptags($vars['order_creator_city'])) .
                ',order_creator_zip_or_po=' . db_input(Format::striptags($vars['order_creator_zip_or_po'])) .
                ',order_creator_country=' . db_input(Format::striptags($vars['order_creator_country'])) .
                ',order_creator_office_phone=' . db_input(Format::striptags($vars['order_creator_office_phone'])) .
                ',order_creator_fax=' . db_input(Format::striptags($vars['order_creator_fax'])) .
                ',order_creator_mobile=' . db_input(Format::striptags($vars['order_creator_mobile'])) .
                ',order_creator_service_ready_date=' . db_input(Format::striptags($vars['order_creator_service_ready_date'])) .
                ',order_customer_name=' . db_input(Format::striptags($vars['order_customer_name'])) .
                ',order_customer_designation=' . db_input(Format::striptags($vars['order_customer_designation'])) .
                ',order_customer_dept_name=' . db_input(Format::striptags($vars['order_customer_dept_name'])) .
                ',order_customer_address=' . db_input(Format::striptags($vars['order_customer_address'])) .
                ',order_customer_city=' . db_input(Format::striptags($vars['order_customer_city'])) .
                ',order_customer_zip_or_po=' . db_input(Format::striptags($vars['order_customer_zip_or_po'])) .
                ',order_customer_country=' . db_input(Format::striptags($vars['order_customer_country'])) .
                ',order_customer_phone_office=' . db_input(Format::striptags($vars['order_customer_phone_office'])) .
                ',order_customer_fax=' . db_input(Format::striptags($vars['order_customer_fax'])) .
                ',order_customer_mobile=' . db_input(Format::striptags($vars['order_customer_mobile'])) .
                ',order_customer_backhaul_provider=' . db_input(Format::striptags($vars['order_customer_backhaul_provider'])) .
                ',order_customer_backhaul_responsibility=' . db_input(Format::striptags($vars['order_customer_backhaul_responsibility'])) .
                ',order_customer_equipment_to_be_used=' . db_input(Format::striptags($vars['order_customer_equipment_to_be_used'])) .
                ',order_customer_equipment_others=' . db_input(Format::striptags($vars['order_customer_equipment_others'])) .
                ',order_customer_equipment_name=' . db_input(Format::striptags($vars['order_customer_equipment_name'])) .
                ',order_customer_equipment_model=' . db_input(Format::striptags($vars['order_customer_equipment_model'])) .
                ',order_customer_equipment_vendor=' . db_input(Format::striptags($vars['order_customer_equipment_vendor'])) .
                ',order_customer_connectivity_interface=' . db_input(Format::striptags($vars['order_customer_connectivity_interface'])) .
                ',order_customer_connectivity_interface_others=' . db_input(Format::striptags($vars['order_customer_connectivity_interface_others'])) .
                ',order_customer_protocol_to_be_used=' . db_input(Format::striptags($vars['order_customer_protocol_to_be_used'])) .
                ',order_customer_protocol_others=' . db_input(Format::striptags($vars['order_customer_protocol_others'])) .
                ',order_customer_connectivity_capacity=' . db_input(Format::striptags($vars['order_customer_connectivity_capacity'])) .
                ',order_customer_connectivity_capacity_others=' . db_input(Format::striptags($vars['order_customer_connectivity_capacity_others'])) .
                ',order_customer_special_ins=' . db_input(Format::striptags($vars['order_customer_special_ins'])) .
                ',order_technical_contact_name=' . db_input(Format::striptags($vars['order_technical_contact_name'])) .
                ',order_technical_contact_mobile=' . db_input(Format::striptags($vars['order_technical_contact_mobile'])) .
                ',order_technical_contact_phone=' . db_input(Format::striptags($vars['order_technical_contact_phone'])) .
                ',order_technical_contact_email=' . db_input(Format::striptags($vars['order_technical_contact_email'])) .
                ',order_technical_contact_messengers=' . db_input(Format::striptags($vars['order_technical_contact_messengers'])) .
                ',order_routing_type=' . db_input(Format::striptags($vars['order_routing_type'])) .
                ',order_customer_as_sys_name=' . db_input(Format::striptags($vars['order_customer_as_sys_name'])) .
                ',order_customer_as_sys_num=' . db_input(Format::striptags($vars['order_customer_as_sys_num'])) .
                ',order_customer_as_set_num=' . db_input(Format::striptags($vars['order_customer_as_set_num'])) .
                ',order_bgp_routing=' . db_input(Format::striptags($vars['order_bgp_routing'])) .
                ',order_router_name=' . db_input(Format::striptags($vars['order_router_name'])) .
                ',order_bw_speed_cir=' . db_input(Format::striptags($vars['order_bw_speed_cir'])) .
                ',order_max_burstable_limit=' . db_input(Format::striptags($vars['order_max_burstable_limit'])) .
                ',connectivity_interface=' . db_input(Format::striptags($vars['connectivity_interface'])) .
                ',order_fiber_type=' . db_input(Format::striptags($vars['order_fiber_type'])) .
                ',order_ip_details_for_global=' . db_input(Format::striptags($vars['order_ip_details_for_global'])) .
                ',order_special_routing_comments=' . db_input(Format::striptags($vars['order_special_routing_comments'])) .
                ',order_billing_total_non_recurring_charges=' . db_input(Format::striptags($vars['order_billing_total_non_recurring_charges'])) .
                ',order_billing_total_monthly_recurring_charges=' . db_input(Format::striptags($vars['order_billing_total_monthly_recurring_charges'])) .
                ',order_billing_hw_charges=' . db_input(Format::striptags($vars['order_billing_hw_charges'])) .
                ',order_billing_misc_charges=' . db_input(Format::striptags($vars['order_billing_misc_charges'])) .
                ',order_billing_special_discount=' . db_input(Format::striptags($vars['order_billing_special_discount'])) .
                ',order_billing_vat_or_tax=' . db_input(Format::striptags($vars['order_billing_vat_or_tax'])) .
                ',order_billing_deposit=' . db_input(Format::striptags($vars['order_billing_deposit'])) .
                ',order_billing_total_payable_with_sof=' . db_input(Format::striptags($vars['order_billing_total_payable_with_sof'])) .
                ',order_special_requests_if_any=' . db_input(Format::striptags($vars['order_special_requests_if_any'])) .
                ',applicants_name=' . db_input(Format::striptags($vars['applicants_name'])) .
                ',applicants_designation=' . db_input(Format::striptags($vars['applicants_designation'])) .
                ',application_date=' . db_input(Format::striptags($vars['application_date']));


        $order = null;
        //mysql_query($sql) or die(mysql_error());
        if (db_query($sql) && ($id = db_insert_id())) {
            $order = new ServiceOrder($order_id);
            $subj = 'New order alert';
            $body = 'New order id:' . $order->getId() . ' submitted at ' . date('l jS \of F Y h:i:s A') . '(server time) from ip: ' . $ipaddress;
            $order->responseEmail($subj, $body);
        }
        return $order;
    }

}

?>
