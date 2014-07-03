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
?>
<br>
<br>
<div style="display: table; margin-left: auto; margin-right: auto">
    <form style="height: 50px; margin-right: 50px" target="" method="post">
    <select name="client_id">
        <option value="">Select a client</option>
        <?php foreach($clients as $c) { ?>
        <option value="<?php echo $c['client_id']; ?>" <?php if ( $client_id==$c['client_id'] ) echo 'selected'; ?>><?php echo $c['client_name']; ?></option>
        <?php } ?>
    </select>
    <input type="submit" name="select_client" value="apply">
    </form>
</div>

<?php if ( $client_id ) { ?>
    <table align="center">
        <tr>
            <td align="center" nowrap >
                <?php if ( $client_id ) { ?>
                <a href="sla.php?page=last_year&amp;client_id=<?php echo $client_id ?>" style="border: 2px solid green; padding: 5px">Last year</a>
                <a href="sla.php?page=current_year&amp;client_id=<?php echo $client_id ?>" style="border: 2px solid green; padding: 5px">Current year</a>
                <a href="sla.php?page=last_month&amp;client_id=<?php echo $client_id ?>" style="border: 2px solid green; padding: 5px">Last month</a>
                <a href="sla.php?page=current_month&amp;client_id=<?php echo $client_id ?>" style="border: 2px solid green; padding: 5px">Current month</a>
                <a href="sla.php?page=date_range&amp;client_id=<?php echo $client_id ?>" style="border: 2px solid green; padding: 5px">Customize date</a>
                <?php } else { ?>
                <a href="sla.php?page=last_year" style="border: 2px solid green; padding: 5px">Last year</a>
                <a href="sla.php?page=current_year" style="border: 2px solid green; padding: 5px">Current year</a>
                <a href="sla.php?page=last_month" style="border: 2px solid green; padding: 5px">Last month</a>
                <a href="sla.php?page=current_month" style="border: 2px solid green; padding: 5px">Current month</a>
                <a href="sla.php?page=date_range" style="border: 2px solid green; padding: 5px">Customize date</a>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px"></td>
        </tr>
        <tr>
            <td>
                <form action="sla.php" method="get">
                    <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
                    <span class="msg">customize date</span> &nbsp; from Date(before)&nbsp;<input id="sd" name="startDate" value="<?php echo Format::htmlchars($_REQUEST['startDate']) ?>"
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
    <h2 align="center"><?php echo ($client && $client->getName())?'Selected client : '.$client->getName():'no client selected' ?></h2>
    <h3 align="center"><?php echo $sla_title ?></h3>
    <br />
        <?php if ($data) { ?>
        <table align="center" class="dtable" border="2" cellpadding="10">
            <tr>
                <th>Month</th>
                <th>Committed SLA</th>
                <th>Achievement</th>
                <th>Raw data</th>
            </tr>
            <?php if (is_array($data)) { ?>
                <?php foreach ($data as $key => $value) { ?>
                    <tr>
                        <td><?php echo $key; ?></td>
                        <td><?php echo '99.95%'; ?></td>
                        <td><?php echo $value['sla']; ?></td>
                        <td><?php echo $value['raw_data']; ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </table>
    <?php } ?>
<?php } else { ?>
    <h2 align="center">no client selected</h2>
<?php } ?>

<script type="text/javascript">
    
</script>