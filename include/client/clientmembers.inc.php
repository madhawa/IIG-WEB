<?php
/**
 * clientmembers.inc.php
 * show all members under a client
 * client side admin include
 * 
 */
//  echo   "now i'm in staffmembers.inc.php";
if (!defined('CLIENT_ADMIN_INC'))
    die('Access Denied');
    
if (!$thisuser->canManageUsers()) $errors['err'] = 'Access Denied, you have no permission for this action';

//List all staff members...not pagenating...
$sql = 'SELECT * FROM ' . CLIENT_TABLE . ' WHERE boss_id=' . db_input($thisuser->getId());

$users = db_query($sql);
$showing = ($num = db_num_rows($users)) ? "Client Members" : "No member found. <a href='admin.php?t=client&a=new'>Add New User</a>.";
?>
<?php if ($errors['err']) { ?>
    <p align="center" id="errormessage"><?php   echo   $errors['err'] ?></p>
<?php } ?>

<?php if ($msg) { ?>
    <p align="center" id="infomessage"><?php   echo   $msg ?></p>
<?php } ?>

<?php if ($warn) { ?>
    <p id="warnmessage"><?php   echo   $warn ?></p>
<?php } ?>


<?php if ($thisuser->canManageUsers()) { ?>
<div class="msg">&nbsp;<?php   echo   $showing ?>&nbsp;</div>
<table border="0" cellspacing=0 cellpadding=2 class="dtable" align="center" width="100%">
    <tr>
        <th>Name</th>
        <th>User Name</th>
        <th>Email</th>
        <th>Permission</th>
        <th>Created</th>
        <th>Last Login</th>
    </tr>
    <?php
    $class = 'row1';
    $total = 0;
    if ($users && db_num_rows($users)) {
        while ($row = db_fetch_array($users)) {
            $sel = false;
            ?>
            <tr class="<?php   echo   $class ?>" id="<?php   echo   $row['client_id'] ?>">
                <td><?php   echo   $row['name'] ?></td>
                <td><a href="admin.php?t=client&id=<?php   echo   $row['username'] ?>"><?php   echo   $row['username'] ?></a>&nbsp;</td>
                <td><?php   echo   Format::htmlchars($row['email']) ?></td>
                <td><?php   echo   $row['permission'] ?></td>
                <td><?php   echo   Format::db_date($row['created']) ?></td>
                <td><?php   echo   Format::db_datetime($row['lastlogin']) ? Format::db_datetime($row['lastlogin']): 'never logged in' ?>&nbsp;</td>
            </tr>
        <?php
        $class = ($class == 'row2') ? 'row1' : 'row2';
        }
        ?> 
    <?php } ?>
</table>
<?php } ?>