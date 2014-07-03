<?php
//to view/update a department

if (!defined('OSTADMININC') || $thisuser->is_normal_staff())
    die('Access Denied');
require_once(INCLUDE_DIR . 'class.dept.php');
$info = null;
if ($dept && $_REQUEST['a'] != 'new') {
    //Editing Department.
    $title = $dept->getName() . ' Department';
    $action = 'update';
    $info = $dept->getInfo();
} else {
    die('cannot load the selected department');
}

$info = ($errors && $_POST) ? Format::input($_POST) : Format::htmlchars($info);
$staffs = Staff::get_all_staffs();
/*
if (!count($staffs)) {
    die('add some executives at first');
}
*/
?>

<div style="display: table; margin-left: auto; margin-right: auto">
<?php if ( !$dept->isSystemDept() ) { ?>
    <form action="departments.php" method="POST">
        <input type="hidden" name="t" value="dept">
        <input type="hidden" name="do" value="delete">
        <input type="hidden" name="dept_id" value="<?php echo $dept->getId(); ?>">
        <button style="float: right; color: red" class="" type="submit" name="delete">X</button>
    </form>
    <div style="clear: both">
    </div>
<?php } ?>

<form action="departments.php?t=dept&id=<?php echo $dept->getId(); ?>" method="POST" name="dept">
    <table align="center" width="" border="0" cellspacing=0 cellpadding=5 class="tform">
        <tr class="header">
            <td colspan=2><h3 class="msg"><?php echo $title ?></h3></td>
        </tr>
        <tr>
            <th>Executive members and managers<br><a href="executives.php?t=staff&amp;a=new" title="add executive" target="_blank">add new</a></th>
            <td>
                <input type="hidden" name="do" value="update">
                <input type="hidden" name="a" value="update">
                <input type="hidden" name="t" value="dept">
                <input type="hidden" name="dept_id" value="<?php echo $dept->getId(); ?>">
                <?php
                foreach ($staffs as $staff) {
                    if (!$staff->isSuperAdmin()) { //this dept member
                        $manager = ($staff->isadmin() && ($dept->getId() == $staff->getDeptId())) ? true : false;
                        $eligible = ( ($staff->getDeptId() && ( $staff->getDeptId() != $dept->getId())) || $staff->isSuperAdmin() ) ? false : true; //can be added to this department
                        ?>
                        <div>
                            <?php if ( $eligible ) { ?>
                                <input type="checkbox" name="dept_members[]" value="<?php echo $staff->getId() ?>" <?php if ($staff->getDeptId() == $dept->getId()) { echo 'checked'; } ?>>
                                <?php echo '<b>'.$staff->getName().'</b>'; ?>
                                    <input type="checkbox" name="dept_managers[]" value="<?php echo $staff->getId() ?>" <?php if ($staff->isadmin() && ($staff->getDeptId() == $dept->getId())) { echo 'checked'; } ?>>manager
                            <?php } else { ?>
                                <input type="checkbox" name="disabled[]" value="" disabled>
                                <?php echo $staff->getName().' <a href="departments.php?t=dept&amp;id='.$staff->getDeptId().'" target="_blank">'.$staff->get_access_level_name().'</a>'; ?>
                            <?php } ?>
                        </div>
                        <br>
                    <?php } ?>
                <?php } ?>
            </td>
        </tr>
    </table>
    <input style="float: right" class="button save" type="submit" name="submit" value="update">
</form>
</div>
<script type="text/javascript">
    $('[type="checkbox"]').css({
        'border': '2px solid black'
    });
    $('tr.header td').css({
        'background': ''
    });
    $('table').css({
        'margin-bottom': '0px'
    });
    
    $('[name="delete"]').click(function(event) {
        var confirm = window.confirm('delete the department ?');
        if ( confirm === false ) {
            return false;
        }
    });
</script>