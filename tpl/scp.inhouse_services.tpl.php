<select name="service_type">
    <option value="">Filter by Service Type</option>
    <option value="only IP Transit" >only IP Transit</option>
    <option value="IP Bandwidth" >IP Bandwidth</option>
    <option value="IP transit + IPLC[Full Circuit]" >IP transit + IPLC[Full Circuit]</option>
    <option value="P transit + IPLC[half Circuit]" >IP transit + IPLC[half Circuit]</option>
    <option value="IPLC[Half Circuit]" >IPLC[Half Circuit]</option>
    <option value="IPLC[Full Circuit]" >IPLC[Full Circuit]</option>
    <option value="Global MPLS" >Global MPLS</option>
    <option value="Internartional Ethernet" >Internartional Ethernet</option>
</select>
<h2><?php   echo   $title ?></h2>
<?php foreach( $services as $service ) { ?>
<table class="dtable">
    <tr>
        <th>Service Id</th>
        <td><?php   echo   $service['id'] ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?php   echo   $service['client_name'] ?></td>
    </tr>
    <tr>
        <th>Service Type</th>
        <td class="service_type"><?php   echo   $service['service_type'] ?></td>
    </tr>
    <tr>
        <th>Circuit Type</th>
        <td><?php   echo   $service['circuit_type'] ?></td>
    </tr>
    <tr>
        <th>Circuit ID</th>
        <td><?php   echo   $service['circuit_id'] ?></td>
    </tr>
    <tr>
        <th>Circuit details</th>
        <td class="long_text"><?php   echo   $service['circuit_details'] ?></td>
    </tr>
    <tr>
        <th>Circuit Location</th>
        <td class="long_text"><?php   echo   $service['circuit_location'] ?></td>
    </tr>
    <tr>
        <th>Activation Date</th>
        <td><?php   echo   $service['activation_date'] ?></td>
    </tr>
    <?php if ( $page == 'active' ) { ?>
    <tr>
        <form action="" method="post">
        <input type="hidden" name="action" value="discontinue">
        <input type="hidden" name="staff_id" value="<?php   echo   $thisuser->getId(); ?>">
        <input type="hidden" name="staff_name" value="<?php   echo   $thisuser->getName(); ?>">
        <input type="hidden" name="sid" value="<?php   echo   $service['id'] ?>">
        <th></th>
        <td><button type="submit">Discontinue</button></td>
        </form>
    </tr>
    <?php } else { ?>
        <tr>
        <th>Discontinution Date</th>
        <td><?php   echo   $service['discontinue_date'] ?></td>
    </tr>
    <?php } ?>
</table>
<?php } ?>

<script type="text/javascript">
    var prev_title = $('h2').text();
    $('[name="service_type"]').change(function(event) {
        var service_type = $(event.target).val();
        if ( service_type ) {
            var found = 0;
            $('table.dtable').each(function(index, element) {
                if ( service_type != $(element).find('td.service_type').text() ) {
                    $(element).hide();
                } else {
                    found++;
                    $(element).show();
                }
            });
            $('h2').text(found+' services found');
        } else {
            $('h2').text(prev_title);
            $('table.dtable').each(function(index, element) {
                $(element).show();
            });
        }
    });
</script>