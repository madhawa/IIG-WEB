<?php
//Note that ticket is initiated in tickets.php.
if (!defined('OSTSCPINC') || !@$thisuser->isStaff() || !is_object($ticket))
    die('Invalid path');

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
<div>
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

    <h3 align="center">Trouble Ticket #<?php echo $ticket->getExtId() ?>
        &nbsp;<a href="tickets.php?id=<?php echo $id ?>" title="Reload"><span class="Icon refresh">&nbsp;</span></a></h3>


    <table align="center" border=0 style="position: fixed">
        <tr>
            <td colspan="2">
                <h3 class="msg">Action</h3>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <a href="tickets.php?id=<?php echo $id ?>" title="Reload"><span class="Icon refresh">&nbsp;reload</span></a>
                <br>
                <br>
                <form name=action action='tickets.php?id=<?php echo $id ?>' method=post class="inline" >
                    <input type='hidden' name='ticket_id' value="<?php echo $id ?>"/>
                    <input type='hidden' name='a' value="process"/>
                    <?php if ($ticket->isAssigned()) { ?>
                        <input type="radio" name="do" value="release" <?php echo $info['do'] == 'release' ? 'checked' : '' ?> >&nbsp;&nbsp;Release executive<br>
                    <?php } ?>

                    <?php
                    if ($thisuser->canCloseTickets()) {
                        //if you can close a ticket...reopening it is given.
                        if ($ticket->isOpen()) {
                            ?>
                            <input type="radio" name="do" value="close" <?php echo $info['do'] == 'close' ? 'checked' : '' ?> >&nbsp;&nbsp;Close Ticket<br>
                        <?php } else { ?>
                            <input type="radio" name="do" value="reopen" <?php echo $info['do'] == 'reopen' ? 'checked' : '' ?> >&nbsp;&nbsp;Reopen Ticket<br>
                            <?php
                        }
                    }
                    ?>

                    <?php if ($thisuser->canDeleteTickets()) { //oooh...fear the deleters!    ?>
                        <input type="radio" name="do" value="delete" class="red" >&nbsp;&nbsp;Delete Ticket<br>
                    <?php } ?>
                    <br>
                    <input type="submit" name="ticket_action" value="confirm">
                </form>
            </td>
        </tr>
        
    </table>

    <table align="center" border=2 style="width: 600px">
        <tr class="title">
            <td colspan="2">
                <h3 class="msg">Info</h3>
            </td>
        </tr>
        <form action="" method="post" name="edit_ticket">
            <input type="hidden" name="a" value="update">
            <input type="hidden" name="ticket_id" value="<?php echo $ticket_info['ticket_id'] ?>">
            <tr>
                <th>Create Date:</th>
                <td><?php echo Format::db_date($ticket->getCreateDate()) ?></td>
            </tr>
            <tr><th nowrap>Last Message(client):</th>
                <td><?php echo $ticket->getLastMessageDate()?Format::db_datetime($ticket->getLastMessageDate()):'N/A' ?></td>
            </tr>
            <tr>
                <th nowrap>Last Response(NOC):</th>
                <td><?php echo $ticket->getLastResponseDate()?Format::db_datetime($ticket->getLastResponseDate()):'N/A' ?></td>
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
                        <?php echo $ticket->getSubject()?Format::htmlchars($ticket->getSubject()):'N/A'; ?>
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
                <td colspan="2"><strong>Cc email address</strong><br>
                    <div style="" class="current_value">
                        <?php echo $ticket->getAltEmail()?Format::display_single_email($ticket->getAltEmail()):'N/A'; ?>
                    </div>
                    <button type="button" name="edit_value">change</button>
                    <div class="change_value">
                        <textarea name="alt_email" style="width: 600px; max-height: 300px">
                            <?php echo $ticket->getAltEmail(); ?>
                        </textarea>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2"></td>
            </tr>
            <tr class="title">
                <td colspan="2">
                    <div><h3 class="msg">Client Info</h3></div>
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
                <td><?php echo $ticket_info['cin']?$ticket_info['cin']:'N/A'; ?></td>
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
                <td><?php echo $ticket->getPhoneNumber()?Format::phone($ticket->getPhoneNumber()):'N/A' ?></td>
            </tr>

            <?php if ($ticket_info['raiser_name']) { ?>

                <tr class="title">
                    <td colspan="2">
                        <div>
                            <h3 class="msg">Ticket raiser info</h3>
                        </div>
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
            <tr class="title">
                <td colspan="2">
                    <div><h3 class="msg">Site Info</h3></div>
                </td>
            </tr>
            <tr>
                <?php if ($ticket_info['site_visited_by']) { ?>
                    <td><strong>Site visited by</strong></td>
                    <td><?php echo $ticket_info['site_visited_by']?$ticket_info['site_visited_by']:'N/A'; ?></td>
                <?php } else { ?>
                    <td><strong>Site visited by</strong></td>
                    <td>
                        <div class="current_value">
                            <?php echo $ticket_info['site_visited_by']?$ticket_info['site_visited_by']:'N/A'; ?>
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
                        <?php echo $ticket_info['site_visited_date']?$ticket_info['site_visited_date']:'N/A'; ?>
                    </div>
                    <button type="button" name="edit_value">change</button>
                    <div class="change_value">
                        <input class="normal ticket_datetimepicker" type="text" name="site_visited_date" autocomplete=OFF placeholder="select date from calender">
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div><h3 class="msg">Link Info</h3><button type="button" name="edit_link_info">change</button></div>
                </td>
            </tr>
            <tr class="link_info title">
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
                        <?php echo $ticket_info['restoration_date']?$ticket_info['restoration_date']:'N/A'; ?>
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
                        <?php echo $ticket_info['downtime_duration']?$ticket_info['downtime_duration'] . ' minutes':'N/A'; ?>
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
                        <?php echo $ticket_info['restoration_done_by']?$ticket_info['restoration_done_by']:'N/A'; ?>
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
                        <?php echo $ticket_info['restoration_confirmed_by']?$ticket_info['restoration_confirmed_by']:'N/A'; ?>
                    </div>
                    <button type="button" name="edit_value">change</button>
                    <div class="change_value">
                        <input class="normal" type="text" name="restoration_confirmed_by" value="<?php echo $ticket_info['restoration_confirmed_by']; ?>">
                    </div>
                </td>
            </tr>

            <tr class="title">
                <td colspan="2">
                    <h3 class="msg">SLA Info</h3>
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
                            <strong>SLA claim cause:</strong><?php echo $ticket_info['sla_claim_cause']?$ticket_info['sla_claim_cause']:'N/A'; ?>
                            <br>
                            <strong>SLA claim duration:</strong><?php echo $ticket_info['sla_claim_duration']?$ticket_info['sla_claim_duration'] . ' minutes':'N/A'; ?>
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
                <td colspan="2">
                    <strong>Note</strong>
                    <br>
                    <textarea style="width: 600px;" name="note">
                    </textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input class="save" type="submit" name="save" value="Save">
                </td>
            </tr>
    </table>
</form>

<script type="text/javascript">

    //css
    $('h3.msg').css({
        'margin-top': '50px',
        'background-color': '#F4F4FF',
        'padding': '10px'
    });
    $('table').css({
        'background-color': ''
    });
    
    $('td, th').css({
        //'border-bottom': '1px solid',
        'padding': '10px'
    });
    $('button').css({'float': 'right', 'margin-right': ''});
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

<!-- <div>
<?php if ($errors['err'] && $_POST['a'] == 'process') { ?>
            <p align="center" class="errormessage"><?php echo $errors['err'] ?></p>
<?php } elseif ($msg && $_POST['a'] == 'process' || $_POST['a'] == 'update') { ?>
            <p align="center" class="infomessage"><?php echo $msg ?></p>
<?php } elseif ($warn) { ?>
            <p id="warnmessage"><?php echo $warn ?></p>
<?php } ?>
</div> -->

                            <div style="width: 1024px; margin-left: auto; margin-right: auto; margin-top: 50px">
                                <h2 align="center">Conversations</h2>
                                    <?php
                                    //Internal Notes
                                    $sql = 'SELECT note_id,title,note,source,created FROM ' . TICKET_NOTE_TABLE . ' WHERE ticket_id=' . db_input($id) . ' ORDER BY created DESC';
                                    if (($resp = db_query($sql)) || ($notes = db_num_rows($resp))) {
                                        //$display = ($notes > 5) ? 'none' : 'block'; //Collapse internal notes if more than 5.
                                        ?>
                                        <div>
                                            <a class="Icon note" href="#" onClick="toggleLayer('ticketnotes');
                                                    return false;">Internal Notes</a><br><br>
                                            <div id='ticketnotes' style="text-align:center;">
                                                <?php while ($row = db_fetch_array($resp)) { ?>
                                                    <table align="center" class="note" cellspacing="0" cellpadding="1" width="100%" border=0>
                                                        <tr><th colspan="2"><?php echo Format::db_daydatetime($row['created']) ?></th></tr>
                                                        <tr>
                                                        <td style="border: 1px solid; width: 200px; text-align: center"><?php echo ' note from: ' . '<b>'.$row['source'].'</b>'; ?></td>
                                                        <td><?php echo 'title: '.Format::display($row['title']).'<br>'. Format::display($row['note']) ?></td>
                                                        </tr>
                                                    </table>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div style="margin-top: 50px">
                                        <a class="Icon thread" href="#" onClick="toggleLayer('ticketthread');
                                            return false;">Client vs Executives conversations</a>
                                        <div id="ticketthread">
                                            <?php
                                            //get messages
                                            $sql = 
                                                'SELECT msg.msg_id as msg_id,msg.created as created,msg.message as message,msg.source as source,TRUE as ismessage FROM ' . TICKET_MESSAGE_TABLE .' msg WHERE  msg.ticket_id=' . db_input($id) 
                                                . ' UNION ALL ' . 
                                                'SELECT resp.msg_id as msg_id,resp.created as created,resp.response as message,resp.staff_name as source,NULL as ismessage FROM ' . TICKET_RESPONSE_TABLE . ' resp  WHERE  resp.ticket_id=' . db_input($id) . ' ORDER BY created DESC';
                                            $msgres = db_query($sql);
                                            //mysql_query($sql) or die(mysql_error());
                                            $rows = db_assoc_array($msgres);
                                            //while ($msg_row = db_fetch_array($msgres)) {
                                            foreach( $rows as $index=>$msg_row ) {
                                                $type = $msg_row['ismessage']?'message':'response';
                                            ?>
                                                <table align="center" class="<?php echo $msg_row['ismessage']?'message':'response'; ?>" cellspacing="0" cellpadding="1" width="100%" border=0>
                                                    <tr><th colspan="2"><?php echo Format::db_daydatetime($msg_row['created']); ?></th></tr>
                                                    <tr>
                                                    <td style="border: 1px solid; width: 200px; text-align: center"><?php echo $type . ' From: ' . '<b>'.$msg_row['source'].'</b>'; ?></td>
                                                    <td style="text-align: center"><?php echo Format::display($msg_row['message']) ?></td>
                                                    </tr>
                                                </table>
                                                
                                                <?php
                                                /* these are no more needed for the new format above
                                                //get answers for messages
                                                $sql = 'SELECT resp.* FROM ' . TICKET_RESPONSE_TABLE . ' resp ' .
                                                        ' WHERE msg_id=' . db_input($msg_row['msg_id']) . ' AND resp.ticket_id=' . db_input($id) .
                                                        ' GROUP BY resp.response_id ORDER BY created';
                                                $resp = db_query($sql);
                                                while ($resp_row = db_fetch_array($resp)) {
                                                    $respID = $resp_row['response_id'];
                                                    ?>
                                                    <table align="center" class="response" cellspacing="0" cellpadding="1" width="100%" border=0>
                                                        <tr><th><?php echo Format::db_daydatetime($resp_row['created']) ?>&nbsp;-&nbsp;<?php echo $resp_row['staff_name'] ?></th></tr>
                                                        <tr><td> <?php echo Format::display($resp_row['response']) ?></td></tr>
                                                    </table>
                                                    <?php
                                                }
                                                */
                                            }
                                            #getting the latest dated message id
                                            $msgid = $rows[0]['msg_id'];
                                            unset($rows);
                                            ?>
                                        </div>
                                    </div>
                                </div>

<br>
<br>
<br>
<div id="ticket_action_div">
    <table align="center" cellspacing="0" cellpadding="3" width="1024" border=0>
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
                            <h2>Reply to client</h2>
                            <p>
                            <form action="tickets.php?id=<?php echo $id ?>#reply" name="reply" id="replyform" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="ticket_id" value="<?php echo $id ?>">
                                <input type="hidden" name="msg_id" value="<?php echo $msgid ?>">
                                <input type="hidden" name="a" value="reply">
                                <?php
                                $checked = isset($info['ticket_status']) ? 'checked' : ''; //Staff must explicitly check the box to change status..
                                if ($ticket->isOpen()) {
                                    ?>
                                    <label><input type="checkbox" name="ticket_status" id="l_ticket_status" value="Close" <?php echo $checked ?> > Close</label>
                                <?php } else { ?>
                                    <label><input type="checkbox" name="ticket_status" id="l_ticket_status" value="Reopen" <?php echo $checked ?> > Reope
                                    <?php } ?>
                                    <div><font class="error">&nbsp;<?php echo $errors['response'] ?></font></div>
                                    <div>

                                        <br>
                                        <br>


                                        <div style="position: relative; width: 800px">
                                            <h4 style="display: inline">Message</h4>
                                            <input style="float: right" class="button save" name="post_reply" type='submit' value='Post Message'>
                                            <textarea class="elastic" name="response" id="response" rows="9" wrap="soft" style="min-width:100%"><?php echo $info['response'] ?></textarea>
                                        </div>
                                    </div>
                                    <p>
                                        <!--                             <div  style="margin-left: 50px; margin-top: 30px; margin-bottom: 10px;border: 0px;">
                                                                        <input class="button save" name="post_reply" type='submit' value='Post Reply' />
                                                                        <input class="button" type='reset' value='Reset' />
                                                                        <input class="button" type='button' value='Cancel' onClick="history.go(-1)" />
                                                                    </div>
                                                                    </p> -->
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

                                        <?php
                                        //When the ticket is assigned Allow assignee, admin or ANY dept manager to close it
                                        if (!$ticket->isAssigned() || $thisuser->isadmin() || $thisuser->isManager() || $thisuser->getId() == $ticket->getStaffId()) {
                                            ?>
                                            <div style="margin-top: 3px;">
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
                                        <br>
                                        <br>
                                        <div style="width: 800px">
                                            <label for="title">Note Title:</label>
                                            <input type="text" name="title" id="title" value="<?php echo $info['title'] ?>" size=30px />
                                            </select><font class="error">*&nbsp;<?php echo $errors['title'] ?></font>
                                            
                                            <span>Enter note content.
                                                <font class="error">*&nbsp;<?php echo $errors['note'] ?></font></span>
                                            <input style="float: right" class="button save" type='submit' value='Post Note' />
                                            <textarea class="elastic" name="note" id="note" rows="7" wrap="soft" style="min-width:100%"><?php echo $info['note'] ?></textarea>
                                            <br>
                                            <br>
                                            <label for="more_cc">Additional Cc for notification email(multiple emails seperated by comma)</label>
                                            <br>
                                            <textarea class="elastic" name="more_cc" wrap="soft" style="min-width:100%"><?php echo $ticket->getInternalCc(); ?></textarea>
                                            <br>
                                            <br>
                                        </div>
                <!--                         <p>
                                        <div  align="left" style="margin-left: 50px;margin-top: 10px; margin-bottom: 10px;border: 0px;">
                                            <input class="button save" type='submit' value='Submit' />
                                        <input class="button" type='reset' value='Reset' />
                                            <input class="button" type='button' value='Cancel' onClick="history.go(-1)" />
                                        </div>
                                        </p> -->
                                    </form>
                                    </p>
                                </div>

                                <?php
                                //anyone can reassign ticket(for unassigned ticket), for assigned tickets, only the assignee can assign the ticket.
                                if ($thisuser->canTransferTickets()) {
                                    ?>
                                    <div id="assign" class="tabbertab"  align="left">

                                        <h2><?php echo $staff ? 'Re transfer Ticket' : 'Assign to executive' ?></h2>
                                        <p>
                                        <form action="tickets.php?id=<?php echo $id ?>#assign" name="notes" method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="ticket_id" value="<?php echo $id ?>">
                                            <input type="hidden" name="a" value="assign">
                                            <div style="width: 800px">
                                                <span for="staffId">Executive:</span>
                                                <select id="staffId" name="staffId">
                                                    <option value="0" selected="selected">-Select Executive.-</option>
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
                                                <br>
                                                <br>
                                                <span>message for assignee. &nbsp;(Saved as internal note)
                                                    <font class='error'>&nbsp;*<?php echo $errors['assign_message'] ?></font></span>
                                                <input style="float: right" class="button submit save" type='submit' value='Assign' />
                                                <textarea name="assign_message" id="assign_message" class="elastic" rows="7"
                                                          wrap="soft" style="min-width:100%;"><?php echo $info['assign_message'] ?></textarea>
                                            </div>
                                            <!--                             <div  style="margin-top: 5px; margin-bottom: 10px;border: 0px;" align="left">
                                                                            <input class="button submit save" type='submit' value='Assign' />
                                                                            <input class="button" type='reset' value='Reset' />
                                                                            <input class="button" type='button' value='Cancel' onClick="history.go(-1)" />
                                                                        </div> -->
                                        </form>
                                        </p>
                                    </div>
                                <?php } ?>
                                </div>
                                </td>
                                </tr>
                                </table>
                                </div>
                            </div>

<script type="text/javascript">

$('div.tabbertab').css({
    'background-color': '#F4FAFF'
});
$('#ticket_action_div').css({
    'display': 'table',
    'margin-left': 'auto',
    'margin-right': 'auto',
    'z-index': '100',
    'background-color': '#F3F3F3',
});

/*
//$('#ticket_action_div').hide();
$('button[name="ticket_action_button"]').click(function(event) {
    $('div#ticket_action_div').toggle('slide');
});
*/
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