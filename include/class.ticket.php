<?php

/* * *******************************************************************
  class.ticket.php

  The most important class! Don't play with fire please.

  Peter Rotich <peter@osticket.com>
  Copyright (c)  2006-2010 osTicket
  http://www.osticket.com

  Released under the GNU General Public License WITHOUT ANY WARRANTY.
  See LICENSE.TXT for details.

  vim: expandtab sw=4 ts=4 sts=4:
  $Id: $
 * ******************************************************************** */
include_once(CLASS_DIR . 'class.client.php');
include_once(INCLUDE_DIR . 'class.staff.php');
include_once(INCLUDE_DIR . 'class.email.php');
include_once(INCLUDE_DIR . 'class.dept.php');
include_once(INCLUDE_DIR . 'class.topic.php');
include_once(INCLUDE_DIR . 'class.lock.php');
include_once(INCLUDE_DIR . 'class.banlist.php');
include_once(CLASS_DIR . 'class.cin.php');

class Ticket {

    var $row;
    var $id;
    var $extid;
    var $email;
    var $status;
    var $sla_claim;
    var $created;
    var $updated;
    var $lastrespdate;
    var $lastmsgdate;
    var $duedate;
    var $priority;
    var $priority_id;
    var $fullname;
    var $staff_id;
    var $dept_id;
    var $topic_id;
    var $dept_name;
    var $subject;
    var $helptopic;
    var $overdue;
    var $lastMsgId;
    var $dept;  //Dept class
    var $staff; //Staff class
    var $topic; //Topic class
    var $tlock; //TicketLock class

    function Ticket($id, $exid = false) {
        $this->load($id);
    }

    function load($id) {

        $now = date('Y-m-d H:i:s');
        $sql = ' SELECT  ticket.*,topic.topic_id as topicId,lock_id,dept_name,priority_desc FROM ' . TICKET_TABLE . ' ticket ' .
                ' LEFT JOIN ' . DEPT_TABLE . ' dept ON ticket.dept_id=dept.dept_id ' .
                ' LEFT JOIN ' . TICKET_PRIORITY_TABLE . ' pri ON ticket.priority_id=pri.priority_id ' .
                ' LEFT JOIN ' . TOPIC_TABLE . ' topic ON ticket.topic_id=topic.topic_id ' .
                ' LEFT JOIN ' . TICKET_LOCK_TABLE . ' tlock ON ticket.ticket_id=tlock.ticket_id AND tlock.expire>NOW() ' .
                ' WHERE ticket.ticket_id=' . db_input($id);
        //  echo   $sql;
        if (($res = db_query($sql)) && db_num_rows($res)):
            $row = db_fetch_array($res);
            $this->id = $row['ticket_id'];
            $this->extid = $row['ticketID'];
            $this->email = $row['email'];
            $this->fullname = $row['name'];
            $this->status = $row['status'];
            $this->sla_claim = $row['sla_claim'];
            $this->created = $row['created'];
            $this->updated = $row['updated'];
            $this->duedate = $row['duedate'];
            $this->closed = $row['closed'];
            $this->lastmsgdate = $row['lastmessagedate'];
            $this->lastrespdate = $row['lastresponsedate'];
            $this->lock_id = $row['lock_id'];
            $this->priority_id = $row['priority_id'];
            $this->priority = $row['priority_desc'];
            $this->staff_id = $row['staff_id'];
            $this->dept_id = $row['dept_id'];
            $this->topic_id = $row['topicId']; //Note that we're actually joining the topic table to make the topic is not deleted (long story!).
            $this->dept_name = $row['dept_name'];
            $this->subject = $row['subject'];
            $this->helptopic = $row['helptopic'];
            $this->overdue = $row['isoverdue'];
            $this->row = $row;
            //Reset the sub classes (initiated ondemand)...good for reloads.
            $this->staff = array();
            $this->dept = array();
            return true;
        endif;
        return false;
    }

    function reload() {
        return $this->load($this->id);
    }

    function get_now() {
        $now = date('Y-m-d H:i:s');
        return $now;
    }

    //has
    function has_raiser_info() {
        if ($this->row['raiser_name'] || $this->row['raiser_phone'] || $this->row['raiser_email'] || $this->row['raised_from']) {
            return true;
        } else {
            return false;
        }
    }

    function get_raiser_name() {
        return $this->row['raiser_name'];
    }

    function get_raiser_email() {
        return $this->row['raiser_email'];
    }

    function get_raiser_phone() {
        return $this->row['raiser_phone'];
    }

    function get_raised_from() {
        return $this->row['raised_from'];
    }

    function has_site_info() {
        if ($this->row['site_visited_by'] || $this->row['site_visited_date']) {
            return true;
        } else {
            return false;
        }
    }

    function get_site_visited_by() {
        return $this->row['site_visited_by'];
    }

    function get_site_visited_date() {
        return $this->row['site_visited_date'];
    }

    function has_link_info() {
        if ($this->row['link_down_date'] || $this->row['restoration_date'] || $this->row['downtime_duration'] || $this->row['restoration_done_by'] || $this->row['restoration_confirmed_by']) {
            return true;
        } else {
            return false;
        }
    }

    function get_link_down_date() {
        return $this->row['link_down_date'];
    }

    function get_link_restoration_date() {
        return $this->row['restoration_date'];
    }

    function get_restoration_done_by() {
        return $this->row['restoration_done_by'];
    }

    function get_restoration_confirmed_by() {
        return $this->row['restoration_confirmed_by'];
    }
    

    function is_sla_ticket() {
        if ((int)$this->row['sla_claim_duration']) {
            return true;
        } else {
            return false;
        }
    }
        
    function get_downtime_duration() {
        return (int)$this->row['downtime_duration'];
    }

    function get_outage_duration() { //to calculate sla, this is U
        if ( $this->is_sla_ticket() ) {
            return $this->row['sla_claim_duration'];
        } else {
            return 0;
        }
    }
        
    function get_service_duration() { //this is E
        if ( $this->is_sla_ticket() ) {
            if ( $this->get_downtime_duration() ) {
                return max( (int)($this->get_downtime_duration()-$this->get_outage_duration()), 0);
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    function get_sla_cause() {
        return $this->row['sla_claim_cause'];
    }
    
    function get_sla_duration() {
        return $this->row['sla_claim_duration'];
    }

    function isOpen() {
        return (strcasecmp($this->getStatus(), 'Open') == 0) ? true : false;
    }

    function isClosed() {
        return (strcasecmp($this->getStatus(), 'Closed') == 0) ? true : false;
    }

    function isAssigned() {
        return $this->getStaffId() ? true : false;
    }

    function isOverdue() {
        return $this->overdue ? true : false;
    }

    function isLocked() {
        return $this->lock_id ? true : false;
    }

    //GET
    function getInfo() {
        return $this->row;
    }

    function getId() {
        return $this->row['ticket_id'];
    }

    function getExtId() {
        return $this->row['TTID'];
    }

    function getEmail() {
        if ($this->row['email']) {
            return $this->row['email'];
        } else {
            return $this->getClient()->getEmail();
        }
    }

    function getAltEmail() {
        $mails = Format::sp_trim($this->row['alt_email']);
        return $mails;
    }

    function add_more_cc($more_cc) {
        /*
          $alt_emails = $this->getAltEmail();
          $alt_emails = trim($alt_emails, ',');
          if ( is_array($more_cc) ) {
          $alt_emails = $alt_emails.','.implode(',', $more_cc);
          } elseif(is_string($more_cc)) {
          $alt_emails= $alt_emails.','.$more_cc;
          } else {
          return false;
          }
          $sql = 'UPDATE '.TICKET_TABLE.' SET alt_email='.db_input($alt_emails);
          if (db_query($sql) && db_affected_rows()) {
          return true;
          } else {
          return false;
          }
         * */
    }

    function addInternalCc($more_cc) {
        if (!is_array($more_cc) || !is_string($more_cc)) {
            return false;
        }
        $sql = 'UPDATE ' . TICKET_TABLE . ' SET internal_cc=' . db_input(trim($more_cc, ','));
        if (db_query($sql) && db_affected_rows()) {
            $this->reload();
            return true;
        } else {
            return false;
        }
    }

    function getInternalCc() {
        return $this->row['internal_cc'];
    }

    function getCINValue() {
        $info = $this->getInfo();
        return $info['cin'];
    }

    function getName() {
        return $this->fullname;
    }

    function getClient() {
        $client = new Client($this->row['client_id']);
        return $client;
    }

    function getClientName() {
        if ($this->row['name']) {
            return $this->row['name'];
        } else {
            if (($client = new Client($this->row['client_id'])) && $client->getId()) {
                return $client->getName();
            } else {
                return false;
            }
        }
    }

    function getClientId() {
        return $this->row['client_id'];
    }

    function getClientEmployeeName() {
        $client_id = $this->getClientId();
        $client = new Client($client_id);
        return $client->getEmployeeName();
    }

    function getContactName() {
        return $this->row['name'];
    }

    function getAltContactName() {
        return $this->row['alt_contact_name'];
    }

    function getAltPhone() {
        return $this->row['alt_phone_num'];
    }

    function getSubject() {
        return $this->row['subject'];
    }

    function getHelpTopic() {
        if ($this->topic_id && ($topic = $this->getTopic()))
            return $topic->getName();

        return $this->row['helptopic'];
    }

    function getCreateDate() {
        return $this->row['created'];
    }

    function getUpdateDate() {
        return $this->row['updated'];
    }

    function getDueDate() {
        return $this->duedate;
    }

    function getCloseDate() {
        return $this->closed;
    }

    function getSLAClaim() {
        return $this->sla_claim;
    }

    function getStatus() {
        return $this->row['status'];
    }

    function getDeptId() {
        return $this->dept_id;
    }

    function getDeptName() {
        return $this->dept_name;
    }

    function getPriorityId() {
        return $this->priority_id;
    }

    function getPriority() {
        return $this->priority;
    }

    function getMobile() {
        return $this->row['mobile'];
    }

    function getPhone() {
        if ($this->row['phone']) {
            return $this->row['phone'];
        } else {
            return $this->getClient()->getPhone();
        }
    }

    function getPhoneNumber() {
        $phone = Format::phone($this->getPhone());
        return $phone;
    }

    function getPhoneExt() {
        return $this->row['phone_ext'];
    }

    function getMobileNumber() {
        $mobile = Format::phone($this->getMobile());

        return $mobile;
    }

    function getSource() {
        return $this->row['source'];
    }

    function getIP() {
        return $this->row['ip_address'];
    }

    function getLock() {

        if (!$this->tlock && $this->lock_id)
            $this->tlock = new TicketLock($this->lock_id);

        return $this->tlock;
    }

    function acquireLock() {
        global $thisuser, $cfg;

        if (!$thisuser or ! $cfg->getLockTime()) //Lockig disabled?
            return null;

        //Check if the ticket is already locked.
        if (($lock = $this->getLock()) && !$lock->isExpired()) {
            if ($lock->getStaffId() != $thisuser->getId()) //someone else locked the ticket.
                return null;
            //Lock already exits...renew it
            $lock->renew(); //New clock baby.

            return $lock;
        }
        //No lock on the ticket or it is expired
        $this->tlock = null; //clear crap
        $this->lock_id = TicketLock::acquire($this->getId(), $thisuser->getId()); //Create a new lock..
        //load and return the newly created lock if any!
        return $this->getLock();
    }

    function getDept() {

        if (!$this->dept && $this->dept_id)
            $this->dept = new Dept($this->dept_id);
        return $this->dept;
    }

    function getStaffId() {
        return $this->staff_id;
    }

    function getAssignee() {
        return $this->getStaffId();
    }

    function getStaff() {

        if (!$this->staff && $this->staff_id)
            $this->staff = new Staff($this->row['staff_id']);
        return $this->staff;
    }

    function getTopicId() {
        return $this->topic_id;
    }

    function getTopic() {

        if (!$this->topic && $this->topic_id)
            $this->topic = new Topic($this->topic_id);

        return $this->topic;
    }

    function getLastRespondent() {

        $sql = 'SELECT  resp.staff_id FROM ' . TICKET_RESPONSE_TABLE . ' resp LEFT JOIN ' . STAFF_TABLE . ' USING(staff_id) ' .
                ' WHERE  resp.ticket_id=' . db_input($this->getId()) . ' AND resp.staff_id>0  ORDER BY resp.created DESC LIMIT 1';
        $res = db_query($sql);
        if ($res && db_num_rows($res))
            list($id) = db_fetch_row($res);

        return ($id) ? new Staff($id) : null;
    }

    function getLastMessageDate() {


        if ($this->lastmsgdate)
            return $this->lastmsgdate;

        //for old versions...
        $createDate = 0;
        $sql = 'SELECT created FROM ' . TICKET_MESSAGE_TABLE . ' WHERE ticket_id=' . db_input($this->getId()) . ' ORDER BY created DESC LIMIT 1';
        if (($res = db_query($sql)) && db_num_rows($res))
            list($createDate) = db_fetch_row($res);

        return $createDate;
    }

    function getLastResponseDate() {


        if ($this->lastrespdate)
            return $this->lastrespdate;

        $createDate = 0;
        $sql = 'SELECT created FROM ' . TICKET_RESPONSE_TABLE . ' WHERE ticket_id=' . db_input($this->getId()) . ' ORDER BY created DESC LIMIT 1';
        if (($res = db_query($sql)) && db_num_rows($res))
            list($createDate) = db_fetch_row($res);

        return $createDate;
    }

    function getRelatedTicketsCount() {

        $num = 0;
        $sql = 'SELECT count(*)  FROM ' . TICKET_TABLE . ' WHERE email=' . db_input($this->getEmail());
        if (($res = db_query($sql)) && db_num_rows($res))
            list($num) = db_fetch_row($res);

        return $num;
    }

    function getLastMsgId() {
        if ( $this->lastMsgId ) {
            return $this->lastMsgId;
        } else {
            $sql = 
                'SELECT msg.msg_id as msg_id,msg.created as created,msg.message as message,msg.source as source,TRUE as ismessage FROM ' . TICKET_MESSAGE_TABLE .' msg WHERE  msg.ticket_id=' . db_input($this->getId()) 
                . ' UNION ALL ' . 
                'SELECT resp.msg_id as msg_id,resp.created as created,resp.response as message,resp.staff_name as source,NULL as ismessage FROM ' . TICKET_RESPONSE_TABLE . ' resp  WHERE  resp.ticket_id=' . db_input($this->getId()) . ' ORDER BY created DESC LIMIT 1';
            $msgres = db_query($sql);
            $row = db_fetch_array($msgres);
            return $row['msg_id'];
        }
    }

    //SET

    function setLastMsgId($msgid) {
        return $this->lastMsgId = $msgid;
    }

    function setPriority($priority_id) {

        if (!$priority_id)
            return false;

        $sql = 'UPDATE ' . TICKET_TABLE . ' SET priority_id=' . db_input($priority_id) . ',updated=NOW() WHERE ticket_id=' . db_input($this->getId());
        if (db_query($sql) && db_affected_rows($res)) {
            //TODO: escalate the ticket params??
            return true;
        }
        return false;
    }

    //DeptId can NOT be 0. No orphans please!
    function setDeptId($deptId) {

        if (!$deptId)
            return false;

        $sql = 'UPDATE ' . TICKET_TABLE . ' SET dept_id=' . db_input($deptId) . ' WHERE ticket_id=' . db_input($this->getId());
        return (db_query($sql) && db_affected_rows()) ? true : false;
    }

    //set staff ID...assign/unassign/release (staff id can be 0)
    function setStaffId($staffId) {
        $sql = 'UPDATE ' . TICKET_TABLE . ' SET staff_id=' . db_input($staffId) . ' WHERE ticket_id=' . db_input($this->getId());
        return (db_query($sql) && db_affected_rows()) ? true : false;
    }

    //Status helper.
    function setStatus($status) {

        if (strcasecmp($this->getStatus(), $status) == 0)
            return true; //No changes needed.

        switch (strtolower($status)):
            case 'reopen':
            case 'open':
                return $this->reopen();
                break;
            case 'close':
                return $this->close();
                break;
        endswitch;

        return false;
    }

    function setAnswerState($isanswered) {
        db_query('UPDATE ' . TICKET_TABLE . ' SET isanswered=' . db_input($isanswered) . ' WHERE ticket_id=' . db_input($this->getId()));
    }

    //Close the ticket
    function close($sla_claim = '') {
        global $thisuser, $cfg;
        $sla_claim = Format::striptags($sla_claim);
        $sql = 'UPDATE ' . TICKET_TABLE . ' SET status=' . db_input('closed') . ',sla_claim=' . db_input($sla_claim) . ',staff_id=0,isoverdue=0,duedate=NULL,updated=' . db_input($this->get_now()) . ',closed=' . db_input($this->get_now()) .
                ' WHERE ticket_id=' . db_input($this->getId());
        if (db_query($sql) && db_affected_rows()) {
            if (file_exists(TEMPLATE_DIR . 'email.ticket-close.tpl.html')) {
                $body = file_get_contents(TEMPLATE_DIR . 'email.ticket-close.tpl.html');
                $subj = 'Update - Ticket: %ticket - %name - %cin - %subject';

                $subj = $this->renderTemplate($subj);

                $body_data = array(
                    '%tt_close_title' => 'Ticket closed by ' . $thisuser->getName(),
                    '%message' => '',
                    '%signature'=>$thisuser->getSignatureForTemplate()
                );
                $body = $this->renderTemplate($body, $body_data);

                $email = $cfg->getDefaultEmail();

                if ($email) {
                    //Reply separator tag.
                    if ($cfg->stripQuotedReply() && ($tag = $cfg->getReplySeparator()))
                        $body = "\n$tag\n\n" . $body;

                    $cc = array();
                    $noc_mail = Email::getNOCmail();
                    $cc[] = $noc_mail;
                    $cc[] = $this->get_raiser_email();
                    $alt_mails = explode(',', $this->getAltEmail());
                    $cc = array_merge($cc, $alt_mails);
                    $cc = array_unique($cc);

                    $recipients = $cc;
                    $recipients[] = $this->getEmail();
                    $recipients = array_unique($recipients);
                    $recipients = implode(',', $recipients);

                    $to_header = $this->getEmail()?$this->getEmail():$this->getClient()->getEmail();
                    $from_header = '';//from noc
                    $attachments = null;
                    
                    /*
                      //debug
                      echo '<pre>';
                      print_r($cc);
                      echo '</pre>';
                      echo '<pre>';
                      print_r($recipients);
                      echo '</pre>';
                      echo '<pre>';
                      echo 'to:'.$to_header;
                      echo '<br>';
                      echo 'from: '.$from_header;
                      echo '</pre>';
                      echo $subj;
                      echo '<br>';
                      echo $body;
                      exit;
                      //debug
                     */

                    $email->send($recipients, $subj, $body, $attachments, $cc, $from_header, $to_header);
                }
            }
            return true;
        } else {
            return false;
        }
    }

    //set status to open on a closed ticket.
    function reopen($isanswered = 0) { //TODO: send email notifications
        global $thisuser;
        $sql = 'UPDATE ' . TICKET_TABLE . ' SET status=' . db_input('open') . ',isanswered=0,updated=' . db_input($this->get_now()) . ',reopened=' . db_input($this->get_now()) . ' WHERE ticket_id=' . db_input($this->getId());
        return (db_query($sql) && db_affected_rows()) ? true : false;
    }

    //TODO: Move alerts here (need PHP 5 for protected fnc)...and add stats collection...for now we are simply doing house cleaning and syncs
    function onResponse() {
        db_query('UPDATE ' . TICKET_TABLE . ' SET isanswered=1,lastresponse=' . db_input($this->get_now()) . ', updated=' . db_input($this->get_now()) . ' WHERE ticket_id=' . db_input($this->getId()));
    }

    function onMessage() {
        db_query('UPDATE ' . TICKET_TABLE . ' SET isanswered=0,lastmessage=' . db_input($this->get_now()) . ' WHERE ticket_id=' . db_input($this->getId()));
    }

    function onNote() {
        
    }

    function onOverdue() {
        
    }

    //Replace base variables.
    function replaceTemplateVars($text) {
        global $cfg;

        $dept = $this->getDept();
        $staff = $this->getStaff();

        $search = array('/%cin/', '/%id/', '/%ticket/', '/%email/', '/%name/', '/%employee_name/', '/%subject/', '/%topic/', '/%mobile/', '/%status/', '/%priority/',
            '/%dept/', '/%assigned_staff/', '/%createdate/', '/%duedate/', '/%closedate/', '/%url/');
        $replace = array($this->getCIN()->get_cin_value(),
            $this->getId(),
            $this->getExtId(),
            $this->getEmail(),
            $this->getClientName(),
            $this->getClientEmployeeName(),
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

    function renderTemplate($text, $replace = false) { //you can provide template variables data. TODO: implement it
        global $thisuser;
        if ($text) {

            if (is_array($replace) && !empty($replace)) { //provided template fields will have first preference
                foreach ($replace as $variable => $value) {
                    if (stripos($text, $variable) != FALSE) {
                        $text = str_replace($variable, $value, $text);
                    }
                }
            }
            //$signature = $this->getStaff()->getSignatureForTemplate();
            //$signature = str_replace("\n", "<br>", $signature);
            /*
              if (stripos($text, '%signature') != FALSE) {
              $text = str_replace('%signature', $signature, $text);
              }
             */
             
            if (stripos($text, '%ticket') != FALSE) {
                $text = str_replace('%ticket', $this->getExtId(), $text);
            }
            if (stripos($text, '%name') != FALSE) { //client name
                $text = str_replace('%name', $this->getName(), $text);
            }
            if (stripos($text, '%subject') != FALSE) {
                $text = str_replace('%subject', $this->getSubject(), $text);
            }
            if (stripos($text, '%cin') != FALSE) {
                if ($this->getCIN()->get_cin_value()) {
                    $text = str_replace('%cin', 'CIN:' . $this->getCIN()->get_cin_value(), $text);
                } else {
                    $text = str_replace('%cin', 'CIN: N/A', $text);
                }
            }
            if (stripos($text, '%raiser_info') != FALSE) {
                if ($this->has_raiser_info()) {
                    if (file_exists(TEMPLATE_DIR . 'raiser_info.html')) {
                        $raiser_info = file_get_contents(TEMPLATE_DIR . 'raiser_info.html');
                        $text = str_replace('%raiser_info', $raiser_info, $text);
                    } else {
                        $text = str_replace('%raiser_info', 'N/A', $text);
                    }
                    $text = str_replace('%raised_from', $this->get_raised_from(), $text);
                    $text = str_replace('%raiser_name', $this->get_raiser_name(), $text);
                    $text = str_replace('%raiser_email', $this->get_raiser_email(), $text);
                    $text = str_replace('%raiser_phone', $this->get_raiser_phone(), $text);
                } else { //remove
                    $text = str_replace('%raiser_info', '', $text);
                }
            }
            if (stripos($text, '%site_info') != FALSE) {
                if ($this->has_site_info()) {
                    if (file_exists(TEMPLATE_DIR . 'site_info.html')) {
                        $raiser_info = file_get_contents(TEMPLATE_DIR . 'site_info.html');
                        $text = str_replace('%site_info', $raiser_info, $text);
                    } else {
                        $text = str_replace('%site_info', 'N/A', $text);
                    }
                    $text = str_replace('%site_visited_yes_no', 'yes', $text);
                    $text = str_replace('%site_visited_by', $this->get_site_visited_by(), $text);
                    $text = str_replace('%site_visited_date', $this->get_site_visited_date(), $text);
                    ;
                } else { //remove
                    $text = str_replace('%site_info', '', $text);
                }
            }
            if (stripos($text, '%link_info') != FALSE) {
                if ($this->has_link_info()) {
                    if (file_exists(TEMPLATE_DIR . 'link_info.html')) {
                        $raiser_info = file_get_contents(TEMPLATE_DIR . 'link_info.html');
                        $text = str_replace('%link_info', $raiser_info, $text);
                    } else {
                        $text = str_replace('%link_info', 'N/A', $text);
                    }
                    $text = str_replace('%link_down_date', $this->get_link_down_date(), $text);
                    $text = str_replace('%restoration_date', $this->get_link_restoration_date(), $text);
                    ;
                    $text = str_replace('%downtime_duration', $this->get_downtime_duration() . ' minutes', $text);
                    ;
                    $text = str_replace('%restoration_done_by', $this->get_restoration_done_by(), $text);
                    ;
                    $text = str_replace('%restoration_confirmed_by', $this->get_restoration_confirmed_by(), $text);
                    ;
                } else { //remove
                    $text = str_replace('%link_info', '', $text);
                }
            }
            if (stripos($text, '%sla_info') != FALSE) {
                if ($this->has_site_info()) {
                    if (file_exists(TEMPLATE_DIR . 'sla_info.html')) {
                        $raiser_info = file_get_contents(TEMPLATE_DIR . 'sla_info.html');
                        $text = str_replace('%sla_info', $raiser_info, $text);
                    } else {
                        $text = str_replace('%sla_info', '', $text);
                    }
                    $text = str_replace('%sla_claim_yes_no', 'yes', $text);
                    $text = str_replace('%sla_claim_cause', $this->get_sla_cause(), $text);
                    $text = str_replace('%sla_claim_duration', $this->get_sla_duration() . ' minutes', $text);
                    ;
                } else { //remove
                    $text = str_replace('%sla_info', '', $text);
                }
            }
            
            if (stripos($text, '%email') != FALSE) {
                $text = str_replace('%email', $this->getEmail()?$this->getEmail():$this->getClient()->getEmail(), $text);
            }
            if (stripos($text, '%phone') != FALSE) {
                $text = str_replace('%phone', $this->getPhone(), $text);
            }
            $text = str_replace('%today', date('D, d M Y h:i:s a'), $text);
            if (stripos($text, '%ticket') != FALSE) {
                $text = str_replace('%ticket', $this->getId(), $text);
            }
            if (stripos($text, '%service_type') != FALSE) {
                $text = str_replace('%service_type', $this->getCIN()->get_service_type(), $text);
            }
            if (stripos($text, '%circuit_type') != FALSE) {
                $text = str_replace('%circuit_type', $this->getCIN()->get_circuit_type(), $text);
            }
            if (stripos($text, '%from_location') != FALSE) {
                $text = str_replace('%from_location', $this->get_from_location(), $text);
            }
            if (stripos($text, '%to_location') != FALSE) {
                $text = str_replace('%to_location', $this->get_to_location(), $text);
            }
            if (stripos($text, '%root_cause') != FALSE) {
                $text = str_replace('%root_cause', $this->get_root_cause(), $text);
            }
            if (stripos($text, '%last_message') != FALSE) {
                $text = str_replace('%last_message', $this->getLastMessage(), $text);
            }
            if (stripos($text, '%last_response') != FALSE) {
                $text = str_replace('%last_response', $this->getLastResponse(), $text);
            }
            if (stripos($text, '%last_note') != FALSE) {
                $text = str_replace('%last_note', $this->getLastNote(), $text);
            }
            if (stripos($text, '%assignee_name') != FALSE) {
                $text = str_replace('%assignee_name', $this->getAssignee()->getName(), $text);
            }
            if (stripos($text, '%last_respondent_name') != FALSE) {
                $text = str_replace('%last_respondent_name', $this->getLastRespondent()->getName(), $text);
            }
            if (stripos($text, '%last_note_poster') != FALSE) {
                $text = str_replace('%last_note_poster', $this->getLastNotePoster()->getName(), $text);
            }
            if (stripos($text, '%last_msg_poster') != FALSE) {
                $text = str_replace('%last_msg_poster', $this->getLastMsgPoster()->getName(), $text);
            }
            if (stripos($text, '%last_response_poster') != FALSE) {
                $text = str_replace('%last_response_poster', $this->getLastRespondent()->getName(), $text);
            }
            if (stripos($text, '%last_edited_by') != FALSE) {
                $text = str_replace('%last_edited_by', $this->getLastEditor()->getName(), $text);
            }
            if (stripos($text, '%closed_by') != FALSE) {
                $text = str_replace('%closed_by', $this->getCloser()->getName(), $text);
            }
            if (stripos($text, '%deleted_by') != FALSE) {
                $text = str_replace('%deleted_by', $this->getDeleter()->getName(), $text);
            }
            if (stripos($text, '%tt_creator_signature') != FALSE) {
                $text = str_replace('%tt_creator_signature', $this->getTTCreator()->getSignature(), $text);
            }
            if (stripos($text, '%conversations') != FALSE) {
                $text = str_replace('%conversations', $this->getConversations(), $text);
            }
            if (stripos($text, '%notes') != FALSE) {
                $text = str_replace('%notes', $this->getNotes(), $text);
            }

            return $text;
        }
    }

    function getCIN() {
        return new cin($this->getCINValue(), $this->getClientId());
    }

    function get_to_location() {
        
    }

    function get_from_location() {
        
    }

    function get_root_cause() {
        return $this->row['root_cause'];
    }

    function isNOCTT() {
        $sql = 'SELECT * FROM ' . TICKET_TABLE . ' WHERE ticket_id=' . db_input($this->getId());
        if (($res = db_query($sql)) && db_num_rows($res)) {
            $row = db_fetch_row($res);
            if (($row['created_by_noc'] == 1) || $this->has_raiser_info()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function getTTCreator() {
        $sql = 'SELECT tt_creator FROM ' . TICKET_TABLE . ' WHERE ticket_id=' . db_input($this->getId());
        if (($res = db_query($sql)) && db_num_rows($res)) {
            $row = db_fetch_row($res);
            $creator_id = $row['tt_creator'];
            if ($this->isNOCTT()) {
                $staff = new Staff($creator_id);
                return $staff;
            } else {
                $client = new Client($creator_id);
                return $client;
            }
        } else {
            return false;
        }
    }

    function getLastMessage() {
        $sql = 'SELECT message FROM ' . TICKET_MESSAGE_TABLE . ' WHERE ticket_id=' . db_input($this->getId()) . ' ORDER BY created LIMIT 1';
        if (($res = db_query($sql)) && db_num_rows($res)) {
            $row = db_fetch_row($res);
            return $row['message'];
        } else {
            return false;
        }
    }

    function getLastMsgPoster() {
        $sql = 'SELECT client_id FROM ' . TICKET_MESSAGE_TABLE . ' WHERE ticket_id=' . db_input($this->getId()) . ' ORDER BY created LIMIT 1';
        if (($res = db_query($sql)) && db_num_rows($res)) {
            $row = db_fetch_row($res);
            $client = new Client($row['client_id']);
            return $client;
        } else {
            return false;
        }
    }

    function getLastResponse() {
        $sql = 'SELECT message FROM ' . TICKET_RESPONSE_TABLE . ' WHERE ticket_id=' . db_input($this->getId()) . ' ORDER BY created LIMIT 1';
        if (($res = db_query($sql)) && db_num_rows($res)) {
            $row = db_fetch_row($res);
            return $row['response'];
        } else {
            return false;
        }
    }

    function getLastResponsePoster() {
        $sql = 'SELECT staff_id FROM ' . TICKET_RESPONSE_TABLE . ' WHERE ticket_id=' . db_input($this->getId()) . ' ORDER BY created LIMIT 1';
        if (($res = db_query($sql)) && db_num_rows($res)) {
            $row = db_fetch_row($res);
            $staff = new Staff($row['staff_id']);
        } else {
            return false;
        }
    }

    function getLastNote() {
        $sql = 'SELECT message FROM ' . TICKET_NOTE_TABLE . ' WHERE ticket_id=' . db_input($this->getId()) . ' ORDER BY created LIMIT 1';
        if (($res = db_query($sql)) && db_num_rows($res)) {
            $row = db_fetch_row($res);
            return $row['note'];
        } else {
            return false;
        }
    }

    function getLastNotePoster() {
        $sql = 'SELECT staff_id FROM ' . TICKET_NOTE_TABLE . ' WHERE ticket_id=' . db_input($this->getId()) . ' ORDER BY created LIMIT 1';
        if (($res = db_query($sql)) && db_num_rows($res)) {
            $row = db_fetch_row($res);
            $staff = new Staff($row['staff_id']);
        } else {
            return false;
        }
    }

    function getLastEditor() {
        if ($this->row['last_editor']) {
            $staff = new Staff($row['last_editor']);
        } else {
            return false;
        }
    }

    function getCloser() {
        if ($this->row['closer']) {
            $staff = new Staff($row['closer']);
        } else {
            return false;
        }
    }

    function getDeleter() {
        if ($this->row['deleter']) {
            $staff = new Staff($row['deleter']);
        } else {
            return false;
        }
    }

    function getConversations() {
        //now appending all previous conversations
        //baby dont look below, you will be lost in those inline styles
        $msg_style = 'style="background-color: #C3D9FF;font-size: 12px;font-weight: bold;line-height: 24px;padding: 1px 1px 1px 5px;text-align: left;border-bottom: 1px solid;color: #3E3E3E;text-decoration: none;"';
        $resp_style = 'style="background-color: #FFE0B3;font-size: 12px;font-weight: bold;line-height: 24px;padding: 1px 1px 1px 5px;text-align: left;border-bottom: 1px solid;color: #3E3E3E;text-decoration: none;"';
        
        $html = '<h3 align="center">All conversations(message(client) and response(NOC))</h3>';
        $html .= '<div style="margin-left: auto; margin-right: auto;">';
        //get messages
        $sql = 
            'SELECT msg.msg_id as msg_id,msg.created as created,msg.message as message,msg.source as source,TRUE as ismessage FROM ' . TICKET_MESSAGE_TABLE .' msg WHERE  msg.ticket_id=' . db_input($this->getId()) 
            . ' UNION ALL ' . 
            'SELECT resp.msg_id as msg_id,resp.created as created,resp.response as message,resp.staff_name as source,NULL as ismessage FROM ' . TICKET_RESPONSE_TABLE . ' resp  WHERE  resp.ticket_id=' . db_input($this->getId()) . ' ORDER BY created DESC';
        $msgres = db_query($sql);
        while ($msg_row = db_fetch_array($msgres)) {
            $style = $msg_row['ismessage']?$msg_style:$resp_style;
            $html .= '<table align="center" cellspacing="0" cellpadding="1" width="100%" border=0 style="margin: 10px 0 5px;-moz-border-bottom-colors: none;-moz-border-left-colors: none;-moz-border-right-colors: none;-moz-border-top-colors: none;border-color: #ADADAD; border-image: none; border-style: solid solid none;border-width: 1px 1px medium;"><tr><th '.$style.'>' . Format::db_daydatetime($msg_row['created']) . ' from ' . $msg_row['source'] . '</th></tr><tr><td style="background-color: #FAFAFA;border-bottom: 1px solid;padding: 5px;color: #3E3E3E;font-size: 12px;text-decoration: none;">' . Format::display($msg_row['message']) . '&nbsp;</td></tr></table>';
            /*
            //get answers for messages
            $sql = 'SELECT resp.*,count(attach_id) as attachments FROM ' . TICKET_RESPONSE_TABLE . ' resp ' .
                    ' LEFT JOIN ' . TICKET_ATTACHMENT_TABLE . " attach ON  resp.ticket_id=attach.ticket_id AND resp.response_id=attach.ref_id AND ref_type='R' " .
                    ' WHERE msg_id=' . db_input($msg_row['msg_id']) . ' AND resp.ticket_id=' . db_input($this->getId()) .
                    ' GROUP BY resp.response_id ORDER BY created';
            $resp = db_query($sql);
            while ($resp_row = db_fetch_array($resp)) {
                $html .= '<table align="center" cellspacing="0" cellpadding="1" width="100%" border=0 style="margin: 10px 0 5px;-moz-border-bottom-colors: none;-moz-border-left-colors: none;-moz-border-right-colors: none;-moz-border-top-colors: none;border-color: #ADADAD; border-image: none; border-style: solid solid none;border-width: 1px 1px medium;"><tr><th style="background-color: #FFE0B3;font-size: 12px;font-weight: bold;line-height: 24px;padding: 1px 1px 1px 5px;text-align: left;border-bottom: 1px solid;color: #3E3E3E;text-decoration: none;">' . Format::db_daydatetime($resp_row['created']) . '&nbsp;-&nbsp;' . $resp_row['staff_name'] . '</th></tr><tr><td style="background-color: #FAFAFA;border-bottom: 1px solid;padding: 5px;color: #3E3E3E;font-size: 12px;text-decoration: none;">' . Format::display($resp_row['response']) . '</td></tr></table>';
            }
            */
        }
        $html .= '</div>';
        return $html;
    }

    function getNotes() {
        //baby dont look below, you will be lost in those inline styles
        $html = '<h3 align="center">Internal notes</h3>';
        $html .= '<div style="margin-left: auto; margin-right: auto;">';
        //get messages
        $sql = 'SELECT * FROM ' . TICKET_NOTE_TABLE . ' note WHERE  note.ticket_id=' . db_input($this->getId()) .
                ' GROUP BY note.note_id ORDER BY created DESC';
        $noteres = db_query($sql);
        while ($note_row = db_fetch_array($noteres)) {
            $html .= '<table align="center" cellspacing="0" cellpadding="1" width="100%" border=0 style="margin: 10px 0 5px;-moz-border-bottom-colors: none;-moz-border-left-colors: none;-moz-border-right-colors: none;-moz-border-top-colors: none;border-color: #ADADAD; border-image: none; border-style: solid solid none;border-width: 1px 1px medium;"><tr><th style="background-color: #C3D9FF;font-size: 12px;font-weight: bold;line-height: 24px;padding: 1px 1px 1px 5px;text-align: left;border-bottom: 1px solid;color: #3E3E3E;text-decoration: none;">' . Format::db_daydatetime($note_row['created']) . ' from ' . $note_row['source'] .'</th></tr><tr><td style="background-color: #FAFAFA;border-bottom: 1px solid;padding: 5px;color: #3E3E3E;font-size: 12px;text-decoration: none;">' .'Title: '. Format::display($note_row['title']) . '</td></tr><tr><td style="background-color: #FAFAFA;border-bottom: 1px solid;padding: 5px;color: #3E3E3E;font-size: 12px;text-decoration: none;">' . 'Note: '. Format::display($note_row['note']) . '</td></tr></table>';
        }
        $html .= '</div>';
        return $html;
    }

    function markUnAnswered() {
        $this->setAnswerState(0);
    }

    function markAnswered() {
        $this->setAnswerState(1);
    }

    function markOverdue($bitch = false) {
        global $cfg;

        if ($this->isOverdue())
            return true;

        $sql = 'UPDATE ' . TICKET_TABLE . ' SET isoverdue=1,updated=NOW() WHERE ticket_id=' . db_input($this->getId());
        if (db_query($sql) && db_affected_rows()) {
            //  echo   $sql;
            $dept = $this->getDept();

            if (!$dept || !($tplId = $dept->getTemplateId()))
                $tplId = $cfg->getDefaultTemplateId();

            //if requested && enabled fire nasty alerts.
            if ($bitch && $cfg->alertONOverdueTicket()) {
                $sql = 'SELECT ticket_overdue_subj,ticket_overdue_body FROM ' . EMAIL_TEMPLATE_TABLE .
                        ' WHERE cfg_id=' . db_input($cfg->getId()) . ' AND tpl_id=' . db_input($tplId);
                if (($resp = db_query($sql)) && db_num_rows($resp) && list($subj, $body) = db_fetch_row($resp)) {

                    $body = $this->replaceTemplateVars($body);
                    $subj = $this->replaceTemplateVars($subj);

                    if (!($email = $cfg->getAlertEmail()))
                        $email = $cfg->getDefaultEmail();

                    if ($email && $email->getId()) {
                        //Fire and email to admin. No questions asked.
                        $alert = str_replace("%staff", 'Admin', $body);
                        $email->send($cfg->getAdminEmail(), $subj, $alert);

                        /*                         * * Build list of recipients and fire the alerts ** */
                        $recipients = array();
                        //Assigned staff... if any
                        if ($this->isAssigned() && $cfg->alertAssignedONOverdueTicket()) {
                            $recipients[] = $this->getStaff();
                        } elseif ($cfg->alertDeptMembersONOverdueTicket()) { //Alert assigned or dept members not both
                            //All dept members.
                            $sql = 'SELECT staff_id FROM ' . STAFF_TABLE . ' WHERE dept_id=' . db_input($dept->getId());
                            if (($users = db_query($sql)) && db_num_rows($users)) {
                                while (list($id) = db_fetch_row($users))
                                    $recipients[] = new Staff($id);     //possible mem issues with a large number of staff?
                            }
                        }
                        //Always blame the manager
                        if ($cfg->alertDeptManagerONOverdueTicket() && $dept) {
                            $recipients[] = $dept->getManager();
                        }
                        //Ok...we are ready to go....
                        $sentlist = array();
                        foreach ($recipients as $k => $staff) {
                            if (!$staff || !is_object($staff) || !$staff->isAvailable())
                                continue;
                            if (in_array($staff->getEmail(), $sentlist))
                                continue; //avoid duplicate emails.
                            $alert = str_replace("%staff", $staff->getFirstName(), $body);
                            $email->send($staff->getEmail(), $subj, $alert);
                        }
                    }
                }else {
                    Sys::log(LOG_WARNING, 'Template Fetch Error', "Unable to fetch 'overdue' alert template #$tplId");
                }
            }
            return true;
        }
        return false;
    }

    //Dept Tranfer...with alert..
    function transfer($deptId) {
        global $cfg;
        /*
          TODO:
          1) Figure out what to do when ticket is assigned
          Is the assignee allowed to access target dept?  (At the moment assignee will have access to the ticket anyways regardless of Dept)
          2) Send alerts to new Dept manager/members??
          3) Other crap I don't have time to think about at the moment.
         */
        return $this->setDeptId($deptId) ? true : false;
    }

    //Assign: staff
    function assignStaff($staffId, $message, $alertstaff = true) {
        global $thisuser, $cfg;

        $message = $message ? $message : 'Ticket assigned';

        $staff = new Staff($staffId);
        if (!$staff || !$staff->isAvailable() || !$thisuser)
            return false;

        if ($this->setStaffId($staff->getId())) {
            //Reopen the ticket if cloed.
            if ($this->isClosed()) //Assigned ticket Must be open.
                $this->reopen();
            $this->reload(); //
            //Send Notice + Message to assignee. (if directed)
            //notification email
            //taking email templates from file
            if (file_exists(TEMPLATE_DIR . 'email.ticket-assign.tpl.html')) {
                $body = file_get_contents(TEMPLATE_DIR . 'email.ticket-assign.tpl.html');
                $subj = 'Update - Ticket: %ticket - %name - %cin - %subject';

                $subj_data = array(
                    '%name' => $this->getClient()->getName()
                );
                $subj = $this->renderTemplate($subj, $subj_data);

                $assigner = $thisuser;
                $assignee = new Staff($staffId);
                $assignee_name = $assignee ? $assignee->getName() : 'Error:';
                $body_data = array(
                    '%assigning_title' => 'Ticket assigned to ' . $assignee_name . ' by ' . $thisuser->getName(),
                    '%assigning_message' => $message,
                    '%assigner_signature' => $assigner->getSignatureForTemplate(),
                    '%conversations' => $this->getConversations(),
                    '%notes' => $this->getNotes()
                );
                $body = $this->renderTemplate($body, $body_data);

                $email = $cfg->getDefaultEmail();

                if ($email) {
                    //Reply separator tag.
                    if ($cfg->stripQuotedReply() && ($tag = $cfg->getReplySeparator()))
                        $body = "\n$tag\n\n" . $body;

                    $cc = array();
                    $noc_mail = Email::getNOCmail();
                    $cc[] = $this->get_raiser_email();
                    //$cc[] = $this->getEmail();
                    //$alt_mails = explode(',', $this->getAltEmail());
                    $cc = array_unique($cc);

                    $recipients = $cc;
                    $recipients[] = $noc_mail;
                    $recipients = array_unique($recipients);
                    $recipients = implode(',', $recipients);

                    $attachments = null;

                    //$from_header = $assigner->getName() . ' <' . $assigner->getEmail() . '>';
                    $from_header = sprintf('"%s"<%s>',$assigner->getName(),$assigner->getEmail());
                    $to_header = $noc_mail;
                    
                    /*
                      //debug
                      echo '<pre>';
                      print_r($cc);
                      echo '</pre>';
                      echo '<pre>';
                      print_r($recipients);
                      echo '</pre>';
                      echo '<pre>';
                      echo 'From: '.$from_header;
                      echo 'To: '.$to_header;
                      echo '</pre>';
                      echo $subj;
                      echo '<br>';
                      echo $body;
                      exit;
                      //debug
                      */                     
                     
                    $email->send($recipients, $subj, $body, $attachments, $cc, $from_header, $to_header);
                }
            }

            //Save the message as internal note...(record).
            $this->postNote($title, $message, false); //Notice that we are disabling note alerts!
            return true;
        }
        return false;
    }

    //unassign
    function release($note='', $alert=false) {
        global $thisuser;

        if (!$this->isAssigned()) //We can't release what is not assigned buddy!
            return true;

        if ($this->setStaffId(0)) {
            if ($alert && file_exists(TEMPLATE_DIR . 'email.ticket-release.tpl.html')) {
                $body = file_get_contents(TEMPLATE_DIR . 'email.ticket-release.tpl.html');
                $subj = 'Update - Ticket: %ticket - %name - %cin - %subject';

                $subj_data = array(
                    '%name' => $this->getClient()->getName()
                );
                $subj = $this->renderTemplate($subj, $subj_data);

                $assignee = $this->getAssignee();
                $body_data = array(
                    '%release_title' => 'Ticket unassigned from ' . $assignee->getName() . ' by ' . $thisuser->getName(),
                    '%releaser_signature' => $thisuser->getSignatureForTemplate(),
                    '%conversations' => $this->getConversations(),
                    '%notes' => $this->getNotes()
                );
                $body = $this->renderTemplate($body, $body_data);

                $email = $cfg->getDefaultEmail();

                if ($email) {
                    //Reply separator tag.
                    if ($cfg->stripQuotedReply() && ($tag = $cfg->getReplySeparator()))
                        $body = "\n$tag\n\n" . $body;

                    $cc = array();
                    $noc_mail = Email::getNOCmail();
                    $cc[] = $thisuser->getEmail(); //releaser email

                    $recipients = $cc;
                    $recipients[] = $noc_mail;
                    $recipients = array_unique($recipients);
                    $recipients = implode(',', $recipients);

                    $from_header = $thisuser->getName() . ' <' . $thisuser->getEmail() . '>';
                    $from_header = null;

                    $attachments = null;

                    $from_header = $assigner->getName() . ' <' . $assigner->getEmail() . '>';
                    $to_header = $noc_mail;
                    
                    
                    //debug
                    /*
                    echo '<pre>';
                    print_r($cc);
                    echo '</pre>';
                    echo '<pre>';
                    print_r($recipients);
                    echo '</pre>';
                    echo '<pre>';
                    echo 'From: '.$from_header;
                    echo 'To: '.$to_header;
                    echo '</pre>';
                    echo $subj;
                    echo '<br>';
                    echo $body;
                    exit;
                    //debug
                    */
                    
                    
                    $email->send($recipients, $subj, $body, $attachments, $cc, $from_header, $to_header);
                }
            }
            return true;
        } else {
            return false;
        }
    }

    //Insert message from client
    function postMessage($msg, $source = '', $msgid = NULL, $headers = '', $newticket = false, $suppress_alert = false) {
        global $cfg, $thisuser;

        if (!$this->getId())
            return 0;

        //We don't really care much about the source at message level
        $source = $source ? $source : $_SERVER['REMOTE_ADDR'];

        $sql = 'INSERT INTO ' . TICKET_MESSAGE_TABLE . ' SET created=' . db_input($this->get_now()) .
                ',ticket_id=' . db_input($this->getId()) .
                ',messageId=' . db_input($msgid) .
                ',message=' . db_input(Format::striptags($msg)) . //Tags/code stripped...meaning client can not send in code..etc
                ',headers=' . db_input($headers) . //Raw header.
                ',source=' . db_input($thisuser->getName()) .
                ',ip_address=' . db_input($_SERVER['REMOTE_ADDR']);

        if (db_query($sql) && ($msgid = db_insert_id())) {
            $this->setLastMsgId($msgid);
            $this->onMessage();
            if (!$newticket) {
                //Success and the message is being appended to previously opened ticket.
                //Alerts for new tickets are sent on create.
                $dept = $this->getDept();
                //Reopen if the status is closed...
                if (!$this->isOpen()) {
                    $this->reopen();
                    //If enabled..auto-assign the ticket to last respondent...if they still have access to the Dept.
                    if ($cfg->autoAssignReopenedTickets() && ($lastrep = $this->getLastRespondent())) {
                        //3 months elapsed time limit on auto-assign. Must be available and have access to Dept.
                        if ($lastrep->isAvailable() && $lastrep->canAccessDept($this->getDeptId()) && (time() - strtotime($this->getLastResponseDate())) <= 90 * 24 * 3600) {
                            $this->setStaffId($lastrep->getId()); //Direct Re-assign!!!!????
                        }
                        //TODO: Worry about availability...may be lastlogin also? send an alert??
                    }
                }

                //get the template ID
                if (!$dept || !($tplId = $dept->getTemplateId()))
                    $tplId = $cfg->getDefaultTemplateId();

                /*
                  $autorespond = true; //if anabled.
                  //See if the incoming email is local - no autoresponse.
                  if (Email::getIdByEmail($this->getEmail())) //Loop control---mainly for emailed tickets.
                  $autorespond = false;
                  elseif (strpos(strtolower($var['email']), 'mailer-daemon@') !== false || strpos(strtolower($var['email']), 'postmaster@') !== false)
                  $autorespond = false;
                 */

                //notification email
                if (!$suppress_alert) {
                    //taking email templates from file
                    if (file_exists(TEMPLATE_DIR . 'email.ticket-msg.tpl.html')) {
                        $body = file_get_contents(TEMPLATE_DIR . 'email.ticket-msg.tpl.html');
                        $subj = 'Update - Ticket: %ticket - %name - %cin - %subject';

                        $subj = $this->renderTemplate($subj);

                        $body_data = array(
                            '%message' => $msg
                        );
                        $body = $this->renderTemplate($body, $body_data);

                        $email = $cfg->getDefaultEmail();

                        if ($email) {
                            //Reply separator tag.
                            if ($cfg->stripQuotedReply() && ($tag = $cfg->getReplySeparator()))
                                $body = "\n$tag\n\n" . $body;

                            $noc_mail = Email::getNOCmail();

                            $attachments = null;
                            $from_header = sprintf('"%s"<%s>',$this->getClient()->getName(),$this->getClient()->getEmail());
                            $to_header = $noc_mail;

                            $more_cc = array();
                            $more_cc = explode(',', $this->getAltEmail());
                            $more_cc[] = $this->getEmail();
                            $more_cc[] = $this->getClient()->getEmail();
                            $more_cc[] = $this->get_raiser_email();
                            $more_cc = array_unique($more_cc);

                            $recipients = $more_cc;
                            $recipients[] = $noc_mail;
                            $recipients = array_unique($recipients);
                            $recipients = implode(',', $recipients);
                            
                            /*
                            //debug
                            echo '<pre>';
                            print_r($cc);
                            echo '</pre>';
                            echo '<pre>';
                            print_r($recipients);
                            echo '</pre>';
                            echo '<pre>';
                            echo 'From: '.$from_header;
                            echo 'To: '.$to_header;
                            echo '</pre>';
                            echo $subj;
                            echo '<br>';
                            echo $body;
                            exit;
                            //debug
                            */

                            $email->send($recipients, $subj, $body, $attachments, $more_cc, $from_header, $to_header);
                        }
                    }
                }
            }
        }
        return $msgid;
    }

    //Insert Staff Reply
    function postResponse($msgid, $response, $signature = 'none', $attachment = false, $alert=true) {
        global $thisuser, $cfg;

        if (!$thisuser || !$thisuser->getId() || !$thisuser->isStaff()) //just incase
            return 0;


        $sql = 'INSERT INTO ' . TICKET_RESPONSE_TABLE . ' SET created=' . db_input($this->get_now()) .
                ',ticket_id=' . db_input($this->getId()) .
                ',msg_id=' . db_input($msgid) .
                ',response=' . db_input(Format::striptags($response)) .
                ',staff_id=' . db_input($thisuser->getId()) .
                ',staff_name=' . db_input($thisuser->getName()) .
                ',ip_address=' . db_input($thisuser->getIP());
        $resp_id = 0;
        if (db_query($sql) && ($resp_id = db_insert_id())) {
            $this->onResponse(); //do house cleaning..

            $dept = $this->getDept();

            //notification email
            //taking email templates from file
            if ($alert && file_exists(TEMPLATE_DIR . 'email.ticket-resp.tpl.html')) {
                $body = file_get_contents(TEMPLATE_DIR . 'email.ticket-resp.tpl.html');
                $subj = 'Update - Ticket: %ticket - %name - %cin - %subject';

                $subj = $this->renderTemplate($subj);

                $body_data = array(
                    '%message' => $response,
                    '%signature' => $thisuser->getSignatureForTemplate()
                );
                $body = $this->renderTemplate($body, $body_data);

                $email = $cfg->getDefaultEmail();

                if ($email) {
                    //Reply separator tag.
                    if ($cfg->stripQuotedReply() && ($tag = $cfg->getReplySeparator()))
                        $body = "\n$tag\n\n" . $body;

                    $noc_mail = Email::getNOCmail();

                    $attachments = null;
                    $from_header = null;
                    $to_header = $this->getEmail();

                    $more_cc = array();
                    $more_cc = explode(',', $this->getAltEmail());
                    $more_cc[] = $noc_mail;
                    $more_cc[] = $this->getEmail();
                    if ($this->get_raiser_email())
                        $more_cc[] = $this->get_raiser_email();
                    //$more_cc[] = $this->getEmail();
                    $more_cc = array_unique($more_cc);

                    $recipients = $more_cc;
                    $recipients[] = $this->getEmail();
                    $recipients[] = $noc_mail;
                    $recipients = array_unique($recipients);
                    $recipients = implode(',', $recipients);

                    /*
                      //debug
                      echo '<pre>';
                      print_r($more_cc);
                      echo '</pre>';
                      echo '<pre>';
                      print_r($recipients);
                      echo '</pre>';
                      echo '<pre>';
                      echo $to_header;
                      echo '</pre>';
                      echo $subj;
                      echo '<br>';
                      echo $body;
                      exit;
                      //debug
                     */

                    $email->send($recipients, $subj, $body, $attachments, $more_cc, $from_header, $to_header);
                }
            }
            return $resp_id;
        }

        return 0;
    }

    //Activity log - saved as internal notes WHEN enabled!!
    function logActivity($title, $note) {
        global $cfg;

        if (!$cfg || !$cfg->logTicketActivity())
            return 0;

        return $this->postNote($title, $note, false, 'system');
    }

    //Insert Internal Notes
    function postNote($title, $note, $alert = true, $poster = '') {
        global $thisuser, $cfg;

        $sql = 'INSERT INTO ' . TICKET_NOTE_TABLE . ' SET created=' . db_input($this->get_now()) .
                ',ticket_id=' . db_input($this->getId()) .
                ',title=' . db_input(Format::striptags($title)) .
                ',note=' . db_input(Format::striptags($note)) .
                ',staff_id=' . db_input($thisuser ? $thisuser->getId() : 0) .
                ',source=' . db_input($thisuser->getName());
        //  echo   $sql;
        if (db_query($sql) && ($id = db_insert_id())) {
            //If enabled...send alert to staff (Internal Note Alert)
            //notification email
            if ($alert) {
                //taking email templates from file
                if (file_exists(TEMPLATE_DIR . 'email.ticket-note.tpl.html')) {
                    $body = file_get_contents(TEMPLATE_DIR . 'email.ticket-note.tpl.html');
                    $subj = 'Update - Ticket: %ticket - %name - %cin - %subject';

                    $subj = $this->renderTemplate($subj);

                    $body_data = array(
                        '%exec_name' => $thisuser->getName(),
                        '%note_title' => $title,
                        '%message' => $note,
                        '%signature' => $thisuser->getSignatureForTemplate()
                    );
                    $body = $this->renderTemplate($body, $body_data);


                    $email = $cfg->getDefaultEmail();

                    if ($email) {
                        //Reply separator tag.
                        if ($cfg->stripQuotedReply() && ($tag = $cfg->getReplySeparator()))
                            $body = "\n$tag\n\n" . $body;

                        $noc_mail = Email::getNOCmail();

                        $attachments = null;
                        $to_header = $noc_mail;
                        $from_header = sprintf('"%s"<%s>',$thisuser->getName(),$thisuser->getEmail());
                        
                        if ( $internal_cc = Format::sp_trim($this->getInternalCc()) ) {
                            $more_cc = explode(',', $internal_cc);
                        }
                        $more_cc[] = $thisuser->getEmail();
                        $more_cc = array_unique($more_cc);

                        $recipients = $more_cc;
                        $recipients[] = $noc_mail;
                        $recipients = array_unique($recipients);
                        $recipients = implode(',', $recipients);

                        //debug
                        /*
                          echo '<pre>';
                          echo 'cc:';
                          print_r($more_cc);
                          echo '</pre>';
                          echo '<pre>';
                          echo 'recp:';
                          print_r($recipients);
                          echo '</pre>';
                          echo '<pre>';
                          echo 'to:';
                          echo $to_header;
                          echo '</pre>';
                          echo '<pre>';
                          echo 'from:';
                          echo $from_header;
                          echo '</pre>';
                          echo $subj;
                          echo '<br>';
                          echo $body;
                          exit;
                         */
                        //debug
                        $email->send($recipients, $subj, $body, $attachments, $more_cc, $from_header, $to_header);
                    }
                }
            }
        }
        return $id;
    }

    //online based attached files.
    function uploadAttachment($file, $refid, $type) {
        global $cfg;

        if (!$file['tmp_name'] || !$refid || !$type)
            return 0;

        $dir = $cfg->getUploadDir();
        $rand = Misc::randCode(16);
        $file['name'] = Format::file_name($file['name']);
        $month = date('my', strtotime($this->getCreateDate()));

        //try creating the directory if it doesn't exists.
        if (!file_exists(rtrim($dir, '/') . '/' . $month) && @mkdir(rtrim($dir, '/') . '/' . $month, 0777))
            chmod(rtrim($dir, '/') . '/' . $month, 0777);

        if (file_exists(rtrim($dir, '/') . '/' . $month) && is_writable(rtrim($dir, '/') . '/' . $month))
            $filename = sprintf("%s/%s/%s_%s", rtrim($dir, '/'), $month, $rand, $file['name']);
        else
            $filename = sprintf("%s/%s_%s", rtrim($dir, '/'), $rand, $file['name']);

        if (move_uploaded_file($file['tmp_name'], $filename)) {
            $sql = 'INSERT INTO ' . TICKET_ATTACHMENT_TABLE . ' SET created=NOW() ' .
                    ',ticket_id=' . db_input($this->getId()) .
                    ',ref_id=' . db_input($refid) .
                    ',ref_type=' . db_input($type) .
                    ',file_size=' . db_input($file['size']) .
                    ',file_name=' . db_input($file['name']) .
                    ',file_key=' . db_input($rand);
            if (db_query($sql) && ($id = db_insert_id()))
                return $id;
            //DB  insert failed!--remove the file..
            @unlink($filename);
        }
        return 0;
    }

    //incoming email or json/xml bases attachments.
    function saveAttachment($name, $data, $refid, $type) {
        global $cfg;

        if (!$refid || !$name || !$data)
            return 0;

        $dir = $cfg->getUploadDir();
        $rand = Misc::randCode(16);
        $name = Format::file_name($name);
        $month = date('my', strtotime($this->getCreateDate()));

        //try creating the directory if it doesn't exists.
        if (!file_exists(rtrim($dir, '/') . '/' . $month) && @mkdir(rtrim($dir, '/') . '/' . $month, 0777))
            chmod(rtrim($dir, '/') . '/' . $month, 0777);

        if (file_exists(rtrim($dir, '/') . '/' . $month) && is_writable(rtrim($dir, '/') . '/' . $month))
            $filename = sprintf("%s/%s/%s_%s", rtrim($dir, '/'), $month, $rand, $name);
        else
            $filename = rtrim($dir, '/') . '/' . $rand . '_' . $name;

        if (($fp = fopen($filename, 'w'))) {
            fwrite($fp, $data);
            fclose($fp);
            $size = @filesize($filename);
            $sql = 'INSERT INTO ' . TICKET_ATTACHMENT_TABLE . ' SET created=NOW() ' .
                    ',ticket_id=' . db_input($this->getId()) .
                    ',ref_id=' . db_input($refid) .
                    ',ref_type=' . db_input($type) .
                    ',file_size=' . db_input($size) .
                    ',file_name=' . db_input($name) .
                    ',file_key=' . db_input($rand);
            if (db_query($sql) && ($id = db_insert_id()))
                return $id;

            @unlink($filename); //insert failed...remove the link.
        }
        return 0;
    }

    function delete($message = '', $alert = true) {
        global $thisuser, $cfg;

        if (db_query('DELETE FROM ' . TICKET_TABLE . ' WHERE ticket_id=' . $this->getId()) && db_affected_rows()) {

            if ($alert && file_exists(TEMPLATE_DIR . 'email.ticket-delete.tpl.html')) {
                $body = file_get_contents(TEMPLATE_DIR . 'email.ticket-delete.tpl.html');
                $subj = 'Update - Ticket: %ticket - %name - %cin - %subject';

                $subj = $this->renderTemplate($subj);

                $body_data = array(
                    '%message' => $message,
                    '%signature'=>$thisuser->getSignatureForTemplate()
                );
                $body = $this->renderTemplate($body, $body_data);


                $email = $cfg->getDefaultEmail();

                if ($email) {
                    //Reply separator tag.
                    if ($cfg->stripQuotedReply() && ($tag = $cfg->getReplySeparator()))
                        $body = "\n$tag\n\n" . $body;

                    $noc_mail = Email::getNOCmail();

                    $attachments = null;
                    $to_header = $noc_mail;
                    $from_header = sprintf('"%s"<%s>',$thisuser->getName(),$thisuser->getEmail());

                    $more_cc = explode(',', $this->getAltEmail());
                    $more_cc = array_unique($more_cc);

                    $recipients = $more_cc;
                    $recipients[] = $noc_mail;
                    $recipients = array_unique($recipients);
                    $recipients = implode(',', $recipients);
                    
                    /*
                      //debug
                      echo '<pre>';
                      print_r($more_cc);
                      echo '</pre>';
                      echo '<pre>';
                      print_r($recipients);
                      echo '</pre>';
                      echo '<pre>';
                      echo $to_header;
                      echo '</pre>';
                      echo $subj;
                      echo '<br>';
                      echo $body;
                      exit;
                      //debug
                     */

                    $email->send($recipients, $subj, $body, $attachments, $more_cc, $from_header, $to_header);
                }
            }

            db_query('DELETE FROM ' . TICKET_MESSAGE_TABLE . ' WHERE ticket_id=' . db_input($this->getId()));
            db_query('DELETE FROM ' . TICKET_RESPONSE_TABLE . ' WHERE ticket_id=' . db_input($this->getId()));
            db_query('DELETE FROM ' . TICKET_NOTE_TABLE . ' WHERE ticket_id=' . db_input($this->getId()));
            $this->deleteAttachments();
            return TRUE;
        }

        return FALSE;
    }

    function fixAttachments() {
        global $cfg;

        $sql = 'SELECT attach_id,file_name,file_key FROM ' . TICKET_ATTACHMENT_TABLE . ' WHERE ticket_id=' . db_input($this->getId());
        $res = db_query($sql);
        if ($res && db_num_rows($res)) {
            $dir = $cfg->getUploadDir();
            $month = date('my', strtotime($this->getCreateDate()));
            while (list($id, $name, $key) = db_fetch_row($res)) {
                $origfilename = sprintf("%s/%s_%s", rtrim($dir, '/'), $key, $name);
                if (!file_exists($origfilename))
                    continue;

                if (!file_exists(rtrim($dir, '/') . '/' . $month) && @mkdir(rtrim($dir, '/') . '/' . $month, 0777))
                    chmod(rtrim($dir, '/') . '/' . $month, 0777);

                if (!file_exists(rtrim($dir, '/') . '/' . $month) || !is_writable(rtrim($dir, '/') . '/' . $month))
                    continue; //cannot create the new dir???

                $filename = sprintf("%s/%s/%s_%s", rtrim($dir, '/'), $month, $key, $name); //new destination.
                if (rename($origfilename, $filename) && file_exists($filename)) {
                    @unlink($origfilename);
                }
            }
        }
    }

    function deleteAttachments() {
        global $cfg;

        $sql = 'SELECT attach_id,file_name,file_key FROM ' . TICKET_ATTACHMENT_TABLE . ' WHERE ticket_id=' . db_input($this->getId());
        $res = db_query($sql);
        if ($res && db_num_rows($res)) {
            $dir = $cfg->getUploadDir();
            $month = date('my', strtotime($this->getCreateDate()));
            $ids = array();
            while (list($id, $name, $key) = db_fetch_row($res)) {
                $filename = sprintf("%s/%s/%s_%s", rtrim($dir, '/'), $month, $key, $name);
                if (!file_exists($filename))
                    $filename = sprintf("%s/%s_%s", rtrim($dir, '/'), $key, $name);
                @unlink($filename);
                $ids[] = $id;
            }
            if ($ids) {
                db_query('DELETE FROM ' . TICKET_ATTACHMENT_TABLE . ' WHERE attach_id IN(' . implode(',', $ids) . ') AND ticket_id=' . db_input($this->getId()));
            }
            return TRUE;
        }
        return FALSE;
    }

    function getAttachmentStr($refid, $type) {

        $sql = 'SELECT attach_id,file_size,file_name FROM ' . TICKET_ATTACHMENT_TABLE .
                ' WHERE deleted=0 AND ticket_id=' . db_input($this->getId()) . ' AND ref_id=' . db_input($refid) . ' AND ref_type=' . db_input($type);
        $res = db_query($sql);
        if ($res && db_num_rows($res)) {
            while (list($id, $size, $name) = db_fetch_row($res)) {
                $hash = MD5($this->getId() * $refid . session_id());
                $size = Format::file_size($size);
                $name = Format::htmlchars($name);
                $attachstr.= "<a class='Icon file' href='attachment.php?id=$id&ref=$hash' target='_blank'><b>$name</b></a>&nbsp;(<i>$size</i>)&nbsp;&nbsp;";
            }
        }
        return ($attachstr);
    }

    /* ============== Functions below do not require an instance of the class to be used. To call it use Ticket::function(params); ================== */

    function getIdByExtId($extid) {
        $sql = 'SELECT  ticket_id FROM ' . TICKET_TABLE . ' ticket WHERE ticketID=' . db_input($extid);
        $res = db_query($sql);
        if ($res && db_num_rows($res))
            list($id) = db_fetch_row($res);

        return $id;
    }

    //TODO: implement this genExtRandID() functional into Service Orders

    function genExtRandID() {
        global $cfg;

        //We can allow collissions...extId and email must be unique ...so same id with diff emails is ok..
        // But for clarity...we are going to make sure it is unique.
        $id = Misc::randNumber(EXT_TICKET_ID_LEN);
        if (db_num_rows(db_query('SELECT ticket_id FROM ' . TICKET_TABLE . ' WHERE ticketID=' . db_input($id))))
            return Ticket::genExtRandID();

        return $id;
    }

    function getIdByMessageId($mid, $email) {

        if (!$mid || !$email)
            return 0;

        $sql = 'SELECT ticket.ticket_id FROM ' . TICKET_TABLE . ' ticket ' .
                ' LEFT JOIN ' . TICKET_MESSAGE_TABLE . ' msg USING(ticket_id) ' .
                ' WHERE messageId=' . db_input($mid) . ' AND email=' . db_input($email);
        $id = 0;
        if (($res = db_query($sql)) && db_num_rows($res))
            list($id) = db_fetch_row($res);

        return $id;
    }

    function getOpenTicketsByEmail($email) {

        $sql = 'SELECT count(*) as open FROM ' . TICKET_TABLE . ' WHERE status=' . db_input('open') . ' AND email=' . db_input($email);
        if (($res = db_query($sql)) && db_num_rows($res))
            list($num) = db_fetch_row($res);

        return $num;
    }

    function update($var, &$errors) {
        global $cfg, $thisuser;

        $fields = array();
        $fields['name'] = array('type' => 'string', 'required' => 1, 'error' => 'Name required');
        $fields['email'] = array('type' => 'email', 'required' => 1, 'error' => 'Email is required');
        $fields['note'] = array('type' => 'text', 'required' => 1, 'error' => 'Reason for the update required');
        $fields['subject'] = array('type' => 'string', 'required' => 1, 'error' => 'Subject required');
        $fields['topicId'] = array('type' => 'int', 'required' => 0, 'error' => 'Invalid Selection');
        $fields['pri'] = array('type' => 'int', 'required' => 0, 'error' => 'Invalid Priority');
        $fields['mobile'] = array('type' => 'mobile', 'required' => 0, 'error' => 'Valid mobile # required');
        $fields['duedate'] = array('type' => 'date', 'required' => 0, 'error' => 'Invalid date - must be MM/DD/YY');


        $params = new Validator($fields);
        if (!$params->validate($var)) {
            $errors = array_merge($errors, $params->errors());
        }

        if ($var['duedate']) {
            if ($this->isClosed())
                $errors['duedate'] = 'Duedate can NOT be set on a closed ticket';
            elseif (!$var['time'] || strpos($var['time'], ':') === false)
                $errors['time'] = 'Select time';
            elseif (strtotime($var['duedate'] . ' ' . $var['time']) === false)
                $errors['duedate'] = 'Invalid duedate';
            elseif (strtotime($var['duedate'] . ' ' . $var['time']) <= time())
                $errors['duedate'] = 'Due date must be in the future';
        }

        //Make sure phone extension is valid
        /*
          if ($var['phone_ext']) {
          if (!is_numeric($var['phone_ext']) && !$errors['phone'])
          $errors['phone'] = 'Invalid phone ext.';
          elseif (!$var['phone']) //make sure they just didn't enter ext without phone #
          $errors['phone'] = 'Phone number required';
          }
         */

        $cleartopic = false;
        $topicDesc = '';
        if ($var['topicId'] && ($topic = new Topic($var['topicId'])) && $topic->getId()) {
            $topicDesc = $topic->getName();
        } elseif (!$var['topicId'] && $this->getTopicId()) {
            $topicDesc = '';
            $cleartopic = true;
        }


        if (!$errors) {
            $sql = 'UPDATE ' . TICKET_TABLE . ' SET updated=' . db_input($this->get_now()) .
                    ',email=' . db_input($var['email']) .
                    ',name=' . db_input(Format::striptags($var['name'])) .
                    ',subject=' . db_input(Format::striptags($var['subject'])) .
                    ',mobile="' . db_input($var['mobile'], false) . '"' .
                    ',phone_ext=' . db_input($var['phone_ext'] ? $var['phone_ext'] : NULL) .
                    ',priority_id=' . db_input($var['pri']) .
                    ',topic_id=' . db_input($var['topicId']) .
                    ',duedate=' . ($var['duedate'] ? db_input(date('Y-m-d G:i', Misc::dbtime($var['duedate'] . ' ' . $var['time']))) : 'NULL');
            if ($var['duedate']) { //We are setting new duedate...
                $sql.=',isoverdue=0';
            }
            if ($topicDesc || $cleartopic) { //we're overwriting previous topic.
                $sql.=',helptopic=' . db_input($topicDesc);
            }
            $sql.=' WHERE ticket_id=' . db_input($this->getId());
            //  echo   $sql;
            if (db_query($sql)) {
                $this->postNote('Ticket Updated', $var['note']);
                $this->reload();
                return true;
            }
        }

        return false;
    }

    //fetch more response email from database
    //will return array
    function getMoreResponseEmail() {
        $sql = 'SELECT more_ticket_alert_email FROM ' . CUSTOM_PREF_TABLE . ' WHERE id=' . db_input(1);
        if ($res = db_query($sql)) {
            $emails = db_fetch_array($res);
            $emails = explode(',', $emails['more_ticket_alert_email']);
            return $emails;
        } else {
            return false;
        }
    }

    //generate trouble ticket number in a specified format
    //current algorithm is: client login name four digits truncated # client id # cin # randomness
    function gen_tt_id($client_id, $cin) {
        $client = new Client($client_id);
        $cl_num = $client->get_db_id();
        $login_name = $client->get_login_name();
        $login_name = substr($login_name, 0, 4);

        $now = date('d-m-Y@H:i:s');
        if ($cin) {
            $tt_id = $login_name . '#' . $cl_num . '#' . $cin . '#' . $now;
        } else {
            $tt_id = $login_name . '#' . $cl_num . '#' . $now;
        }
    }

    function update_ticket($var, &$errors, $origin = 'staff') {
        global $thisuser, $cfg;
        $ticket_id = $var['ticket_id'];
        $now = date('Y-m-d H:i:s');

        $sql = 'UPDATE ' . TICKET_TABLE . ' SET updated=' . db_input($now);

        if ($var['subject']) {
            $sql = $sql . ',subject=' . db_input(Format::striptags($var['subject']));
        }
        if ($var['root_cause']) {
            $sql = $sql . ',root_cause=' . db_input(Format::striptags($var['root_cause']));
        }
        if ($var['alt_email']) {
            $sql = $sql . ',alt_email=' . db_input(Format::striptags($var['alt_email']));
        }
        if ($var['site_visited_date']) {
            $sql = $sql . ',site_visited_date=' . db_input($var['site_visited_date']);
        }
        if ($var['link_down_date']) {
            $sql = $sql . ',link_down_date=' . db_input($var['link_down_date']);
        }
        if ($var['restoration_date']) {
            $sql = $sql . ',restoration_date=' . db_input($var['restoration_date']);
        }
        if ($var['restoration_done_by']) {
            $sql = $sql . ',restoration_done_by=' . db_input($var['restoration_done_by']);
        }
        if ($var['restoration_confirmed_by']) {
            $sql = $sql . ',restoration_confirmed_by=' . db_input($var['restoration_confirmed_by']);
        }
        if ($var['downtime_duration']) { //in minutes
            $sql = $sql . ',downtime_duration=' . db_input($var['downtime_duration']);
        }

        if ($var['sla_claim'] == 'yes') {
            if ($var['sla_claim_duration']) {
                $sql = $sql . ',sla_claim_duration=' . db_input($var['sla_claim_duration']);
            }
            if ($var['sla_claim_cause']) {
                $sql = $sql . ',sla_claim_cause=' . db_input($var['sla_claim_cause']);
            }
        } else {
            if ($var['sla_claim_duration']) {
                $sql = $sql . ',sla_claim_duration=' . db_input('');
            }
            if ($var['sla_claim_cause']) {
                $sql = $sql . ',sla_claim_cause=' . db_input('');
            }
        }

        $sql = $sql . ' WHERE ticket_id=' . db_input($var['ticket_id']);

        if (db_query($sql)) {
            if ($var['note']) {
                $ticket = new Ticket($ticket_id);
                $ticket->postResponse($ticket->getLastMsgId(), $var['note'], '', null, false);
                //$ticket->postResponse('Ticket updated by ' . $thisuser->getName(), $var['note'], false);
            }

            $ticket_b = $ticket; //before update

            $this->reload(); //reload after update
            
            //$this->postResponse($var['msg_id'], $var['note'], '', null, false);

            if (file_exists(TEMPLATE_DIR . 'email.ticket-edit.tpl.html') && file_exists(TEMPLATE_DIR . 'ticket-info.tpl.html')) {
                $body = file_get_contents(TEMPLATE_DIR . 'email.ticket-edit.tpl.html');
                $ticket_info = file_get_contents(TEMPLATE_DIR . 'ticket-info.tpl.html');
                $subj = 'Update - Ticket: %ticket - %name - %cin - %subject';

                $subj_data = array(
                    '%name' => $this->getClient()->getName()
                );
                $subj = $this->renderTemplate($subj, $subj_data);

                $ticket_before = $ticket_b->renderTemplate($ticket_info);
                $ticket_now = $this->renderTemplate($ticket_info);

                $body_data = array(
                    '%tt_edit_title' => 'Ticket updated by ' . $thisuser->getName(),
                    '%message' => $var['note'],
                    '%ticket_info_before' => $ticket_before,
                    '%ticket_info_after' => $ticket_now,
                    '%signature'=>$thisuser->getSignatureForTemplate()
                );
                $body = $this->renderTemplate($body, $body_data);

                $email = $cfg->getDefaultEmail();

                if ($email) {
                    //Reply separator tag.
                    if ($cfg->stripQuotedReply() && ($tag = $cfg->getReplySeparator()))
                        $body = "\n$tag\n\n" . $body;

                    $cc = array();
                    $noc_mail = Email::getNOCmail();
                    
                    /*
                    $cc = explode(',', $this->getAltEmail());
                    $cc[] = $this->get_raiser_email();
                    $cc[] = $this->getEmail()?$this->getEmail():$this->getClient()->getEmail();

                    $cc = array_unique($cc);

                    $recipients = $cc;
                    $recipients[] = $noc_mail;
                    $recipients = array_unique($recipients);
                    $recipients = implode(',', $recipients);
                    */
                    
                    $more_cc = array();
                    $more_cc = explode(',', $this->getAltEmail());
                    $more_cc[] = $noc_mail;
                    $more_cc[] = $this->getEmail();
                    if ($this->get_raiser_email())
                        $more_cc[] = $this->get_raiser_email();
                    //$more_cc[] = $this->getEmail();
                    $more_cc = array_unique($more_cc);

                    $recipients = $more_cc;
                    $recipients[] = $this->getEmail();
                    $recipients[] = $noc_mail;
                    $recipients = array_unique($recipients);
                    $recipients = implode(',', $recipients);

                    $to_header = $noc_mail;
                    //$from_header = $thisuser->getName() . ' &lt' . $thisuser->getEmail() . '&gt';
                    $from_header = sprintf('"%s"<%s>',$thisuser->getName(),$thisuser->getEmail());
                    
                    /*
                    //debug
                    echo '<pre>';
                    print_r($cc);
                    echo '</pre>';
                    echo '<pre>';
                    print_r($recipients);
                    echo '</pre>';
                    echo '<pre>';
                    echo $to_header;
                    echo '</pre>';
                    echo $subj;
                    echo '<br>';
                    echo $body;
                    exit;
                    //debug
                    */

                    $email->send($recipients, $subj, $body, $attachments, $more_cc, $from_header, $to_header);
                }
            }
            return true;
        } else {
            $errors['err'] = 'ticket update failure';
            return false;
        }
    }
        
    //set tt id specific to 1asiaahl
    function set_tt_id($client_id, $cin, $ticket=null) {
        $today = date('dmy');
        if ( is_object($ticket) ) {
            $curdate = trim($ticket->getCreateDate());
            $temp = preg_split("/\s+/", $curdate);
            $sql = 'SELECT TTID FROM '.TICKET_TABLE.' ticket WHERE DATE(created)='.db_input($temp[0]).' AND client_id='.db_input($client_id);
        } else {
            $sql = 'SELECT TTID FROM '.TICKET_TABLE.' ticket WHERE DATE(created)=CURDATE() AND client_id='.db_input($client_id);
        }
        if ( $res = db_query($sql) ) {
            $num_1 = db_num_rows($res)+1;
            
            $num_2 = 0;
            
            $test = array();
            $rows = db_assoc_array($res);
            if ( $rows && count($rows) ) {
                foreach( $rows as $r ) {
                    if ($r['TTID']) {
                        $parts = explode('-', $r['TTID']);
                        $num_2 = $parts[2];
                        $test[] = $num_2;
                    }
                }
                
                $num_2 = max($test)+1;
            }
            $num = max($num_1, $num_2);
            
            $num = (strlen($num)==1)?'0'.$num:$num;
            
            $cin_digit = '';
            if ($cin) {
                $temp = explode('/', $cin);
                $cin = $temp[0];
                $cin_digit = substr($cin, -4, 4);
            } else {
                $cin_digit = 'XXXX';
            }
            
            $tt_id = $today.'-'.$cin_digit.'-'.$num;
            
            return $tt_id;
            
        } else {
            return null;
        }
    }
    
    function save_tt_id() {
        $client_id = $this->getClientId();
        $cin = $this->getCINValue();
        
        $ttid = self::set_tt_id($client_id, $cin, $this);
        return $this->update_a_field('TTID', $ttid);
        
    }
    
    
    function update_a_field($name, $value) {
        if ( !$this->getId() || !$name || !$value ) {
            return false;
        }
        $sql = 'UPDATE '.TICKET_TABLE.' SET '.$name.'='.db_input($value).' WHERE ticket_id='.db_input($this->getId());
        if ( db_query($sql) && db_affected_rows() ) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * The mother of all functions...You break it you fix it!
     *
     *  $autorespond and $alertstaff overwrites config info...
     */

    function create($var, &$errors, $origin, $autorespond = true, $alertstaff = true) {
        global $cfg, $thisuser, $_FILES;

        /* Coders never code so fully and joyfully as when they do it for free  - Peter Rotich */

        $id = 0;
        /*
          $fields = array();
          $fields['name'] = array('type' => 'string', 'required' => 1, 'error' => 'Name required');
          $fields['alt_contact_name'] = array('type' => 'string', 'required' => 0, 'error' => 'Name required');
          $fields['phone'] = array('type' => 'string', 'required' => 0, 'error' => 'Phone number required');
          $fields['alt_phone_num'] = array('type' => 'phone', 'required' => 0, 'error' => 'Phone number required');
          $fields['email'] = array('type' => 'email', 'required' => 1, 'error' => 'Valid email required');
          $fields['alt_email'] = array('type' => 'text', 'required' => 0, 'error' => 'Valid email required');
          $fields['subject'] = array('type' => 'string', 'required' => 1, 'error' => 'Subject required');
          $fields['message'] = array('type' => 'text', 'required' => 1, 'error' => 'Message required');
          if (strcasecmp($origin, 'web') == 0) { //Help topic only applicable on web tickets.
          $fields['topicId'] = array('type' => 'int', 'required' => 0, 'error' => 'Select help topic');
          } elseif (strcasecmp($origin, 'staff') == 0) { //tickets created by staff...e.g on callins.
          $fields['deptId'] = array('type' => 'int', 'required' => 0, 'error' => 'Dept. required');
          $fields['source'] = array('type' => 'string', 'required' => 0, 'error' => 'Indicate source');
          $fields['duedate'] = array('type' => 'date', 'required' => 0, 'error' => 'Invalid date - must be MM/DD/YY');
          } else { //Incoming emails
          $fields['emailId'] = array('type' => 'int', 'required' => 0, 'error' => 'Email unknown');
          }
          $fields['pri'] = array('type' => 'int', 'required' => 0, 'error' => 'Invalid Priority');
          $fields['mobile'] = array('type' => 'mobile', 'required' => 0, 'error' => 'Valid mobile # required');

          $validate = new Validator($fields);
          if (!$validate->validate($var)) {
          $errors = array_merge($errors, $validate->errors());
          }

          //Make sure the email is not banned
          if (!$errors && BanList::isbanned($var['email'])) {
          $errors['err'] = 'Ticket denied. Error #403'; //We don't want to tell the user the real reason...Psssst.
          Sys::log(LOG_WARNING, 'Ticket denied', 'Banned email - ' . $var['email']); //We need to let admin know which email got banned.
          }

          if (!$errors && $thisuser && strcasecmp($thisuser->getEmail(), $var['email'])) {
          $errors['email'] = 'Email mismatch.';
          }

          //Make sure phone extension is valid
          if ($var['phone_ext']) {
          if (!is_numeric($var['phone_ext']) && !$errors['phone'])
          $errors['phone'] = 'Invalid phone ext.';
          }
          //Make sure the due date is valid
          if ($var['duedate']) {
          if (!$var['time'] || strpos($var['time'], ':') === false)
          $errors['time'] = 'Select time';
          elseif (strtotime($var['duedate'] . ' ' . $var['time']) === false)
          $errors['duedate'] = 'Invalid duedate';
          elseif (strtotime($var['duedate'] . ' ' . $var['time']) <= time())
          $errors['duedate'] = 'Due date must be in the future';
          }

          //check attachment..if any is set ...only set on webbased tickets..
          if ($_FILES['attachment']['name'] && $cfg->allowOnlineAttachments()) {
          if (!$cfg->canUploadFileType($_FILES['attachment']['name'])) {
          $errors['attachment'] = 'Invalid file type [ ' . Format::htmlchars($_FILES['attachment']['name']) . ' ]';
          }
          elseif ($_FILES['attachment']['size'] > $cfg->getMaxFileSize()) {
          $errors['attachment'] = 'File is too big. Max ' . $cfg->getMaxFileSize() . ' bytes allowed';
          }
          }

          //check ticket limits..if limit set is >0
          //TODO: Base ticket limits on SLA...
          if ($var['email'] && !$errors && $cfg->getMaxOpenTickets() > 0 && strcasecmp($origin, 'staff')) {
          $openTickets = Ticket::getOpenTicketsByEmail($var['email']);
          if ($openTickets >= $cfg->getMaxOpenTickets()) {
          $errors['err'] = "You've reached the maximum open tickets allowed.";
          //Send the notice only once (when the limit is reached) incase of autoresponders at client end.
          if ($cfg->getMaxOpenTickets() == $openTickets && $cfg->sendOverlimitNotice()) {
          if ($var['deptId'])
          $dept = new Dept($var['deptId']);

          if (!$dept || !($tplId = $dept->getTemplateId()))
          $tplId = $cfg->getDefaultTemplateId();

          $sql = 'SELECT ticket_overlimit_subj,ticket_overlimit_body FROM ' . EMAIL_TEMPLATE_TABLE .
          ' WHERE cfg_id=' . db_input($cfg->getId()) . ' AND tpl_id=' . db_input($tplId);
          $resp = db_query($sql);
          if (db_num_rows($resp) && list($subj, $body) = db_fetch_row($resp)) {

          $body = str_replace("%name", $var['name'], $body);
          $body = str_replace("%email", $var['email'], $body);
          $body = str_replace("%url", $cfg->getBaseUrl(), $body);
          $body = str_replace('%signature', ($dept && $dept->isPublic()) ? $dept->getSignature() : '', $body);

          if (!$dept || !($email = $dept->getAutoRespEmail()))
          $email = $cfg->getDefaultEmail();
          if ($email) {
          $email->send($var['email'], $subj, $body);
          }
          }
          //Alert admin...this might be spammy (no option to disable)...but it is helpful..I think.
          $msg = 'Ticket request denied for ' . $var['email'] . "\n" .
          'Open ticket:' . $openTickets . "\n" .
          'Max Allowed:' . $cfg->getMaxOpenTickets() . "\n\nNotice only sent once";
          Sys::alertAdmin('Overlimit Notice', $msg);
          }
          }
          }
         */
        //Any error above is fatal.
        if ($errors) {
            return 0;
        }

        // OK...just do it.
        $deptId = $var['deptId']; //pre-selected Dept if any.
        $priorityId = $var['pri'];
        $source = ucfirst($var['source']);
        $topic = NULL;
        // Intenal mapping magic...see if we need to overwrite anything
        if (isset($var['topicId'])) { //Ticket created via web by user/or staff
            if ($var['topicId'] && ($topic = new Topic($var['topicId'])) && $topic->getId()) {
                $deptId = $deptId ? $deptId : $topic->getDeptId();
                $priorityId = $priorityId ? $priorityId : $topic->getPriorityId();
                $topicDesc = $topic->getName();
                if ($autorespond)
                    $autorespond = $topic->autoRespond();
            }
            $source = $var['source'] ? $var['source'] : 'Web';
        }elseif ($var['emailId'] && !$var['deptId']) { //Emailed Tickets
            $email = new Email($var['emailId']);
            if ($email && $email->getId()) {
                $deptId = $email->getDeptId();
                $priorityId = $priorityId ? $priorityId : $email->getPriorityId();
                if ($autorespond)
                    $autorespond = $email->autoRespond();
            }
            $email = null;
            $source = 'Email';
        }elseif ($var['deptId']) { //Opened by staff.
            $deptId = $var['deptId'];
            $source = ucfirst($var['source']);
        }

        //Don't auto respond to mailer daemons.
        if (strpos(strtolower($var['email']), 'mailer-daemon@') !== false || strpos(strtolower($var['email']), 'postmaster@') !== false)
            $autorespond = false;

        //Last minute checks
        $priorityId = $priorityId ? $priorityId : $cfg->getDefaultPriorityId();
        $deptId = $deptId ? $deptId : $cfg->getDefaultDeptId();
        $topicId = $var['topicId'] ? $var['topicId'] : 0;
        $ipaddress = $var['ip'] ? $var['ip'] : $_SERVER['REMOTE_ADDR'];

        //We are ready son...hold on to the rails.
        $extId = Ticket::genExtRandID();
        $TTID = Ticket::set_tt_id($var['client_id'], $var['cin']);
        if( !$extId || !$TTID ) {
            $errors['err'] = 'Internal error, cannot generate id';
            return false;
        }

        $client_id = $var['client_id'];
        $client = new Client($client_id);
        if ( !$client->getId() ) {
            $errors['err'] = 'no client selected';
            return false;    
        }
        $client_name = $client->getName();
        $client_email = $client->getEmail();

        //durations are in minutes
        $now = date('Y-m-d H:i:s');
        $sql = 'INSERT INTO ' . TICKET_TABLE . ' SET created=' . db_input($now) .
                ',ticketID=' . db_input($extId) .
                ',TTID=' . db_input($TTID) .
                ',client_id=' . db_input($client_id) .
                ',raiser_name=' . db_input($_POST['raiser_name']) .
                ',raiser_phone=' . db_input($_POST['raiser_phone']) .
                ',raiser_email=' . db_input($_POST['raiser_email']) .
                ',raised_from=' . db_input($_POST['raised_from']) .
                ',dept_id=' . db_input($deptId) .
                ',topic_id=' . db_input($topicId) .
                ',priority_id=' . db_input($priorityId) .
                ',email=' . db_input(Format::striptags($var['email'])) .
                ',alt_email=' . db_input(Format::sp_trim($var['alt_email'])) .
                ',cin=' . db_input(Format::striptags($var['cin'])) .
                ',name=' . db_input($client_name) .
                ',alt_contact_name=' . db_input(Format::striptags($var['alt_contact_name'])) .
                ',phone=' . db_input(Format::striptags($var['phone'])) .
                ',alt_phone_num=' . db_input(Format::striptags($var['alt_phone_num'])) .
                ',subject=' . db_input(Format::striptags($var['subject'])) .
                ',root_cause=' . db_input(Format::striptags($var['root_cause'])) .
                ',helptopic=' . db_input(Format::striptags($topicDesc)) .
                ',site_visited_by=' . db_input($var['site_visited_by']) .
                ',site_visited_date=' . db_input($var['site_visited_date']) .
                ',link_down_date=' . db_input($var['link_down_date']) .
                ',restoration_date=' . db_input($var['restoration_date']) .
                ',restoration_done_by=' . db_input($var['restoration_done_by']) .
                ',restoration_confirmed_by=' . db_input($var['restoration_confirmed_by']) .
                ',downtime_duration=' . db_input($var['downtime_duration']) .
                ',sla_claim_duration=' . db_input($var['sla_claim_duration']) .
                ',sla_claim_cause=' . db_input($var['sla_claim_cause']) .
                ',ip_address=' . db_input($ipaddress) .
                ',source=' . db_input($source) .
                ',created_by_noc=' . db_input($thisuser->isStaff()?1:0) .
                ',tt_creator=' . db_input($thisuser->getId());


        //  echo   $sql;
        $ticket = null;
        //return $ticket;
        if (db_query($sql) && ($id = db_insert_id())) {
            if (!$cfg->useRandomIds()) {
                //Sequential ticketIDs support really..really suck arse.
                $extId = $id; //To make things really easy we are going to use autoincrement ticket_id.
                db_query('UPDATE ' . TICKET_TABLE . ' SET ticketID=' . db_input($extId) . ' WHERE ticket_id=' . $id);
                //TODO: RETHING what happens if this fails?? [At the moment on failure random ID is used...making stuff usable]
            }
            //Load newly created ticket.
            $ticket = new Ticket($id);
            $info = $ticket->getInfo();

            //post the message.
            $msgid = $ticket->postMessage($var['message'], $source, $var['mid'], $var['header'], true, true);
            //TODO: recover from postMessage error??
            //Upload attachments...web based.
            if ($_FILES['attachment']['name'] && $cfg->allowOnlineAttachments() && $msgid) {
                if (!$cfg->allowAttachmentsOnlogin() || ($cfg->allowAttachmentsOnlogin() && ($thisuser && $thisuser->isValid()))) {
                    $ticket->uploadAttachment($_FILES['attachment'], $msgid, 'M');
                    //TODO: recover from upload issues?
                }
            }

            //$dept = $ticket->getDept();
            
            //SEND OUT NEW TICKET AUTORESP && ALERTS.
            if ($origin == 'client') { //tickets from client
                //taking email templates from file
                if (file_exists(TEMPLATE_DIR . 'email.new-ticket.tpl.html')) {
                    $body = file_get_contents(TEMPLATE_DIR . 'email.new-ticket.tpl.html');
                    $subj = 'Ticket: %ticket - %name - %cin - %subject';

                    $subj = $ticket->renderTemplate($subj);

                    $body_data = array(
                        '%message' => $var['message'],
                        '%name' => $ticket->getClient()->getName(),
                        '%email' => $ticket->getEmail(),
                        '%phone' => $ticket->getClient()->getPhone()
                    );
                    
                    $body = $ticket->renderTemplate($body, $body_data);
                    
                    $email = $cfg->getDefaultEmail();

                    if ($email) {
                        //Reply separator tag.
                        if ($cfg->stripQuotedReply() && ($tag = $cfg->getReplySeparator()))
                            $body = "\n$tag\n\n" . $body;

                        $noc_mail = Email::getNOCmail();

                        $attachments = null;
                        $to_header = $noc_mail;
                        //$to_header = '';
                        //$from_header = $ticket->getClient()->getName() . '@' . $ticket->getEmail();
                        $from_header = sprintf('"%s"<%s>',$ticket->getClient()->getName(),$ticket->getClient()->getEmail());
                        //$from_header = $ticket->getClient()->getName();

                        $cc = array();
                        $cc = explode(',', $ticket->getAltEmail());
                        $cc[] = $ticket->getEmail();
                        $cc = array_unique($cc);

                        $recipients = $cc;
                        $recipients[] = $noc_mail;
                        $recipients = array_unique($recipients);
                        $recipients = implode(',', $recipients);
                        
                        /*
                          //debug
                          echo '<pre>';
                          print_r($cc);
                          echo '</pre>';
                          echo '<pre>';
                          print_r($recipients);
                          echo '</pre>';
                          echo '<pre>';
                          echo $to_header;
                          echo '</pre>';
                          echo $subj;
                          echo '<br>';
                          echo $body;
                          exit;
                          //debug
                          */
                         
                        $email->send($recipients, $subj, $body, $attachments, $cc, $from_header, $to_header);
                        //$email->send($recipients, $subj, $body);
                    }
                }
            }
            if ($origin == 'staff') { //tickets created by staff
                //taking email templates from file
                if (file_exists(TEMPLATE_DIR . 'email.staff-ticket.tpl.html')) {
                    $body = file_get_contents(TEMPLATE_DIR . 'email.staff-ticket.tpl.html');
                    $subj = 'Ticket: %ticket - %name - %cin - %subject';

                    $subj = $ticket->renderTemplate($subj);

                    $body_data = array(
                        '%message' => $var['message'],
                        '%signature' => $thisuser->getSignatureForTemplate(),
                        '%name' => $ticket->getClient()->getName(),
                        '%email' => $ticket->getEmail(),
                        '%phone' => $ticket->getClient()->getPhone()
                    );
                    $body = $ticket->renderTemplate($body, $body_data);

                    $email = $cfg->getDefaultEmail();

                    if ($email) {
                        //Reply separator tag.
                        if ($cfg->stripQuotedReply() && ($tag = $cfg->getReplySeparator()))
                            $body = "\n$tag\n\n" . $body;

                        //building cc list
                        $cc = array();
                        $cc = explode(',', $ticket->getAltEmail());
                        $noc_mail = Email::getNOCmail();
                        $cc[] = $noc_mail;
                        $cc[] = Format::sp_trim($ticket->get_raiser_email());

                        $attachments = null;

                        //building "to" header
                        $to_header = $ticket->getClient()->getEmail();

                        //building "from" header
                        $from_header = null;

                        //for emails to be sent into cc list, you have to add them into recipients list
                        $recipients = $cc;
                        $recipients[] = Format::sp_trim($ticket->getEmail());
                        $recipients[] = $noc_mail;

                        //now cleansing
                        $cc = array_unique($cc);
                        $recipients = array_unique($recipients);
                        $recipients = trim(implode(',', $recipients), ',');
                        
                        /*
                          //debug
                          echo '<pre>';
                          print_r($cc);
                          echo '</pre>';
                          echo '<pre>';
                          print_r($recipients);
                          echo '</pre>';
                          echo '<pre>';
                          echo $to_header;
                          echo '</pre>';
                          echo $subj;
                          echo '<br>';
                          echo 'noc is: '.$noc_mail.'<br>';
                          echo $body;
                          exit;
                          //debug
                          */
                          
                         
                        $email->send($recipients, $subj, $body, $attachments, $cc, $from_header, $to_header);
                    }
                }
            }
        }
        return $ticket;
    }

    function create_by_staff($var, &$errors) {
        global $_FILES, $thisuser, $cfg;

        //check if the staff is allowed to create tickets.
        /*
          if (!$thisuser || !$thisuser->getId() || !$thisuser->isStaff() || !$thisuser->canCreateTickets())
          $errors['err'] = 'Permission denied';
         */

        $var['source'] = 'Other';

        $var['emailId'] = 0; //clean crap.
        //$var['message'] = 'Ticket created by staff';

        if (($ticket = Ticket::create($var, $errors, 'staff', true, (!$var['staffId'])))) {  //Staff are alerted only IF the ticket is not being assigned.
            //post issue as a response...
            $msgId = $ticket->getLastMsgId();
            $issue = $ticket->replaceTemplateVars($var['issue']);

            //post create actions
            if ($var['staffId']) { //Assign ticket to staff if any. (internal note as message)
                $ticket->assignStaff($var['staffId'], $var['note'], false);
            } elseif ($var['note']) { //Not assigned...save optional note if any
                $ticket->postNote('New Ticket', $var['note'], false);
            } else { //Not assignment and no internal note - log activity
                $ticket->logActivity('New Ticket by Executive', 'Ticket created by executive -' . $thisuser->getName());
            }
        } else {
            $errors['err'] = $errors['err'] ? $errors['err'] : 'Unable to create the ticket. Correct the error(s) and try again';
        }

        return $ticket;
    }

    function checkOverdue() {

        global $cfg;

        if (($hrs = $cfg->getGracePeriod())) {
            $sec = $hrs * 3600;
            $sql = 'SELECT ticket_id FROM ' . TICKET_TABLE . ' WHERE status=\'open\' AND isoverdue=0 ' .
                    ' AND ((reopened is NULL AND duedate is NULL AND TIME_TO_SEC(TIMEDIFF(NOW(),created))>=' . $sec . ')  ' .
                    ' OR (reopened is NOT NULL AND duedate is NULL AND TIME_TO_SEC(TIMEDIFF(NOW(),reopened))>=' . $sec . ') ' .
                    ' OR (duedate is NOT NULL AND duedate<NOW()) ' .
                    ') ORDER BY created LIMIT 50'; //Age upto 50 tickets at a time?
        } else { //No aging....simply check duedates.
            $sql = 'SELECT ticket_id FROM ' . TICKET_TABLE . ' WHERE status=\'open\' AND isoverdue=0 ' .
                    ' AND (duedate is NOT NULL AND duedate<NOW()) ORDER BY created LIMIT 100';
        }
        //  echo   $sql;
        if (($stale = db_query($sql)) && db_num_rows($stale)) {
            while (list($id) = db_fetch_row($stale)) {
                $ticket = new Ticket($id);
                if ($ticket->markOverdue(true))
                    $ticket->logActivity('Ticket Marked Overdue', 'Ticket flagged as overdue by the system.');
            }
        }
    }

}

?>
