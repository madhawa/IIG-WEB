<?php
if (!defined('OSTADMININC') || !$thisuser->isadmin())
    die('Access Denied');
//List all Depts
$depts = Dept::get_all_depts();
$total_dept = count($depts);
if ( !$total_dept ) die('no departments found');
?>
<h4 align="center">Departments</h4>
<!-- <table align="center" border="0" cellspacing=1 cellpadding=2>
    <form action="admin.php?t=dept" method="POST" name="depts" onSubmit="return checkbox_checker(document.forms['depts'],1,0);">
    <input type=hidden name='do' value='mass_process'>
    <tr><td> -->
<table border="0" cellspacing=0 cellpadding=2 class="dtable" width="50%" align="center" >
    <tr>
        <th width="20%">Department Name</th>
        <th width="20%">total executives</th>
        <th width="20%">Manager</th>
    </tr>
    <?php
        foreach($depts as $dept) {
            $managers = $dept->get_managers();
            ?>
            <tr>
                <td><a href="departments.php?t=dept&id=<?php echo $dept->getId(); ?>"><?php echo $dept->getName(); ?></a></td>
                <td><b><?php echo $dept->get_num_staff(); ?></b></td>
                <td>
                    <?php if ( count($managers) ) { ?>
                        <?php foreach( $managers as $man ) { ?>
                            <a href="executives.php?t=staff&id=<?php echo $man->getId(); ?>" target="_blank"><?php echo $man->getName(); ?></a><br>
                        <?php } ?>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
</table>
<!--
    </td></tr>
<?php
if ($depts && db_num_rows($depts)): //Show options..
    ?>
    <tr>
            <td style="padding-left:20px">
                Select:&nbsp;
                <a href="#" onclick="return select_all(document.forms['depts'],true)">All</a>&nbsp;&nbsp;
                <a href="#" onclick="return reset_all(document.forms['depts'])">None</a>&nbsp;&nbsp;
                <a href="#" onclick="return toogle_all(document.forms['depts'],true)">Toggle</a>&nbsp;&nbsp;
            </td>
        </tr>
        <tr>
            <td align="center">
                <input class="button" type="submit" name="public" value="Make Public"
                    onClick=' return confirm("Are you sure you want to make selected depts(s) public?");'>
                <input class="button" type="submit" name="private" value="Make Private" 
                    onClick=' return confirm("Are you sure you want to make selected depts(s) private?");'>
                <input class="button" type="submit" name="delete" value="Delete Dept(s)" 
                    onClick=' return confirm("Are you sure you want to DELETE selected depts(s)?");'>
            </td>
        </tr>
    <?php
endif;
?>
    </form>
</table>
-->
<script type="text/javascript">
    $('table.dtable th').css({
        'padding': '10px'
    });
    $('table.dtable td').css({
        'padding': '10px'
    });
</script>