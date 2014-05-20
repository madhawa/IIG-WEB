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