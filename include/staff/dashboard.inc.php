<?php
/**
 * includes charts and syslogs code and html design
 */
if (!defined('OSTSCPINC') || !@$thisuser->isStaff())
    die('Access Denied');
    
require_once (INCLUDE_DIR . 'class.ticket.php');
require 'chart.inc.php';
require 'report.inc.php';
$stats_today = new Chart;
 ?>
<div id="dashboard">
    <div>
        <a href="admin.php?t=syslog">view logs(ticket and access related)</a> &nbsp;&nbsp;&nbsp;&nbsp;
        <a href="admin.php?t=orderlogs">order Logs</a> &nbsp;&nbsp;&nbsp;&nbsp;
    </div>
    <h2><?php   echo   "Hello " . $thisuser->getName();  ?></h2>
    <hr>
    <h3>Ticket Inflow, today total <?php   echo   $stats_today->total_tickets();  ?>, pending:<?php   echo   $stats_today->pending_tickets();  ?></h3>
    <div class="inline" id="line_chart"></div>
    
    
    
    <h3>Percentage</h3>
    <div id="pie_charts">
        <div id="pie_chart_status"></div>

        <div id="pie_chart_priority"></div>
    </div>
</div>


<div id="ticket_just_by_now">
    <h3>ticket just by now</h3>
    <?php
        $sql = 'SELECT ticket_id, client_id, subject, status FROM '.TICKET_TABLE.' WHERE DATE(created)=DATE(NOW())';
        $tickets_res = db_query($sql);
        //  echo   $sql;
        $num = db_num_rows($tickets_res);
        if ( $num>0 ) {
     ?>
    <table>
        <tr>
            <th>id</th>
            <th>client id</th>
            <th>subject</th>
            <th>status</th>
        </tr>
        <?php
            for ( $i=0; $i<$num; $i++ ) {
            $res = db_fetch_array($tickets_res);
         ?>
        
        <tr>
            <td><a href="tickets.php?id=<?php   echo   $res['ticket_id']  ?>" target="_blank"><?php   echo   $res['ticket_id']  ?></a></td>
            <td><?php   echo   $res['client_id']  ?></td>
            <td><?php   echo   $res['subject']  ?></td>
            <td><?php   echo   $res['status']  ?></td>
        </tr>
        
        <?php }  ?>
    </table>
    <?php 
        } else {
              echo   'no tickets for today';
        }
     ?>
</div>


<div class="dash_div">
<table>
<tr>
    <th> ticket id </th>
    <th> interval without any response </th>
    <th> status </th>
    <th> assign to a staff </th>
</tr>
<h3>Idle tickets</h3> &nbsp;<span>idle for more than 15 minutes<span><br>
<?php
$info=($_POST && $errors)?Format::input($_POST):array(); //Re-use the post info on error...savekeyboards.org
$sql = 'SELECT ticket_id, ticketID, staff_id, status, created, lastresponse, updated, lastmessage FROM ' . TICKET_TABLE . ' WHERE staff_id=0 AND status="open"';
$res = db_query($sql);
if ( db_num_rows($res) ) {
      echo   'total ' . db_num_rows($res) . '<br>';
    while ( $row = db_fetch_array($res) ) {
        if ( ticketisidle($row) ) {
            $ticket = new Ticket($row['ticket_id']);
             ?>
                <tr>
                    <td><a href="tickets.php?id=<?php   echo   $row['ticket_id'];  ?>" target="_blank"><?php   echo   $row['ticket_id'];  ?></a></td>
                    <td><?php   echo   ticketidleintervalstring($row);  ?></td>
                    <td>
                    <?php
                        if ( $row['lastresponse'] == NULL ) {
                              echo   '<span class="red">untouched</span>';
                        } else {
                              echo   'response posted';
                        }
                     ?>
                    </td>
                    <!-- staff assignment -->
                    <td>
                        <form action="tickets.php?id=<?php   echo  $row['ticket_id'] ?>#assign" target="_blank" name="notes" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="ticket_id" value="<?php   echo   $row['ticket_id'];  ?>">
                            <input type="hidden" name="a" value="assign">
                                <select id="staffId" name="staffId">
                                    <option value="0" selected="selected">Select Staff member</option>
                                    <?php
                                        $sql = 'SELECT staff_id,CONCAT_WS(", ",lastname,firstname) as name FROM '.STAFF_TABLE.
                                            ' WHERE isactive=1 AND onvacation=0 ';
                                        if ($ticket->isAssigned()) {
                                            $sql.=' AND staff_id!='.db_input($ticket->getStaffId());
                                        }
                                        $depts=db_query($sql.' ORDER BY lastname,firstname ');
                                        while (list($staffId, $staffName)=db_fetch_row($depts)) {
                                            $selected = ($info['staffId']==$staffId)?'selected':'';  ?>
                                            <option value="<?php   echo   $staffId  ?>" <?php   echo   $selected  ?>><?php   echo   $staffName  ?></option>
                                            
                                        <?php 
                                        }
                                         ?>
                                </select>
                                <textarea name="assign_message" id="assign_message" placeholder="message" ><?php   echo   $info['assign_message']  ?></textarea>
                                <input name="assign_staff" class="button" type="submit" value="assign" />
                                <script type="text/javascript">
                                //these javascript bit is used to reload the page
                                    $('input[name="assign_staff"]').click(function() {
                                        //window.location.reload();
                                        window.setTimeout('location.reload()', 4000);
                                    });
                                </script>
                        </form>
                    </td>
                </tr>
            <?php
        }
    }
}
 ?>
</table>

<br>
<?php
    $stats_global = new Chart(false);
 ?>
total tickets in database:<?php   echo   $stats_global->total_tickets();  ?>, pending:<?php   echo   $stats_global->pending_tickets();  ?>, accepted:<?php   echo   $stats_global->closed_tickets();  ?>
<h4><a href="tickets.php" target="_blank">view all</a></h4>
</div>

<div class="dash_div" id="staff_activity_div">
    <h3>staffs picking the tickets</h3>
    <table>
        <tr>
            <th>staff name</th> <th>total tickets responsed</th> <th>responsed today</th> <th>total closed</th> <th>closed today</th> <th>total ticket time</th> <th>ticket time today</th>
        </tr>
    <?php
        $sql = 'SELECT staff.staff_id, staff.firstname, staff.lastname, staff.lastlogin, response.response_id, response.ticket_id, response.created FROM ' . STAFF_TABLE . ' staff LEFT JOIN ' . TICKET_RESPONSE_TABLE . ' response ON staff.staff_id=response.staff_id';
        $res = db_query($sql);
        if ( db_num_rows($res) ) {
            $today = date('Y:m:d');
            $staffs = array();
            while ( $row = db_fetch_array($res) ) {
                if ( !$staffs[$row['staff_id']]) { //new staff, already not in the array
                    $staffs[$row['staff_id']] = array(); // global response count
                    $staffs[$row['staff_id']]['name'] = $row['firstname'] . ' ' . $row['lastname'];
                    $staffs[$row['staff_id']]['lastlogin'] = $row['lastlogin'];
                    $staffs[$row['staff_id']]['tickets'] = array();
                    $staffs[$row['staff_id']]['tickets']['closed'] = array();
                }
                if ( !$staffs[$row['staff_id']]['tickets'][$row['ticket_id']]['response'] ) { //unique ticket id
                    $staffs[$row['staff_id']]['tickets'][$row['ticket_id']]['response'] = $row['created']; // adding ticket id
                    $ticket = new Ticket($ticketid);
                    if ( $ticket->isClosed() ) { // if ticket is closed
                        array_push($staffs[$row['staff_id']]['tickets']['closed'], $row['ticket_id']); // pushing closed ticket ids
                    }
                    if ( is_today($row['created']) ) {
                        $staffs[$row['staff_id']]['tickets']['today'] = $row['ticket_id']; // responsed today
                        if ( $ticket->isClosed() ) { // if ticket is closed
                            array_push($staffs[$row['staff_id']]['tickets']['closedtoday'], $row['ticket_id']); // pushing closed ticket ids
                        }
                    }
                } else { // ticket id already in, so adding just the response time
                    if ( $row['created'] < $staffs[$row['staff_id']]['tickets'][$row['ticket_id']]['response'] ) { //adding each ticket beforemost response time
                        $staffs[$row['staff_id']]['tickets'][$row['ticket_id']]['response'] = $row['created'];
                    }
                }
                /*
                else { // staff already in the array
                    if ( !$staffs[$row['staff_id']]['tickets'][$row['ticket_id']]['response'] ) { //unique ticket id
                        $staffs[$row['staff_id']]['tickets'][$row['ticket_id']]['response'] = $row['created']; // adding ticket id
                        if ( is_today($row['created']) ) {
                            $staffs[$row['staff_id']]['tickets']['today'] = $row['ticket_id']; // responsed today
                        }
                    } else {
                        if ( $row['created'] < $staffs[$row['staff_id']]['tickets'][$row['ticket_id']]['response'] ) { //adding each ticket beforemost response time
                            $staffs[$row['staff_id']]['tickets'][$row['ticket_id']]['response'] = $row['created'];
                        }
                    }
                } */
            }
        }
        if ($staffs) {
            foreach ($staffs as $key=>$value) { // looping over
                $total_tkt_time = 0;
                $today_tkt_time = 0;
                foreach ($value['tickets'] as $ticketid=>$info) { // looping over tickets under each staff
                    $total_tkt_time += getintervalminutes($info['response']);
                    if ( is_today($info['response']) ) {
                        $today_tkt_time += getintervalminutes($info['response']);
                    }
                }
     ?>
                <tr>
                    <td><?php   echo   $value['name']  ?></td> <td><?php   echo   count($value['tickets']);  ?></td> <td><?php   echo   count($value['tickets']['today']);  ?></td> <td><?php   echo   count($value['tickets']['closed']);  ?></td> <td><?php   echo   count($value['tickets']['closedtoday']);  ?></td> <td><?php   echo   $total_tkt_time . ' minutes';  ?></td> <td> <?php   echo   $today_tkt_time . ' minutes';  ?> </td>
                </tr>
            <?php }  ?>
        <?php }  ?>
    </table>
</div>



<script type="text/javascript">
    $('div.inline').css('display', 'inline-block');
</script>