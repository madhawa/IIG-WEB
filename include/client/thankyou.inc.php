<?php
if (!defined('OSTCLIENTINC') || !is_object($ticket))
    die('Kwaheri rafiki!'); //Say bye to our friend..

    
//Please customize the message below to fit your organization speak!
?>
<div>
    <?php if ($errors['err']) { ?>
        <p align="center" id="errormessage"><?php   echo   $errors['err'] ?></p>
    <?php } elseif ($msg) { ?>
        <p align="center" id="infomessage"><?php   echo   $msg ?></p>
    <?php } elseif ($warn) { ?>
        <p id="warnmessage"><?php   echo   $warn ?></p>
    <?php } ?>
</div>
<div style="margin:5px 100px 100px 0;">
<?php   echo   Format::htmlchars($ticket->getName()) ?>,<br>
    <p>
        Thank you for contacting us.<br>
        A support ticket request has been created and a representative will be getting back to you shortly if necessary.</p>

    <p>Support Team </p>
</div>
<?php
unset($_POST); //clear to avoid re-posting on back button??
?>
