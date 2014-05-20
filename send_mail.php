<?php
require('client.inc.php');
require(CLIENTINC_DIR . 'header.inc.php');

$ticket = new Ticket(3);
    $sql = 'SELECT ticket_autoresp_subj,ticket_autoresp_body FROM ' . EMAIL_TEMPLATE_TABLE . ' WHERE cfg_id=' . db_input(1) . ' AND tpl_id=' . db_input(1);
    if (($resp = db_query($sql)) && db_num_rows($resp) && list($subj, $body) = db_fetch_row($resp)) {
        $body = $ticket->replaceTemplateVars($body);
        $subj = $ticket->replaceTemplateVars($subj);
    }

if ( $_POST ) {
    include_once(CLASS_DIR . 'class.email.php');
    $email_id = $_POST['from_id'];
    $to = $_POST['to'];
    
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $header = 'From: webmaster@1asia-ahl.com';
    
    if ($email_id) {
        $email = new Email($email_id);
    }
    if ( !$email || !is_object($email) ) {
        $email = new Email(1);
    }
        
    if ($email->send($to, $subj, $body)) {
        $msg = 'email sent successfully';
    } else {
        $errors['err'] = 'email sending failure';
    }
}


?>

<div>
    <?php if ($errors['err']) { ?>
        <p align="center" id="errormessage"><?php   echo   $errors['err'] ?></p>
    <?php } ?>
    
    <?php if ($msg) { ?>
        <p align="center" id="infomessage"><?php   echo   $msg ?></p>
    <?php } ?>
    
    <?php if ($warn) { ?>
        <p id="warnmessage"><?php   echo   $warn ?></p>
    <?php } ?>
</div>

<br>
<br>
<div>
    <p id="view_smtp_config"> SMTP configuration: <?php if ($email) { print_r($email->getSMTPInfo()); } ?></p>
</div>

<form name="bal" action="" method="post" >
    <table>
    <tr>
        <td>
            <label>from email address id</label>
        </td>
        <td>
            <input type="text" name="from_id">
        </td>
    </tr>
    <tr>
    <td><label>to email address :</label></td>
    <td>
    <input type="email" name="to" required>
    </td>
    </tr>

    <tr>
    <td><label>Subject</label></td>
    <td><input type="text" name="subject" required></td>
    </tr>
    <tr><span class="error">&nbsp;<?php if ($errors['subject'])   echo   $errors['subject']; ?></span></tr>

    <tr>
    <td><label>Message</label></td>
    <td><textarea cols="50" rows="4" name="message"></textarea></td>
    </tr>

    <tr><td></td><td><input class="button" type="submit" name="send" value="send"></td></tr>
    </table>
</form>
<script type="text/javascript">
    $('[name="send"]').click(function() {
        var to = $('[name="to"]');
        var subject = $('[name="subject"]');
        var message = $('[name="message"]');
        if ( !to || !subject || !message ) {
            alert('fill the required fields');
            return false;
        }
    });
</script>

<?php
require(CLIENTINC_DIR . 'footer.inc.php');
?>