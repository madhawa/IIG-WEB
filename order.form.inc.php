<?php
/**
 *order.create.inc.php
 *contains the Service Order Form
 *
 *
 *
 */
//TODO: log permission denied issues, access violations
if (!defined('OSTCLIENTINC'))
    die('access denied, not client side include');
    
//TODO: log permission denied issues, access violations
if ($thisuser->isClientAdmin()) {
    $info = Format::input($_POST); //error...use the post data
} else $errors['err'] = 'Permission denied for this action.';
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

<br />

<?php
//TODO: log permission denied issues, access violations
if ($thisuser->isClientAdmin()) {
    if (!$do) $do = 'create_by_client';
    require(TEMPLATE_DIR.'order.tpl.php');
}
?>

