<?php
//Note that ticket is initiated in tickets.php.
if (!defined('OSTSCPINC') || !@$thisuser->isStaff() || !is_object($ticket))
    die('Invalid path');
/*
  if(!$ticket->getId() or (!$thisuser->canAccessDept($ticket->getDeptId()) and $thisuser->getId()!=$ticket->getStaffId())) die('Access Denied');
 */

$info = ($_POST && $errors) ? Format::input($_POST) : array(); //Re-use the post info on error...savekeyboards.org
//Auto-lock the ticket if locking is enabled..if locked already simply renew it.
if ($cfg->getLockTime() && !$ticket->acquireLock())
    $warn.='Unable to obtain a lock on the ticket';

//We are ready baby...lets roll. Akon rocks!
$dept = $ticket->getDept();  //Dept
$staff = $ticket->getStaff(); //Assiged staff.
$lock = $ticket->getLock();  //Ticket lock obj
$id = $ticket->getId(); //Ticket ID.

$ticket_info = $ticket->getInfo();

if ($staff)
    $warn.='&nbsp;&nbsp;<span class="Icon assignedTicket">Ticket is assigned to ' . $staff->getName() . '</span>';
if (!$errors['err'] && ($lock && $lock->getStaffId() != $thisuser->getId()))
    $errors['err'] = 'This ticket is currently locked by another staff member!';
if (!$errors['err'] && ($emailBanned = BanList::isbanned($ticket->getEmail())))
    $errors['err'] = 'Email is in banlist! Must be removed before any reply/response';
if ($ticket->isOverdue())
    $warn.='&nbsp;&nbsp;<span class="Icon overdueTicket">Marked overdue!</span>';
?>

<!-- <?php if ($thisuser->canEditTickets()) { ?>
        <button type="button" onclick="go_there(<?php echo '&#39;' . SCP_URL . '/tickets.php?action=edit_ticket&amp;ticket_id=' . $ticket->getId() . '&#39;' ?>)">edit this ticket</button>
        
        <script type="text/javascript">
            function go_there(url) {
                window.location.href = url;
            }
        </script>
<?php } ?>

<table align="center" cellspacing="2" cellpadding="5" border=1 style="display: none">
    <tr>
        <td colspan="2">
            <div><h3>Ticket Info</h3></div>
            <hr>
        </td>
    </tr>
    <tr>
        <th>Status:</th>
        <td><?php echo (($ticket->getStatus() == 'closed') && $ticket->getSLAClaim()) ? $ticket->getStatus() . ' & sla:' . $ticket->getSLAClaim() . '%' : $ticket->getStatus() ?></td>
    </tr>
    <tr>
        <th>Assigned executive:</th>
        <td><?php echo $staff ? Format::htmlchars($staff->getName()) : '- unassigned -' ?></td>
    </tr>
    <tr>
        <th>Subject</th>
        <td><?php echo Format::htmlchars($ticket->getSubject()) ?></td>
    </tr>
    <tr>
        <th>Root cause</th>
        <td><?php echo Format::htmlchars($ticket_info['root_cause']) ?></td>
    </tr>

    <tr>
        <th>Create Date:</th>
        <td><?php echo Format::db_date($ticket->getCreateDate()) ?></td>
    </tr>
    <tr><th nowrap>Last Message(client):</th>
        <td><?php echo Format::db_datetime($ticket->getLastMessageDate()) ?></td>
    </tr>
    <tr>
        <th nowrap>Last Response(NOC):</th>
        <td><?php echo Format::db_datetime($ticket->getLastResponseDate()) ?></td>
    </tr>
<?php if ($ticket->isOpen()) { ?>
<?php } else {
    ?>
            <tr>
                <th>Close Date:</th>
                <td><?php echo Format::db_datetime($ticket->getCloseDate()) ?></td>
            </tr>
    <?php
}
?>
    <tr>
        <th>Cc email address</th>
        <td><?php echo $ticket->getAltEmail(); ?></td>
    </tr>
    <tr>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2">
            <div><h3>Client Info</h3></div>
            <hr>
        </td>
    </tr>
    <tr>
        <th>Client Name:</th>
        <td><?php echo Format::htmlchars($ticket->getName()) ?></td>
    </tr>
    <tr>
        <th>Email:</th>
        <td><?php
echo $ticket->getEmail();
?>
        </td>
    </tr>
    <tr>
        <th>Phone:</th>
        <td><?php echo Format::phone($ticket->getPhoneNumber()) ?></td>
    </tr>
<?php if ($ticket_info['raiser_name']) { ?>
            <tr>
                <td colspan="2">
                    <div>
                        <h3>Ticket raiser info</h3>
                    </div>
                    <hr>
                </td>
            </tr>
            <th>Raised by</th>
            <td><?php echo $ticket_info['raised_from']; ?></td>
        </tr>
        <tr>
            <th>Name</th>
            <td><?php echo $ticket_info['raiser_name']; ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo $ticket_info['raiser_email']; ?></td>
        </tr>
        <tr>
            <th>Phone</th>
            <td><?php echo $ticket_info['raiser_phone']; ?></td>
        </tr>
<?php } ?>


<tr>
    <td colspan="2"></td>
</tr>
<tr>
    <th>CIN</th>
    <td><?php echo $ticket_info['cin']; ?></td>
</tr>
<tr>
    <td colspan="2"></td>
</tr>

<?php if ($ticket_info['site_visited_by']) { ?>
        <tr>
            <td colspan="2">
                <div><h3>Site Info</h3></div>
                <hr>
            </td>
        </tr>
        <tr>
            <th>Site visited by</th>
            <td><?php echo $ticket_info['site_visited_by']; ?></td>
        </tr>
        <tr>
            <th>Site visited date</th>
            <td><?php echo $ticket_info['site_visited_date']; ?></td>
        </tr>
<?php } ?>

<?php if ($ticket_info['link_down_date']) { ?>
        <tr>
            <td colspan="2">
                <div><h3>Link Info</h3></div>
                <hr>
            </td>
        </tr>
        <tr>
            <th>Link down date</th>
            <td><?php echo $ticket_info['link_down_date']; ?></td>
        </tr>
        <tr>
            <th>Restoration date</th>
            <td><?php echo $ticket_info['restoration_date']; ?></td>
        </tr>
        <tr>
            <th>Downtime duration</th>
            <td><?php echo $ticket_info['downtime_duration'] . ' minutes'; ?></td>
        </tr>
        <tr>
            <th>Restoration done by</th>
            <td><?php echo $ticket_info['restoration_done_by']; ?></td>
        </tr>
        <tr>
            <th>Restoration confirmed by</th>
            <td><?php echo $ticket_info['restoration_confirmed_by']; ?></td>
        </tr>
<?php } ?>
<?php if ($ticket_info['sla_claim_duration']) { ?>
        <tr>
            <td colspan="2">
                <h3>SLA Info</h3>
            </td>
            <td>
                <hr>
            </td>
        </tr>
        <tr>
            <th>SLA claim duration</th>
            <td><?php echo $ticket_info['sla_claim_duration'] . ' minutes'; ?></td>
        </tr>
        <tr>
            <th>SLA claim cause</th>
            <td><?php echo $ticket_info['sla_claim_cause']; ?></td>
        </tr>
<?php } ?>
</table> -->


<div id="edit">
    <div>
        <?php if ($errors['err'] && $_POST['a'] == 'process') { ?>
            <p align="center" class="errormessage"><?php echo $errors['err'] ?></p>
        <?php } elseif ($msg) { ?>
            <p align="center" class="infomessage"><?php echo $msg ?></p>
        <?php } elseif ($warn) { ?>
            <p id="warnmessage"><?php echo $warn ?></p>
        <?php } ?>
    </div>
    <form action="" method="post">
        <input type="hidden" name="a" value="update">
        <input type="hidden" name="ticket_id" value="<?php echo $ticket_info['ticket_id'] ?>">
        <table align="center" cellspacing="2" cellpadding="5" border=1>
            <tr>
                <td colspan="2">
                    <div><h3>Ticket Info  #<?php echo $ticket->getExtId() ?>
                            &nbsp;<a href="tickets.php?id=<?php echo $id ?>" title="Reload"><span class="Icon refresh">&nbsp;</span></a></h3></div>
                    <hr>
                </td>
            </tr>
            <tr>
                <th>Create Date:</th>
                <td><?php echo Format::db_date($ticket->getCreateDate()) ?></td>
            </tr>
            <tr><th nowrap>Last Message(client):</th>
                <td><?php echo Format::db_datetime($ticket->getLastMessageDate()) ?></td>
            </tr>
            <tr>
                <th nowrap>Last Response(NOC):</th>
                <td><?php echo Format::db_datetime($ticket->getLastResponseDate()) ?></td>
            </tr>
            <?php if ($ticket->isOpen()) { ?>
            <?php } else {
                ?>
                <tr>
                    <th>Close Date:</th>
                    <td><?php echo Format::db_datetime($ticket->getCloseDate()) ?></td>
                </tr>
                <?php
            }
            ?>
            <tr>
                <td>
                    <strong>Status</strong>
                </td>
                <td>
                    <?php echo (($ticket->getStatus() == 'closed') && $ticket->getSLAClaim()) ? $ticket->getStatus() . ' & sla:' . $ticket->getSLAClaim() . '%' : $ticket->getStatus() ?></td>
            </tr>
            <tr>
                <th>Assigned executive:</th>
                <td><?php echo $staff ? Format::htmlchars($staff->getName()) : '- unassigned -' ?></td>
            </tr>
            <tr>
                <td><strong>Subject</strong></td>
                <td>
                    <div class="current_value">
                        <?php echo Format::htmlchars($ticket->getSubject()) ?>
                    </div>
                    <button type="button" name="edit_value">change</button>
                    <div class="change_value">
                        <input type="text" name="subject" value="<?php echo Format::htmlchars($ticket->getSubject()) ?>">
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Root cause</strong>
                </td>
                <td>
                    <div class="current_value">
                        <?php echo $ticket_info['root_cause'] ? Format::htmlchars($ticket_info['root_cause']) : 'N/A' ?>
                    </div>
                    <button type="button" name="edit_value">change</button>
                    <div class="change_value">
                        <input type="text" name="root_cause" value="<?php echo Format::htmlchars($ticket->getSubject()) ?>">
                    </div>
                </td>
            </tr>

            <tr>
                <td><strong>Cc email address</strong></td>
                <td>
                    <div class="current_value">
                        <?php echo $ticket->getAltEmail(); ?>
                    </div>
                    <button type="button" name="edit_value">change</button>
                    <div class="change_value">
                        <input type="text" name="alt_email" value="<?php echo $ticket->getAltEmail(); ?>">
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="2">
                    <div><h3>Client Info</h3></div>
                    <hr>
                </td>
            </tr>
            <tr>
                <th>Client Name:</th>
                <td><?php echo Format::htmlchars($ticket->getName()) ?></td>
            </tr>
            <tr>
                <td colspan="2"></td>
            </tr>
            <tr>
                <th>CIN</th>
                <td><?php echo $ticket_info['cin']; ?></td>
            </tr>
            <tr>
                <th>Email:</th>
                <td><?php
                    echo $ticket->getEmail();
                    ?>
                </td>
            </tr>
            <tr>
                <th>Phone:</th>
                <td><?php echo Format::phone($ticket->getPhoneNumber()) ?></td>
            </tr>

            <?php if ($ticket_info['raiser_name']) { ?>

                <tr>
                    <td colspan="2">
                        <div>
                            <h3>Ticket raiser info</h3>
                        </div>
                        <hr>
                    </td>
                </tr>

                <th>Raised by</th>
                <td><?php echo $ticket_info['raised_from']; ?></td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td><?php echo $ticket_info['raiser_name']; ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo $ticket_info['raiser_email']; ?></td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td><?php echo $ticket_info['raiser_phone']; ?></td>
                </tr>
            <?php } ?>

            <tr>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="2">
                    <div><h3>Site Info</h3></div>
                    <hr>
                </td>
            </tr>
            <tr>
                <?php if ($ticket_info['site_visited_by']) { ?>
                    <td><strong>Site visited by</strong></td>
                    <td><?php echo $ticket_info['site_visited_by']; ?></td>
                <?php } else { ?>
                    <td><strong>Site visited by</strong></td>
                    <td>
                        <div class="current_value">
                            <?php echo $ticket_info['site_visited_by']; ?>
                        </div>
                        <button type="button" name="edit_value">change</button>
                        <div class="change_value">
                            <input class="normal" type="text" name="site_visited_by">
                        </div>
                    </td>
                <?php } ?>
            </tr>
            <tr>
                <td><strong>Site visited date</strong></td>
                <td>
                    <div class="current_value">
                        <?php echo $ticket_info['site_visited_date']; ?>
                    </div>
                    <button type="button" name="edit_value">change</button>
                    <div class="change_value">
                        <input class="normal ticket_datetimepicker" type="text" name="site_visited_date" autocomplete=OFF placeholder="select date from calender">
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div><h3>Link Info</h3><button type="button" name="edit_link_info">change</button></div>
                    <hr>
                </td>
            </tr>
            <tr class="link_info">
                <td><strong>Link down date</strong></td>
                <td>
                    <div class="current_value">
                        <?php echo $ticket_info['link_down_date'] ? $ticket_info['link_down_date'] : 'N/A'; ?>
                    </div>
                    <div class="change_value">
                        <input class="normal ticket_datetimepicker" type="text" name="link_down_date" autocomplete=OFF>
                    </div>
                </td>
            </tr>

            <tr class="link_info">
                <td>
                    <strong>Restoration date</strong>
                </td>
                <td>
                    <div class="current_value">
                        <?php echo $ticket_info['restoration_date']; ?>
                    </div>
                    <div class="change_value">
                        <input class="normal ticket_datetimepicker" type="text" name="restoration_date" autocomplete=OFF>
                    </div>
                </td>
            </tr>
            <tr class="link_info">
                <td>
                    <strong>Downtime duration</strong>
                </td>
                <td>
                    <div class="current_value">
                        <?php echo $ticket_info['downtime_duration'] . ' minutes'; ?>
                    </div>
                    <div class="change_value">
                        <input class="normal" type="text" name="downtime_duration_front">
                        <input type="hidden" name="downtime_duration"> <!-- duration in minutes -->
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Restoration done by</strong>
                </td>
                <td>
                    <div class="current_value">
                        <?php echo $ticket_info['restoration_done_by']; ?>
                    </div>
                    <button type="button" name="edit_value">change</button>
                    <div class="change_value">
                        <input class="normal" type="text" name="restoration_done_by" value="<?php echo $ticket_info['restoration_done_by']; ?>">
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Restoration confirmed by</strong>
                </td>
                <td>
                    <div class="current_value">
                        <?php echo $ticket_info['restoration_confirmed_by']; ?>
                    </div>
                    <button type="button" name="edit_value">change</button>
                    <div class="change_value">
                        <input class="normal" type="text" name="restoration_confirmed_by" value="<?php echo $ticket_info['restoration_confirmed_by']; ?>">
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <h3>SLA Info</h3>
                    <hr>
                </td>
                <td>
                    <hr>
                </td>
            </tr>

            <tr>
                <td>
                    <strong>SLA claim</strong>
                </td>
                <td>
                    <div class="current_value">
                        <?php if ($ticket_info['sla_claim_duration']) { ?>
                            <strong>SLA claim cause:</strong><?php echo $ticket_info['sla_claim_cause']; ?>
                            <br>
                            <strong>SLA claim duration:</strong><?php echo $ticket_info['sla_claim_duration'] . ' minutes'; ?>
                        <?php } ?>
                    </div>
                    <button type="button" name="edit_value">change</button>
                    <div class="change_value">
                        <strong>SLA claim</strong>
                        <select class="normal" name="sla_claim" style="width: 150px">
                            <option value=""></option>
                            <option value="yes">yes</option>
                            <option value="no">no</option>
                        </select>
                        <div class="sla_data">
                            <strong>Duration(E) in minutes</strong>
                            <input class="normal" type="text" name="sla_claim_duration_front">
                            <input type="hidden" name="sla_claim_duration">
                            <br>
                            <strong>Cause</strong>
                            <input class="normal" type="text" name="sla_claim_cause">
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Note</strong>
                </td>
                <td>
                    <textarea style="width: 400px;" name="note">
                        
                    </textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <button class="submit" type="submit">Save</button>
                </td>
            </tr>
        </table>
</form>
        <script type="text/javascript">

            //css
            $('button').css('float', 'right');
            $('.current_value').css('float', 'left');
            $('p.title').css({
                'font-weight': 'bold',
                'font-size': '1.2em'
            });
            $('dlddl').css({
                'font-size': '1.2em'
            });

            //initializations
            $('div.change_value').hide();
            $('.sla_data').hide();

            //update
            $('table').on('click', 'button[name="edit_value"]', function(event) {
                $(event.target).closest('tr').find('div.current_value').hide();
                $(event.target).closest('tr').find('div.change_value').show();
                $(event.target).hide();
            });

            //for link info
            $('button[name="edit_link_info"]').on('click', function(event) {
                $('.link_info .current_value').hide();
                $('.link_info .change_value').show();
                $(event.target).hide();
            });


            $('.ticket_datetimepicker').datetimepicker({
                controlType: 'select',
                dateFormat: "dd-mm-yy",
                timeFormat: 'hh:mm tt'
            });

            $('[name="downtime_duration_front"]').on('click', function(event) { //TODO: currently its users duty to select valid range, before and after validation
                var link_down_date = $('[name="link_down_date"]').datetimepicker('getDate');
                var link_restoration_date = $('[name="restoration_date"]').datetimepicker('getDate');
                if (link_down_date && link_restoration_date) {
                    var link_down_duration = (link_restoration_date - link_down_date) / 60000; //in minutes
                    if (link_down_duration <= 0) {
                        alert('select valid dates to calculate duration');
                        $('[name="link_down_date"]').val('');
                        $('[name="restoration_date"]').val('');
                    } else {
                        $('[name="downtime_duration"]').val(link_down_duration);
                        $('[name="downtime_duration_front"]').val(link_down_duration + ' minutes');
                    }
                }
            });

            $('[name="sla_claim_duration_front"]').blur(function(event) {

                var duration = parseInt($(event.target).val());
                if (duration) {
                    $('[name="sla_claim_duration"]').val(duration);
                    $('[name="sla_claim_duration_front"]').val(duration + ' minutes');
                } else {
                    $('[name="sla_claim_duration"]').val('');
                    $('[name="sla_claim_duration_front"]').val('');
                }
            });




            $('select[name="sla_claim"]').change(function(event) {
                var sla_claim = $(event.target).val();
                if (sla_claim == 'yes') {
                    $('.sla_data').show();
                } else if (sla_claim == 'no') {
                    $('.sla_data').hide();
                    $('.sla_claim_tr').hide();
                }

                if (!sla_claim) {
                    $('div#sla_data').hide();
                }
            });



        </script>
</div>

<div>
    <?php if ($errors['err'] && $_POST['a'] == 'process') { ?>
        <p align="center" class="errormessage"><?php echo $errors['err'] ?></p>
    <?php } elseif ($msg && $_POST['a'] == 'process' || $_POST['a'] == 'update') { ?>
        <p align="center" class="infomessage"><?php echo $msg ?></p>
    <?php } elseif ($warn) { ?>
        <p id="warnmessage"><?php echo $warn ?></p>
    <?php } ?>
</div>

<?php
//Internal Notes

$sql = 'SELECT note_id,title,note,source,created FROM ' . TICKET_NOTE_TABLE . ' WHERE ticket_id=' . db_input($id) . ' ORDER BY created ASC';
if (($resp = db_query($sql)) && ($notes = db_num_rows($resp))) {
    $display = ($notes > 5) ? 'none' : 'block'; //Collapse internal notes if more than 5.
    ?>
    <div align="left">
        <a class="Icon note" href="#" onClick="toggleLayer('ticketnotes');
                return false;">Internal Notes (<?php echo $notes ?>)</a><br><br>
        <div id='ticketnotes' style="display:<?php echo $display ?>;text-align:center;">
            <?php while ($row = db_fetch_array($resp)) { ?>
                <table align="center" class="note" cellspacing="0" cellpadding="1" width="100%" border=0>
                    <tr><th><?php echo Format::db_daydatetime($row['created']) ?>&nbsp;-&nbsp; posted by <?php echo $row['source'] ?></th></tr>
                    <?php if ($row['title']) { ?>
                        <tr class="header"><td><?php echo Format::display($row['title']) ?></td></tr>
                    <?php } ?>
                    <tr><td><?php echo Format::display($row['note']) ?></td></tr>
                </table>
            <?php } ?>
        </div>
    </div>
<?php } ?>
<div align="left">
    <a class="Icon thread" href="#" onClick="toggleLayer('ticketthread');
            return false;">Ticket Thread</a>
    <div id="ticketthread">
        <?php
        //get messages
        $sql = 'SELECT msg.msg_id,msg.created,msg.message,count(attach_id) as attachments  FROM ' . TICKET_MESSAGE_TABLE . ' msg ' .
                ' LEFT JOIN ' . TICKET_ATTACHMENT_TABLE . " attach ON  msg.ticket_id=attach.ticket_id AND msg.msg_id=attach.ref_id AND ref_type='M' " .
                ' WHERE  msg.ticket_id=' . db_input($id) .
                ' GROUP BY msg.msg_id ORDER BY created';
        $msgres = db_query($sql);
        while ($msg_row = db_fetch_array($msgres)) {
            ?>
            <table align="center" class="message" cellspacing="0" cellpadding="1" width="100%" border=0>
                <tr><th><?php echo Format::db_daydatetime($msg_row['created']) ?></th></tr>
                <?php if ($msg_row['attachments'] > 0) { ?>
                    <tr class="header"><td><?php echo $ticket->getAttachmentStr($msg_row['msg_id'], 'M') ?></td></tr>
                <?php } ?>
                <tr><td><?php echo Format::display($msg_row['message']) ?>&nbsp;</td></tr>
            </table>
            <?php
            //get answers for messages
            $sql = 'SELECT resp.*,count(attach_id) as attachments FROM ' . TICKET_RESPONSE_TABLE . ' resp ' .
                    ' LEFT JOIN ' . TICKET_ATTACHMENT_TABLE . " attach ON  resp.ticket_id=attach.ticket_id AND resp.response_id=attach.ref_id AND ref_type='R' " .
                    ' WHERE msg_id=' . db_input($msg_row['msg_id']) . ' AND resp.ticket_id=' . db_input($id) .
                    ' GROUP BY resp.response_id ORDER BY created';
            $resp = db_query($sql);
            while ($resp_row = db_fetch_array($resp)) {
                $respID = $resp_row['response_id'];
                ?>
                <table align="center" class="response" cellspacing="0" cellpadding="1" width="100%" border=0>
                    <tr><th><?php echo Format::db_daydatetime($resp_row['created']) ?>&nbsp;-&nbsp;<?php echo $resp_row['staff_name'] ?></th></tr>
                    <?php if ($resp_row['attachments'] > 0) { ?>
                        <tr class="header">
                            <td><?php echo $ticket->getAttachmentStr($respID, 'R') ?></td></tr>
                    <?php } ?>
                    <tr><td> <?php echo Format::display($resp_row['response']) ?></td></tr>
                </table>
                <?php
            }
            $msgid = $msg_row['msg_id'];
        }
        ?>
    </div>
</div>
<br>
<br>
<table cellpadding="0" cellspacing="2" border="0" width="100%" class="ticketoptions">
    <tr><td>
            <form name=action action='tickets.php?id=<?php echo $id ?>' method=post class="inline" >
                <input type='hidden' name='ticket_id' value="<?php echo $id ?>"/>
                <input type='hidden' name='a' value="process"/>
                <span for="do" class="big_text"> &nbsp;Action:</span>
                <select id="do" name="do">
                    <option value="">Select Action</option>
                    <?php if ($ticket->isAssigned()) { ?>
                        <option value="release" <?php echo $info['do'] == 'release' ? 'selected' : '' ?> >Release (unassign)</option>
                    <?php } ?>

                    <?php
                    if ($thisuser->canCloseTickets()) {
                        //if you can close a ticket...reopening it is given.
                        if ($ticket->isOpen()) {
                            ?>
                            <option value="close" <?php echo $info['do'] == 'close' ? 'selected' : '' ?> >Close Ticket</option>
                        <?php } else { ?>
                            <option value="reopen" <?php echo $info['do'] == 'reopen' ? 'selected' : '' ?> >Reopen Ticket</option>
                            <?php
                        }
                    }
                    ?>
                    <?php /*
                      if($thisuser->canManageBanList()) {
                      if(!$emailBanned) { ?>
                      <option value="banemail" >Ban Email <?php    echo   $ticket->isOpen()?'&amp; Close':'' ?></option>
                      <?php }else{ ?>
                      <option value="unbanemail">Un-Ban Email</option>
                      <?php }
                      } */ ?>

                    <?php if ($thisuser->canDeleteTickets()) { //oooh...fear the deleters!    ?>
                        <option value="delete" class="red" >Delete Ticket</option>
                    <?php } ?>
                </select>
                <?php /*   ?>
                  <span for="ticket_priority">Priority:</span>
                  <select id="ticket_priority" name="ticket_priority" <?php    echo  !$info['do']?'disabled':'' ?> >
                  <option value="0" selected="selected">-Unchanged-</option>
                  <?php
                  $priorityId=$ticket->getPriorityId();
                  $resp=db_query('SELECT priority_id,priority_desc FROM '.TICKET_PRIORITY_TABLE);
                  while($row=db_fetch_array($resp)){  ?>
                  <option value="<?php    echo  $row['priority_id'] ?>" <?php    echo  $priorityId==$row['priority_id']?'disabled':'' ?> ><?php    echo  $row['priority_desc'] ?></option>
                  <?php } ?>
                  </select>
                  <?php */ ?>
                &nbsp;&nbsp;
                <!--
                <span id="sla_claim" style="display:none">
                    <span for="sla_claim" class="big_text">Insert SLA claim</span>
                    <input type="number" name="sla_claim">&nbsp;&nbsp;<span class="big_text">%</span>
                    &nbsp;&nbsp;
                </span>
                -->
                <br>
                <input type="submit" name="ticket_action_submit" style="float: left" value="CONFIRM ACTION">
            </form>
            <script type="text/javascript">
                $('[name="ticket_action_submit"]').hide();
                //calculate sla upon feld change
                $('input.sla_field').change(function(event) { //check sla fields and calculate
                    var m = $('[name="sla_claim_m"]').val();
                    m = Number(m);
                    var e = $('[name="sla_claim_e"]').val();
                    e = Number(e);
                    var u_client = $('[name="sla_claim_u_client_end"]').val();
                    u_client = Number(u_client);
                    var u_asiaahl = $('[name="sla_claim_u_asiaahl_end"]').val();
                    u_asiaahl = Number(u_asiaahl);

                    var upper_part = (m - e - u_client);
                    var lower_part = (m - e);

                    if (lower_part != 0) {
                        var final_sla = (upper_part / lower_part) * 100;
                        $('[name="sla_claim"]').val(final_sla);
                    } else {
                        final_sla = 0;
                    }

                });

                //action select dropdown change
                $('select[name="do"]').change(function(event) { //on change
                    var action = $(event.target).val();
                    //sla claim section hide and show
                    if (action == 'close') {
                        $('div#sla_claim').show();
                    } else {
                        $('div#sla_claim').hide();
                    }
                    //submit button hide and show
                    if (action == '') {
                        $('[name="ticket_action_submit"]').hide();
                    } else {
                        $('[name="ticket_action_submit"]').show();
                    }
                });

                //on submit
                $('input[name="ticket_action_submit"]').click(function(event) { //on submit
                    $(event.target).css('border', '3px inset #b37d00');
                    if ($('select[name="do"]').val() == 'close') {
                    }
                });

            </script>

    </tr></td>
</table>
<table align="center" cellspacing="0" cellpadding="3" width="90%" border=0>
    <?php if ($_POST['a'] != 'process') { ?>
        <tr> <td align="center">
                <?php if ($errors['err']) { ?>
                    <p align="center" class="errormessage"><?php echo $errors['err'] ?></p>
                <?php } elseif ($msg) { ?>
                    <p align="center" class="infomessage"><?php echo $msg ?></p>
                <?php } ?>
            </td></tr>
    <?php } ?>
    <tr> <td align="center">

            <div class="tabber">
                <?php if ($thisuser->canReplyTickets()) { ?>
                    <div id="reply" class="tabbertab" align="left">
                        <h2>Post Reply</h2>
                        <p>
                        <form action="tickets.php?id=<?php echo $id ?>#reply" name="reply" id="replyform" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="ticket_id" value="<?php echo $id ?>">
                            <input type="hidden" name="msg_id" value="<?php echo $msgid ?>">
                            <input type="hidden" name="a" value="reply">
                            <div><font class="error">&nbsp;<?php echo $errors['response'] ?></font></div>
                            <div>

                                <br>
                                <br>
                                <b>Enter additional message</b>:
                                <textarea name="response" id="response" cols="90" rows="9" wrap="soft" style="width:90%"><?php echo $info['response'] ?></textarea>
                            </div>

                            <div style="margin-top: 3px;">
                                <b>Ticket Status:</b>
                                <?php
                                $checked = isset($info['ticket_status']) ? 'checked' : ''; //Staff must explicitly check the box to change status..
                                if ($ticket->isOpen()) {
                                    ?>
                                    <label class="red"><input type="checkbox" name="ticket_status" id="l_ticket_status" value="Close" <?php echo $checked ?> > Close on Reply</label>
                                <?php } else { ?>
                                    <label><input type="checkbox" name="ticket_status" id="l_ticket_status" value="Reopen" <?php echo $checked ?> > Reopen on Reply</label>
                                <?php } ?>
                                <br>
                                <br>
                                <br>
                            </div>
                            <p>
                            <div  style="margin-left: 50px; margin-top: 30px; margin-bottom: 10px;border: 0px;">
                                <input class="button save" name="post_reply" type='submit' value='Post Reply' />
                                <input class="button" type='reset' value='Reset' />
                                <input class="button" type='button' value='Cancel' onClick="history.go(-1)" />
                            </div>
                            <script type="text/javascript">
                                $('input[name="post_reply"]').click(function(event) {
                                    var response = $('[name="response"]').val();
                                    var more_cc = $('input[name="more_cc"]').val();
                                    if (!response) {
                                        alert('Enter Additional Message');
                                        return false;
                                    }
                                    if (more_cc) {
                                        var cc_mails = more_cc.split(','); // array of cc emails
                                        var invalid_cc_mail = 0;
                                        $.each(cc_mails, function(index, value) { //looping
                                            if (!valid_email(value)) {
                                                invalid_cc_mail++;
                                                alert('invalid additional cc email address :' + value);
                                                event.preventDefault();
                                            }
                                        });
                                    }
                                });
                            </script>
                            </p>
                        </form>
                        </p>
                    </div>
                <?php } ?>
                <div id="notes" class="tabbertab"  align="left">
                    <h2>Post Internal Note</h2>
                    <p>
                    <form action="tickets.php?id=<?php echo $id ?>#notes" name="notes" class="inline" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="ticket_id" value="<?php echo $id ?>">
                        <input type="hidden" name="a" value="postnote">
                        <div>
                            <label for="title">Note Title:</label>
                            <input type="text" name="title" id="title" value="<?php echo $info['title'] ?>" size=30px />
                            </select><font class="error">*&nbsp;<?php echo $errors['title'] ?></font>
                        </div>
                        <div style="margin-top: 3px;">
                            <label for="note" valign="top">Enter note content.
                                <font class="error">*&nbsp;<?php echo $errors['note'] ?></font></label><br/>
                            <textarea name="note" id="note" cols="80" rows="7" wrap="soft" style="width:90%"><?php echo $info['note'] ?></textarea>
                            <br>
                            <label for="more_cc">Additional Cc for notification email(multiple emails seperated by comma)</label>
                            <br>
                            <textarea name="more_cc" cols="80"><?php echo $ticket->getInternalCc(); ?></textarea>
                            <br>
                            <br>
                        </div>

                        <?php
                        //When the ticket is assigned Allow assignee, admin or ANY dept manager to close it
                        if (!$ticket->isAssigned() || $thisuser->isadmin() || $thisuser->isManager() || $thisuser->getId() == $ticket->getStaffId()) {
                            ?>
                            <div style="margin-top: 3px;">
                                <b>Ticket Status:</b>
                                <?php
                                $checked = ($info && isset($info['ticket_status'])) ? 'checked' : ''; //not selected by default.
                                if ($ticket->isOpen()) {
                                    ?>
                                    <label><input type="checkbox" name="ticket_status" id="ticket_status" value="Close" <?php echo $checked ?> > Close Ticket</label>
                                <?php } else { ?>
                                    <label><input type="checkbox" name="ticket_status" id="ticket_status" value="Reopen" <?php echo $checked ?> > Reopen Ticket</label>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <p>
                        <div  align="left" style="margin-left: 50px;margin-top: 10px; margin-bottom: 10px;border: 0px;">
                            <input class="button save" type='submit' value='Submit' />
                            <input class="button" type='reset' value='Reset' />
                            <input class="button" type='button' value='Cancel' onClick="history.go(-1)" />
                        </div>
                        </p>
                    </form>
                    </p>
                </div>

                <?php
                //anyone can reassign ticket(for unassigned ticket), for assigned tickets, only the assignee can assign the ticket.
                if ($thisuser->canTransferTickets()) {
                    ?>
                    <div id="assign" class="tabbertab"  align="left">

                        <h2><?php echo $staff ? 'Re Assign Ticket' : 'Assign to Staff' ?></h2>
                        <p>
                        <form action="tickets.php?id=<?php echo $id ?>#assign" name="notes" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="ticket_id" value="<?php echo $id ?>">
                            <input type="hidden" name="a" value="assign">
                            <div>
                                <span for="staffId">Staff Member:</span>
                                <select id="staffId" name="staffId">
                                    <option value="0" selected="selected">-Select Staff Member.-</option>
                                    <?php
                                    //TODO: make sure the user's group is also active....DO a join.
                                    $sql = ' SELECT staff_id,CONCAT_WS(" ",firstname,lastname) as name FROM ' . STAFF_TABLE .
                                            ' WHERE isactive=1 AND onvacation=0 ';
                                    if ($ticket->isAssigned())
                                        $sql.=' AND staff_id!=' . db_input($ticket->getStaffId());
                                    $depts = db_query($sql . ' ORDER BY lastname,firstname ');
                                    while (list($staffId, $staffName) = db_fetch_row($depts)) {

                                        $selected = ($info['staffId'] == $staffId) ? 'selected' : '';
                                        ?>
                                        <option value="<?php echo $staffId ?>"<?php echo $selected ?>><?php echo $staffName ?></option>
                                    <?php }
                                    ?>
                                </select><font class='error'>&nbsp;*<?php echo $errors['staffId'] ?></font>
                            </div>
                            <div>
                                <span >Comments/message for assignee. &nbsp;(<i>Saved as internal note</i>)
                                    <font class='error'>&nbsp;*<?php echo $errors['assign_message'] ?></font></span>
                                <textarea name="assign_message" id="assign_message" cols="80" rows="7"
                                          wrap="soft" style="width:90%;"><?php echo $info['assign_message'] ?></textarea>
                            </div>
                            <p>
                            <div  style="margin-left: 50px; margin-top: 5px; margin-bottom: 10px;border: 0px;" align="left">
                                <input class="button" type='submit' value='Assign' />
                                <input class="button" type='reset' value='Reset' />
                                <input class="button" type='button' value='Cancel' onClick="history.go(-1)" />
                            </div>
                            </p>
                        </form>
                        </p>
                    </div>
                <?php } ?>
            </div>
        </td>
    </tr>
</table>
