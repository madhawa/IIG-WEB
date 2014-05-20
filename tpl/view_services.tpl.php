<h2><?php   echo   $title; ?></h2>

<table>
    <tr>
        <th>View</th>
        <th>Client</th>
        <th>Service Type</th>
        <th>Circuit type</th>
        <th>CIN</th>
    </tr>
    <?php foreach( $services as $service ) { ?>
        <tr>
            <td><a hef="<?php   echo   SCP_URL . 'services.php?page=view_service&amp;id=' . $service['id'] ?>">views</a></td>
            <td><?php   echo   $service['client_name'] ?></td>
            <td><?php   echo   $service['service_type'] ?></td>
            <td><?php   echo   $service['circuit_type'] ?></td>
            <td><?php   echo   $service['cin'] ?></td>
        </tr>
    <?php } ?>
</table>