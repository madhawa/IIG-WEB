<?php
/**
 * shows contents of a ticket, add messages to ticket, attach files, contains html design, processing done in another file tickets.php
 */
if (!defined('OSTCLIENTINC') || !is_object($thisuser) || !is_object($ticket))
    die('Kwaheri'); //bye..see ya

$info = ($_POST && $errors) ? Format::input($_POST) : array(); //Re-use the post info on error...savekeyboards.org

$dept = $ticket->getDept();
//Making sure we don't leak out internal dept names
$dept = ($dept && $dept->isPublic()) ? $dept : $cfg->getDefaultDept();
//We roll like that...
?>
<div class="ticket_tables_container">
    <table cellpadding="1" cellspacing="0" border="0" id="ticketInfo">
        <tr><td colspan=2 class="msg">Ticket #<?php echo $ticket->getExtId() ?> 
                &nbsp;<a href="view.php?id=<?php echo $ticket->getExtId() ?>" title="Reload"><span class="Icon refresh">&nbsp;</span></a></td></tr> 
        <tr>
            <td>	
                <table align="center" class="infotable" cellspacing="1" cellpadding="5" width="100%" border=1>
                    <tr>
                        <th>CIN</th>
                        <td><?php echo $ticket->getInfo()['cin']; ?></td>
                    </tr>
                    <tr>
                        <th>Ticket Status:</th>
                        <td><?php echo (($ticket->getStatus() == 'closed') && $ticket->getSLAClaim()) ? $ticket->getStatus() . ' & sla:' . $ticket->getSLAClaim() . '%' : $ticket->getStatus() ?></td>
                    </tr>

                    <tr>
                        <th>Create Date:</th>
                        <td><?php echo Format::db_daydatetime($ticket->getCreateDate()) ?></td>
                    </tr>
                    <tr>
                        <th>Last message date(helpdesk)</th>
                        <td><?php echo Format::db_daydatetime($ticket->getLastResponseDate()); ?></td>
                    </tr>
                    <tr>
                        <th>Last message date(me)</th>
                        <td><?php echo Format::db_daydatetime($ticket->getLastMessageDate()); ?></td>
                    </tr>
                </table>
            </td>
            <td>
                <table align="center" class="infotable" cellspacing="1" cellpadding="5" border=1 width="100%">
                    <tr>
                        <th>Contact Name:</th>
                        <td><?php echo Format::htmlchars($ticket->getName()) ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?php echo $ticket->getEmail() ?></td>
                    </tr>
                    <tr>
                        <th>Cc Emails</th>
                        <td><?php echo $ticket->getAltEmail(); ?></td>
                    </tr>

                    <tr>
                        <th>Phone:</th>
                        <td><?php echo Format::phone($ticket->getPhone()); ?></td>
                    </tr>
                    <tr>
                        <th>Alternate Phone Number</th>
                        <td><?php echo Format::phone($ticket->getAltPhone()); ?></td>
                    </tr>
                    <!--
                    <tr>
                        <th>Mobile:</th>
                        <td><?php //  echo   Format::phone($ticket->getMobileNumber())  ?></td>
                    </tr>
                    -->

                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 5px"></td>
        </tr>
        <tr>
            <td>
                <?php if ($ticket->has_site_info()) { ?>
                    <table align="center" class="infotable" cellspacing="1" cellpadding="5" border=1 width="100%">
                        <tr>
                            <th>Site visited</th>
                            <td>Yes</td>
                        </tr>
                        <tr>
                            <th>Site visited by</th>
                            <td><?php echo $ticket->get_site_visited_by(); ?></td>
                        </tr>
                        <tr>
                            <th>Site visited date</th>
                            <td><?php echo $ticket->get_site_visited_date(); ?></td>
                        </tr>
                    </table>
                <?php } ?>
            </td>
            <td>
                <?php if ($ticket->has_link_info()) { ?>
                    <table align="center" class="infotable" cellspacing="1" cellpadding="5" border=1 width="100%">
                        <tr>
                            <th>Link down date</th>
                            <td><?php echo $ticket->get_link_down_date(); ?></td>
                        </tr>
                        <tr>
                            <th>Link restoration  date</th>
                            <td><?php echo $ticket->get_link_restoration_date(); ?></td>
                        </tr>
                        <tr>
                            <th>Downtime duration</th>
                            <td><?php echo $ticket->get_downtime_duration() . ' minutes'; ?></td>
                        </tr>
                    </table>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 5px"></td>
        </tr>
        <tr>
            <td>
                <?php if ($ticket->is_sla_ticket()) { ?>
                    <table align="center" class="infotable" cellspacing="1" cellpadding="5" border=1 width="100%">
                        <tr>
                            <th>SLA claim cause</th>
                            <td><?php echo $ticket->get_sla_cause(); ?></td>
                        </tr>
                        <tr>
                            <th>SLA duration</th>
                            <td><?php echo $ticket->get_sla_duration() . ' minutes'; ?></td>
                        </tr>
                    </table>
                <?php } ?>
            </td>
            <td>
            </td>
        </tr>
    </table>

    <div>
        <?php if ($errors['err']) { ?>
            <p align="center" id="errormessage"><?php echo $errors['err'] ?></p>
        <?php } elseif ($msg) { ?>
            <p align="center" id="infomessage"><?php echo $msg ?></p>
        <?php } ?>
    </div>
    <h2>Problem: <?php echo Format::htmlchars($ticket->getSubject()) ?></h2>
    <br>
    <span class="Icon thread">Ticket Thread</span>
    <div id="ticketThread">
        <?php
        //get messages
        $sql = 'SELECT msg.*, count(attach_id) as attachments  FROM ' . TICKET_MESSAGE_TABLE . ' msg ' .
                ' LEFT JOIN ' . TICKET_ATTACHMENT_TABLE . ' attach ON  msg.ticket_id=attach.ticket_id AND msg.msg_id=attach.ref_id AND ref_type=\'M\' ' .
                ' WHERE  msg.ticket_id=' . db_input($ticket->getId()) .
                ' GROUP BY msg.msg_id ORDER BY created';
        $msgres = db_query($sql);
        while ($msg_row = db_fetch_array($msgres)):
            ?>
            <table align="center" class="thread-entry message" cellspacing="0" cellpadding="1" border=0 width="100%">
                <tbody>
                    <tr><th><?php echo Format::db_daydatetime($msg_row['created']) ?></th></tr>
                    <tr>
                        <td class="thread-body"><div><?php echo Format::display($msg_row['message']) ?></div></td></tr>
                <tbody>
            </table>
            <?php
            //get answers for messages
            $sql = 'SELECT resp.*,count(attach_id) as attachments FROM ' . TICKET_RESPONSE_TABLE . ' resp ' .
                    ' LEFT JOIN ' . TICKET_ATTACHMENT_TABLE . ' attach ON  resp.ticket_id=attach.ticket_id AND resp.response_id=attach.ref_id AND ref_type=\'R\' ' .
                    ' WHERE msg_id=' . db_input($msg_row['msg_id']) . ' AND resp.ticket_id=' . db_input($ticket->getId()) .
                    ' GROUP BY resp.response_id ORDER BY created';
            //  echo   $sql;
            $resp = db_query($sql);
            while ($resp_row = db_fetch_array($resp)) {
                $respID = $resp_row['response_id'];
//                 $name = $cfg->hideStaffName() ? 'staff' : Format::htmlchars($resp_row['staff_name']);
                $name = Format::htmlchars($resp_row['staff_name']);
                ?>
                <table align="center" class="thread-entry response" cellspacing="0" cellpadding="1" width="100%" border=0>
                    <tr>
                        <th><?php echo Format::db_daydatetime($resp_row['created']) ?>&nbsp;-&nbsp;<?php echo $name ?></th></tr>
                    <tr class="info">
                        <td class="thread-body"> <?php echo Format::display($resp_row['response']) ?></td></tr>
                </table>
                <?php
            } //endwhile...response loop.
            $msgid = $msg_row['msg_id'];
        endwhile; //message loop.
        ?>
    </div>
    <div>
        <div align="center">
            <?php if ($_POST && $errors['err']) { ?>
                <p align="center" id="errormessage"><?php echo $errors['err'] ?></p>
            <?php } elseif ($msg) { ?>
                <p align="center" id="infomessage"><?php echo $msg ?></p>
            <?php } ?>
        </div> 
        <div id="reply" style="padding:10px 0 20px 40px;">
            <?php if ($ticket->isClosed()) { ?>
                <div class="msg">Ticket will be reopened on message post</div>
            <?php } ?>
            <form action="view.php?id=<?php echo $id ?>#reply" name="reply" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $ticket->getExtId() ?>">
                <input type="hidden" name="respid" value="<?php echo $respID ?>">
                <input type="hidden" name="a" value="postmessage">
                <div align="left">
                    Enter Message <font class="error">*&nbsp;<?php echo $errors['message'] ?></font><br/>
                    <textarea name="message" id="message" cols="60" rows="7" wrap="soft"><?php echo $info['message'] ?></textarea>
                </div>
                <?php if ($cfg->allowOnlineAttachments()) { ?>
                    <div align="left">
                        Attach File<br><input type="file" name="attachment" id="attachment" size=30px value="<?php echo $info['attachment'] ?>" /> 
                        <font class="error">&nbsp;<?php echo $errors['attachment'] ?></font>
                    </div>
                <?php } ?>
                <div align="left"  style="padding:10px 0 10px 0;">
                    <input class="button" type='submit' value='Post Reply' />
                    <input class="button" type='reset' value='Reset' />
                    <input class="button" type='button' value='Cancel' onClick='window.location.href = "view.php"' />
                </div>
            </form>
        </div>
    </div>
    <br><br>
</div>