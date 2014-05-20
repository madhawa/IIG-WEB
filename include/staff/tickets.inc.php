<?php
if (!defined('OSTSCPINC') || !@$thisuser->isStaff())
    die('Access Denied');

//Get ready for some deep shit..(I admit..this could be done better...but the shit just works... so shutup for now).

$qstr = '&'; //Query string collector
if ($_REQUEST['status']) { //Query string status has nothing to do with the real status used below; gets overloaded.
    $qstr.='status=' . urlencode($_REQUEST['status']);
}

//See if this is a search
$search = $_REQUEST['a'] == 'search' ? true : false;
$searchTerm = '';
//make sure the search query is 3 chars min...defaults to no query with warning message
if ($search) {
    $searchTerm = $_REQUEST['query'];
    if (($_REQUEST['query'] && strlen($_REQUEST['query']) < 3) || (!$_REQUEST['query'] && isset($_REQUEST['basic_search']))) { //Why do I care about this crap...
        $search = false; //Instead of an error page...default back to regular query..with no search.
        $errors['err'] = 'Search term must be more than 3 chars';
        $searchTerm = '';
    }
}
$showoverdue = $showanswered = false;
$staffId = 0; //Nothing for now...TODO: Allow admin and manager to limit tickets to single staff level.
//Get status we are actually going to use on the query...making sure it is clean!
$status = null;
switch (strtolower($_REQUEST['status'])) { //Status is overloaded
    case 'open':
        $status = 'open';
        break;
    case 'closed':
        $status = 'closed';
        break;
    case 'overdue':
        $status = 'open';
        $showoverdue = true;
        $results_type = 'Overdue Tickets';
        break;
    case 'assigned':
        //$status='Open'; //
        $staffId = $thisuser->getId();
        break;
    case 'answered':
        $status = 'open';
        $showanswered = true;
        $results_type = 'Answered Tickets';
        break;
    default:
        if (!$search)
            $status = 'open';
}

// This sucks but we need to switch queues on the fly! depending on stats fetched on the parent.
if ($stats) {
    if (!$stats['open'] && (!$status || $status == 'open')) {
        if (!$cfg->showAnsweredTickets() && $stats['answered']) {
            $status = 'open';
            $showanswered = true;
            $results_type = 'Answered Tickets';
        } elseif (!$stats['answered']) { //no open or answered tickets (+-queue?) - show closed tickets.???
            $status = 'closed';
            $results_type = 'Closed Tickets';
        }
    }
}

$qwhere = ' WHERE ticket.ticketID<>' . db_input('');
//STATUS
if ($status) {
    $qwhere.=' AND status=' . db_input(strtolower($status));
}

//Sub-statuses Trust me!
if ($staffId && ($staffId == $thisuser->getId())) { //Staff's assigned tickets.
    $results_type = 'Assigned Tickets';
    $qwhere.=' AND ticket.staff_id=' . db_input($staffId);
} elseif ($showoverdue) { //overdue
    $qwhere.=' AND isoverdue=1 ';
} elseif ($showanswered) { ////Answered
    $qwhere.=' AND isanswered=1 ';
} elseif (!$search && !$cfg->showAnsweredTickets() && !strcasecmp($status, 'open')) {
    $qwhere.=' AND isanswered=0 ';
}


//Search?? Somebody...get me some coffee
$deep_search = false;
if ($search):
    $qstr.='&a=' . urlencode($_REQUEST['a']);
    $qstr.='&t=' . urlencode($_REQUEST['t']);
    if (isset($_REQUEST['advance_search'])) { //advance search box!
        $qstr.='&advance_search=Search';
    }

    //query
    if ($searchTerm) {
        $qstr.='&query=' . urlencode($searchTerm);
        $queryterm = db_real_escape($searchTerm, false); //escape the term ONLY...no quotes.
        if (is_numeric($searchTerm)) {
            $qwhere.=" AND ticket.ticketID LIKE '$queryterm%'";
        } elseif (strpos($searchTerm, '@') && Validator::is_email($searchTerm)) { //pulling all tricks!
            $qwhere.=" AND ticket.email='$queryterm'";
        } else {//Deep search!
            //This sucks..mass scan! search anything that moves!
            $deep_search = true;
            if ($_REQUEST['stype'] && $_REQUEST['stype'] == 'FT') { //Using full text on big fields.
                $qwhere.=" AND ( ticket.email LIKE '%$queryterm%'" .
                        " OR ticket.name LIKE '%$queryterm%'" .
                        " OR ticket.subject LIKE '%$queryterm%'" .
                        " OR note.title LIKE '%$queryterm%'" .
                        " OR MATCH(message.message)   AGAINST('$queryterm')" .
                        " OR MATCH(response.response) AGAINST('$queryterm')" .
                        " OR MATCH(note.note) AGAINST('$queryterm')" .
                        ' ) ';
            } else {
                $qwhere.=" AND ( ticket.email LIKE '%$queryterm%'" .
                        " OR ticket.name LIKE '%$queryterm%'" .
                        " OR ticket.subject LIKE '%$queryterm%'" .
                        " OR message.message LIKE '%$queryterm%'" .
                        " OR response.response LIKE '%$queryterm%'" .
                        " OR note.note LIKE '%$queryterm%'" .
                        " OR note.title LIKE '%$queryterm%'" .
                        ' ) ';
            }
        }
    }
    //department
    if ($_REQUEST['dept'] && ($thisuser->isadmin() || in_array($_REQUEST['dept'], $thisuser->getDepts()))) {
        //This is dept based search..perm taken care above..put the sucker in.
        $qwhere.=' AND ticket.dept_id=' . db_input($_REQUEST['dept']);
        $qstr.='&dept=' . urlencode($_REQUEST['dept']);
    }
    //dates
    $startTime = ($_REQUEST['startDate'] && (strlen($_REQUEST['startDate']) >= 8)) ? strtotime($_REQUEST['startDate']) : 0;
    $endTime = ($_REQUEST['endDate'] && (strlen($_REQUEST['endDate']) >= 8)) ? strtotime($_REQUEST['endDate']) : 0;
    if (($startTime && $startTime > time()) or ( $startTime > $endTime && $endTime > 0)) {
        $errors['err'] = 'Entered date span is invalid. Selection ignored.';
        $startTime = $endTime = 0;
    } else {
        //Have fun with dates.
        if ($startTime) {
            $qwhere.=' AND ticket.created>=FROM_UNIXTIME(' . $startTime . ')';
            $qstr.='&startDate=' . urlencode($_REQUEST['startDate']);
        }
        if ($endTime) {
            $qwhere.=' AND ticket.created<=FROM_UNIXTIME(' . $endTime . ')';
            $qstr.='&endDate=' . urlencode($_REQUEST['endDate']);
        }
    }

endif;

//grouping tickets for asia-ahl
$action = $_POST['action'];
if ($action == 'sort_tickets') {
    $sort_by = $_POST['sort_by'];
    $select_client = $_POST['select_client'];
    $service_type = $_POST['select_service_type'];
    if (($sort_by == 'Customer') && $select_client) {
        $qwhere .= ' AND ticket.client_id=' . db_input($select_client);
    } elseif (($sort_by == 'Services') && $service_type) {
        $parts = preg_split('/\s+/', $service_type);
        if (is_array($parts) && count($parts) > 1) {
            foreach ($parts as $index => $p) {
                if ($index == 0) {
                    $qwhere .= " AND ( ticket.cin LIKE '%$p%'";
                } else {
                    $qwhere .= " OR ticket.cin LIKE '%$p%'";
                }
            }
            $qwhere .= ' )';
        }
        $qwhere .= " AND ticket.cin LIKE '%$service_type%'";
    }
}

//I admit this crap sucks...but who cares??
$sortOptions = array('date' => 'ticket.created', 'respdate'=>'ticket.lastresponse', 'msgdate'=>'ticket.lastmessage', 'ID' => 'ticketID', 'pri' => 'priority_urgency', 'dept' => 'dept_name');
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
if (!$order_by && $showanswered) {
    $order_by = 'ticket.lastresponse DESC, ticket.created'; //No priority sorting for answered tickets.
} elseif (!$order_by && !strcasecmp($status, 'closed')) {
    $order_by = 'ticket.closed DESC, ticket.created'; //No priority sorting for closed tickets.
}


$order_by = $order_by ? $order_by : 'ticket.created';
$order = $order ? $order : 'DESC';
$pagelimit = $_GET['limit'] ? $_GET['limit'] : $thisuser->getPageLimit();
$pagelimit = $pagelimit ? $pagelimit : 20; //true default...if all fails.
$page = ($_GET['p'] && is_numeric($_GET['p'])) ? $_GET['p'] : 1;


$qselect = 'SELECT DISTINCT ticket.* ,count(attach.attach_id) as attachments ';
$qfrom = ' FROM ' . TICKET_TABLE . ' ticket ';

if ($search && $deep_search) {
    $qfrom.=' LEFT JOIN ' . TICKET_MESSAGE_TABLE . ' message ON (ticket.ticket_id=message.ticket_id )';
    $qfrom.=' LEFT JOIN ' . TICKET_RESPONSE_TABLE . ' response ON (ticket.ticket_id=response.ticket_id )';
    $qfrom.=' LEFT JOIN ' . TICKET_NOTE_TABLE . ' note ON (ticket.ticket_id=note.ticket_id )';
}

$qgroup = ' GROUP BY ticket.ticket_id';
//get ticket count based on the query so far..
$total = db_count('SELECT count(*) ' . $qfrom . ' ' . $qwhere);
//pagenate
$pageNav = new Pagenate($total, $page, $pagelimit);
$pageNav->setURL('tickets.php', $qstr . '&sort=' . urlencode($_REQUEST['sort']) . '&order=' . urlencode($_REQUEST['order']));
//
//Ok..lets roll...create the actual query
//ADD attachment,priorities and lock crap
$qselect.=' ,count(attach.attach_id) as attachments, IF(ticket.reopened is NULL,ticket.created,ticket.reopened) as effective_date';
$qfrom.=' LEFT JOIN ' . TICKET_PRIORITY_TABLE . ' pri ON ticket.priority_id=pri.priority_id ' .
        ' LEFT JOIN ' . TICKET_LOCK_TABLE . ' tlock ON ticket.ticket_id=tlock.ticket_id AND tlock.expire>NOW() ' .
        ' LEFT JOIN ' . TICKET_ATTACHMENT_TABLE . ' attach ON  ticket.ticket_id=attach.ticket_id ';

$query = "$qselect $qfrom $qwhere $qgroup ORDER BY $order_by $order LIMIT " . $pageNav->getStart() . "," . $pageNav->getLimit();

$tickets_res = db_query($query);
$showing = db_num_rows($tickets_res) ? $pageNav->showing() : "";
if (!$results_type) {
    $results_type = ($search) ? 'Search Results' : ucfirst($status) . ' Tickets';
}
$negorder = $order == 'DESC' ? 'ASC' : 'DESC'; //Negate the sorting..
//Permission  setting we are going to reuse.
$canDelete = $canClose = false;
$canDelete = $thisuser->canDeleteTickets();
$canClose = $thisuser->canCloseTickets();
$basic_display = !isset($_REQUEST['advance_search']) ? true : false;

//YOU BREAK IT YOU FIX IT.
?>
<div>
    <?php if ($errors['err']) { ?>
        <p align="center" class="errormessage"><?php echo $errors['err'] ?></p>
    <?php } elseif ($msg) { ?>
        <p align="center" class="infomessage"><?php echo $msg ?></p>
    <?php } elseif ($warn) { ?>
        <p id="warnmessage"><?php echo $warn ?></p>
    <?php } ?>
</div>
<!-- SEARCH FORM START -->
<!--
<div id='basic' style="display:<?php echo $basic_display ? 'block' : 'none' ?>">
    <form action="tickets.php" method="get">
    <input type="hidden" name="a" value="search">
    <table>
        <tr>
            <td>Query: </td>
            <td><input type="text" id="query" name="query" size=30 value="<?php echo Format::htmlchars($_REQUEST['query']) ?>"></td>
            <td><input type="submit" name="basic_search" class="button" value="Search">
             &nbsp;[<a href="#" onClick="showHide('basic','advance'); return false;">Advanced</a> ] </td>
        </tr>
    </table>
    </form>
</div>
<div id='advance' style="display:<?php echo $basic_display ? 'none' : 'block' ?>">
 <form action="tickets.php" method="get">
 <input type="hidden" name="a" value="search">
  <table>
    <tr>
        <td>Query: </td><td><input type="text" id="query" name="query" value="<?php echo Format::htmlchars($_REQUEST['query']) ?>"></td>
        <td>Status is:</td><td>

        <select name="status">
            <option value='any' selected >Any status</option>
            <option value="open" <?php echo!strcasecmp($_REQUEST['status'], 'Open') ? 'selected' : '' ?>>Open</option>
            <option value="overdue" <?php echo!strcasecmp($_REQUEST['status'], 'overdue') ? 'selected' : '' ?>>Overdue</option>
            <option value="closed" <?php echo!strcasecmp($_REQUEST['status'], 'Closed') ? 'selected' : '' ?>>Closed</option>
        </select>
        </td>
     </tr>
    </table>
    <div>
        Date Span:
        &nbsp;From&nbsp;<input id="sd" name="startDate" value="<?php echo Format::htmlchars($_REQUEST['startDate']) ?>"
                onclick="event.cancelBubble=true;calendar(this);" autocomplete=OFF>
            <a href="#" onclick="event.cancelBubble=true;calendar(getObj('sd')); return false;"><img src='images/cal.png'border=0 alt=""></a>
            &nbsp;&nbsp; to &nbsp;&nbsp;
            <input id="ed" name="endDate" value="<?php echo Format::htmlchars($_REQUEST['endDate']) ?>"
                onclick="event.cancelBubble=true;calendar(this);" autocomplete=OFF >
                <a href="#" onclick="event.cancelBubble=true;calendar(getObj('ed')); return false;"><img src='images/cal.png'border=0 alt=""></a>
            &nbsp;&nbsp;
    </div>
    <table>
    <tr>
       <td>Type:</td>
       <td>
        <select name="stype">
            <option value="LIKE" <?php echo (!$_REQUEST['stype'] || $_REQUEST['stype'] == 'LIKE') ? 'selected' : '' ?>>Scan (%)</option>
            <option value="FT"<?php echo $_REQUEST['stype'] == 'FT' ? 'selected' : '' ?>>Fulltext</option>
        </select>


       </td>
       <td>Sort by:</td><td>
<?php
$sort = $_GET['sort'] ? $_GET['sort'] : 'date';
?>
        <select name="sort">
            <option value="ID" <?php echo $sort == 'ID' ? 'selected' : '' ?>>Ticket #</option>
            <option value="pri" <?php echo $sort == 'pri' ? 'selected' : '' ?>>Priority</option>
            <option value="date" <?php echo $sort == 'date' ? 'selected' : '' ?>>Date</option>
            <option value="dept" <?php echo $sort == 'dept' ? 'selected' : '' ?>>Dept.</option>
        </select>
        <select name="order">
            <option value="DESC"<?php echo $_REQUEST['order'] == 'DESC' ? 'selected' : '' ?>>Descending</option>
            <option value="ASC"<?php echo $_REQUEST['order'] == 'ASC' ? 'selected' : '' ?>>Ascending</option>
        </select>
       </td>
        <td>Results Per Page:</td><td>
        <select name="limit">
<?php
$sel = $_REQUEST['limit'] ? $_REQUEST['limit'] : 15;
for ($x = 5; $x <= 25; $x += 5) {
    ?>
                    <option  value="<?php echo $x ?>" <?php echo ($sel == $x ) ? 'selected' : '' ?>><?php echo $x ?></option>
<?php } ?>
            </select>
         </td>
         <td>
         <input type="submit" name="advance_search" class="button" value="Search">
           &nbsp;[ <a href="#" onClick="showHide('advance','basic'); return false;" >Basic</a> ]
        </td>
      </tr>
     </table>
     </form>
    </div>
    <script type="text/javascript">

        var options = {
            script:"ajax.php?api=tickets&f=search&limit=10&",
            varname:"input",
            shownoresults:false,
            maxresults:10,
            callback: function (obj) { document.getElementById('query').value = obj.id; document.forms[0].submit();}
        };
        var autosug = new bsn.AutoSuggest('query', options);
    </script>
-->
<!-- NEW SEARCH FORM -->
<!-- <div id="shalow_search">
    <a href="">not yet assigned</a>&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="" >assigned</a>&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="" >created by today</a>&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="" >created by last hour</a>&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="" >response in last hour</a>&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="" >all by status: open</a>&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="" >closed today</a>
</div> -->

<!-- <button type="button" name="deep_search">Advanced search</button> disabled currently,TODO: will be in next version-->
<div id="deep_search">
<h4 align="center">deep search</h4>
<form action="" method="post">
    <input type="hidden" name="action" value="sort_tickets">
<!--     <select style="margin-left: 100px" name="sort_by">
        <option value="">Sort tickets by:</option>
        <option value="Customer" <?php if ($sort_by == 'Customer') echo 'selected'; ?>>Customer</option>
        <option value="Services" <?php if ($sort_by == 'Services') echo 'selected'; ?>>Services</option>
    </select>
    &nbsp;&nbsp;&nbsp;&nbsp; -->
    <select name="select_client">
        <option value="">Select a client</option>
        <?php foreach ($clients as $client) { ?>
            <option value="<?php echo $client['client_id'] ?>" <?php if ($select_client == $client['client_id']) echo 'selected'; ?>><?php echo $client['client_name'] ?></option>
        <?php } ?>
    </select>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <select name="select_service_type">
        <option value="">Select service type</option>
        <option value="only IP Transit" <?php if ($service_type == 'only IP Transit') echo 'selected'; ?> >only IP Transit</option>
        <option value="IP Bandwidth" <?php if ($service_type == 'IP Bandwidth') echo 'selected'; ?> >IP Bandwidth</option>
        <option value="IP transit + IPLC[Full Circuit]" <?php if ($service_type == 'IP transit + IPLC[Full Circuit]') echo 'selected'; ?> >IP transit + IPLC[Full Circuit]</option>
        <option value="P transit + IPLC[half Circuit]" <?php if ($service_type == 'IP transit + IPLC[half Circuit]') echo 'selected'; ?> >IP transit + IPLC[half Circuit]</option>
        <option value="IPLC[Half Circuit]" <?php if ($service_type == 'IPLC[Half Circuit]') echo 'selected'; ?> >IPLC[Half Circuit]</option>
        <option value="IPLC[Full Circuit]" <?php if ($service_type == 'IPLC[Full Circuit]') echo 'selected'; ?> >IPLC[Full Circuit]</option>
        <option value="Global MPLS" <?php if ($service_type == 'Global MPLS') echo 'selected'; ?> >Global MPLS</option>
        <option value="Internartional Ethernet" <?php if ($service_type == 'Internartional Ethernet') echo 'selected'; ?> >Internartional Ethernet</option>
        <option value="Co-Location" <?php if ($service_type == 'Co-Location') echo 'selected'; ?> >Co-Location</option>
    </select>
    <br>
    <label for="search_date_type">date range</label>
    <select name="search_date_type">
        <option value="">select date range type</option>
        <option value="create">Ticket Create date</option>
        <option value="message">Client message date</option>
        <option value="response">NOC response date</option>
    </select>
    
    <input style="width: 100px" id="sd" class="ticket_datetimepicker" name="startDate" value="<?php   echo  Format::htmlchars($_REQUEST['startDate']) ?>"
         autocomplete=OFF placeholder="from">
    <a href="#" ><img src='images/cal.png'border=0 alt=""></a>

    <input style="width: 100px" id="ed" class="ticket_datetimepicker" name="endDate" value="<?php   echo  Format::htmlchars($_REQUEST['endDate']) ?>"
         autocomplete=OFF placeholder="to">
        <a href="#" ><img src='images/cal.png'border=0 alt=""></a>
    <br>
    <label for="field_name">text search</label>
    <select name="field_name">
        <option value="">select a field</option>
        <option value="subject">subject</option>
        <option value="message">message</option>
        <option value="cin">CIN</option>
        <option value="root_cause">Root Cause</option>
        <option value="any_email">Client email/cc email contains</option>
        <option value="sla_duration">SLA claim</option>
        <option value="sla_duration">SLA claim duration</option>
        <option value="sla_duration_upto">SLA claim duration up to</option>
        <option value="sla_duration_above">SLA claim duration above</option>
        <option value="downtime_duration">downtime duration</option>
        <option value="downtime_duration_above">downtime duration above</option>
        <option value="downtime_duration_upto">downtime duration upto</option>
        <option value="assignee_name">Assignee name</option>
    </select>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    contains text: <input type="text" name="contains_text" value="" placeholder="what to search">
    <br>
    <button type="submit" class="save" name="view_sorted">View Sorted</button>
</form>
</div>
<br>
<br>
<script type="text/javascript">
$('.ticket_datetimepicker').datetimepicker({
    controlType: 'select',
    dateFormat: "dd-mm-yy",
    timeFormat: 'hh:mm tt'
});
$('#shalow_search').css({
    'margin-top': '20px',
    'margin-left': '50px'
    });
$('#shalow_search a').css({
    'border': '5px solid black',
    'padding': '5px'
    });
$('h4').css({
    'margin-bottom': '20px'
    });
$('#deep_search').css({
    'display': 'table',
    'border': '5px solid green',
    'padding': '0 20px',
    'margin': '50px 50px 0',
    'background-color': '#F4FAFF',
    'position': 'fixed'
});

$('div#deep_search').hide()
$('button[name="deep_search"]').click(function(event) {
    $('div#deep_search').toggle('slide');
});

//reload
var Intr = setInterval(function() {
    window.location.reload(false); //cached reloading
}, 1000*60);
/*
    //first time at page loading
    if (!$('[name="select_client"]').val()) {
        $('[name="select_client"]').hide();
    }
    if (!$('[name="select_service_type"]').val()) {
        $('[name="select_service_type"]').hide();
    }

    //now selecting
    $('[name="sort_by"]').change(function(event) {
        var sort_by = $(event.target).val();
        if (sort_by) {
            if (sort_by == 'Customer') {
                $('[name="select_client"]').show();
                $('[name="select_service_type"]').hide();
            } else if (sort_by == 'Services') {
                $('[name="select_service_type"]').show();
                $('[name="select_client"]').hide();
            }
        } else {
            $('[name="select_client"], [name="select_service_type"]').hide();
        }
    });
*/
</script>
<!-- SEARCH FORM END -->
<h3 align="center"><?php echo $showing.' : '.$results_type ?> <a style="margin-left: 50px" href="">refresh</a></h3>
<hr>
<div style="margin-bottom:20px">
<!--     <table width="100%" border="0" cellspacing=0 cellpadding=0 align="center">
        <tr>
            <td width="80%" class="msg" >&nbsp;<b><?php echo $showing ?>&nbsp;&nbsp;&nbsp;<?php echo $results_type ?></b></td>
            <td nowrap style="text-align:right;padding-right:20px;">
                <a href=""><img src="images/refresh.gif" alt="Refresh" border=0></a>
            </td>
        </tr>
    </table> -->
    <table width="100%" border="0" cellspacing=1 cellpadding=2>
        <form action="tickets.php" method="POST" name='tickets' onSubmit="return checkbox_checker(this, 1, 0);">
            <input type="hidden" name="a" value="mass_process" >
            <input type="hidden" name="status" value="<?php echo $statusss ?>" >
            <tr><td>
                    <table width="100%" border="0" cellspacing=0 cellpadding=2 class="dtable" align="center">
                        <tr>
                            <?php if ($canDelete || $canClose) { ?>
                                <th width="8px" style="padding: 10px">&nbsp;</th>
                            <?php } ?>
                            <th width="70" >
                                <a href="tickets.php?sort=ID&order=<?php echo $negorder ?><?php echo $qstr ?>" title="Sort By Ticket ID <?php echo $negorder ?>">Customer</a>
                            </th>
                            <th width="70">
                                <a href="tickets.php?sort=date&order=<?php echo $negorder ?><?php echo $qstr ?>" title="Sort By Date <?php echo $negorder ?>">Create Date</a>
                            </th>
                            <th><a href="tickets.php?sort=subject&order=<?php echo $negorder ?><?php echo $qstr ?>" title="Sort By Subject <?php echo $negorder ?>">subject</a></th>
                            <th><a href="tickets.php?sort=root_cause&order=<?php echo $negorder ?><?php echo $qstr ?>" title="Sort By Root cause <?php echo $negorder ?>">Root Cause</a></th>
                            <th><a href="tickets.php?sort=cin&order=<?php echo $negorder ?><?php echo $qstr ?>" title="Sort By CIN <?php echo $negorder ?>">CIN</a></th>
                            <th><a href="tickets.php?sort=service_type&order=<?php echo $negorder ?><?php echo $qstr ?>" title="Sort By service type <?php echo $negorder ?>">service type</a></th>
                            <th><a href="tickets.php?sort=circuit_type&order=<?php echo $negorder ?><?php echo $qstr ?>" title="Sort By circuit type <?php echo $negorder ?>">circuit type</a></th>
                            <th><a href="tickets.php?sort=sla_claim&order=<?php echo $negorder ?><?php echo $qstr ?>" title="Sort By sla claim <?php echo $negorder ?>">SLA claim</a></th>
                            <th><a href="tickets.php?sort=created_by_noc&order=<?php echo $negorder ?><?php echo $qstr ?>" title="Sort By source <?php echo $negorder ?>">by noc</a></th>
                            <th><a href="tickets.php?sort=msgdate&order=<?php echo $negorder ?><?php echo $qstr ?>" title="Sort By Client MEssage Date <?php echo $negorder ?>">Last Message(client)</a></th>
                            <th><a href="tickets.php?sort=respdate&order=<?php echo $negorder ?><?php echo $qstr ?>" title="Sort By NOC Response Date <?php echo $negorder ?>">Last Response(NOC)</a></th>
                            <th>assigned to</th>
                        </tr>
                        <?php
                        $class = "row1";
                        $total = 0;
                        if ($tickets_res && ($num = db_num_rows($tickets_res))):
                            while ($row = db_fetch_array($tickets_res)) {
                                $client_id = $row['client_id'];
                                $client = new Client($client_id);
                                $client_name = $client->getName();

                                $tag = $row['staff_id'] ? 'assigned' : 'openticket';
                                $flag = null;
                                if ($row['lock_id'])
                                    $flag = 'locked';
                                elseif ($row['staff_id'])
                                    $flag = 'assigned';
                                elseif ($row['isoverdue'])
                                    $flag = 'overdue';

                                $tid = $row['ticketID'];
                                //$subject = Format::truncate($row['subject'],40);
                                $topic_q = db_query('SELECT topic FROM ' . TOPIC_TABLE . ' WHERE topic_id=' . $row['topic_id']);
                                $t_array = db_fetch_array($topic_q);
                                $problem = $t_array['topic'];
                                if (!strcasecmp($row['status'], 'open') && !$row['isanswered'] && !$row['lock_id']) {
                                    $tid = sprintf('<b>%s</b>', $tid);
                                    //$subject=sprintf('<b>%s</b>',Format::truncate($row['subject'],40)); // Making the subject bold is too much for the eye
                                }
                                $ticket = new Ticket($row['ticket_id']);
                                ?>
                                <tr class="<?php echo $class ?> " id="<?php echo $row['ticket_id'] ?>">
                                    <?php if ($canDelete || $canClose) { ?>
                                        <td align="center" class="nohover">
                                            <input type="checkbox" name="tids[]" value="<?php echo $row['ticket_id'] ?>" onClick="highLight(this.value, this.checked);">
                                        </td>
                                    <?php } ?>
                                    <td align="center" title="<?php echo $row['email'] ?>" nowrap>
                                        <a class="Icon <?php echo strtolower($row['source']) ?>Ticket" href="tickets.php?id=<?php echo $row['ticket_id'] ?>"><?php echo $ticket->getName(); ?>
                                        </a>
                                    </td>
                                    <td align="center" nowrap><?php echo Format::db_date($row['created']) ?></td>
                                    <td><a <?php if ($flag) { ?> class="Icon <?php echo $flag ?>Ticket" title="<?php echo ucfirst($flag) ?> Ticket" <?php } ?> href="tickets.php?id=<?php echo $row['ticket_id'] ?>"><?php echo $row['subject'] ?></a>
                                        &nbsp;<?php echo $row['attachments'] ? "<span class='Icon file'>&nbsp;</span>" : '' ?></td>
                                    <td nowrap><?php echo $ticket->get_root_cause(); ?></td>
                                    <td nowrap><?php echo $ticket->getCINValue(); ?></td>
                                    <td nowrap><?php echo $ticket->getCIN()->get_service_type; ?></td>
                                    <td nowrap><?php echo $ticket->getCIN()->get_circuit_type; ?></td>
                                    <td nowrap><?php echo $ticket->is_sla_ticket() ? 'yes' : 'no'; ?></td>
                                    <td nowrap><?php echo $ticket->isNOCTT() ? 'yes' : 'no'; ?></td>
                                    <td><?php echo Format::db_date($row['lastmessage']); ?></td>
                                    <td><?php echo Format::db_date($row['lastresponse']); ?></td>
                                    <td>
                                        <?php
                                        if ($row['staff_id']) {
                                            $assigned_staff = new Staff($row['staff_id']);
                                            if (is_object($assigned_staff)) {
                                                echo $assigned_staff->getName();
                                            } else {
                                                echo 'unassigned';
                                            }
                                        } else {
                                            echo 'unassigned';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                $class = ($class == 'row2') ? 'row1' : 'row2';
                            } //end of while.
                        else: //not tickets found!! 
                            ?>
                            <tr class="<?php echo $class ?>"><td colspan=11><b>Query returned 0 results.</b></td></tr>
                        <?php endif; ?>
                    </table>
                </td></tr>
            <?php
            if ($num > 0) { //if we actually had any tickets returned.
                ?>
                <tr><td style="padding-left:20px">
                        <?php if ($canDelete || $canClose) { ?>
                            Select:
                            <a href="#" onclick="return select_all(document.forms['tickets'], true)">All</a>&nbsp;
                            <a href="#" onclick="return reset_all(document.forms['tickets'])">None</a>&nbsp;
                            <a href="#" onclick="return toogle_all(document.forms['tickets'], true)">Toggle</a>&nbsp;
                        <?php } ?>
                        page:<?php echo $pageNav->getPageLinks() ?>
                    </td></tr>
                <?php if ($canClose or $canDelete) { ?>
                    <tr><td align="center"> <br>
                            <?php
                            $status = $_REQUEST['status'] ? $_REQUEST['status'] : $status;

                            //If the user can close the ticket...mass reopen is allowed.
                            //If they can delete tickets...they are allowed to close--reopen..etc.
                            switch (strtolower($status)) {
                                case 'closed':
                                    ?>
                                    <input class="button" type="submit" name="reopen" value="Reopen"
                                           onClick=' return confirm("Are you sure you want to reopen selected tickets?");'>
                                           <?php
                                           break;
                                       case 'open':
                                       case 'answered':
                                       case 'assigned':
                                           ?>
                                    <input class="button" type="submit" name="close" value="Close"
                                           onClick=' return confirm("Are you sure you want to close selected tickets?");'>
                                           <?php
                                           break;
                                       default: //search??
                                           ?>
                                    <input class="button" type="submit" name="close" value="Close"
                                           onClick=' return confirm("Are you sure you want to close selected tickets?");'>
                                    <input class="button" type="submit" name="reopen" value="Reopen"
                                           onClick=' return confirm("Are you sure you want to reopen selected tickets?");'>
                                       <?php
                                   }
                                   if ($canDelete) {
                                       ?>
                                <input class="button" type="submit" name="delete" value="Delete"
                                       onClick=' return confirm("Are you sure you want to DELETE selected tickets?");'>
                                   <?php } ?>
                        </td></tr>
                <?php
                }
            }
            ?>
        </form>
    </table>
</div>

<?php
