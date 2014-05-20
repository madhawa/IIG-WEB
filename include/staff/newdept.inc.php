<?php
//to view/update a department

if (!defined('OSTADMININC') || $thisuser->is_normal_staff())
    die('Access Denied');
require_once(INCLUDE_DIR . 'class.dept.php');
$info = null;

$info = $_POST ? Format::input($_POST) : Format::htmlchars($info);
$staffs = Staff::get_all_staffs();
if ( !count($staffs) ) { 
    die('add some executives at first');
    }
?>
<table width="100%" border="0" cellspacing=0 cellpadding=0>
    <form action="departments.php?t=dept" method="POST" name="dept">
        <input type="hidden" name="do" value="create">
        <input type="hidden" name="a" value="new">
        <input type="hidden" name="t" value="dept">
        <tr><td>
                <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
                    <tr class="header"><td colspan=2><h3><?php echo $title ?></h3></td></tr>
                    <tr><th>Department Name:</th>
                        <td><input type="text" name="dept_name" value=""><?php echo $errors['dept_name']; ?></td>
                    </tr>
                    <tr><th>Executive member</th>
                        <td>
                            <?php
                            foreach ($staffs as $staff) {
                                if ( !$staff->isSuperAdmin() ) { //this dept member
                                    ?>
                                    <input type="checkbox" name="dept_members[]" value="<?php echo $staff->getId() ?>" <?php if ( $staff->getDeptId() ) { echo 'disabled'; } ?>><?php echo $staff->getName(); ?><br><br>
                                    <?php
                                }
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
</table>
</td></tr>
<tr><td style="padding:10px 0 10px 200px;">
        <input class="button save" type="submit" name="submit" value="Submit">
    </td></tr>
</form>
</table>