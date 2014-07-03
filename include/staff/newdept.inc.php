<?php
//to view/update a department

if (!defined('OSTADMININC') || $thisuser->is_normal_staff())
    die('Access Denied');
require_once(INCLUDE_DIR . 'class.dept.php');
$info = null;

$info = $_POST ? Format::input($_POST) : Format::htmlchars($info);
/*
  $staffs = Staff::get_all_staffs();
  if ( !count($staffs) ) {
  die('add some executives at first');
  }
 */
?>
<table align="center" style="margin-top: 50px" width="" border="0" cellspacing=0 cellpadding=10 class="">
    <form action="departments.php?t=dept" method="POST" name="dept">
        <input type="hidden" name="do" value="create">
        <input type="hidden" name="a" value="new">
        <input type="hidden" name="t" value="dept">
        <tr><th>insert a department name first:</th>
            <td><input type="text" name="dept_name" value=""><?php echo $errors['dept_name']; ?><input style="margin-left: 50px" class="save" type="submit" name="submit" value="Submit"></td>
        </tr>
<!--                     <tr><th>Executive member</th>
            <td>
        <?php
        foreach ($staffs as $staff) {
            if (!$staff->isSuperAdmin()) { //this dept member
                ?>
                                <input type="checkbox" name="dept_members[]" value="<?php echo $staff->getId() ?>" <?php if ($staff->getDeptId()) {
            echo 'disabled';
        } ?>><?php echo $staff->getName(); ?><br><br>
                <?php
            }
        }
        ?>
            </td>
        </tr> -->
</table>
</form>