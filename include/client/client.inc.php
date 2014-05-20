<div width="100%">
    <?php if ($errors['err']) { ?>
        <p align="center" id="errormessage"><?php echo $errors['err'] ?></p>
    <?php } elseif ($msg) { ?>
        <p align="center" class="infomessage"><?php echo $msg ?></p>
    <?php } elseif ($warn) { ?>
        <p class="warnmessage"><?php echo $warn ?></p>
    <?php } ?>
</div>

<h2 align="center">User Info</h2>
<table width="60%" border="1" cellspacing=2 cellpadding=5 class="tform">
    <tr>
        <th>Client Name</th>
        <td>
            <?php echo $rep['client_name'] ?>
        </td>
    </tr>

    <tr>
        <th><span class="highlighted_text_span"> LOGIN</span>:</th>
        <td>
            <?php echo $rep['username'] ?>
        </td>
    </tr>

    <tr>
        <th>Client type</th>
        <td><?php echo $rep['client_type'] ?></td>
    </tr>

    <tr>
        <th>Service Type</th>
        <td>
            <?php
            if (is_array($services)) {
                foreach ($services as $service) {
                    ?>
                    <table class="mytable">
                        <tr>
                            <th>Service Type</th>
                            <td><?php echo $service['service_type'] ?></td>
                        </tr>
                        <tr>
                            <th>Circuit Type</th>
                            <td><?php echo $service['circuit_type'] ?></td>
                        </tr>
                        <tr>
                            <th>CIN</th>
                            <td><?php echo $service['cin'] ?></td>
                        </tr>
                    </table>
                    <br>
                    <?php
                }
            }
            ?>
        </td>
    </tr>
    <tr>
        <th>Single Point of Contact(email):</th>
        <td><?php echo $rep['email']; ?></td>
    </tr>
    <tr>
        <th>Single Point of Contact(phone):</th>
        <td><?php echo $rep['phone']; ?></td>
    </tr>
    <tr>
        <th>Client ASN</th>
        <td><?php echo $rep['client_asn']; ?></td>
    </tr>
</table>

<?php
if ($all_staff) {
    ?>
    <br>
    <h4 align="center">Staffs</h4>
    <table class="tform mytable">
        <tr>
            <th>staff name</th>
            <th>staff designation</th>
            <th>staff department</th>
        </tr>
        <?php
        foreach ($all_staff as $staff) {
            ?>
            <tr>
                <td><?php echo $staff['staff_name'] ?></td>
                <td><?php echo $staff['designation'] ?></td>
                <td><?php echo $staff['department'] ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php }
?>
<script type="text/javascript">
    $('th').css({'width':'40%'});
</script>