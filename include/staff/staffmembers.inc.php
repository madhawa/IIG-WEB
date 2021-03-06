﻿<?php
//  echo   "now i'm in staffmembers.inc.php";
if (!defined('OSTADMININC') || !$thisuser->isadmin())
    die('Access Denied');

$staffs = Staff::get_all_staffs();
$total_staff = count($staffs);
$showing = $total_staff ? $total_staff . " executives" : "No executive found. <a href='executives.php?t=staff&a=new'>Add New</a>.";
?>

<table align="center" width="" border="0" cellspacing=1 cellpadding=2>
    <form action="admin.php?t=staff" method="POST" name="staff">
        <input type=hidden name='a' value='staff'>
        <input type=hidden name='do' value='mass_process'>
        <tr><td>
                <table border="0" cellspacing=0 cellpadding=2 class="dtable" align="center" width="">
                    <tr class="header">
                        <th colspan="3"><h2 class="msg"><?php echo $showing ?></h2></th>
                    </tr>
                    <tr>
                        <th>Full Name</th>
                        <th>Department</th>
                        <th>access level</th>
                    </tr>
                    <?php
                    if ($total_staff) {
                        foreach ($staffs as $staff) {
                            ?>
                            <tr>
                                <?php if ( $thisuser->isSuperAdmin() && $staff->isSuperAdmin() ) { ?>
                                    <td><a href="executives.php?t=staff&id=<?php echo $staff->getId(); ?>"><?php echo Format::htmlchars($staff->getName()) ?></a></td>
                                    <td><a href="departments.php?t=dept&id=<?php echo $staff->getDeptId(); ?>" target="_blank"><?php echo Format::htmlchars($staff->getDeptName()) ?></a></td>
                                <?php } elseif ( !$staff->isSuperAdmin() ) { ?>
                                    <td><a href="executives.php?t=staff&id=<?php echo $staff->getId(); ?>"><?php echo Format::htmlchars($staff->getName()) ?></a></td>
                                    <td><a href="departments.php?t=dept&id=<?php echo $staff->getDeptId(); ?>" target="_blank"><?php echo Format::htmlchars($staff->getDeptName()) ?></a></td>
                                <?php } else { ?>
                                    <td><a href="#"><?php echo Format::htmlchars($staff->getName()) ?></a></td>
                                    <td><a href="#"><?php echo Format::htmlchars($staff->getDeptName()) ?></a></td>
                                <?php } ?>
                                <td><?php echo $staff->get_access_level_name(); ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?> 
                        <tr><td colspan=2><b>no executives</b></td></tr>
                    <?php } ?>
                </table>
            </td></tr>
    </form>
</table>
<script type="text/javascript">
    $('table th').css({
        'padding': '10px'
    });
    $('table td').css({
        'padding': '10px'
    });
</script>