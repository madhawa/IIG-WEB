<?php
if (!defined('OSTCLIENTINC'))
    die('Kwaheri rafiki!'); //Say bye to our friend..

$info = ($_POST && $errors) ? Format::input($_POST) : array(); //on error...use the post data
 ?>
<div>
    <?php if ($errors['err']) {  ?>
        <p align="center" id="errormessage"><?php   echo   $errors['err']  ?></p>
        <?php
        if ( count($errors)>1 ) {
            foreach ( $errors as $key=>$value ) {
                if ( $key != 'err' ) {
                      echo   '<p class="errormessage">' . $value . '</p>';
                }
            }
        }
         ?>
    <?php } elseif ($msg) {  ?>
        <p align="center" id="infomessage"><?php   echo   $msg  ?></p>
    <?php } elseif ($warn) {  ?>
        <p id="warnmessage"><?php   echo   $warn  ?></p>
    <?php }  ?>
</div>

<script type="text/javascript">

    $('ul#tickets_nav').css({
        'position': 'fixed',
        'list-style-type': 'none',
        'margin-top': '50px'
    });

    $('ul#tickets_nav li').css({
        'width': '200px',
        'font-size': '1.1em',
        'text-align': 'center',
        'padding': '10px',
        'margin-top': '10px',
        'background-color': '#FDF3E7'
    });

</script>


<form action="" method="POST">
    <input type="hidden" name="email" value="<?php   echo   $thisuser->getEmail();  ?>">
    <input type="hidden" name="client_id" value="<?php   echo   $thisuser->getId();  ?>">
    <input type="hidden" name="name" value="<?php   echo   $thisuser->getName();  ?>">
    <table width="800" cellpadding="1" cellspacing="0" border="0">
        <tr>
            <td colspan="2">
                <h1>Open a New Ticket</h1>
                <p>Please fill in the form below to open a new ticket.</p>
            </td>
        </tr>
        <tbody>
        <tr>
            <td colspan="2">
                <hr>
                <div style="margin-bottom:0.5em" class="form-header">
                <h3>Your Information</h3>
                <em></em>
                </div>
            </td>
        </tr>
        <tr>
            <td>
            <label class="required" for="phone">
                Contact Phone No:
            </label>
            </td>
            <td>
                <input type="text" name="phone" value="<?php   echo   $thisuser->getPhone();  ?>" required>
            </td>
        </tr>
        <tr>
            <td>
                <label for="alt_phone_num"><strong>Alternate Contact Phone No:</strong></label>
            </td>
            <td>
                <input type="text" name="alt_phone_num" value="<?php   echo   $info['alt_phone_num'];  ?>">
            </td>
        </tr>
        <tr>
            <td><label><strong>Contact email address</strong></label></td>
            <td>
                <?php   echo   $thisuser->getEmail();  ?>
            </td>
        </tr>
        <tr>
            <td><label class="required" for="alt_email">Cc for notificaiton emails</label></td>
            <td>
                <input class="black" type="text" name="alt_email" value="<?php   echo   $info['alt_email']  ?>" placeholder="put multiple emails seperated by comma">
            </td>
        </tr>
        </tbody>
        <tbody>
        <tr>
            <td colspan="2">
                <hr>
                <div>
                    <h3>Ticket information</h3>
                </div>
            </td>
        </tr>
        <tr>
            <td><label class="required" for="cin">Select CIN</label></td>
            <td>
                <select name="cin">
                <option value=""></option>
                <?php
                    $sql_cin = 'SELECT cin, service_type FROM ' . SERVICE_CIN_TABLE . ' WHERE client_id=' . db_input($thisuser->getId()).' AND cin<>'.db_input('');
                    $res = db_query($sql_cin);
                    $cins = db_assoc_array($res, true);
                    if ( count($cins) ) {
                    foreach($cins as $cin_row) {
                     ?>
                        <option value="<?php   echo   $cin_row['cin'];  ?>">service: <?php   echo   $cin_row['service_type']  ?> and CIN: <?php   echo   $cin_row['cin'];  ?></option>
                    <?php
                        }
                    } else {
                          echo   '<option value="">no CIN found</option>';
                    }
                     ?>
                </select>
            </td>
        </tr>

        <!--
        <tr>
            <input type="hidden" name="client_id" value="<?php   echo   $thisuser->getId();  ?>">
            <th><span class="red">*</span>Problem:</th>
            <td>
                <select name="topicId" required>
                    <option value="" selected >Select One</option>
                    <?php
                    $services = db_query('SELECT topic_id,topic FROM ' . TOPIC_TABLE . ' WHERE isactive=1 ORDER BY topic');
                    if ($services && db_num_rows($services)) {
                        while (list($topicId, $topic) = db_fetch_row($services)) {
                            $selected = ($info['topicId'] == $topicId) ? 'selected' : '';
                             ?>
                            <option value="<?php   echo   $topicId  ?>"<?php   echo   $selected  ?>><?php   echo   $topic  ?></option>
                            <?php
                        }
                    } else {
                         ?>
                        <option value="0" >General Inquiry</option>
                    <?php }  ?>
                </select>
            </td>
        </tr>
        -->
        <tr>
            <td><label class="required" for="subject">Ticket Subject:</label></td>
            <td>
                <input type="text" name="subject" size="35" value="<?php   echo  $info['subject'] ?>" required>
            &nbsp;<font class="error"><?php   echo  $errors['subject'] ?></font>
            </td>
        </tr>
        <tr>
            <td><strong>Issue details:</strong></td>
            <td>
                <?php if ($errors['message']) {  ?> <font class="error"><b>&nbsp;<?php   echo   $errors['message']  ?></b></font><br/><?php }  ?>
                <textarea name="message" cols="35" rows="8" wrap="soft" style="width:85%" required><?php   echo   $info['message']  ?></textarea>
            </td>
        </tr>
        </tbody>
        <?php
        if ($cfg->allowPriorityChange()) {
            $sql = 'SELECT priority_id,priority_desc FROM ' . TICKET_PRIORITY_TABLE . ' WHERE ispublic=1 ORDER BY priority_urgency DESC';
            if (($priorities = db_query($sql)) && db_num_rows($priorities)) {
                 ?>
                <tr>
                    <td>Priority:</td>
                    <td>
                        <select name="pri">
                            <?php
                            $info['pri'] = $info['pri'] ? $info['pri'] : $cfg->getDefaultPriorityId(); //use system's default priority.
                            while ($row = db_fetch_array($priorities)) {
                                 ?>
                                <option value="<?php   echo   $row['priority_id']  ?>" <?php   echo   $info['pri'] == $row['priority_id'] ? 'selected' : ''  ?> ><?php   echo   $row['priority_desc']  ?></option>
                            <?php }  ?>
                        </select>
                    </td>
                </tr>
                <?php
            }
        }
         ?>

        <?php
        if (($cfg->allowOnlineAttachments() && !$cfg->allowAttachmentsOnlogin())
                || ($cfg->allowAttachmentsOnlogin() && ($thisuser && $thisuser->isValid()))) {
             ?>
            <tr>
                <td>Attachment:</td>
                <td>
                    <input type="file" name="attachment"><font class="error">&nbsp;<?php   echo   $errors['attachment']  ?></font>
                </td>
            </tr>
        <?php }  ?>
        <?php
        if ($cfg && $cfg->enableCaptcha() && (!$thisuser || !$thisuser->isValid())) {
            if ($_POST && $errors && !$errors['captcha'])
                $errors['captcha'] = 'Please re-enter the text again';
             ?>
            <tr>
                <th valign="top">Captcha Text:</th>
                <td><img src="captcha.php" border="0" align="left">
                    <span>&nbsp;&nbsp;<input type="text" name="captcha" size="7" value="">&nbsp;<i>Enter the text shown on the image.</i></span><br/>
                </td>
            </tr>
        <?php }  ?>
        <tr height=2px><td align="left" colspan=2 >&nbsp;</td></tr>
        <tr>
            <td>
                <input class="button save" type="submit" name="submit" value="Submit Ticket">
                <!--
                <input class="button" type="reset" value="Reset">
                <input class="button" type="button" name="cancel" value="back to home" onClick='window.location.href="index.php"'>
                -->
            </td>
        </tr>
    </table>
</form>

<script type="text/javascript">
    $('table').css({
        'margin-left': 'auto',
        'margin-right': 'auto'
    });
    $('h2').css({
        'font-weight': 'bold',
        'margin-top': '50px',
        'margin-bottom': '50px'
    });
    $('p.title').css({
        'font-weight': 'bold',
        'font-size': '1.2em',
        'margin-bottom': '5px'
    });

    $('input:not([type="submit"],[type="reset"],[type="button"]), textarea, select').css('width', '400px');
    $('input:not([type="submit"],[type="reset"],[type="button"]), select').css('height', '30px');

    $('td').css({
        'padding-bottom': '30px'
    });

    $('[type="submit"]').css('width', '200px')
    $('[type="submit"]').css('height', '50px')
</script>