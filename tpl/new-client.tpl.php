<?php    echo   $bc_text;  ?>
<div class="space"></div>
<div width="100%">
    <?php if($errors['err']) { ?>
        <p align="center" class="errormessage error"><?php    echo  $errors['err'] ?></p>
    <?php }elseif($msg) { ?>
        <p align="center" class="infomessage msg"><?php    echo  $msg ?></p>
    <?php }elseif($warn) { ?>
        <p class="warnmessage"><?php    echo  $warn ?></p>
    <?php } ?>
</div>

<h2 align="center"><?php    echo   $title  ?></h2>

<form action="" method="post" enctype="multipart/form-data">
<?php 
if ( $rep['client_id'] ) {
 ?>
<input type="hidden" name="do" value="update_client">
<input type="hidden" name="client_id" value="<?php    echo   $rep['client_id']  ?>">
<?php 
} else {
 ?>
<input type="hidden" name="do" value="newclient">
<?php 
}
 ?>
<table width="60%" border="0" cellspacing=0 cellpadding=2 class="tform" align="center">
    <tr>
        <th>Client Name</th>
        <td>
            <input type="text" name="client_name" value="<?php    echo   $rep['client_name'];  ?>" required>
            &nbsp;<span class="error"><?php    echo   $errors['name'];  ?></span>
        </td>
    </tr>

    <tr>
        <th><span class="highlighted_text_span"> LOGIN</span>:</th>
        <td>
            <input type="text" name="username" value="<?php    echo   $rep['username'];  ?>" required>
            &nbsp;<span class="error"><?php    echo   $errors['username'];  ?></span>
        </td>
    </tr>

    <tr>
        <th>Client of</th>
        <td>
            <select name="client_of" required>
                <option value="">Please Select</option>
                <option value="IIG" <?php  if ($rep['client_of'] == 'IIG')   echo   'selected';  ?> >IIG</option>
                <option value="ITC" <?php  if ($rep['client_of'] == 'ITC')   echo   'selected';  ?> >ITC</option>
            </select>
        </td>
    </tr>

    <tr>
        <th>Client type</th>
        <td>
            <select name="client_type" required>
                <option value="">Please Select</option>
                <option value="Enterprise Customer" <?php  if ($rep['client_type'] == 'Enterprise Customer')   echo   'selected';  ?> >Enterprise Customer</option>
                <option value="ISP/BWAs" <?php  if ($rep['client_type'] == 'ISP/BWAs')   echo   'selected';  ?> >ISP/BWAs</option>
                <option value="IIG" <?php  if ($rep['client_type'] == 'IIG')   echo   'selected';  ?> >IIG</option>
                <option value="IGW" <?php  if ($rep['client_type'] == 'IGW')   echo   'selected';  ?> >IGW</option>
                <option value="ANS" <?php  if ($rep['client_type'] == 'ANS')   echo   'selected';  ?> >ANS</option>
                <option value="Call Center/BPO" <?php  if ($rep['client_type'] == 'Call Center/BPO')   echo   'selected';  ?> >Call Center/BPO</option>
                <option value="NGO/Banks" <?php  if ($rep['client_type'] == 'NGO/Banks')   echo   'selected';  ?> >NGO/Banks</option>
                <option value="Government" <?php  if ($rep['client_type'] == 'Government')   echo   'selected';  ?> >Government</option>
                <option value="Others" <?php  if ($rep['client_type'] == 'Others')   echo   'selected';  ?> >Others</option>
            </select>
            <input type="text" name="other_type" value="<?php    echo   $rep['other_type']  ?>" placeholder="other client type">
        </td>
    </tr>
    <script type="text/javascript">
        if ( !$('[name="other_type"]').val() ) {
            $('[name="other_type"]').hide();
        }
        $('[name="client_type"]').change(function(event) {
            if ( $(event.target).val() == 'Others' ) {
                $('[name="other_type"]').show();
            } else {
                $('[name="other_type"]').hide();
            }
        });
    </script>

    <tr>
        <th>Add Service type/CIN</th>
        <td>
            <div id="other_type_container_div">
                <button class="save" type="button" name="add_other_type">add new</button>
                <input type="hidden" name="remove_cin" value="">
                <br>
                <br>
                <!-- loaded from database -->
                <div id="loaded">
                    <?php 
                    if ( count($services) ) {
                        foreach( $services as $service ) {
                         ?>
                            <div class="each_other_type_div">
                                <button class="save" type="button" name="edit_service">edit</button>
                                <button class="save button" type="button" name="delete_service">delete</button>
                                <div class="service_info">
                                    <table>
                                        <tr>
                                            <th>From</th>
                                            <td><?php    echo   $service['from_location']  ?></td>
                                        </tr>
                                        <tr>
                                            <th>To</th>
                                            <td><?php    echo   $service['to_location']  ?></td>
                                        </tr>
                                        <tr>
                                            <th>Link details</th>
                                            <td><?php    echo   $service['link_details']  ?></td>
                                        </tr>
                                        <tr>
                                            <th>Bandwidth speed(CIR)</th>
                                            <td><?php    echo   $service['bw_speed_cir']  ?></td>
                                        </tr>
                                        <tr>
                                            <th>Maximum burstable limit</th>
                                            <td><?php    echo   $service['max_burstable_limit']  ?></td>
                                        </tr>
                                        <tr>
                                            <th>Service Type</th>
                                            <td><?php    echo   $service['service_type']  ?></td>
                                        </tr>
                                        <tr>
                                            <th>Circuit Type</th>
                                            <td><?php    echo   $service['circuit_type']  ?></td>
                                        </tr>
                                        <tr>
                                            <th>Circuit ID (CIN)</th>
                                            <td><?php    echo   $service['cin']  ?></td>
                                        </tr>
                                        <?php  if ($service['ckt_diag']) {  ?>
                                        <tr>

                                            <th>Circuit Diagram</th>
                                            <td>
                                            <div class="yoxview">
                                                <a href=<?php    echo   '"'.'upload/ckt_diag/'.$service['ckt_diag'].'"'  ?> target="_blank">view</a>
                                            </div>
                                            </td>
                                        </tr>
                                        <?php  }  ?>
                                    </table>
                                </div>
                                <div class="service_input">
                                    Service Type
                                    <br>
                                    <select name="service_type[]" required>
                                        <option value="">Please Select</option>
                                        <option value="only IP Transit" <?php  if ($service['service_type'] == 'only IP Transit')   echo   'selected';  ?> >only IP Transit</option>
                                        <option value="IP Bandwidth" <?php  if ($service['service_type'] == 'IP Bandwidth')   echo   'selected';  ?> >IP Bandwidth</option>
                                        <option value="IP transit + IPLC[Full Circuit]" <?php  if ($service['service_type'] == 'IP transit + IPLC[Full Circuit]')   echo   'selected';  ?> >IP transit + IPLC[Full Circuit]</option>
                                        <option value="P transit + IPLC[half Circuit]" <?php  if ($service['service_type'] == 'IP transit + IPLC[half Circuit]')   echo   'selected';  ?> >IP transit + IPLC[half Circuit]</option>
                                        <option value="IPLC[Half Circuit]" <?php  if ($service['service_type'] == 'IPLC[Half Circuit]')   echo   'selected';  ?> >IPLC[Half Circuit]</option>
                                        <option value="IPLC[Full Circuit]" <?php  if ($service['service_type'] == 'IPLC[Full Circuit]')   echo   'selected';  ?> >IPLC[Full Circuit]</option>
                                        <option value="Global MPLS" <?php  if ($service['service_type'] == 'Global MPLS')   echo   'selected';  ?> >Global MPLS</option>
                                        <option value="Internartional Ethernet" <?php  if ($service['service_type'] == 'Internartional Ethernet')   echo   'selected';  ?> >Internartional Ethernet</option>
                                        <option value="Co-Location" <?php  if ($service['service_type'] == 'Co-Location')   echo   'selected';  ?> >Co-Location</option>
                                    </select>
                                    <br>
                                    Circuit type:
                                    <br>
                                    <select name="circuit_type[]" required>
                                        <option value="">Please Select</option>
                                        <option value="Half-Circuit" <?php  if ($service['circuit_type'] == 'Half-Circuit')   echo   'selected';  ?> >Half-Circuit</option>
                                        <option value="Full-Circuit" <?php  if ($service['circuit_type'] == 'Full-Circuit')   echo   'selected';  ?> >Full-Circuit</option>
                                        <option value="OSS" <?php  if ($service['circuit_type'] == 'OSS')   echo   'selected';  ?> >OSS</option>
                                        <option value="Partial" <?php  if ($service['circuit_type'] == 'Partial')   echo   'selected';  ?> >Partial</option>
                                    </select>
                                    <br>
                                    Circuit ID (CIN):<br><input class="other_type" type="text" name="cin_no[]" value="<?php    echo   $service['cin']  ?>" required>
                                    <br>
                                    Circuit Digaram: <br><input class="other_type" type="file" name="ckt_diag[]">
                                    <input class="other_type" type="hidden" name="ckt_diag[]" value="<?php    echo   $service['ckt_diag'];  ?>">
                                    <br>
                                    From: <br><input type="text" name="from[]" value="<?php    echo   $service['from']  ?>">
                                    <br>
                                    To: <br><input type="text" name="to[]" value="<?php    echo   $service['to'];  ?>">
                                    <br>
                                    Link details<br>
                                    <textarea name="link_details[]"><?php    echo   $service['link_details']  ?></textarea>
                                    <br>
                                    bandwidth speed(CIR):
                                    <br>
                                    <input type="text" name="bw_speed_cir[]" value="<?php    echo   $service['bw_speed_cir'];  ?>"> &nbsp; Mbps
                                    <br>
                                    Max Burstable Limit:
                                    <br>
                                    <input type="text" name="max_burstable_limit[]" value="<?php    echo   $service['max_burstable_limit'];  ?>">&nbsp;Mbps
                                </div>
                            </div>
                        <?php 
                        }
                    }
                     ?>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <th>Single Point of Contact(email):</th>
        <td>
            <input type="email" name="email" value="<?php    echo   $rep['email'];  ?>" required>
            &nbsp;<span class="error"><?php    echo   $errors['email'];  ?></span>
        </td>
    </tr>
    <tr>
        <th>Single Point of Contact(phone):</th>
        <td>
            <input type="text" name="phone" value="<?php    echo   $rep['phone'];  ?>" required>
            &nbsp;<span class="error">&nbsp;<?php    echo   $errors['phone'];  ?></span>
        </td>
    </tr>
    <tr>
        <th>Client ASN: &nbsp;&nbsp; <button type="button" name="add_asn_button">add ASN</button></th>
        <td>
        <input type="text" name="client_asn" placeholder="" value="<?php    echo   $rep['client_asn'];  ?>">
        </td>
    </tr>
    <tr>
        <th>Password:</th>
        <td><input type="password" name="password" AUTOCOMPLETE=OFF placeholder="client login password" <?php  if (!$client) {   echo   'required'; }  ?> >
            <button type="button" class="show_pass_buton" name="show_pass_buton">show password</button>
            <!--<button type="button" name="gen_pass" class="button">generate password</button>-->
        </td>
    </tr>
    <tr>
        <th>Password(Confirm):</th>
        <td>
            <input type="password" name="password_again" AUTOCOMPLETE=OFF placeholder="confirm password" <?php  if (!$client) {   echo   'required'; }  ?> >
        </td>
    </tr>
    <tr>
        <td colspan="2"><button class="save" type="submit">Save</button></td>
    </tr>
</table>


 </form>
<?php 
if ( $client && $client->getId() ) {
 ?>
<br>
<hr>
<br>
    <h2 align="center"><?php    echo   ($staffs && count($staffs))? count($staffs):0;  ?> Staffs under <?php    echo   $client_name  ?></h2>
<br>
<br>
<?php 
    if($staffs && count($staffs)) { //there are staffs
         ?>
        <form action="" post="post">
            <input type="hidden" name="client_id" value="<?php    echo   $rep['client_id'];  ?>">
            <input type="hidden" name="do" value="delete_staff">
            <table class="tform" width="70%" align="center">
                <tr>
                    <th></th>
                    <th>staff name</th>
                    <th>staff designation</th>
                    <th>staff department</th>
                </tr>
                <?php 
                foreach( $staffs as $staff ) {
                     ?>
                    <tr>
                        <td width="5%"><input type="checkbox" name="staff_ids[]" value="<?php    echo   $staff['id'];  ?>"></td>
                        <td><a href="client.php?do=view_staff&staff_id=<?php    echo   $staff['id'];  ?>&client_id=<?php    echo   $rep['client_id']  ?>"><?php    echo   $staff['staff_name']  ?></td>
                        <td><?php    echo   $staff['designation']  ?></td>
                        <td><?php    echo   $staff['department']  ?></td>
                    </tr>
                    <?php 
                }
                 ?>
                <tr>
                    <td colspan="4"><input class="save" type="submit" name="delete_staff" value="delete staff"></td>
                </tr>
            </table>

        </form>
        <?php 
    }
}
 ?>
<script type="text/javascript">
    $('[name="delete_staff"]').hide();
    $('[name="staff_ids[]"]').click(function() {
        $('[name="staff_ids[]"]').each(function(index, element) {
            if ( $(element).is(':checked') ) {
                $('[name="delete_staff"]').show();
            }
        });
    });

    $('[name="delete_staff"]').click(function(event) {
        var r=confirm("Do you really want to delete staffs ?");
        if (r!=true) {
            return false;
        }
    });
</script>