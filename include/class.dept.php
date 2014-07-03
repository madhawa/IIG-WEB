<?php
/*********************************************************************
    class.dept.php
    
    Department class

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2010 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id: $
**********************************************************************/
class Dept {
    var $id;
    var $name;
    var $signature;
    
    var $tplId;

    var $emailId;
    var $email;

    var $autorespEmail;
    
    var $managerId;
    var $manager;
    
    var $row;
  
    function Dept($id=0){
        $this->id=0;
        if($id && ($info=$this->getInfoById($id))){
            $this->row=$info;
            $this->id=$info['dept_id'];
            $this->tplId=$info['tpl_id'];
            $this->emailId=$info['email_id'];
            $this->managerId=$info['manager_id'];
            $this->deptname=$info['dept_name'];
            $this->signature=$info['dept_signature'];
            $this->getEmail(); //Auto load email struct.
        }
    }
    
    function reload() {
        $this->Dept($this->getId());
    }

    function getId(){
        return $this->row['dept_id'];
    }
    
    function getName(){
        return $this->deptname;
    }

        
    function getEmailId(){
        return $this->emailId;
    }

    function getEmail(){
        
        if(!$this->email && $this->emailId)
            $this->email= new Email($this->emailId);
            
        return $this->email;
    }

    function getTemplateId() {
         return $this->tplId;
    }
   
    function getAutoRespEmail() {

        if(!$this->autorespEmail && $this->row['autoresp_email_id'])
            $this->autorespEmail= new Email($this->row['autoresp_email_id']);
        else // Defualt to dept email if autoresp is not specified.
            $this->autorespEmail= $this->getEmail();

        return $this->autorespEmail;
    }
 
    function getEmailAddress() {
        return $this->email?$this->email->getAddress():null;
    }

    function getSignature() {
        
        return $this->signature;
    }

    function canAppendSignature() {
        return ($this->signature && $this->row['can_append_signature'])?true:false;
    }
    
    function getManagerId(){
        $ids = array();
        $sql = 'SELECT staff_id FROM ' . STAFF_TABLE . ' WHERE dept_id=' . db_input($this->getId()) . ' AND access_level=' . db_input(ACCESS_LEVEL_MANAGER);
        if ( $res = db_query($sql) && db_num_rows($res) ) {
            $rows = db_assoc_array($res, true);
            foreach( $rows as $r ) {
                $ids[] = $r['staff_id'];
            }
        }
        return $ids;
    }
    
    function getMembers() {
        $m = array();
        $sql = 'SELECT staff_id FROM ' . STAFF_TABLE . ' WHERE dept_id=' . db_input($this->getId());
        if ( ($res=db_query($sql)) && db_num_rows($res) ) {
            while( $row = db_fetch_array($res) ) {
                if(($staff = new Staff($row['staff_id'])) && $staff->getId()) {
                    $m[] = $staff;
                }
            }
        }
        return $m;
    }

    function getManagers(){
        $managers = array();
        $rows = $this->getManagerId();
        if ( count($rows) ) {
            foreach( $rows as $r ) {
                $man = new Staff($r['staff_id']);
                $managers[] = $man;
            }
            return $managers;
        } else {
            return array();
        }
    }

    function getManagerNames(){
        $managers = $this->getManagers();
        $names = array();
        if ( count($managers) ) {
            foreach( $managers as $man ) {
                $names[] = $man->getName();
            }
            return $names;
        } else {
            return array();
        }
    }
    
    function get_section_permission() {
        return $this->row['permission_can_access_sections'];
    }

//     function getManager(){
//      
//         if(!$this->manager && $this->managerId)
//             $this->manager= new Staff($this->managerId);
//         
//         return $this->manager;
//     }

    
    //for templates
    function get_sample_dept_admin_id($dept_id) {
        $sql='SELECT staff_id FROM '.STAFF_TABLE.' WHERE dept_id='.db_input($dept_id).' AND access_level=' . db_input(ACCESS_LEVEL_MANAGER) . ' LIMIT 1';
        
        if ( ($res = db_query($sql)) && db_num_rows($res) ) {
            $row = db_fetch_array($res);
            return $row['staff_id'];
        } else {
            return false;
        }
    }
    
    function get_sample_dept_staff_id($dept_id) {
        $sql='SELECT staff_id FROM '.STAFF_TABLE.' WHERE dept_id='.db_input($dept_id).' AND access_level=' . db_input(ACCESS_LEVEL_STAFF) . ' LIMIT 1';
        
        if ( ($res = db_query($sql)) && db_num_rows($res) ) {
            $row = db_fetch_array($res);
            return $row['staff_id'];
        } else {
            return false;
        }
    }

    function isPublic() {
         return $this->row['ispublic']?true:false;
    }
    
    function autoRespONNewTicket() {
        return $this->row['ticket_auto_response']?true:false;
    }
        
    function autoRespONNewMessage() {
        return $this->row['message_auto_response']?true:false;
    }

    function noreplyAutoResp(){
         return $this->row['noreply_autoresp']?true:false;
    }
    
    function getInfo() {
        return $this->row;
    }


    
	function getInfoById($id) {
		$sql='SELECT * FROM '.DEPT_TABLE.' WHERE dept_id='.db_input($id);
		if(($res=db_query($sql)) && db_num_rows($res))
            return db_fetch_array($res);
        
        return null;
	}

    
	function getIdByName($name) {
        $name_1 = $name;
        $name_2 = strtolower($name);
        $name_3 = strtoupper($name);
        $name_4 = ucfirst($name);//if the name in the db is uppercase first character
        $id=0;
        $sql_1 ='SELECT dept_id FROM '.DEPT_TABLE.' WHERE dept_name LIKE "%'.db_input($name_1, false).'%"';
        $sql_2 ='SELECT dept_id FROM '.DEPT_TABLE.' WHERE dept_name LIKE "%'.db_input($name_2, false).'%"';
        $sql_3 ='SELECT dept_id FROM '.DEPT_TABLE.' WHERE dept_name LIKE "%'.db_input($name_3, false).'%"';
        $sql_4 ='SELECT dept_id FROM '.DEPT_TABLE.' WHERE dept_name LIKE "%'.db_input($name_4, false).'%"';
        if(($res=db_query($sql_1)) && db_num_rows($res)) {
            list($id)=db_fetch_row($res);
        } elseif ( ($res=db_query($sql_2)) && db_num_rows($res) ) {
            list($id)=db_fetch_row($res);
        } elseif ( ($res=db_query($sql_3)) && db_num_rows($res) ) {
            list($id) = db_fetch_row($res);
        } elseif ( ($res=db_query($sql_4)) && db_num_rows($res) ) {
            list($id) = db+db_fetch_row($sql_4);
        }

        return $id;
    }

    function getIdByEmail($email) {
        $id=0;
        $sql ='SELECT dept_id FROM '.DEPT_TABLE.' WHERE dept_email='.db_input($email);
        if(($res=db_query($sql)) && db_num_rows($res))
            list($id)=db_fetch_row($res);

        return $id;
    }

    function getNameById($id) {
        $sql ='SELECT dept_name FROM '.DEPT_TABLE.' WHERE dept_id='.db_input($id);
        if(($res=db_query($sql)) && db_num_rows($res))
            list($name)=db_fetch_row($res);
        return $name;
    }

    function getDefaultDeptName() {
        global $cfg;
        return Dept::getNameById($cfg->getDefaultDeptId());
    }
    
    function expandNames($short_name) {
        switch($short_name) {
            case NOC:
                return 'NOC 1ASIA_AHL';
                break;
            case MANAGEMENT:
                return 'Management Department';
                break;
            case SALES:
                return 'Sales Department';
                break;
            case PROVISIONING:
                return 'Provisioning Department';
                break;
            default:
                return false;
                break;
        }
    }


    function create($vars,&$errors) {
        if(!$vars['dept_name']) {
            $errors['dept_name']='Dept name empty';
            return false;
        }elseif(strlen($vars['dept_name'])<4) {
            $errors['dept_name']='Dept name must be at least 4 chars.';
            return false;
        }else{
            $sql='SELECT dept_id FROM '.DEPT_TABLE.' WHERE dept_name='.db_input($vars['dept_name']);
            if($id)
                $sql.=' AND dept_id!='.db_input($id);
                
            if(db_num_rows(db_query($sql))) {
                $errors['dept_name']='Department already exist';
                return false;
            }
        }
        
        return Dept::save(0,$vars,$errors);
    }
    
    function update($vars,&$errors) {
    
        if( $vars['dept_name'] && (strlen($vars['dept_name'])<4) ) {
            $errors['dept_name']='Dept name must be at least 4 chars.';
            return false;
        } elseif ( $vars['dept_name'] ) {
            $sql='SELECT dept_id FROM '.DEPT_TABLE.' WHERE dept_name='.db_input($vars['dept_name']);
            if($id)
                $sql.=' AND dept_id!='.db_input($id);
                
            if(db_num_rows(db_query($sql))) {
                $errors['dept_name']='Department already exist';
                return false;
            }
        }
    
        if($this->save($this->getId(),$vars,$errors)){
            $this->update_membership($vars);
            $this->reload();
            return true;
        }
        return false;
    }
    
    function update_membership($vars) {
        $new_members = is_array($vars['dept_members'])?$vars['dept_members']:array();
        $new_managers = is_array($vars['dept_managers'])?$vars['dept_managers']:array();
        
        if ( ($members = $this->getMembers()) && count($members) ) { //updating existing members
            $changed = 0;
            foreach( $members as $staff ) {
                //echo $staff->getName().' ';
                if ( in_array($staff->getId(), $new_managers) ) { //is in managers
                    if ($staff->setDeptId($this->getId(), true)) {
                        $changed++;
                    }
                } elseif( in_array($staff->getId(), $new_members) ) { //only normal members
                    if ($staff->setDeptId($this->getId(), false)) {
                        $changed++;
                    }
                } else { //reset membership
                    if ($staff->setDeptId(0, false)) {
                        $changed++;
                    }
                }
            }
            //echo $changed;
            unset($staff);
            unset($members);
            
        }
        
        foreach( $new_managers as $mid ) {
            if ( $manager = new Staff($mid) ) {
                //echo 'make <b>manager</b> '.$manager->getName().' ';
                $manager->setDeptId($this->getId(), true);
            }
        }
        unset($mid);
        unset($manager);
    
        foreach( $new_members as $memid ) {
            if ( $staff = new Staff($memid) ) {
                if ( in_array($staff->getId(), $new_managers) ) { //is in managers
                    //echo 'make <b>manager</b> '.$staff->getName().' ';
                    $staff->setDeptId($this->getId(), true);
                } else { //only normal members
                    //echo 'make member '.$staff->getName().' ';
                    $staff->setDeptId($this->getId(), false);
                }
            }
        }
    }
    
    function getFixedId() {
        return $row['fixed_id'];
    }


    function delete($id, &$errors) {
        global $cfg;
        
        $sql='DELETE FROM '.DEPT_TABLE.' WHERE dept_id='.db_input($id);
        if(db_query($sql) && ($num=db_affected_rows())){
            // DO SOME HOUSE CLEANING
            //Move Dept members 
            //This should never happen..since delete should be issued only to empty Depts...but check it anyways 
            $sql = 'UPDATE '.STAFF_TABLE.' SET dept_id='.db_input(0).' AND access_level='.db_input('').' WHERE dept_id='.db_input($id);
            
            if (db_query($sql) && ($num2 = db_affected_rows())) {
                return true;
            } else {
                $errors['err'] .= ' no executive affected ';
                return true;
            }
        } else {
            $errors['err'] .= ' department not deleted, internal error! ';
            return false;
        }        
    }
    
    
    

    function save($id,$vars,&$errors) {
        global $cfg;
        /*
            $sql=' SET updated=NOW() '.
                 ',ispublic='.db_input($_POST['ispublic']).
                 ',email_id='.db_input($_POST['email_id']).
                 ',tpl_id='.db_input($_POST['tpl_id']).
                 ',autoresp_email_id='.db_input($_POST['autoresp_email_id']).
                 ',manager_id='.db_input($_POST['manager_id']?$_POST['manager_id']:0).
                 ',dept_name='.db_input(Format::striptags($_POST['dept_name'])).
                 ',dept_signature='.db_input(Format::striptags($_POST['dept_signature'])).
                 ',ticket_auto_response='.db_input($_POST['ticket_auto_response']).
                 ',message_auto_response='.db_input($_POST['message_auto_response']).
                 ',can_append_signature='.db_input(isset($_POST['can_append_signature'])?1:0);
        */
        $sql=' SET updated=NOW()';

        if($id) {
            $sql='UPDATE '.DEPT_TABLE.' '.$sql.' WHERE dept_id='.db_input($id);
            if(!db_query($sql) || !db_affected_rows()) {
                $errors['err']='Unable to update, query failure';
                return false;
            } else {
                return true;
            }
        }else{
            $sql='INSERT INTO '.DEPT_TABLE.' '.$sql.',dept_name='.db_input(Format::striptags($vars['dept_name'])).',created=NOW()';
            if(db_query($sql) && ($deptID=db_insert_id())) {
                //Dept::setDept($vars, $errors);
                return $deptID;
            } else {
                $errors['err']='Unable to create department. Internal error';
                return false;
            }
        }
    }
    

    //adding staffs and managers into a department
    function setDept($var, &$errors) {
        $error = '';
        if ( count($var['dept_members']) ) { //saving normal staffs
            foreach( $var['dept_members'] as $staff_id ) { //adding staffs
                if ($staff = new Staff($staff_id)) {
                    $sql = 'UPDATE ' . STAFF_TABLE . ' SET dept_id=' . db_input($var['dept_id']) . ' AND access_level='.db_input(ACCESS_LEVEL_STAFF).' WHERE staff_id=' . db_input($staff_id);
                    if ( db_query($sql) && db_affected_rows() ) {
                        $error .= ' error adding members: '.$staff->getName();
                    }
                } else {
                    $error.=' invalid executive id ';
                }
            }
        }
        if ( count($var['dept_managers']) ) { //saving managers
            foreach( $var['dept_managers'] as $manager_id ) {
                if ($manager = new Staff($manager_id)) { //addin manager
                    $sql = 'UPDATE ' . STAFF_TABLE . ' SET dept_id=' . db_input($var['dept_id']) . ' AND access_level='.db_input(ACCESS_LEVEL_MANAGER).' WHERE staff_id=' . db_input($manager->getId());
                    if ( db_query($sql) && db_affected_rows() ) {
                        $error .= ' error adding manager: '.$manager->getName();
                    }
                } else {
                    $error.=' invalid manager id ';
                }
            }
        }
        
        $errors['err'] = $error;
        if ( $error ) {
            return false;
        } else {
            return true;
        }
    }

    //get department members as associative array of 
    function get_members($dept_id) {
        $sql = 'SELECT * FROM ' . STAFF_TABLE . ' WHERE dept_id=' . db_input($dept_id);
        if ( $res = db_query($sql) && db_num_rows($res) ) {
            return db_assoc_array($res);
        } else {
            return false;
        }
    }
    
    //get only staffrs ids in an array
    function get_members_ids($dept_id) {
        $m = array();
        if ( $members = Dept::get_members($dept_id) ) {
            $m[] = $members['staff_id'];
            return $m;
        } else {
            return false;
        }
    }
    
    //check dept membership
    function is_dept_member( $dept_id, $staff_id ) {
        if ( $member_ids = Dept::get_members_ids($dept_id) ) {
            if ( in_array( $staff_id, $member_ids ) ) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
/*
    //adding staff and manager to dept
    function add_staff($vars, &$errors) {
        $new_members = $vars['dept_members'];
        $dept_id = $vars['dept_id'];
        $manager_id = $vars['manager_id'];
        
        print_r($staffs);
        
        if ( $dept_id ) {
            if ($already_members = Dept::get_members_ids($dept_id)) {
                foreach( $already_members as $id ) {
                    if ( !in_array($id, $new_members) ) { //membership cancelled
                        $staff = new Staff($id);
                        $staff->setDeptId(0); //removing membership
                    }
                }
            }
        } else {
            $errors['err'] .= ' dept id not present ';
        }
        
        if ( $errors['err'] ) {
            return false;
        } else {
            return true;
        }
    }
    
    //add manager to dept
    function add_manager($vars, &$errors) {
        $dept_id = $vars['dept_id'];
        $manager_id = $vars['manager_id'];
        
        //now adding the manager
        if ( $dept_id && $manager_id ) {
            $sql = 'UPDATE ' . DEPT_TABLE . ' SET manager_id=' . db_input($manager_id) . ' WHERE dept_id=' . db_input($dept_id);
            if ( !db_query($sql) ) {
                $errors['err'] .= ' failed to assign the staff as manager ';
            }
        }
        
        if ( $errors['err'] ) {
            return false;
        } else {
            return true;
        }
    }
*/
    
    function isSystemDept() {
        if ( !$this->row['fixed_id'] ) {
            return false;
        } else {
            return true;
        }
    }

    function get_all_depts() {
        $depts = array();
        $sql = 'SELECT dept_id FROM '.DEPT_TABLE.' WHERE dept_name<>'.db_input('');
        if ( ($res=db_query($sql)) && db_num_rows($res) ) {
            while( $row=db_fetch_array($res) ) {
                $depts[] = new Dept($row['dept_id']);
            }
        }
        return $depts;
    }

    function get_num_staff() {
        $sql = 'SELECT staff_id FROM '.STAFF_TABLE.' WHERE dept_id='.db_input($this->getId());
        if ( $res=db_query($sql) ) {
            return db_num_rows($res);
        } else {
            return 0;
        }
    }
    
    function get_managers() {
        $names = array();
        $sql = 'SELECT staff_id FROM '.STAFF_TABLE.' WHERE dept_id='.db_input($this->getId()).' AND access_level='.ACCESS_LEVEL_MANAGER;
        if ( ($res = db_query($sql)) && db_num_rows($res) ) {
            while( $row=db_fetch_array($res) ) {
                $names[] = new Staff($row['staff_id']);
            }
        }
        return $names;
    }
    
    function getNOCmail() {
        return 'noc@1asia-ahl.com';
    }
    
    function getNOCId() {
        $sql = 'SELECT dept_id FROM ' . DEPT_TABLE . ' WHERE dept_name=';
    }
   
   
    function get_dept_id_by_fixed_id($fixed_id) {
        $sql = 'SELECT dept_id FROM ' . DEPT_TABLE . ' WHERE fixed_id=' . db_input($fixed_id);
        if ( ($res=db_query($sql)) && db_num_rows($res) ) {
            $row = db_fetch_array($res);
            return $row['dept_id'];
        } else {
            return false;
        }
    }
        
    //department access
    function can_access($staff) {
        if ( !is_object($staff) ) return false;
        $isadmin = $staff->isAdmin() ? true:false;
        if ( stripos($this->getName(), NOC) !== FALSE ) {
            if ( $isadmin ) {
                return array(
                    $this->getIdByName(NOC),
                    $this->getIdByName(ORDER),
                    $this->getIdByName(MRTG),
                    $this->getIdByName(SERVICE),
                    $this->getIdByName(CAPACITY),
                    $this->getIdByName(CLIENT),
                    $this->getIdByName(DASH),
                    $this->getIdByName(MANAGE),
                    $this->getIdByName(EXECUTIVES),
                    $this->getIdByName(DEPARTMENT)
                );
            } else {
                return array(
                    $this->getIdByName(NOC),
                    $this->getIdByName(ORDER),
                    $this->getIdByName(MRTG),
                    $this->getIdByName(SERVICE),
                    $this->getIdByName(CAPACITY),
                    $this->getIdByName(CLIENT),
                    $this->getIdByName(DASH),
                    $this->getIdByName(MANAGE),
                    $this->getIdByName(EXECUTIVES),
                    $this->getIdByName(DEPARTMENT)
                );
            }
        } elseif ( stripos($this->getName(), MANAGEMENT) !== FALSE ) {
            if ( $isadmin ) {
                return array(
                    $this->getIdByName(NOC),
                    $this->getIdByName(ORDER),
                    $this->getIdByName(MRTG),
                    $this->getIdByName(SERVICE),
                    $this->getIdByName(CAPACITY),
                    $this->getIdByName(CLIENT),
                    $this->getIdByName(DASH),
                    $this->getIdByName(MANAGE),
                    $this->getIdByName(EXECUTIVES),
                    $this->getIdByName(DEPARTMENT)
                );
            } else {
                return array(
                    $this->getIdByName(NOC),
                    $this->getIdByName(ORDER),
                    $this->getIdByName(MRTG),
                    $this->getIdByName(SERVICE),
                    $this->getIdByName(CAPACITY),
                    $this->getIdByName(CLIENT),
                    $this->getIdByName(DASH),
                    $this->getIdByName(MANAGE),
                    $this->getIdByName(EXECUTIVES),
                    $this->getIdByName(DEPARTMENT)
                );
            }
        } elseif ( stripos($this->getName(), SALES) !== FALSE ) {
            if ( $isadmin ) {
                return array(
                    $this->getIdByName(NOC),
                    $this->getIdByName(ORDER),
                    $this->getIdByName(MRTG),
                    $this->getIdByName(SERVICE),
                    $this->getIdByName(CAPACITY),
                    $this->getIdByName(CLIENT),
                    $this->getIdByName(DASH),
                    $this->getIdByName(MANAGE),
                    $this->getIdByName(EXECUTIVES),
                    $this->getIdByName(DEPARTMENT)
                );
            } else {
                return array(
                    $this->getIdByName(NOC),
                    $this->getIdByName(ORDER),
                    $this->getIdByName(MRTG),
                    $this->getIdByName(SERVICE),
                    $this->getIdByName(CAPACITY),
                    $this->getIdByName(CLIENT),
                    $this->getIdByName(DASH),
                    $this->getIdByName(MANAGE),
                    $this->getIdByName(EXECUTIVES),
                    $this->getIdByName(DEPARTMENT)
                );
            }
        } elseif ( stripos($this->getName(), PROVISIONING) !== FALSE ) {
            if ( $isadmin ) {
                return array(
                    $this->getIdByName(NOC),
                    $this->getIdByName(ORDER),
                    $this->getIdByName(MRTG),
                    $this->getIdByName(SERVICE),
                    $this->getIdByName(CAPACITY),
                    $this->getIdByName(CLIENT),
                    $this->getIdByName(DASH),
                    $this->getIdByName(MANAGE),
                    $this->getIdByName(EXECUTIVES),
                    $this->getIdByName(DEPARTMENT)
                );
            } else {
                return array(
                    $this->getIdByName(NOC),
                    $this->getIdByName(ORDER),
                    $this->getIdByName(MRTG),
                    $this->getIdByName(SERVICE),
                    $this->getIdByName(CAPACITY),
                    $this->getIdByName(CLIENT),
                    $this->getIdByName(DASH),
                    $this->getIdByName(MANAGE),
                    $this->getIdByName(EXECUTIVES),
                    $this->getIdByName(DEPARTMENT)
                );
            }
        }
    }
}
?>
