<h2><?php   echo   $title ?></h2>
<?php if ( $clients && count($clients) ) { ?>
<?php foreach( $clients as $client ) { ?>
<table class="dtable">
    <tr>
        <th>Client Name</th>
        <th>Email</th>
        <th>ASN</th>
        <th>Client Of</th>
    </tr>
        <td><?php   echo   $client['client_name'] ?></td>
        <td><?php   echo   $client['email'] ?></td>
        <td><?php   echo   $client['client_asn'] ?></td>
        <td><?php   echo   $client['client_type'] ?></td>
</table>
<?php } ?>
<script type="text/javascript">
    $('th').css('padding', '20px');
</script>
<?php } ?>