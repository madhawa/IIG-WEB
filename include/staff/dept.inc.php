<?php
//to view/update a department

if (!defined('OSTADMININC') || $thisuser->is_normal_staff())
    die('Access Denied');
require_once(INCLUDE_DIR . 'class.dept.php');
$info = null;
if ($dept && $_REQUEST['a'] != 'new') {
    //Editing Department.
    $title = $dept->getName().' Department';
    $action = 'update';
    $info = $dept->getInfo();
} else {
    die('cannot load the selected department');
}

$info = ($errors && $_POST) ? Format::input($_POST) : Format::htmlchars($info);
$staffs = Staff::get_all_staffs();
if ( !count($staffs) ) { 
    die('add some executives at first');
    }
?>
<table width="100%" border="0" cellspacing=0 cellpadding=0>
    <form action="departments.php?t=dept&id=<?php echo $dept->getId(); ?>" method="POST" name="dept">
        <input type="hidden" name="do" value="update">
        <input type="hidden" name="a" value="update">
        <input type="hidden" name="t" value="dept">
        <input type="hidden" name="dept_id" value="<?php echo $dept->getId(); ?>">
        <tr><td>
                <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
                    <tr class="header"><td colspan=2><h3><?php echo $title ?></h3></td></tr>
                    <tr><th>Executive members and managers<br><a href="executives.php?t=staff&amp;a=new" title="add executive" target="_blank">add new</a></th>
                        <td>
                            <?php
                            foreach ($staffs as $staff) {
                                if ( !$staff->isSuperAdmin() ) { //this dept member
                                    $manager = ($staff->isadmin() && ($dept->getId() == $staff->getDeptId()))?true:false;
                                    $eligible = ( ($staff->getDeptId() && ( $staff->getDeptId() != $dept->getId())) || $staff->isSuperAdmin() )?false:true; //can be added to this department
                                    ?>
                                    <input type="checkbox" name="dept_members[]" value="<?php echo $staff->getId() ?>" <?php if ( $staff->getDeptId()==$dept->getId() ) { echo 'checked'; } elseif ( $staff->getDeptId() && ($staff->getDeptId()!=$dept->getId()) ) { echo 'disabled'; } ?>><?php echo $staff->getName(); ?> <?php if ( $staff->getDeptId() && ($staff->getDeptId()!=$dept->getId()) ) { echo ': <span style="font-weight: bold">'.$staff->get_access_level_name().'</span>'; } ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <?php if ( $eligible ) { ?>
                                        <input type="checkbox" name="dept_managers[]" value="<?php echo $staff->getId() ?>" <?php if ( $staff->isadmin() && ($staff->getDeptId()==$dept->getId()) ) { echo 'checked'; } ?>>manager
                                    <?php } ?>
                                    <br>
                                    <br>
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
<script type="text/javascript">
    $('[type="checkbox"]').css({
        'border': '2px solid black'
        });
</script>