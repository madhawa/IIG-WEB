<form action="" method="post">
<input type="hidden" name="action" value="capacity-add">
<input type="hidden" name="staff_id" value="">
<input type="hidden" name="staff_name" value="<?php   echo   $thisuser->getName(); ?>">
<table>
    <tr>
        <td colspan="2"><h2 align="center">Upstream</h2></td>
    </tr>
    <tr>
        <th>Client Name</th>
        <td><input type="text" name="name" value="<?php   echo   $rep['name']; ?>" required></td>
    </tr>
    <tr>
        <th>Service Type</th>
        <td>
            <select name="service_type" required>
                <option value=""></option>
                <option value="only IP Transit" <?php if ($rep['service_type'] == 'only IP Transit')   echo   'selected'; ?> >only IP Transit</option>
                <option value="IP Bandwidth" <?php if ($rep['service_type'] == 'IP Bandwidth')   echo   'selected'; ?> >IP Bandwidth</option>
                <option value="IP transit + IPLC[Full Circuit]" <?php if ($rep['service_type'] == 'IP transit + IPLC[Full Circuit]')   echo   'selected'; ?> >IP transit + IPLC[Full Circuit]</option>
                <option value="P transit + IPLC[half Circuit]" <?php if ($rep['service_type'] == 'IP transit + IPLC[half Circuit]')   echo   'selected'; ?> >IP transit + IPLC[half Circuit]</option>
                <option value="IPLC[Half Circuit]" <?php if ($rep['service_type'] == 'IPLC[Half Circuit]')   echo   'selected'; ?> >IPLC[Half Circuit]</option>
                <option value="IPLC[Full Circuit]" <?php if ($rep['service_type'] == 'IPLC[Full Circuit]')   echo   'selected'; ?> >IPLC[Full Circuit]</option>
                <option value="Global MPLS" <?php if ($rep['service_type'] == 'Global MPLS')   echo   'selected'; ?> >Global MPLS</option>
                <option value="Internartional Ethernet" <?php if ($rep['service_type'] == 'Internartional Ethernet')   echo   'selected'; ?> >Internartional Ethernet</option>
                <option value="Co-Location" <?php if ($rep['service_type'] == 'Co-Location')   echo   'selected'; ?> >Co-Location</option>
            </select>
        </td>
    </tr>
    <tr>
        <th>Circuit type</th>
        <td>
            <select name="circuit_type" required>
                <option value=""></option>
                <option value="Half-Circuit" <?php if ($rep['circuit_type'] == 'Half-Circuit')   echo   'selected'; ?> >Half-Circuit</option>
                <option value="Full-Circuit" <?php if ($rep['circuit_type'] == 'Full-Circuit')   echo   'selected'; ?> >Full-Circuit</option>
                <option value="OSS" <?php if ($rep['circuit_type'] == 'OSS')   echo   'selected'; ?> >OSS</option>
                <option value="Partial" <?php if ($rep['circuit_type'] == 'Partial')   echo   'selected'; ?> >Partial</option>
            </select>
        </td>
    </tr>
    <tr>
        <th>Circuit ID</th>
        <td><input type="text" name="ckt_id" value="<?php   echo   $rep['ckt_id']; ?>" required></td>
    </tr>
    <tr>
        <th>Circuit Details</th>
        <td><textarea name="ckt_details" required><?php   echo   $rep['ckt_details']; ?></textarea></td>
    </tr>
    <tr>
        <th>Circuit location</th>
        <td><textarea name="ckt_location" required><?php   echo   $rep['ckt_location']; ?></textarea></td>
    </tr>
    <tr>
        <th></th>
        <td><button type="submit" class="save">Save</button></td>
    </tr>
</table>
</form>

<script type="text/javascript">
    $('div#services_content textarea, div#services_content input:not([type="submit"],[type="reset"],[type="button"]), div#services_content select').css('width', '300px');
</script>