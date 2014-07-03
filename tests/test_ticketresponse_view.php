<?php
require_once '../main.inc.php';
?>
<div style="margin-top: 50px">
    <a class="Icon thread" href="#" onClick="toggleLayer('ticketthread');
        return false;">Client vs Executives conversations</a>
    <div id="ticketthread">
        <?php
        //get messages
        $sql = 
            'SELECT msg.msg_id as msg_id,msg.created as created,msg.message as message,msg.source as source FROM ' . TICKET_MESSAGE_TABLE .' msg WHERE  msg.ticket_id=' . db_input(286) 
            . ' UNION ALL ' . 
            'SELECT resp.msg_id as msg_id,resp.created as created,resp.response as message,resp.staff_name as source FROM ' . TICKET_RESPONSE_TABLE . ' resp  WHERE  resp.ticket_id=' . db_input(286) . ' ORDER BY created DESC';
        $msgres = db_query($sql);
        //mysql_query($sql) or die(mysql_error());
        while ($msg_row = db_fetch_array($msgres)) { ?>
            <table align="center" class="message" cellspacing="0" cellpadding="1" width="100%" border=0>
                <tr><th><?php echo Format::db_daydatetime($msg_row['created']) . ' by ' . $msg_row['source'] ?></th></tr>
                <tr><td style="text-align: center"><?php echo Format::display($msg_row['message']) ?></td></tr>
            </table>
            <?php
            /*
            //get answers for messages
            $sql = 'SELECT resp.* FROM ' . TICKET_RESPONSE_TABLE . ' resp ' .
                    ' WHERE msg_id=' . db_input($msg_row['msg_id']) . ' AND resp.ticket_id=' . db_input(286) .
                    ' GROUP BY resp.response_id ORDER BY created';
            $resp = db_query($sql);
            while ($resp_row = db_fetch_array($resp)) {
                $respID = $resp_row['response_id'];
                ?>
                <table align="center" class="response" cellspacing="0" cellpadding="1" width="100%" border=0>
                    <tr><th><?php echo Format::db_daydatetime($resp_row['created']) ?>&nbsp;-&nbsp;<?php echo $resp_row['staff_name'] ?></th></tr>
                    <tr><td style="text-align: center"><?php echo Format::display($resp_row['response']) ?></td></tr>
                </table>
                <?php
            }
            $msgid = $msg_row['msg_id'];
            */
        } ?>
    </div>
</div>