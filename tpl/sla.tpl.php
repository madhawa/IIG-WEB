<?php
/**
 * SLA page template
 * template tags: $slaData
 * $slaData structure:
 * $slaData = array
 * (
 * '$month_name'=>array
 *    (
 *    'committed_sla'=>99.95%,
 *    'achievement'=>100%
 *    )
 * )
 * 
 * 
 * 
 */
if (!defined('OSTCLIENTINC'))
    die('NOT CLIENT SIDE INCLUDE');
?>

<table>
    <tr>
        <td nowrap >
            <a href="sla.php?page=last_year" style="border: 2px solid green; padding: 5px">Last year</a>
            <a href="sla.php?page=current_year" style="border: 2px solid green; padding: 5px">Current year</a>
            <a href="sla.php?page=last_month" style="border: 2px solid green; padding: 5px">Last month</a>
            <a href="sla.php?page=current_month" style="border: 2px solid green; padding: 5px">Current month</a>
            <a href="sla.php?page=date_range" style="border: 2px solid green; padding: 5px">Customize date</a>
        </td>
    </tr>
    <tr>
        <td style="padding: 10px"></td>
    </tr>
    <tr>
        <td>
            <form action="sla.php" method="get">
                <span class="msg">Select date</span> &nbsp; from Date(before)&nbsp;<input id="sd" name="startDate" value="<?php echo Format::htmlchars($_REQUEST['startDate']) ?>"
                                                                                          onclick="event.cancelBubble = true;
                                                                                                  calendar(this);" autocomplete=OFF>
                <a href="#" onclick="event.cancelBubble = true;
                        calendar(getObj('sd'));
                        return false;"><img src='images/cal.png'border=0 alt=""></a>
                &nbsp;&nbsp; To Date(after) &nbsp;&nbsp;
                <input id="ed" name="endDate" value="<?php echo Format::htmlchars($_REQUEST['endDate']) ?>"
                       onclick="event.cancelBubble = true;
                               calendar(this);" autocomplete=OFF >
                <a href="#" onclick="event.cancelBubble = true;
                        calendar(getObj('ed'));
                        return false;"><img src='images/cal.png'border=0 alt=""></a>
                &nbsp;&nbsp;
                <input type="hidden" name="page" value="custom_date">
                <input type="submit" name="submit" value="Search">
            </form>
        </td>
    </tr>
</table>
<br>
<br>
<h2 align="center"><?php echo $sla_title ?></h2>
<br />
    <?php if ($data) { ?>
    <table align="center" class="dtable" border="2" cellpadding="10">
        <tr>
            <th>Month</th>
            <th>Committed SLA</th>
            <th>Achievement</th>
        </tr>
        <?php if (is_array($data)) { ?>
            <?php foreach ($data as $key => $value) { ?>
                <tr>
                    <td><?php echo $key; ?></td>
                    <td><?php echo '99.95%'; ?></td>
                    <td><?php echo $value; ?></td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td><?php echo $date; ?></td>
                <td><?php echo '99.95%'; ?></td>
                <td><?php echo $data; ?></td>
            </tr>
    <?php } ?>
    </table>
<?php } else { ?>
    <h3>No date selected</h3>
<?php } ?>