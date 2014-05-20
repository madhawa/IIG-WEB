<h2><?php   echo   $title; ?></h2>
<div class="service_input">
    Service Type
    <br>
    <select name="service_type" required>
        <option value="">Please Select</option>
        <option value="only IP Transit" <?php if ($service['service_type'] == 'only IP Transit')   echo   'selected'; ?> >only IP Transit</option>
        <option value="IP Bandwidth" <?php if ($service['service_type'] == 'IP Bandwidth')   echo   'selected'; ?> >IP Bandwidth</option>
        <option value="IP transit + IPLC[Full Circuit]" <?php if ($service['service_type'] == 'IP transit + IPLC[Full Circuit]')   echo   'selected'; ?> >IP transit + IPLC[Full Circuit]</option>
        <option value="P transit + IPLC[half Circuit]" <?php if ($service['service_type'] == 'IP transit + IPLC[half Circuit]')   echo   'selected'; ?> >IP transit + IPLC[half Circuit]</option>
        <option value="IPLC[Half Circuit]" <?php if ($service['service_type'] == 'IPLC[Half Circuit]')   echo   'selected'; ?> >IPLC[Half Circuit]</option>
        <option value="IPLC[Full Circuit]" <?php if ($service['service_type'] == 'IPLC[Full Circuit]')   echo   'selected'; ?> >IPLC[Full Circuit]</option>
        <option value="Global MPLS" <?php if ($service['service_type'] == 'Global MPLS')   echo   'selected'; ?> >Global MPLS</option>
        <option value="Internartional Ethernet" <?php if ($service['service_type'] == 'Internartional Ethernet')   echo   'selected'; ?> >Internartional Ethernet</option>
        <option value="Co-Location" <?php if ($service['service_type'] == 'Co-Location')   echo   'selected'; ?> >Co-Location</option>
    </select>
    <br>
    Circuit type:
    <br>
    <select name="circuit_type" required>
        <option value="">Please Select</option>
        <option value="Half-Circuit" <?php if ($service['circuit_type'] == 'Half-Circuit')   echo   'selected'; ?> >Half-Circuit</option>
        <option value="Full-Circuit" <?php if ($service['circuit_type'] == 'Full-Circuit')   echo   'selected'; ?> >Full-Circuit</option>
        <option value="OSS" <?php if ($service['circuit_type'] == 'OSS')   echo   'selected'; ?> >OSS</option>
        <option value="Partial" <?php if ($service['circuit_type'] == 'Partial')   echo   'selected'; ?> >Partial</option>
    </select>
    <br>
    Circuit ID (CIN):<br><input class="other_type" type="text" name="cin_no" value="<?php   echo   $service['cin'] ?>" required>
    <br>
    Circuit Digaram: <br><input class="other_type" type="file" name="ckt_diag">
    <input class="other_type" type="hidden" name="ckt_diag" value="<?php   echo   $service['ckt_diag']; ?>">
    <br>
    From: <br><input type="text" name="from" value="<?php   echo   $service['from'] ?>">
    <br>
    To: <br><input type="text" name="to" value="<?php   echo   $service['to']; ?>">
    <br>
    Link details<br>
    <textarea name="link_details"><?php   echo   $service['link_details'] ?></textarea>
    <br>
    bandwidth speed(CIR):
    <br>
    <input type="text" name="bw_speed_cir" value="<?php   echo   $service['bw_speed_cir']; ?>"> &nbsp; Mbps
    <br>
    Max Burstable Limit:
    <br>
    <input type="text" name="max_burstable_limit" value="<?php   echo   $service['max_burstable_limit']; ?>">&nbsp;Mbps
</div>