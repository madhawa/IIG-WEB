<?php
//  echo   "now i'm in staffmembers.inc.php";
if (!defined('OSTADMININC') || !$thisuser->isadmin())
    die('Access Denied');

$staffs = Staff::get_all_staffs();
$total_staff = count($staffs);
$showing = $total_staff ? $total_staff . " executives" : "No executive found. <a href='executives.php?t=staff&a=new'>Add New</a>.";
?>
<div class="msg">&nbsp;<?php echo $showing ?>&nbsp;</div>
<table width="100%" border="0" cellspacing=1 cellpadding=2>
    <form action="admin.php?t=staff" method="POST" name="staff">
        <input type=hidden name='a' value='staff'>
        <input type=hidden name='do' value='mass_process'>
        <tr><td>
                <table border="0" cellspacing=0 cellpadding=2 class="dtable" align="center" width="100%">
                    <tr>
                        <th width="20%">Full Name</th>
                        <!--
                    <th>User Name</th>
                    <th>Status</th>
                    <th>Group</th>
                        -->
                        <th width="20%">Department</th>
                        <th width="20%">access level</th>
<!--
                                    <th>Last Login</th>
                        -->
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
</script>