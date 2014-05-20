<?php
/**
 * show all tickets, contains html design and codes
 */
if (!defined('OSTCLIENTINC') || !is_object($thisuser) || !$thisuser->isValid())
    die('Kwaheri');

//Get ready for some deep shit.
$qstr = '&'; //Query string collector
$status = null;
$sla_tickets = null;
if ($_REQUEST['status']) { //Query string status has nothing to do with the real status used below.
    $qstr.='status=' . urlencode($_REQUEST['status']);
    //Status we are actually going to use on the query...making sure it is clean!
    switch (strtolower($_REQUEST['status'])) {
        case 'open':
        case 'closed':
            $status = $_REQUEST['status'];
            break;
        case 'sla':
            $sla_tickets = true;
            break;
        case 'no_sla':
            $sla_tickets = false;
            break;
        default:
            $status = ''; //ignore
    }
}

//Restrict based on id of the user...STRICT!
$qwhere = ' WHERE client_id=' . db_input($thisuser->getId());

//STATUS
if ($status) {
    $qwhere.=' AND status=' . db_input($status);
}
if ($sla_tickets===true) { //show sla claim tickets
    $qwhere.=' AND sla_claim_duration<>' . db_input('');
} elseif ($sla_tickets===false) { //show non sla claim tickets
$qwhere.=' AND sla_claim_duration=' . db_input('');
}

//dates
    $startTime  =($_REQUEST['startDate'] && (strlen($_REQUEST['startDate'])>=8))?strtotime($_REQUEST['startDate']):0;
    $endTime    =($_REQUEST['endDate'] && (strlen($_REQUEST['endDate'])>=8))?strtotime($_REQUEST['endDate']):0;
    if( ($startTime && $startTime>time()) or ($startTime>$endTime && $endTime>0)){
        $errors['err']='Entered date span is invalid. Selection ignored.';
        $startTime=$endTime=0;
    }else{
        //Have fun with dates.
        if($startTime){
            $qwhere.=' AND ticket.created>=FROM_UNIXTIME('.$startTime.')';
            $qstr.='&startDate='.urlencode($_REQUEST['startDate']);

        }
        if($endTime){
            $qwhere.=' AND ticket.created<=FROM_UNIXTIME('.$endTime.')';
            $qstr.='&endDate='.urlencode($_REQUEST['endDate']);
        }
    }


//Admit this crap sucks...but who cares??
$sortOptions = array('date' => 'ticket.created', 'ID' => 'ticketID', 'pri' => 'priority_id', 'dept' => 'dept_name');
$orderWays = array('DESC' => 'DESC', 'ASC' => 'ASC');

//Sorting options...
if ($_REQUEST['sort']) {
    $order_by = $sortOptions[$_REQUEST['sort']];
}
if ($_REQUEST['order']) {
    $order = $orderWays[$_REQUEST['order']];
}
if ($_GET['limit']) {
    $qstr.='&limit=' . urlencode($_GET['limit']);
}


$order_by = $order_by ? $order_by : 'ticket.created';
$order = $order ? $order : 'DESC';
$pagelimit = $_GET['limit'] ? $_GET['limit'] : PAGE_LIMIT;
$page = ($_GET['p'] && is_numeric($_GET['p'])) ? $_GET['p'] : 1;

$qselect = 'SELECT ticket.ticket_id,ticket.ticketID,ticket.dept_id,ticket.topic_id,isanswered,ispublic,subject,name,email ' .
        ',dept_name,status,sla_claim,source,priority_id ,ticket.created ';
$qfrom = ' FROM ' . TICKET_TABLE . ' ticket LEFT JOIN ' . DEPT_TABLE . ' dept ON ticket.dept_id=dept.dept_id ';

//Pagenation stuff....wish MYSQL could auto pagenate (something better than limit)
$total = db_count('SELECT count(*) ' . $qfrom . ' ' . $qwhere);
$pageNav = new Pagenate($total, $page, $pagelimit);
$pageNav->setURL('view.php', $qstr . '&sort=' . urlencode($_REQUEST['sort']) . '&order=' . urlencode($_REQUEST['order']));

//Ok..lets roll...create the actual query
$qselect.=' ,count(attach_id) as attachments ';
$qfrom.=' LEFT JOIN ' . TICKET_ATTACHMENT_TABLE . ' attach ON  ticket.ticket_id=attach.ticket_id ';
$qgroup = ' GROUP BY ticket.ticket_id';
$query = "$qselect $qfrom $qwhere $qgroup ORDER BY $order_by $order LIMIT " . $pageNav->getStart() . "," . $pageNav->getLimit();
//  echo   $query;
$tickets_res = db_query($query);
$showing = db_num_rows($tickets_res) ? $pageNav->showing() : "";
$results_type = ($status) ? ucfirst($status) . ' Tickets' : ' All Tickets';
$negorder = $order == 'DESC' ? 'ASC' : 'DESC'; //Negate the sorting..
 ?>
 
 <h1 align="center">Tickets</h1>
<br>
<br>
<div>
    <?php if ($errors['err']) {  ?>
        <p align="center" id="errormessage"><?php   echo   $errors['err']  ?></p>
    <?php } elseif ($msg) {  ?>
        <p align="center" id="infomessage"><?php   echo   $msg  ?></p>
    <?php } elseif ($warn) {  ?>
        <p id="warnmessage"><?php   echo   $warn  ?></p>
<?php }  ?>
</div>

<div id="ticket_grouped_view">
    <table class="tickets-table" align="center">
        <tr>
            <td nowrap >
                <a href="view.php?status=open" style="border: 2px solid green; padding: 5px">View open tickets</a>
                <a href="view.php?status=closed" style="border: 2px solid green; padding: 5px">View closed tickets</a>
                <a href="view.php?status=sla" style="border: 2px solid green; padding: 5px">SLA claim tickets</a>
                <a href="view.php?status=no_sla" style="border: 2px solid green; padding: 5px">SLA non claim tickets</a>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px"></td>
        </tr>
        <tr>
            <td>
                <form name="search_date" action="tickets.php" method="get">
                <span class="msg">Search Ticket</span> &nbsp; from Date&nbsp;<input id="sd" name="startDate" value="<?php   echo  Format::htmlchars($_REQUEST['startDate']) ?>"
                    onclick="event.cancelBubble=true;calendar(this);" autocomplete=OFF>
                <a href="#" onclick="event.cancelBubble=true;calendar(getObj('sd')); return false;"><img src='images/cal.png'border=0 alt=""></a>
                &nbsp;&nbsp; To Date &nbsp;&nbsp;
                <input id="ed" name="endDate" value="<?php   echo  Format::htmlchars($_REQUEST['endDate']) ?>"
                    onclick="event.cancelBubble=true;calendar(this);" autocomplete=OFF >
                    <a href="#" onclick="event.cancelBubble=true;calendar(getObj('ed')); return false;"><img src='images/cal.png'border=0 alt=""></a>
                &nbsp;&nbsp;
                <input type="submit" name="advance_search" value="Search">
                </form>
            </td>
        </tr>
    </table>
    <br>
    <br>
    <table id="ticketTable" width="800" border="0" cellspacing="0" cellpadding="0">
        <caption><?php   echo   $showing;  ?><a class="refresh" href="<?php   echo   $_SERVER['REQUEST_URI'];  ?>">Refresh</a></caption>
                    <tr>
                        <th width="70" nowrap>
                            <a href="view.php?sort=ID&order=<?php   echo   $negorder  ?><?php   echo   $qstr  ?>" title="Sort By Ticket ID <?php   echo   $negorder  ?>">Ticket #</a></th>
                        <th width="100">
                            <a href="view.php?sort=date&order=<?php   echo   $negorder  ?><?php   echo   $qstr  ?>" title="Sort By Date <?php   echo   $negorder  ?>">Create Date</a></th>
                        <th width="240">Problem</th>
                        <th >State</th>
                        <th >Last message date</th>
                    </tr>
                    <?php
                    $class = "row1";
                    $total = 0;
                    if ($tickets_res && ($num = db_num_rows($tickets_res))):
                        $defaultDept = Dept::getDefaultDeptName();
                        while ($row = db_fetch_array($tickets_res)) {
                            $dept = $row['ispublic'] ? $row['dept_name'] : $defaultDept; //Don't show hidden/non-public depts.
                            $subject = Format::htmlchars(Format::truncate($row['subject'], 40));
                            $ticketID = $row['ticketID'];
                            if ($row['isanswered'] && !strcasecmp($row['status'], 'open')) {
                                $subject = "<b>$subject</b>";

                                $ticketID = "<b>$ticketID</b>";
                            }
                            $topic_q = db_query('SELECT topic FROM ' . TOPIC_TABLE . ' WHERE topic_id='.$row['topic_id']);
                            $t_array = db_fetch_array($topic_q);
                            $problem = $t_array['topic'];
                             ?>
                            <tr class="<?php   echo   $class  ?> " id="<?php   echo   $row['ticketID']  ?>">
                                <td align="center" title="<?php   echo   $row['email']  ?>" nowrap>
                                    <a class="Icon <?php   echo   strtolower($row['source'])  ?>Ticket" title="<?php   echo   $row['email']  ?>" href="view.php?id=<?php   echo   $row['ticketID']  ?>">
        <?php   echo   'view this ticket'  ?></a></td>
                                <td nowrap>&nbsp;<?php   echo   Format::db_date($row['created'])  ?></td>
                                <td>&nbsp;<a href="view.php?id=<?php   echo   $row['ticketID']  ?>"><?php   echo   $row['subject']  ?></a>
                                    &nbsp;<?php   echo   $row['attachments'] ? "<span class='Icon file'>&nbsp;</span>" : ''  ?></td>
                                <td>&nbsp;<?php   echo   (($row['status']=='closed') && $row['sla_claim']) ? $row['status'].' & sla:'.$row['sla_claim'].'%' : $row['status']  ?></td>
                                <td><?php   echo   $row['lastmessage']  ?></td>
                            </tr>
                            <?php
                            $class = ($class == 'row2') ? 'row1' : 'row2';
                        } //end of while.
                    else: //not tickets found!!
                         ?>
                        <tr class="<?php   echo   $class  ?>"><td colspan=7><b>NO tickets found.</b></td></tr>
<?php endif;  ?>
        <tr><td>
                <?php if ($num > 0 && $pageNav->getNumPages() > 1) { //if we actually had any tickets returned ?>
            <tr><td colspan="5" style="text-align:left;padding-left:20px">page:<?php   echo   $pageNav->getPageLinks()  ?>&nbsp;</td></tr>
        <?php }  ?>
    </table>
</div>
